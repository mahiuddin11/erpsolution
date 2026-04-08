<?php

namespace App\Http\Controllers\Backend\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Adjust;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectRequisition;
use App\Models\ProjectRequisitionDetails;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use App\Services\Project\ProjectRequisitionService;
use App\Transformers\ProjectRequisitionTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class ProjectRequisitionController extends Controller
{

    /**
     * @var ProjectRequisitionService
     */
    private $systemService;

    /**
     * @var ProjectRequisitionTransformer
     */
    private $systemTransformer;

    /**
     * ProjectMoneyController constructor.
     * @param ProjectRequisitionService $systemService
     * @param ProjectRequisitionService $systemTransformer
     */
    public function __construct(ProjectRequisitionService $ProjectRequisitionService, ProjectRequisitionTransformer $ProjectRequisitionTransformer)
    {
        $this->systemService = $ProjectRequisitionService;
        $this->systemTransformer = $ProjectRequisitionTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Project Requisition List';
        $user = Auth::user();
        $project = Project::where('manager_id', $user->id)->pluck('condition')->first();
        // dd($project);
        return view('backend.pages.project_requisition.index', get_defined_vars());
    }

    public function actionindex(Request $request)
    {
        $title = 'Project Requisition List';
        return view('backend.pages.project_requistion_action.index', get_defined_vars());
    }

    public function dataProcessingrequisition(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    public function dataProcessingRequisitionAction(Request $request)
    {
        $json_data = $this->systemService->getpandingList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Project  Requisition';
        $user = Auth::user();
        // $branch = Branch::where('status', 'Active')->get();

        $user = auth()->user();
        // $branch = Branch::where('status', 'Active');
        // if ($user->branch_id !== null) {
        //     $branch = $branch->where('id', $user->branch_id);
        // }
        // $branch = $branch->get();


        $category_info = Category::where('status', 'Active')->get();
        $PurchaseRequisition = ProjectRequisition::latest('id')->first();
        $projects = Project::where('manager_id', $user->id)->where('status', 'Active')->get();

        if ($PurchaseRequisition) :
            $requisitionCode = $PurchaseRequisition->id + 1;
        else :
            $requisitionCode = 1;
        endif;

        $requisitionCode = 'PPR' . str_pad($requisitionCode, 5, "0", STR_PAD_LEFT);
        return view('backend.pages.project_requisition.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Project Invoice';
        $invoice = ProjectRequisition::find($id);
        $projectDetails = ProjectRequisitionDetails::where('project_requisition_id', $id)->get();
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.project_requisition.invoice', get_defined_vars());
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
        return redirect()->route('project.Productrequisition.index');
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
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Edit Project Requisition';
        $user = Auth::user();
        // $branch = Branch::where('status', 'Active')->get();

        $user = auth()->user();
        $branch = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branch = $branch->where('id', $user->branch_id);
        }
        $branch = $branch->get();


        $category_info  = Category::where('status', 'Active')->get();
        $requisition = ProjectRequisition::find($id);
        $projects = Project::where('status', 'Active')->get();
        $requisitionDetails = ProjectRequisitionDetails::where('project_requisition_id', $id)->get();
        return view('backend.pages.project_requisition.edit', get_defined_vars());
    }

    public function approve($id)
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
        $title = 'Approve Project Requisition';
        $branch = Branch::where('status', 'Active')->get();
        $category_info  = Category::where('status', 'Active')->get();
        $requisition = ProjectRequisition::find($id);
        // dd($requisition);
        $suppliers = Supplier::all();
        $projects = Project::where('status', 'Active')->get();
        $requisitionDetails = ProjectRequisitionDetails::where('project_requisition_id', $id)->get();
        $user = Auth::user();
        return view('backend.pages.project_requistion_action.approve', get_defined_vars());
    }

    public function checkstock(Request $request)
    {
        // dd($request->all());
        $message = [];
        for ($i = 0; $i < count($request->product_nm); $i++) {
            $product_array = array(
                'type' => 'Branch',
                'branch_id' =>  $request->branch_id,
                'product_id' => $request->product_nm[$i],
            );

            $stocksamary = StockSummary::where($product_array)->exists();
            $stocksamaryquntity = StockSummary::where($product_array)->pluck('quantity')->first();
            $qty = (int)$request->qty[$i];

            if (!$stocksamary || $stocksamaryquntity < $qty) {
                $status = '<span class="error text-red text-bold">product not available in stock!!</span>';
            } else {
                // dd('out');
                $status = '<span class="error text-green text-bold">product available in stock!!</span>';
            }
            $message[$request->product_nm[$i]] = [$status];
        }


        echo json_encode($message);
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
        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('project.Productrequisition.index');
    }

    public function storeapprove(Request $request, $id)
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
        try {
            $this->validate($request, $this->systemService->approveupdateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->approveupdate($request, $id);

        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('project.RequisitionAction.index');
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
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo = $this->systemService->statusUpdate($id, $status);
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
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo = $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }

    public function getProductListForproject(Request $request)
    {
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);
        //   dd($productList);
        $add = '';
        if (!empty($productList)) :
            $add .= "<option value=''>Select Product</option>";
            foreach ($productList as $key => $value) :
                $add .= "<option proName='" . $value->name . "'   value='" . $value->id . "'>$value->productCode - $value->name</option>";
            endforeach;
            echo $add;
            die;
        else :
            echo "<option value='' selected disabled>No Product Available</option>";
            die;
        endif;
    }

    public function getCustomerBalance(Request $request)
    {

        $finalValue = 0;
        $conditionalArray = array(
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
        );

        $debit = Adjust::where($conditionalArray)->sum('debit');
        $credit = Adjust::where($conditionalArray)->sum('credit');

        $adjustArray = array(
            'customer_id' => $request->customer_id,
            'payment_type' => 'Credit',
        );

        $expireData = Adjust::where($adjustArray)->orderBy('id', 'desc')->first();
        $finalValue = $debit - $credit;
        echo json_encode(array('finalBalance' => $finalValue, 'expireData' => $expireData['expire_date']));
    }

    public function unitPiceForproject(Request $request)
    {
        $proid = $request->productId;
        $productPrice = Product::get()->where('id', $proid)->first();

        echo json_encode(array('purchases_price' => $productPrice->purchases_price, 'project_price' => $productPrice->project_price));
    }

    function getProductStock(Request $request)
    {

        $product_id = $request->productId;
        $productStock = StockSummary::get()->where('product_id', $product_id)->first();
        if (!empty($productStock->quantity) && $productStock->quantity > 0) :
            echo $productStock->quantity;
        endif;
    }
}
