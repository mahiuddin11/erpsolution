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
use App\Models\Project;
use App\Models\ProjectTransfer;
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
    public function create()
    {
        $title = 'Add New Project Transfer';
        $category_info = Category::where('status', 'Active')->get();
        $PurchaseOrder = PurchaseOrder::latest('id')->first();

        if ($PurchaseOrder) :
            $requisitionCode = $PurchaseOrder->id + 1;
        else :
            $requisitionCode = 1;
        endif;

        $requisitionCode = 'PT' . str_pad($requisitionCode, 5, "0", STR_PAD_LEFT);

        $purchaserequisitions = PurchaseRequisition::whereIn('status', ['Accepted'])->get();

        $suppliers = Supplier::where('status', 'Active')->get();
        $projects = Project::where('condition', 'One Going')->get();
        $branchs = Branch::get();
        return view('backend.pages.inventories.project_transfer.create', get_defined_vars());
    }
    public function invoice(Request $request, $id)
    {
        $title = 'Project Transfer Invoice';
        $purchaseorder = ProjectTransfer::findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.project_transfer.invoice', get_defined_vars());
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('project.transferproject.index');
    }
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id)->load('details');
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Edit Project Tranfer';
        $category_info = Category::where('status', 'Active')->get();
        $purchaserequisitions = PurchaseRequisition::where('status', 'Pending')->orWhere('id', $editInfo->purchase_requisition_id)->get();
        $prDetails = PrDetails::where('pr_id', $editInfo->purchase_requisition_id)->get();
        $branchs = Branch::get();
        return view('backend.pages.inventories.project_transfer.edit', get_defined_vars());
    }
    public function searchpr(Request $request)
    {
        $purchase = $this->systemService->getprList($request);
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
            // dd($e->getMessage(), $e->getFile(), $e->getLine());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->update($request, $id);
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
    }
}
