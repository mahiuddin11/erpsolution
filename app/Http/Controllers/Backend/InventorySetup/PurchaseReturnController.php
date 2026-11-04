<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Project;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Purchases;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\InventorySetup\PurchaseReturnService;
use App\Transformers\PurchaseReturnTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    private $purchaseReturnService;
    private $purchaseReturnTransformer;

    public function __construct(PurchaseReturnService $purchaseReturnService, PurchaseReturnTransformer $purchaseReturnTransformer)
    {
        $this->purchaseReturnService = $purchaseReturnService;
        $this->purchaseReturnTransformer = $purchaseReturnTransformer;
    }

    public function index()
    {
        $title = 'Purchase Return';
        return view('backend.pages.inventories.purchase.return.purchasereturn', get_defined_vars());
    }

    public function dataProcessing(Request $request)
    {
        $json_data = $this->purchaseReturnService->getList($request);

        return json_encode($this->purchaseReturnTransformer->dataTable($json_data));
    }

public function create()
{
    $title = 'Add New Purchase Return';
    $category_info = Category::with('parent')->where('status', 'Active')->get();

    $ledgers = ChartOfAccount::where('status', 'Active')
        ->where(function ($q) {
            $q->whereIn('accountable_type', [
                    \App\Models\Customer::class,
                    \App\Models\Supplier::class,
                ])
                ->orWhere('parent_id', 4);
        })
        ->with(['subAccount', 'parent'])
        ->get();

    $branch = Branch::where('status', 'Active')->where('parent_id', 0)->get();


    $nextReturnNo = 'PRT' . str_pad(((int) \App\Models\PurchaseReturn::withTrashed()->max('id') + 1), 5, '0', STR_PAD_LEFT);

    return view('backend.pages.inventories.purchase.return.create', get_defined_vars());
}

    public function store(Request $request)
    {
        

        try {
            $this->validate($request, $this->purchaseReturnService->storeValidation($request));

            
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

       
        try {
            $this->purchaseReturnService->store($request);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }

        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.purchase.return.index');
    }

    public function approvePage($id)
{
    $title = 'Approve Purchase Return';

    $purchaseReturn = PurchaseReturn::with([
        'details.product.category',
        'details.product.parent',
        'purchase',
        'branch',
        'warehouse',
        'project',
        'ledger',
        'supplier',
        'createdBy',
        'approvedBy',
    ])->findOrFail($id);

    if ($purchaseReturn->status !== 'pending') {
        return redirect()
            ->route('inventorySetup.purchase.return.show', $id)
            ->with('error', 'Only pending returns can be approved.');
    }

    $canDecide = \App\Helpers\Helper::roleAccess('inventorySetup.purchase.return.approve')
        || \App\Helpers\Helper::roleAccess('inventorySetup.purchase.return.reject');

    return view(
        'backend.pages.inventories.purchase.return.approve',
        compact('title', 'purchaseReturn', 'canDecide')
    );
}

    // public function edit($id)
    // {
    //     $title = 'Edit Purchase Return';

    //     try {
    //         $editData = $this->purchaseReturnService->edit($id);
    //     } catch (\Throwable $e) {
    //         return redirect()->route('inventorySetup.purchase.return.index')->with('error', $e->getMessage());
    //     }

    //     $purchaseReturn = $editData['purchaseReturn'];
    //     $purchase       = $editData['purchase'];
    //     $items          = $editData['items'];

    //     return view('backend.pages.inventories.purchase.return.edit', get_defined_vars());
    // }

public function edit($id)
{
    $title = 'Edit Purchase Return';

    try {
        $editData = $this->purchaseReturnService->edit($id);
    } catch (\Throwable $e) {
        return redirect()->route('inventorySetup.purchase.return.index')->with('error', $e->getMessage());
    }

    $purchaseReturn = $editData['purchaseReturn'];
    $purchase       = $editData['purchase'];
    $items          = $editData['items'];

    $category_info = Category::with('parent')->where('status', 'Active')->get();
   

    $ledgers = ChartOfAccount::where('status', 'Active')
        ->where(function ($q) {
            $q->whereIn('accountable_type', [
                    \App\Models\Customer::class,
                    \App\Models\Supplier::class,
                ])
                ->orWhere('parent_id', 4);
        })
        ->with(['subAccount', 'parent'])
        ->get();

    $branch = Branch::where('status', 'Active')->where('parent_id', 0)->get();
    $wearhouses = Warehouse::where('status', 'Active')->get();

    return view(
        'backend.pages.inventories.purchase.return.edit',
        compact('title', 'purchaseReturn', 'purchase', 'items', 'category_info', 'ledgers', 'branch', 'wearhouses' )
    );
}

public function update(Request $request, $id)
{

    $validation = $request->validate($this->purchaseReturnService->updateValidation($request, $id));

    try {
        $this->purchaseReturnService->update($request, $id);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage())->withInput();
    }

    return redirect()
        ->route('inventorySetup.purchase.return.index')
        ->with('success', 'Purchase Return updated successfully.');
}

   

    public function show($id)
    {
        $title = 'Purchase Return Details';

        $purchaseReturn = PurchaseReturn::with([
            'purchase',
            'purchase.project',
            'branch',
            'warehouse',
            'ledger',
            'supplier',
            'details.product',
            'details.purchaseDetail.supplier',
            'details.purchaseDetail.ledger',
            'approvedBy',
        ])->findOrFail($id);

        $companyInfo = Company::latest('id')->first();

        $canDecide = $purchaseReturn->status === 'pending'
            && Helper::roleAccess('inventorySetup.purchase.return.approve');

        return view('backend.pages.inventories.purchase.return.show', get_defined_vars());
    }

        public function destroy($id)
    {
        try {
            $purchaseReturn = PurchaseReturn::findOrFail($id);
            $returnNo = $purchaseReturn->return_no;
            $this->purchaseReturnService->destroy($id);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('inventorySetup.purchase.return.index')->with('success', 'Return #' . $returnNo . ' deleted.');
    }

    public function approve($id)
    {
        try {
            $purchaseReturn = $this->purchaseReturnService->approve($id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to approve return: ' . $e->getMessage());
        }

        return redirect()->route('inventorySetup.purchase.return.index')->with('success', 'Return #' . $purchaseReturn->return_no . ' approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        try {
            $purchaseReturn = $this->purchaseReturnService->reject($request, $id);

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('inventorySetup.purchase.return.index')->with('success', 'Return #' . $purchaseReturn->return_no . ' rejected.');
    }

    // public function searchPurchases(Request $request)
    // {
    //     $term = $request->get('term');
    //     $user = auth()->user();
    //     $query = Purchases::query();

    //     if ($user->type != 'Admin' && $user->branch_id) {
    //         $query->where('branch_id', $user->branch_id);
    //     }

    //     if ($term) {
    //         $query->where(function ($q) use ($term) {
    //             $q->where('invoice_no', 'like', "%{$term}%")
    //                 ->orWhere('custom_invoice', 'like', "%{$term}%");
    //         });
    //     }

    //     $purchases = $query->limit(20)->get();

    //     $results = $purchases->map(function ($purchase) {
    //         return [
    //             'id'   => $purchase->id,
    //             'text' => $purchase->invoice_no . ' - ' . optional($purchase->supplier)->name,
    //         ];
    //     });

    //     return response()->json(['results' => $results]);
    // }

    public function searchPurchases(Request $request)
    {
        $term = $request->get('term');
        $user = auth()->user();
        $query = Purchases::query();

        if ($user->type != 'Admin' && $user->branch_id) {
            
            $query->where(function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                    ->orWhere('type', 'Project');
            });
        }

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('invoice_no', 'like', "%{$term}%")
                    ->orWhere('custom_invoice', 'like', "%{$term}%");
            });
        }

        $purchases = $query->limit(20)->get();

        $results = $purchases->map(function ($purchase) {
            return [
                'id'   => $purchase->id,
                'text' => $purchase->invoice_no . (optional($purchase->supplier)->name ? ' - ' . optional($purchase->supplier)->name : '') . ($purchase->type === 'Project' ? ' [Project]' : ''),
            ];
        });

        return response()->json(['results' => $results]);
    }

    // public function getPurchaseDetails($id = null)
    // {
    //     $request    = request();
    //     $purchaseId = $request->get('purchase_id', $id);

    //     $purchase = Purchases::with([
    //         'supplier',
    //         'branch',
    //         'warehouse',
    //         'ledger',
    //         'details.product',
    //     ])->findOrFail($purchaseId);

    //     $items = $purchase->details->map(function ($detail) {
    //         $alreadyReturned = PurchaseReturnDetail::where('purchase_detail_id', $detail->id)
    //             ->whereHas('purchaseReturn', function ($q) {
    //                 $q->where('status', '!=', 'rejected');
    //             })
    //             ->sum('returned_qty');

    //         return [
    //             'purchase_detail_id'   => $detail->id,
    //             'product_id'           => $detail->product_id,
    //             'product_name'         => optional($detail->product)->name,
    //             'original_qty'         => (float) $detail->quantity,
    //             'already_returned_qty' => (float) $alreadyReturned,
    //             'unit_price'           => (float) $detail->unit_price,
    //             'purchasetype'         => $detail->purchasetype,
    //         ];
    //     })->filter(function ($item) {
    //         return $item['original_qty'] > $item['already_returned_qty'];
    //     })->values();

    //     return response()->json([
    //         'supplier_id'   => $purchase->supplier_id,
    //         'supplier_name' => optional($purchase->supplier)->name,

    //         'branch_id'     => $purchase->branch_id,
    //         'branch_name'   => optional($purchase->branch)->name,

    //         'warehouse_id'   => $purchase->warehouse_id,
    //         'warehouse_name' => optional($purchase->warehouse)->name,

    //         'ledger_id'     => $purchase->ledger_id,
    //         'ledger_name'   => optional($purchase->ledger)->account_name,

    //         'payment_type'  => $purchase->payment_type,
    //         'discount'      => (float) $purchase->discount,

    //         'items' => $items,
    //     ]);
    // }

    public function getPurchaseDetails($id = null)
{
    $request    = request();
    $purchaseId = $request->get('purchase_id', $id);

    $purchase = Purchases::with([
        'supplier',
        'branch',
        'warehouse',
        'ledger',
        'project',
        'details.product',
        'details.supplier',
        'details.ledger',
    ])->findOrFail($purchaseId);

    // Manual purchases can carry suppliers per line with an EMPTY header
    // supplier/ledger. Fall back to the first detail that has one, so the
    // header displays never stay blank while the lines have suppliers.
    $firstDetail = $purchase->details->first(function ($d) {
        return (int) $d->supplier_id > 0 || (int) $d->ledger_id > 0;
    });

    $headerSupplier   = $purchase->supplier ?: optional($firstDetail)->supplier;
    $headerLedger     = $purchase->ledger ?: optional($firstDetail)->ledger;
    $headerSupplierId = (int) ($purchase->supplier_id ?: optional($firstDetail)->supplier_id);
    $headerLedgerId   = (int) ($purchase->ledger_id ?: optional($firstDetail)->ledger_id);

    $items = $purchase->details->map(function ($detail) use ($headerSupplier, $headerLedger, $headerSupplierId, $headerLedgerId) {
        $alreadyReturned = PurchaseReturnDetail::where('purchase_detail_id', $detail->id)
            ->whereHas('purchaseReturn', function ($q) {
                $q->where('status', '!=', 'rejected');
            })
            ->sum('returned_qty');

        return [
            'purchase_detail_id'   => $detail->id,
            'product_id'           => $detail->product_id,
            'product_name'         => optional($detail->product)->name,
            'original_qty'         => (float) $detail->quantity,
            'already_returned_qty' => (float) $alreadyReturned,
            'unit_price'           => (float) $detail->unit_price,
            'purchasetype'         => $detail->purchasetype,
            'supplier_id'          => (int) ($detail->supplier_id ?: $headerSupplierId),
            'supplier_name'        => optional($detail->supplier)->name ?: optional($headerSupplier)->name,
            'ledger_id'            => (int) ($detail->ledger_id ?: $headerLedgerId),
            'ledger_name'          => optional($detail->ledger)->account_name ?: optional($headerLedger)->account_name,
        ];
    })->filter(function ($item) {
        return $item['original_qty'] > $item['already_returned_qty'];
    })->values();

    return response()->json([
        'supplier_id'   => $headerSupplierId,
        'supplier_name' => optional($headerSupplier)->name,

        'branch_id'     => $purchase->branch_id,
        'branch_name'   => optional($purchase->branch)->name,

        'warehouse_id'   => $purchase->warehouse_id,
        'warehouse_name' => optional($purchase->warehouse)->name,

        'ledger_id'     => $headerLedgerId,
        'ledger_name'   => optional($headerLedger)->account_name,

        'payment_type'  => $purchase->payment_type,
        'discount'      => (float) $purchase->discount,

        'type'          => $purchase->type,
        'purchase_type' => $purchase->purchase_type,
        'project_id'    => (int) $purchase->project_id,
        'project_name'  => optional($purchase->project)->name,

        'items' => $items,
    ]);
}



}
