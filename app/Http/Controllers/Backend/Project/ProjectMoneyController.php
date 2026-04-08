<?php

namespace App\Http\Controllers\Backend\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\User;
use App\Models\Adjust;
use App\Models\Product;
use App\Models\Navigation;
use App\Models\ChartOfAccount;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectMoney;
use DB;
use App\Services\Project\ProjectMoneyService;
use App\Transformers\ProjectMoneyTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\ValifdationException;
use Illuminate\Support\Facades\Validator;

class ProjectMoneyController extends Controller
{

    /**
     * @var ProjectMoneyService
     */
    private $systemService;

    /**
     * @var ProjectMoneyTransformer
     */
    private $systemTransformer;

    /**
     * ProjectMoneyController constructor.
     * @param ProjectMoneyService $systemService
     * @param ProjectMoneyService $systemTransformer
     */
    public function __construct(ProjectMoneyService $ProjectMoneyService, ProjectMoneyTransformer $ProjectMoneyTransformer)
    {
        $this->systemService = $ProjectMoneyService;
        $this->systemTransformer = $ProjectMoneyTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {


        $title = 'Project Balance List';
        return view('backend.pages.project_money.index', get_defined_vars());
    }

    public function dataProcessingBalance(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Project Balance';

        $projectLastData = ProjectMoney::latest('id')->first();
        if ($projectLastData) :
            $projectData = $projectLastData->id + 1;
        else :
            $projectData = 1;
        endif;

        $projectCode = 'PB' . str_pad($projectData, 5, "0", STR_PAD_LEFT);
        $project = Project::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');


        return view('backend.pages.project_money.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Project Invoice';

        $invoice = project::with(['details.product.category', 'branch', 'customer'])->findOrFail($id);

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.project_money.invoice', get_defined_vars());
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
        return redirect()->route('project.balance.index');
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
        $title = 'Edit Project';
        $project = Project::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');

        return view('backend.pages.project_money.edit', get_defined_vars());
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
        return redirect()->route('project.balance.index');
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
