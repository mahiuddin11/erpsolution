<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\PrDetails;
use App\Models\Product;
use App\Models\Company;
use App\Models\Project;
use App\Models\Purchases;
use App\Models\PurchaseRequisition;
use App\Services\InventorySetup\PurchaseRequisitionService;
use App\Transformers\PurchaseRequisitionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseRequisitionController extends Controller
{

    /**
     * @var prService
     */
    private $systemService;
    /**
     * @var prTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param prService $systemService
     * @param prTransformer $systemTransformer
     */
    public function __construct(PurchaseRequisitionService $prService, PurchaseRequisitionTransformer $prTransformer)
    {
        $this->systemService = $prService;

        $this->systemTransformer = $prTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Purchase Requisition List';
        return view('backend.pages.inventories.pr.index', get_defined_vars());
    }

    public function dataProcessingAdjust(Request $request)
    {
        // dd('ff');
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Purchase Requisition';
        // $branch = Branch::where('status', 'Active')->get();

        $user = auth()->user();
        $project = Project::where('condition', 'One Going')->get();

        $category_info = Category::where('status', 'Active')->get();
        $PurchaseRequisition = PurchaseRequisition::latest('id')->first();
        if ($PurchaseRequisition) :
            $requisitionCode = $PurchaseRequisition->id + 1;
        else :
            $requisitionCode = 1;
        endif;
        $requisitionCode = 'PVR' . str_pad($requisitionCode, 5, "0", STR_PAD_LEFT);
        return view('backend.pages.inventories.pr.create', get_defined_vars());
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

        $title = 'Edit Purchase Requisition';
        $project = Project::all();
        $category_info  = Category::where('status', 'Active')->get();
        $requisition = PurchaseRequisition::find($id);
        $requisitionDetails = PrDetails::where('pr_id', $id)->get();
        return view('backend.pages.inventories.pr.edit', get_defined_vars());
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.purchaserequisition.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function approve($id)
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

        $title = 'Edit Purchase Requisition';
        $project = Project::all();
        $category_info  = Category::where('status', 'Active')->get();
        $requisition = PurchaseRequisition::find($id);
        $requisitionDetails = PrDetails::where('pr_id', $id)->get();
        return view('backend.pages.inventories.pr.approve', get_defined_vars());
    }

    public function invoice($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Invoice id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Invoice info is invalid!!');
            return redirect()->back();
        }

        $title = 'Purchase Requisition';
        $purchaseReq = PurchaseRequisition::findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.pr.invoice', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveUpdate(Request $request, $id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Approve id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Approve info is invalid!!');
            return redirect()->back();
        }
        // $this->systemService->approvepr($request, $id);
        $purchasereq['approve_by'] = Auth::user()->id;
        $purchasereq['approve_at'] = date('Y-m-d');
        $purchasereq['status'] = 'Accepted';
        PurchaseRequisition::where('id', $id)->update($purchasereq);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.purchaserequisition.index');
    }
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
        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.purchaserequisition.index');
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

    public function filterproduct(Request $req)
    {

        $product = Product::where('category_id', $req->id)->get();
        $data = "";
        if (count($product) > 0) {
            foreach ($product as $value) {
                $data .= ' <option value="' . $value->id . '">
                ' . $value->productCode . '-' . $value->name . '
                     </option>';
            }
        } else {

            $data .= '<option > Nothing found </option>';
        }

        echo json_encode($data);
    }
}
