<?php

namespace App\Http\Controllers\Backend\Project;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\PrDetails;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectTransfer;
use App\Models\ProjectTransferDetails;
use App\Models\StockSummary;
use App\Services\InventorySetup\PurchaseOrderService;
use App\Services\Project\ProjectTransferService;
use App\Transformers\ProjectTransformer;
use App\Transformers\PurchaseOrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectTransferController extends Controller
{
    /**
     * @var ProjectTransferService
     */
    private $systemService;
    /**
     * @var PurchaseOrderTransformer
     */
    private $systemTransformer;
    public function __construct(ProjectTransferService $ProjectTransferService, PurchaseOrderTransformer $PurchaseOrderTransformer)
    {
        $this->systemService = $ProjectTransferService;
        $this->systemTransformer = $PurchaseOrderTransformer;
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Project Transfer List';
        return view('backend.pages.inventories.project_transfer.index', get_defined_vars());
    }

    public function dataProcessing(Request $request)
    {

        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // old funtion
    // public function create()
    // {
    //     $title = 'Add New Project Transfer';
    //     $category_info = Category::where('status', 'Active')->get();
    //     $PurchaseOrder = PurchaseOrder::latest('id')->first();

    //     if ($PurchaseOrder) :
    //         $requisitionCode = $PurchaseOrder->id + 1;
    //     else :
    //         $requisitionCode = 1;
    //     endif;

    //     $requisitionCode = 'PT' . str_pad($requisitionCode, 5, "0", STR_PAD_LEFT);

    //     $purchaserequisitions = PurchaseRequisition::whereIn('status', ['Accepted'])->get();

    //     $suppliers = Supplier::where('status', 'Active')->get();
    //     $projects = Project::where('condition', 'One Going')->get();
    //     $branchs = Branch::get();
    //     return view('backend.pages.inventories.project_transfer.create', get_defined_vars());
    // }



    // new funciton
    public function create()
    {
        $title = 'Add New Project Transfer';

        $category_info = Category::where('status', 'Active')->get();

        $lastTransfer   = ProjectTransfer::latest('id')->first();
        $nextId         = $lastTransfer ? $lastTransfer->id + 1 : 1;
        $transferCode   = 'PT' . str_pad($nextId, 5, "0", STR_PAD_LEFT);

        $prIdsWithRemaining = PrDetails::where(function ($q) {
            $q->where('remaining_qty', '>', 0)
                ->orWhereNull('remaining_qty');
        })
            ->pluck('pr_id')
            ->unique();

        $purchaserequisitions = PurchaseRequisition::where('status', 'Accepted')
            ->whereIn('id', $prIdsWithRemaining)
            ->get();

        $suppliers = Supplier::where('status', 'Active')->get();
        $projects  = Project::where('condition', 'One Going')->get();
        $branchs   = Branch::get();

        return view('backend.pages.inventories.project_transfer.create', get_defined_vars());
    }

    public function invoice(Request $request, $id)
    {
        $title = 'Project Transfer Invoice';
        $purchaseorder = ProjectTransfer::findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.project_transfer.invoice', get_defined_vars());
    }

  public function store(Request $request)
{
    try {
        $this->validate($request, $this->systemService->storeValidation($request));
    } catch (ValidationException $e) {
        session()->flash('error', 'Validation error !!');
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
 
    $businessError = $this->systemService->storeBusinessRules($request);
 
    if ($businessError) {
        session()->flash('error', $businessError);
        return redirect()->back()->withInput();
    }
 
    $result = $this->systemService->store($request);
 
    // FIXED: repo fail korle null return kore (ar error flash kore rakhe).
    // Age ekhane sobsomoy "success" flash hoto, tai data save na hoileo success dekhato.
    if (!$result) {
        return redirect()->back()->withInput();
    }
 
    session()->flash('success', 'Data successfully save!!');
    return redirect()->route('project.transferproject.index');
}

    // public function edit($id)
    // {
    //     if (!is_numeric($id)) {
    //         session()->flash('error', 'Edit id must be numeric!!');
    //         return redirect()->back();
    //     }
    //     $editInfo = $this->systemService->details($id)->load('details');
    //     if (!$editInfo) {
    //         session()->flash('error', 'Edit info is invalid!!');
    //         return redirect()->back();
    //     }
    //     $title = 'Edit Project Tranfer';
    //     $category_info = Category::where('status', 'Active')->get();
    //     $purchaserequisitions = PurchaseRequisition::where('status', 'Pending')->orWhere('id', $editInfo->purchase_requisition_id)->get();
    //     $prDetails = PrDetails::where('pr_id', $editInfo->purchase_requisition_id)->get();
    //     $branchs = Branch::get();
    //     return view('backend.pages.inventories.project_transfer.edit', get_defined_vars());
    // }



    public function edit($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }

        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }

        $details = ProjectTransferDetails::with('product')
            ->where('project_transfer_id', $id)
            ->get();

        /* ---------- per-line max = live PR remaining + this line's own qty ---------- */
        $lineMax = [];
        if ($editInfo->purchase_requisition_id) {
            $prDetails = PrDetails::where('pr_id', $editInfo->purchase_requisition_id)->get();
            $byKey     = $prDetails->keyBy(fn($p) => $p->product_id . '|' . $p->purchasetype);
            $byProduct = $prDetails->keyBy('product_id');

            foreach ($details as $d) {
                if ($d->requested_qty === null) {
                    continue; // manually added line, no requisition cap
                }

                $prDetail = $byKey->get($d->product_id . '|' . $d->purchasetype)
                    ?? $byProduct->get($d->product_id);

                $liveRemaining = $prDetail
                    ? ($prDetail->remaining_qty !== null ? (float) $prDetail->remaining_qty : (float) $prDetail->qty)
                    : 0;

                $lineMax[$d->id] = $liveRemaining + (float) $d->qty;
            }
        }

        $title         = 'Edit Project Transfer';
        $category_info = Category::where('status', 'Active')->get();

        // the requisition select is disabled on edit, so the current one is enough
        $purchaserequisitions = PurchaseRequisition::where('id', $editInfo->purchase_requisition_id)->get();

        /* ---------- items of the same requisition that are not on this transfer yet ---------- */
        $remainingPrProducts = collect();
        if (
            $editInfo->purchase_requisition_id
            && in_array($editInfo->transfer_type, ['branch_to_project', 'project_to_project'])
        ) {
            $usedProductIds = $details->pluck('product_id')->all();

            $remainingPrProducts = PrDetails::with('product')
                ->where('pr_id', $editInfo->purchase_requisition_id)
                ->whereNotIn('product_id', $usedProductIds)
                ->where(function ($q) {
                    $q->where('remaining_qty', '>', 0)->orWhereNull('remaining_qty');
                })
                ->get()
                ->map(function ($pd) {
                    return [
                        'product_id'    => $pd->product_id,
                        'category_id'   => $pd->category_id,
                        'product_name'  => optional($pd->product)->name,
                        'purchasetype'  => $pd->purchasetype,
                        'remaining_qty' => $pd->remaining_qty !== null ? (float) $pd->remaining_qty : (float) $pd->qty,
                    ];
                })
                ->values();
        }

        // keep the projects already on this transfer even if they are no longer "One Going"
        $projects = Project::where('condition', 'One Going')
            ->orWhereIn('id', array_filter([$editInfo->project_id, $editInfo->to_project_id]))
            ->get();

        $branchs = Branch::get();

        /* ---------- branch + warehouse of the branch side of this transfer ---------- */
        // The blade reads these as arrays: $sourceResolved['branch_id'] / ['warehouse_id'].
        $empty          = ['branch_id' => null, 'warehouse_id' => null];
        $sourceResolved = $empty;
        $destResolved   = $empty;

        $side = [
            'branch_id'    => $editInfo->branch_id,      // real branch
            'warehouse_id' => $editInfo->warehouse_id,   // warehouse
        ];

        if ($editInfo->transfer_type === 'branch_to_project') {
            $sourceResolved = $side;
        } elseif ($editInfo->transfer_type === 'project_to_branch') {
            $destResolved = $side;
        }

        $sourceWarehouses = $sourceResolved['branch_id']
            ? $branchs->where('parent_id', $sourceResolved['branch_id'])->values()
            : collect();

        $destWarehouses = $destResolved['branch_id']
            ? $branchs->where('parent_id', $destResolved['branch_id'])->values()
            : collect();

        return view('backend.pages.inventories.project_transfer.edit', get_defined_vars());
    }

    public function searchpr(Request $request)
    {
        $purchase = $this->systemService->getprList($request);
        echo json_encode($purchase);
    }

    public function pr_voucher_product(Request $request)
    {
        $purchase = $this->systemService->getprProduct($request);
        echo json_encode($purchase);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
 public function update(Request $request, $id)
{
    if (!is_numeric($id)) {
        session()->flash('error', 'Edit id must be numeric!!');
        return redirect()->back();
    }
    $editInfo = $this->systemService->details($id);
    if (!$editInfo) {
        session()->flash('error', 'Edit info is invalid!!');
        return redirect()->back();
    }
    try {
        $this->validate($request, $this->systemService->updateValidation($request, $id));
    } catch (ValidationException $e) {
        session()->flash('error', 'Validation error !!');
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
 
    $result = $this->systemService->update($request, $id);
 
    // FIXED: repo fail korle null return kore (error flash kore rakhe)
    if (!$result) {
        return redirect()->back()->withInput();
    }
 
    session()->flash('success', 'Data successfully updated!!');
    return redirect()->route('project.transferproject.index');
}


    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function destroy($id)
    // {
    //     if (!is_numeric($id)) {
    //         return response()->json($this->systemTransformer->invalidId($id), 200);
    //     }
    //     $detailsInfo = $this->systemService->details($id);
    //     if (!$detailsInfo) {
    //         return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
    //     }
    //     $deleteInfo = $this->systemService->destroy($id);
    //     if ($deleteInfo) {
    //         return response()->json($this->systemTransformer->delete($deleteInfo), 200);
    //     }
    // }

    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }

        $deleteInfo = $this->systemService->destroy($id);

        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }

        return response()->json(['success' => false, 'message' => session('error', 'Delete failed')], 200);
    }



    public function filterproduct(Request $request)
    {
        $products = Product::where('category_id', $request->category_id)
            ->where('status', 'Active')
            ->get(['id', 'name', 'productCode']); //  productCode column name

        return response()->json($products->map(function ($p) {
            return ['id' => $p->id, 'name' => ($p->productCode ?? '') . ' - ' . $p->name];
        }));
    }

    public function availableStock(Request $request)
    {
   
    
    $type = $request->source_type === 'project'
            ? 'Project'
            : 'Branch';

        if ($type === 'Branch') {

            $query = StockSummary::where([
                'branch_id'    => $request->branch_id,
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'type'         => 'Branch',
            ]);
        } else {

            $query = StockSummary::where([
                'project_id' => $request->project_id,
                'product_id' => $request->product_id,
                'type'       => 'Project',
            ]);

            if ($request->filled('purchase_type')) {
                $query->where('purchasetype', $request->purchase_type);
            }
        }

        $qty = $query->value('quantity');

             
        return response()->json([
            'quantity' => (float) ($qty ?? 0),
        ]);
    }



    public function getWarehouses(Request $request)
    {
        $warehouses = Branch::where('parent_id', $request->branch_id)
            ->get(['id', 'name']);

        return response()->json($warehouses);
    }
}
