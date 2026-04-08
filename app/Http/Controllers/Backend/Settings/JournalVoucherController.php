<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Customer;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetails;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Transection;
use App\Services\Settings\JournalVoucherService;
use helper;
use App\Services\Settings\DabitVoucherService;
use App\Transformers\ExpenseTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class JournalVoucherController extends Controller
{
    /**
     * @var JournalVoucherService
     */
    private $systemService;
    /**
     * @var ExpenseTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param JournalVoucherService $systemService
     * @param ExpenseTransformer $systemTransformer
     */
    public function __construct(JournalVoucherService $JournalVoucherService, ExpenseTransformer $expenseTransformer)
    {
        $this->systemService = $JournalVoucherService;
        $this->systemTransformer = $expenseTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Journal Voucher List';
        return view('backend.pages.settings.journal_voucher.index', get_defined_vars());
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
        $title = 'Add New Journal Voucher';
        $branches = Branch::get()->where('status', 'Active');
        $creditaccountheas = ChartOfAccount::whereIn('id', [16, 17])->get();
        $accounts = ChartOfAccount::where('parent_id', 0)->get();
        $creditvoucher = JournalVoucher::get();

        $creditvoucherLastData = JournalVoucher::latest('id')->first();
        if ($creditvoucherLastData) :
            $creditvoucherData = $creditvoucherLastData->id + 1;
        else :
            $creditvoucherData = 1;
        endif;
        $invoice_no = 'JV' . str_pad($creditvoucherData, 5, "0", STR_PAD_LEFT);
        $projects = Project::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        return view('backend.pages.settings.journal_voucher.create', get_defined_vars());
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
        return redirect()->route('settings.journal.voucher.index');
    }
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

     public function show($id)
     {
         $title = "Journal Voucher";
         $journalVoucher = JournalVoucher::findOrFail($id);
         $account_transactions = AccountTransaction::whereIn('table_id', $journalVoucher->details->pluck("id"))->where('type', 8)->get();

         $companyInfo = Company::latest('id')->first();
         return view('backend.pages.settings.journal_voucher.show', get_defined_vars());
     }

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

        $title = 'Edit Journal Voucher';
        $branches = Branch::get()->where('status', 'Active');
        $creditaccountheas = ChartOfAccount::whereIn('id', [16, 17])->get();
        $accounts = ChartOfAccount::where('parent_id', 0)->whereNotIn('id', [16, 17])->get();
        $projects = Project::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        return view('backend.pages.settings.journal_voucher.edit', get_defined_vars());
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
        return redirect()->route('settings.journal.voucher.index');
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

    public function singledestroy($id)
    {
        $local = JournalVoucherDetails::find($id);
        // dd($local);
        $accountsdf =  AccountTransaction::where('type', 8)->where('table_id', $local->credit_voucher_id)->whereNull('credit')->first();

        $account =   AccountTransaction::where('type', 8)->where('table_id', $local->credit_voucher_id)
            ->where('account_id', $local->account_id)->where('credit', $local->amount)->first();

        $accountsdf->debit = $accountsdf->debit - $account->credit;
        // dd($accountsdf);
        $accountsdf->save();

        $account->delete();
        $local->delete();
        session()->flash('success', 'Data successfully Delete!!');
        return redirect()->route('settings.journal.voucher.index');
    }

    public function getSubCategory(Request $request)
    {
        $category_id = $request->catId;
        $subcetegoris = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', $category_id);
        if ($subcetegoris) {
            return view('backend.pages.settings.journal_voucher.subcategory', get_defined_vars());
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
