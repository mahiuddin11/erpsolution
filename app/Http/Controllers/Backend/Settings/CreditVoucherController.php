<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CreditVoucher;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\CreditVoucherDetails;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Transection;
use App\Services\Settings\CreditVoucherService;
use helper;
use App\Services\Settings\DabitVoucherService;
use App\Transformers\ExpenseTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CreditVoucherController extends Controller
{

    /**
     * @var CreditVoucherService
     */
    private $systemService;
    /**
     * @var ExpenseTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param CreditVoucherService $systemService
     * @param ExpenseTransformer $systemTransformer
     */
    public function __construct(CreditVoucherService $CreditVoucherService, ExpenseTransformer $expenseTransformer)
    {
        $this->systemService = $CreditVoucherService;
        $this->systemTransformer = $expenseTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //$this->expenseFix();
        $title = 'Receive Voucher List';
        return view('backend.pages.settings.credit_voucher.index', get_defined_vars());
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
        $title = 'Add New Receive Voucher';
        $branches = Branch::get()->where('status', 'Active');
        $creditaccountheas = ChartOfAccount::whereIn('id', [16, 17])->get();
        $accounts = ChartOfAccount::where('parent_id', 0)->get();
        $creditvoucher = CreditVoucher::get();

        $creditvoucherLastData = CreditVoucher::latest('id')->first();
        if ($creditvoucherLastData) :
            $creditvoucherData = $creditvoucherLastData->id + 1;
        else :
            $creditvoucherData = 1;
        endif;
        $branchs = Branch::get();

        $invoice_no = 'CV' . str_pad($creditvoucherData, 5, "0", STR_PAD_LEFT);
        $projects = Project::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();

        return view('backend.pages.settings.credit_voucher.create', get_defined_vars());
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
        return redirect()->route('settings.credit.voucher.index');
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
        $title = 'Edit Receive Voucher';
        $branches = Branch::get()->where('status', 'Active');
        $creditaccountheas = ChartOfAccount::whereIn('id', [16, 17])->get();
        $accounts = ChartOfAccount::where('parent_id', 0)->whereNotIn('id', [16, 17])->get();
        $dabitvoucher = DabitVoucher::get();
        $projects = Project::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        return view('backend.pages.settings.credit_voucher.edit', get_defined_vars());
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
        return redirect()->back();
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

    public function purchasevoucher(Request $request)
    {
        $supplier_id = $request->supplier_id;
        $account_id = ChartOfAccount::where('bill_by_bill', 1)->pluck('id');
        $paymentid = getOldAccount([4]);
        $supplierLedger = new AccountTransaction();
        $invoice = $supplierLedger->whereIn('account_id', $account_id)->pluck('invoice');
        $supplierLedger = $supplierLedger->whereIn('account_id', $paymentid)->where('type', '!=', 5)->whereIn('invoice', $invoice)->selectRaw('SUM(debit) as debit,SUM(credit) as credit,account_id,payment_invoice,invoice');
        $supplierLedger =  $supplierLedger->where('supplier_id', $supplier_id);
        $supplierLedger =  $supplierLedger->groupBy('invoice')->get();
        $data = "";

        if (!$supplierLedger->isEmpty()) {
            $data .= '<option selected disabled>Select Voucher</option>';
            foreach ($supplierLedger as $value) {
                $payinvouce = AccountTransaction::where('payment_invoice', $value->invoice)->selectRaw('invoice')->first();
                $total = AccountTransaction::where('invoice', $payinvouce->invoice ?? "")->whereIn('account_id', $paymentid)->selectRaw('SUM(debit) as debit')->first();
                $checkamount =  $value->credit - $total->debit;
                if ($checkamount > 0) {
                    $accountid = AccountTransaction::where('invoice', $value->invoice)->whereNotIn('account_id', $paymentid)->first()->account;
                    $data .= '<option amount="' . $checkamount . '" accountid="' . $accountid->id . '" accountname="' . $accountid->account_name . '"  value="' . $value->invoice . '">' . $value->invoice . ' - ' . $checkamount . '</option>';
                }
            }
        } else {
            $data .= '<option selected disabled>No account found</option>';
        }

        return $data;
    }

    public function employeevoucher(Request $request)
    {
        $employee_id = $request->employee_id;
        $account_id = ChartOfAccount::where('bill_by_bill', 1)->pluck('id');
        $paymentid = getOldAccount([4]);
        $employeeedger = new AccountTransaction();
        $invoice = $employeeedger->whereIn('account_id', $account_id)->pluck('invoice');
        $employeeedger = $employeeedger->whereIn('account_id', $paymentid)->where('type', '!=', 6)->whereIn('invoice', $invoice)->selectRaw('SUM(debit) as debit,SUM(credit) as credit,account_id,payment_invoice,invoice');
        $employeeedger =  $employeeedger->where('employee_id', $employee_id);
        $employeeedger =  $employeeedger->groupBy('invoice')->get();
        $data = "";

        if (!$employeeedger->isEmpty()) {
            $data .= '<option selected disabled>Select Voucher</option>';
            foreach ($employeeedger as $value) {
                $payinvouce = AccountTransaction::where('payment_invoice', $value->invoice)->selectRaw('invoice')->first();
                $total = AccountTransaction::where('invoice', $payinvouce->invoice ?? "")->whereIn('account_id', $paymentid)->selectRaw('SUM(debit) as debit')->first();
                $checkamount =  $value->credit - $total->debit;
                if ($checkamount > 0) {
                    $accountid = AccountTransaction::where('invoice', $value->invoice)->whereNotIn('account_id', $paymentid)->first()->account;
                    $data .= '<option amount="' . $checkamount . '" accountid="' . $accountid->id . '" accountname="' . $accountid->account_name . '"  value="' . $value->invoice . '">' . $value->invoice . ' - ' . $checkamount . '</option>';
                }
            }
        } else {
            $data .= '<option selected disabled>No account found</option>';
        }

        return $data;
    }

    public function customervoucher(Request $request)
    {
        $customer_id = $request->customer_id;
        $account_id = ChartOfAccount::where('bill_by_bill', 1)->pluck('id');
        $paymentid = getOldAccount([4]);
        $customerLedger = new AccountTransaction();
        $invoice = $customerLedger->whereIn('account_id', $account_id)->pluck('invoice');
        $customerLedger = $customerLedger->whereIn('account_id', $paymentid)->where('type', '!=', 5)->whereIn('invoice', $invoice)->selectRaw('SUM(debit) as debit,SUM(credit) as credit,account_id,payment_invoice,invoice');
        $customerLedger =  $customerLedger->where('customer_id', $customer_id);
        $customerLedger =  $customerLedger->groupBy('invoice')->get();
        $data = "";

        if (!$customerLedger->isEmpty()) {
            $data .= '<option selected disabled>Select Voucher</option>';
            foreach ($customerLedger as $value) {
                $payinvouce = AccountTransaction::where('payment_invoice', $value->invoice)->selectRaw('invoice')->first();
                $total = AccountTransaction::where('invoice', $payinvouce->invoice ?? "")->whereIn('account_id', $paymentid)->selectRaw('SUM(debit) as debit')->first();
                $checkamount =  $value->credit - $total->debit;

                if ($checkamount > 0) {
                    $accountid = AccountTransaction::where('invoice', $value->invoice)->whereNotIn('account_id', $paymentid)->first()->account;
                    $data .= '<option amount="' . $checkamount . '" accountid="' . $accountid->id . '" accountname="' . $accountid->account_name . '"  value="' . $value->invoice . '">' . $value->invoice . ' - ' . $checkamount . '</option>';
                }
            }
        } else {
            $data .= '<option selected disabled>No account found</option>';
        }

        return $data;
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
        $local = CreditVoucherDetails::find($id);
        $accountsdf =  AccountTransaction::where('type', 6)->where('table_id', $local->credit_voucher_id)->whereNull('credit')->first();

        $account =   AccountTransaction::where('type', 6)->where('table_id', $local->credit_voucher_id)
            ->where('account_id', $local->account_id)->where('credit', $local->amount)->first();

        $accountsdf->debit = $accountsdf->debit - $account->credit;
        $accountsdf->save();

        $account->delete();
        $local->delete();
        session()->flash('success', 'Data successfully Delete!!');
        return redirect()->route('settings.credit.voucher.index');
    }

    public function getSubCategory(Request $request)
    {
        $category_id = $request->catId;
        $subcetegoris = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', $category_id);
        if ($subcetegoris) {
            return view('backend.pages.settings.credit_voucher.subcategory', get_defined_vars());
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

    /**
     * Approved Credit Voucher
     *
     * @author itwaybd
     * @contributor Sajjad <sajjad.develpr@gmail.com>
     * @param $id
     *
     * @return true
     */
    public function approve($id)
    {
        $approved = $this->systemService->approve($id);

        if ($approved) {
            session()->flash('success', 'This vourcher approve successfully.');
            return redirect()->back();
        }
    }

    /**
     * View Credit Voucher
     *
     * @author itwaybd
     * @contributor Sajjad <sajjad.develpr@gmail.com>
     * @param $id
     *
     * @return true
     */

    public function show($id)
    {
        $account_transactions = AccountTransaction::where('table_id', $id)->where('type', 6)->get();
        $title = "Receive Voucher";
        $creditVoucher = CreditVoucher::findOrFail($id);

        if ($creditVoucher && count($account_transactions) != 0 && Auth::user()->type == 'Admin') {
            if ($creditVoucher->viewed == 0) {
                $creditVoucher->viewed = 1;
                $creditVoucher->save();
            }
        }

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.settings.credit_voucher.credit_voucher_show', get_defined_vars());
    }
}
