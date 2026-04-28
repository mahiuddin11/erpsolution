<?php

namespace App\Http\Controllers\Backend\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\User;
use App\Models\Adjust;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Grn;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectMoney;
use App\Models\ProjectTransfer;
use App\Models\Supplier;
use DB;
use App\Services\Project\ProjectService;
use App\Transformers\ProjectTransformer;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{

    /**
     * @var ProjectService
     */
    private $systemService;

    /**
     * @var ProjectTransformer
     */
    private $systemTransformer;

    /**
     * ProjectController constructor.
     * @param ProjectService $systemService
     * @param ProjectService $systemTransformer
     */
    public function __construct(ProjectService $projectService, ProjectTransformer $projectTransformer)
    {
        $this->systemService = $projectService;
        $this->systemTransformer = $projectTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $title = 'Project List';
        return view('backend.pages.project.index', get_defined_vars());
    }

    public function dataProcessingproject(Request $request)
    {
        $json_data = $this->systemService->getList($request);
      
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $title = 'Add New Project';

        $projectLastData = project::latest('id')->first();
        if ($projectLastData) :
            $projectData = $projectLastData->id + 1;
        else :
            $projectData = 1;
        endif;

        $projectCode = 'PRJ' . str_pad($projectData, 5, "0", STR_PAD_LEFT);
        $managers = User::get()->whereNotIn('id', [1])->where('status', 'Active');

        // $branchs = Branch::get()->where('status', 'Active');
        
        
        
        $user = auth()->user();
        $branchs = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branchs = $branchs->where('id', $user->branch_id);
            }
            $branchs = $branchs->get();

        $customer = Customer::all();

        $ledgers = ChartOfAccount::whereIn('accountable_type', [
            'App\Models\Customer',
            'App\Models\Supplier'
        ])->get();

    
        return view('backend.pages.project.create', get_defined_vars());
    }

    public function show($id)
    {
        $title = 'Project Invoice';

        $project_id = '';
    
            $projectDetails = '';
            $projectExpense = '';
            $productUses = '';
            $productReturn = '';
            $productIssue = '';

            $project_id = $id;
            $projectDetails = Project::join('users', 'users.id', '=', 'projects.manager_id')
                ->leftJoin('chart_of_accounts', 'chart_of_accounts.id', '=', 'projects.ledger_id')
                ->leftJoin('customers', 'customers.id', '=', 'projects.customer_id')
                ->where("projects.id", $project_id) // Specify the table name
                ->first([
                    'users.name as aname',
                    'projects.budget',
                    'projects.start_date',
                    'projects.estimate_profit',
                    'projects.condition',
                    'projects.closing',
                    'projects.end_date',
                    'projects.name as pname',
                    'users.phone as aphone',
                    'projects.address',
                    'projects.projectCode',
                    'customers.name as customer_name',
                    'chart_of_accounts.account_name as ledger_name'
                ]);
        // dd($projectDetails);

                // dd($projectDetails);
                // $productUses = ProductUse::join('product_use_details', 'product_use_details.product_use_id', '=', 'product_uses.id')
                //     ->join('products', 'products.id', '=', 'product_use_details.product_id')
                //     ->get(['products.name as pname', 'products.productCode as pcode', 'product_uses.invoice_no as in_no', 'product_use_details.updated_at as upDate', 'product_use_details.use_qty as uqty', 'products.purchases_price as purPrice', 'products.id as productId']);
                
                $accountsTrans = AccountTransaction::where('type', 5)->whereNull('credit')->where('project_id', $project_id)->get();
                $productgoodreceive = Grn::with('details')->where('project_id', $project_id)->get();
               
               
             $projectTransfer = ProjectTransfer::with('details')->where('project_id', $project_id)->get();
             $projectMoney = ProjectMoney::where('project_id', $project_id)->sum('debit');

           


            $directIncome = AccountTransaction::whereIn('account_id', getOldAccount(24)->pluck("id"))->where('project_id', $project_id)->get();
            $indirectIncome = AccountTransaction::whereIn('account_id', getOldAccount(25)->pluck("id"))->where('project_id', $project_id)->get();
            $directExpenses = AccountTransaction::whereIn('account_id', getOldAccount(20)->pluck("id"))->where('project_id', $project_id)->get();
            $indirectExpenses = AccountTransaction::whereIn('account_id', getOldAccount(21)->pluck("id"))->where('project_id', $project_id)->get();

            $invoice = Invoice::where('project_id', $project_id)->first();
        

        $companyInfo = Company::latest('id')->first();
        $project = Project::where('status', 'Active')->get();

        // $invoice = project::with(['details.product.category', 'branch', 'customer'])->findOrFail($id);

        $companyInfo = Company::latest('id')->first();

        //Dashbord Data 
        $totalBudget = $projectDetails->budget ?? 0;
        $totalExpense = ($directExpenses->sum('debit') ?? 0)
            + ($indirectExpenses->sum('debit') ?? 0)
            + ($productgoodreceive->sum('total_price') ?? 0);

        
        $totalInvoice = $invoice ? $invoice->sum('total_amount') : 0;


        $totalReceived = $projectMoney ?? 0;
        $dueAmount = max(0, $totalBudget - $totalReceived);
        $profit = ($totalBudget + ($directIncome->sum('credit') ?? 0) + ($indirectIncome->sum('credit') ?? 0))
            - $totalExpense;

        
        return view('backend.pages.project.summary', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
        return redirect()->route('project.project.index');
    }

    public function complete(Request $request)
    {
        try {
            $this->validate($request, $this->systemService->completeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->completestore($request);
        session()->flash('success', 'Project Completed!!');
        return redirect()->route('project.project.index');
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
        // $branchs = Branch::get()->where('status', 'Active');
        $user = auth()->user();
        $branchs = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branchs = $branchs->where('id', $user->branch_id);
        }
        $branchs = $branchs->get();
        $customer = Customer::all();
        $ledgers = ChartOfAccount::whereIn('accountable_type', [
            'App\Models\Customer',
            'App\Models\Supplier'
        ])->get();
        
        $managers = User::get()->whereNotIn('id', [1])->where('status', 'Active');

        return view('backend.pages.project.edit', get_defined_vars());
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
        return redirect()->route('project.project.index');
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

    public function loadmanager(Request $request)
    {
        // dd($request->all());
        $list = null;
        $users = User::where('branch_id', $request->branch_id)->get();
        // dd($users);
        if (!$users->isEmpty()) {
            $list .=  "<option selected disabled> Select Manager </option>";
            foreach ($users as $value) {
                $list .= "<option value=" . $value->id . ">" . $value->name . "</option>
                ";
            }
        } else {
            $list .= "<option selected disabled> No manager found </option>";
        }
        echo $list;
    }
}
