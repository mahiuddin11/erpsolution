<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\PrDetails;
use App\Models\Project;
use App\Models\PurchaseOrderDetail;
use App\Models\SupplierSelectPrice;
use App\Services\InventorySetup\PurchaseOrderService;
use App\Transformers\PurchaseOrderTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{

    private $purchaseService;


    private $systemTransformer;

    public function __construct(PurchaseOrderService $purchaseService, PurchaseOrderTransformer $PurchaseOrderTransformer)
    {
        $this->purchaseService = $purchaseService;
        $this->systemTransformer = $PurchaseOrderTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Purchase List';
        return view('backend.pages.inventories.po.index', get_defined_vars());
    }

    public function datapurchaseorder(Request $request)
    {
        $json_data = $this->purchaseService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New purchase';
        $category_info = Category::where('status', 'Active')->get();
        $PurchaseOrder = PurchaseOrder::latest('id')->first();
        if ($PurchaseOrder) :
            $requisitionCode = $PurchaseOrder->id + 1;
        else :
            $requisitionCode = 1;
        endif;
        $requisitionCode = 'PO' . str_pad($requisitionCode, 5, "0", STR_PAD_LEFT);
        $purchaserequisitions = PurchaseRequisition::where('status', 'Accepted')->get();
        $suppliers = Supplier::where('status', 'Active')->get();
        $projects = Project::where('condition', 'One Going')->get();

        $accounts = ChartOfAccount::whereIn('accountable_type', ['App\Models\Supplier', 'App\Models\Customer'])
            ->where('status', 'Active')
            ->get();

        // dd($leadgers);

        return view('backend.pages.inventories.po.create', get_defined_vars());
    }

    public function invoice(Request $request, $id)
    {
        // dd($request->all() , $id);
        $title = 'Purchase Order Invoice';
        $purchaseorder = PurchaseOrder::findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.po.invoice', get_defined_vars());
    }

    public function approve(Request $request, $id)
    {
        $title = 'Purchase Order Invoice';
        // dd($title);
        $purchaseorder = PurchaseOrder::findOrFail($id);
        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.inventories.po.approve', get_defined_vars());
    }

    public function supplierPurchaseApprove(Request $request)
    {

        $request->validate(([
            'suplirePrice' => 'required'
        ]));
        try {


            $purchaseorder['approved_by'] = Auth::user()->id;
            $purchaseorder['approved_at'] = date('Y-m-d');
            $purchaseorder['status'] = 'Accepted';
            PurchaseOrder::where('id', $request->purchase_order)->update($purchaseorder);
            $suppliersPrices =   SupplierSelectPrice::whereIn('id', $request->suplirePrice)->update(['status' => 1]);
        } catch (Exception $e) {
            session()->flash('error', 'Something was wrong!!');
            return redirect()->back();
        }

        session()->flash('success', 'Approve Successfully!!');
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {


        try {
            $this->validate($request, $this->purchaseService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->purchaseService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.purchaseorder.index');
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

        $editInfo = $this->purchaseService->details($id)->load('details');

        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Edit Purchase Order';
        $category_info = Category::where('status', 'Active')->get();
        $purchaserequisitions = PurchaseRequisition::where('status', 'Pending')->orWhere('id', $editInfo->purchase_requisition_id)->get();
        $prDetails = PrDetails::where('pr_id', $editInfo->purchase_requisition_id)->get();
        $suppliers = Supplier::where('status', 'Active')->get();
        $accounts = ChartOfAccount::whereIn('accountable_type', ['App\Models\Supplier', 'App\Models\Customer'])
            ->where('status', 'Active')
            ->get(); //new added

        $purchaseOrder = PurchaseOrderDetail::where('purchase_order_id', $id);
        $purchaseOrderDtlId = $purchaseOrder->pluck('id')->toArray();

        $purchaseOrderDtls = $purchaseOrder->get();
        $selectedSupplier = SupplierSelectPrice::whereIn('purchase_order_id', $purchaseOrderDtlId)->get();

        return view('backend.pages.inventories.po.edit', get_defined_vars());
    }

    public function selectSupplier(Request $request)
    {
        $selectSupplier = SupplierSelectPrice::where('purchase_order_id', $request->order_id)->where('status', 1)->first();

        return response()->json($selectSupplier);
    }

    public function searchpr(Request $request)
    {
        $purchase = $this->purchaseService->getprList($request);
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
        $editInfo = $this->purchaseService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
    
        try {
            
            $this->validate($request, $this->purchaseService->updateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        
        $this->purchaseService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.purchaseorder.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statusUpdate($id, $status)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->purchaseService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo = $this->purchaseService->statusUpdate($id, $status);
        if ($statusInfo) {
            return response()->json($this->systemTransformer->statusUpdate($statusInfo), 200);
        }
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->purchaseService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo = $this->purchaseService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }
}
