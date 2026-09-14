<?php

namespace App\Http\Controllers\Backend\Sale;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
use App\Models\Warehouse;
use App\Services\Sale\SalesReturnService;
use App\Transformers\SaleReturnTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class SaleReturnController extends Controller
{
    //


    private $SaleReturnService;


    private $SaleReturnServiceTransfromer;

    public function __construct(SalesReturnService $SaleReturnService, SaleReturnTransformer $SaleReturnServiceTransfromer)
    {
        $this->SaleReturnService = $SaleReturnService;
        $this->SaleReturnServiceTransfromer = $SaleReturnServiceTransfromer;
    }


    public function index()
    {
        $title = 'Sale Return';
        return view('backend.pages.sale.return.return', get_defined_vars());
    }


    public function dataProcessingSaleReturn(Request $request)
    {
        $json_data = $this->SaleReturnService->getList($request);

        return json_encode($this->SaleReturnServiceTransfromer->dataTable($json_data));
    }


    public function create()
    {
        $title = 'Add New Sale Return';

        $user = auth()->user();

        $accountEagerLoad = [
            'parent',
            'subAccount.parent',
            'subAccount.subAccount.parent',
            'subAccount.subAccount.subAccount.parent',
        ];

        if ($user->type == "Admin" || !$user->branch_id) {
            $refundAccounts = ChartOfAccount::whereIn('id', [7, 8])
                ->where('status', 'Active')
                ->with($accountEagerLoad)
                ->get();
        } else {
            $refundAccounts = ChartOfAccount::whereIn('id', [7, 8])
                ->where('status', 'Active')
                ->where('branch_id', $user->branch_id)
                ->with($accountEagerLoad)
                ->get();
        }

        $saleReturnLastData = SaleReturn::latest('id')->first();
        $nextId = $saleReturnLastData ? $saleReturnLastData->id + 1 : 1;
        $return_no = 'SVR' . str_pad($nextId, 5, "0", STR_PAD_LEFT);

        return view('backend.pages.sale.return.create', get_defined_vars());
    }


    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->SaleReturnService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->SaleReturnService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('sale.sale.return');
    }

    public function searchInvoices(Request $request)
    {

        $term = $request->get('term');
        $user = auth()->user();
        $query = Sale::query();


        if ($user->type != 'Admin' && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('invoice_no', 'like', "%{$term}%");
            });
        }

        $sales = $query->limit(20)->get();



        $results = $sales->map(function ($sale) {
            return [
                'id'   => $sale->id,
                'text' => $sale->invoice_no . ' - ' . optional($sale->customer)->account_name,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function getInvoiceDetails($id = null)
    {
        $request = request();
        $saleId  = $request->get('sale_id', $id);

        $sale = Sale::with([
            'customer',
            'branch',
            'Warehouse',
            'ledger',
            'salesPerson',
            'details.product',
        ])->findOrFail($saleId);

        $items = $sale->details->map(function ($detail) {

            $alreadyReturned = SaleReturnDetails::where('sale_detail_id', $detail->id)
                ->whereHas('saleReturn', function ($q) {
                    $q->where('status', '!=', 'rejected');
                })
                ->sum('returned_qty');

            return [
                'sale_detail_id'       => $detail->id,
                'product_id'           => $detail->product_id,
                'product_name'         => optional($detail->product)->name,
                'original_qty'         => (float) $detail->qty,
                'already_returned_qty' => (float) $alreadyReturned,
                'unit_price'           => (float) $detail->rate,     // rate = unit price (excl. VAT)
                'vat_percent'          => (float) $detail->vat,      // percentage, e.g. 5, 15
            ];
        })->filter(function ($item) {
            return $item['original_qty'] > $item['already_returned_qty'];
        })->values();

        $stockLocationType = $sale->warehouse_id ? 'warehouse' : 'branch';
        $stockLocationId   = $sale->warehouse_id ?: $sale->branch_id;

        return response()->json([
            'customer_id'         => $sale->ledger_id,
            'customer_name'       => optional($sale->customer)->name,

            'branch_id'           => $sale->branch_id,
            'branch_name'         => optional($sale->branch)->name,

            'warehouse_id'        => $sale->warehouse_id,
            'warehouse_name'      => optional($sale->Warehouse)->name,

            'stock_location_type' => $stockLocationType,
            'stock_location_id'   => $stockLocationId,

            'ledger_id'           => $sale->ledger_id,
            'ledger_name'         => optional($sale->ledger)->account_name,

            'sales_person_id'     => $sale->sales_person_id,
            'sales_person_name'   => optional($sale->salesPerson)->name,
            'discount'            => (float) $sale->discount,
            'carrying_cost'       => (float) $sale->carrying_cost,
            'labor_bill'          => (float) $sale->labor_bill,

            'items' => $items,
        ]);
    }


    public function approve($id, $userId)
    {
        DB::beginTransaction();

        $saleReturn = SaleReturn::with('details')->lockForUpdate()->findOrFail($id);

        if ($saleReturn->status !== 'pending') {
            throw new \InvalidArgumentException(
                "This return is already '{$saleReturn->status}' and cannot be approved again."
            );
        }

        $sale = $saleReturn->sale;

        foreach ($saleReturn->details as $detail) {

            if ($detail->condition !== 'good') {
                // Damaged item স্টকে ফেরত যাবে না — শুধু ledger এ adjust হবে
                continue;
            }

            // ==========================================================
            // TODO: STOCK REVERSAL
            // $sale->warehouse_id থাকলে সেই warehouse এ, নাহলে $sale->branch_id
            // এ $detail->product_id এর stock $detail->returned_qty দিয়ে বাড়াতে হবে।
            //
            // উদাহরণ (আপনার আসল Service/method দিয়ে বদলাতে হবে):
            // app(StockService::class)->increaseStock(
            //     productId: $detail->product_id,
            //     branchId: $sale->branch_id,
            //     warehouseId: $sale->warehouse_id,
            //     qty: $detail->returned_qty
            // );
            // ==========================================================
        }

        // ==========================================================
        // TODO: LEDGER REVERSAL ENTRY
        // $saleReturn->ledger_id (customer account) এর বিপরীতে
        // $saleReturn->grand_total পরিমাণ reversal entry পোস্ট করতে হবে
        // (customer account কে credit / Sales Return account কে debit)।
        //
        // উদাহরণ (আপনার আসল Voucher Service দিয়ে বদলাতে হবে):
        // app(VoucherService::class)->createCreditVoucher([
        //     'voucher_no'  => 'CV' . ...,
        //     'account_id'  => $saleReturn->ledger_id,
        //     'amount'      => $saleReturn->grand_total,
        //     'reference'   => $saleReturn->return_no,
        //     'narration'   => 'Sale Return - ' . $saleReturn->return_no,
        // ]);
        // ==========================================================

        $saleReturn->status      = 'approved';
        $saleReturn->approved_by = $userId;
        $saleReturn->approved_at = now();
        $saleReturn->save();

        DB::commit();
        return $saleReturn;
    }

    public function reject($id, $userId, $reason)
    {
        return DB::transaction(function () use ($id, $userId, $reason) {

            $saleReturn = SaleReturn::lockForUpdate()->findOrFail($id);

            if ($saleReturn->status !== 'pending') {
                throw new \InvalidArgumentException(
                    "This return is already '{$saleReturn->status}' and cannot be rejected."
                );
            }

            $saleReturn->status            = 'rejected';
            $saleReturn->approved_by       = $userId;
            $saleReturn->approved_at       = now();
            $saleReturn->rejection_reason  = $reason;
            $saleReturn->save();

            return $saleReturn;
        });
    }
}
