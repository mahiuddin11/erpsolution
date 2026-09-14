<?php

namespace App\Http\Controllers\Backend\Sale;

use App\Helpers\Helper;
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
use App\Models\Stock;
use App\Models\StockSummary;
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

    public function show($id)
    {
        $title = 'Sale Return Details';


        // NOTE: adjust relation names below if they differ in your actual SaleReturn model.
        $saleReturn = SaleReturn::with([
            'sale',
            'branch',
            'warehouse',
            'ledger',
            'salesPerson',
            'details.product',
            'approvedBy',
        ])->findOrFail($id);



        $canDecide = $saleReturn->status === 'pending' && Helper::roleAccess('sale.return.approve');

        return view('backend.pages.sale.return.show', get_defined_vars());
    }

    public function approve($id)
    {


        $saleReturn = SaleReturn::with(['sale', 'details'])->findOrFail($id);



        if ($saleReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }
        DB::beginTransaction();


        $sale = $saleReturn->sale;
        $targetWarehouseId = $sale->warehouse_id ?? null;
        $targetBranchId    = $sale->branch_id;



        foreach ($saleReturn->details as $detail) {

            if ($detail->condition === 'good') {
                $this->restockReturnedItem($saleReturn, $detail, $targetBranchId, $targetWarehouseId);
            }
        }

        // TODO: accounting reversal — needs your actual account_transactions
        // insert pattern to mirror correctly (see questions below).
        // if ($sale->payment_type === 'Due') {
        //     // reduce the customer's receivable by the return amount
        // } else {
        //     // sale was paid — create a payable voucher owed back to the customer
        // }

        $saleReturn->status      = 'approved';
        $saleReturn->approved_by = auth()->id();
        $saleReturn->approved_at = now();
        $saleReturn->save();
        DB::commit();

        return redirect()->route('sale.sale.return')->with('success', 'Return #' . $saleReturn->return_no . ' approved successfully.');
    }


    private function restockReturnedItem($saleReturn, $detail, $branchId, $warehouseId)
    {

        $stock = new Stock();
        $stock->product_id   = $detail->product_id;
        $stock->branch_id    = $branchId;
        $stock->warehouse_id = $warehouseId;
        $stock->general_id   = $detail->sale_return_id;
        $stock->quantity     = $detail->returned_qty;
        $stock->unit_price   = $detail->unit_price;
        $stock->total_price  = $detail->line_amount;
        $stock->invoice_no   = $saleReturn->return_no;
        $stock->date         = $saleReturn->return_date;
        $stock->status       = 'Sale Return'; // FIX: was 'Sales Return' — didn't match your actual stocks.status enum value, so it would silently never show up in reports filtering on that status
        $stock->created_by   = auth()->id();
        $stock->save();

        $purchasetype = optional($detail->saleDetail)->purchasetype;

        $summaryQuery = StockSummary::where('product_id', $detail->product_id)
            ->where('type', 'Branch')
            ->where('branch_id', $branchId)
            ->where('purchasetype', $purchasetype);

        if ($warehouseId) {
            $summaryQuery->where('warehouse_id', $warehouseId);
        }

        $summary = $summaryQuery->first();

        if ($summary) {
            $summary->increment('quantity', $detail->returned_qty);
        } else {
            $newSummary = new StockSummary();
            $newSummary->product_id   = $detail->product_id;
            $newSummary->branch_id    = $branchId;
            $newSummary->warehouse_id = $warehouseId;
            $newSummary->type         = 'Branch';
            $newSummary->purchasetype = $purchasetype;
            $newSummary->quantity     = $detail->returned_qty;
            $newSummary->save();
        }
    }


    public function reject($id)
    {
        $saleReturn = SaleReturn::findOrFail($id);

        if ($saleReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be rejected.');
        }

        $saleReturn->status = 'rejected';
        $saleReturn->save();

        return redirect()->route('sale.sale.return')->with('success', 'Return #' . $saleReturn->return_no . ' rejected.');
    }
}
