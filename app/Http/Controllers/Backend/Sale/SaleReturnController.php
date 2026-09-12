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
use Illuminate\Http\Request;
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

        // Branch/Warehouse are no longer manually selected here — they are pulled
        // automatically from the original invoice once selected (see getInvoiceDetails()).
        // >>> REMOVED: manual $branch fetch + dropdown data, per updated requirement.

        // Refund account choices = Cash in Hand (id:7) / Cash at Bank (id:8) — only used
        // when refund_method = cash_bank (an ACTUAL cash/bank payout on top of the ledger
        // reversal). This is separate from the original sale's ledger, which is auto-pulled.
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
            //code...

            $this->SaleReturnService->store($request);
        } catch (\Throwable $th) {
            //throw $th;
        }
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

            'ledger_id'           => $sale->account_id,
            'ledger_name'         => optional($sale->ledger)->account_name,

            'sales_person_id'     => $sale->sales_person_id,
            'sales_person_name'   => optional($sale->salesPerson)->name,

            // শুধু তথ্য হিসেবে — return calculation এ কখনো যোগ হয় না
            'discount'            => (float) $sale->discount,
            'carrying_cost'       => (float) $sale->carrying_cost,
            'labor_bill'          => (float) $sale->labor_bill,

            'items' => $items,
        ]);
    }
}
