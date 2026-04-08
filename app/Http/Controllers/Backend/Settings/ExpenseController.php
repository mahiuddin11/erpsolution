<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Transection;
use helper;
use App\Services\Settings\ExpenseService;
use App\Transformers\ExpenseTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{

    /**
     * @var ExpenseService
     */
    private $systemService;
    /**
     * @var ExpenseTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param ExpenseService $systemService
     * @param ExpenseTransformer $systemTransformer
     */
    public function __construct(ExpenseService $expenseService, ExpenseTransformer $expenseTransformer)
    {
        $this->systemService = $expenseService;
        $this->systemTransformer = $expenseTransformer;
    }



    function expenseFix()
    {
        $Expense = Expense::get();
        // dd($Expense->all());

        foreach ($Expense as $each) {
            $transection = new transection();
            $transection->account_id = $each->account_id;
            $transection->branch_id = $each->branch_id;
            $transection->credit = $each->amount;
            $transection->amount = $each->amount;
            $transection->note = $each->note;
            $transection->date = $each->date;
            $transection->payment_id = $each->id;
            $transection->type = 4;
            $transection->user_id = 2;
            $transection->created_by = 2;
            $transection->save();
        }

        die('hello');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //$this->expenseFix();
        $title = 'Expense List';
        return view('backend.pages.settings.expense.index', get_defined_vars());
    }

    public function dataProcessingExpense(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Expense';
        $category = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', 0);
        $branch = Branch::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');
        return view('backend.pages.settings.expense.create', get_defined_vars());
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
        return redirect()->route('settings.expense.index');
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

        $editInfo =   $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }

        $title = 'Edit Expense';
        $category = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', 0);
        $branch = Branch::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');
        $expense = Expense::findOrFail($id);
        $subcategorys = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', $expense->expensecategorie_id);
        $remainingBalance = Transection::where('account_id', '=', $expense->chartofaccount_id)->sum('debit') - Transection::where('account_id', '=', $expense->chartofaccount_id)->sum('credit');
        return view('backend.pages.settings.expense.edit', get_defined_vars());
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
        return redirect()->route('settings.expense.index');
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
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo =  $this->systemService->statusUpdate($id, $status);
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
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo =  $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }

    public function getSubCategory(Request $request)
    {
        $category_id = $request->catId;
        $subcetegoris = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', $category_id);
        if ($subcetegoris) {
            return view('backend.pages.settings.expense.subcategory', get_defined_vars());
        } else {
            echo '<option> No Data Records</option>';
        }
    }

    public function accountsearch(Request $request)
    {
        // dd($request->all());
        $data = "";
        $account = ChartOfAccount::where('branch_id', $request->branch_id)->get();

        if (!$account->isEmpty()) {
            $data .= '<option selected disabled>Select Account</option>';
            foreach ($account as $value) {
                $data .= '<option value="' . $value->id . '">' . $value->accountCode . ' - ' . $value->account_name . '</option>';
            }
        } else {
            $data .= '<option selected disabled>No account found</option>';
        }

        echo $data;
    }
}
