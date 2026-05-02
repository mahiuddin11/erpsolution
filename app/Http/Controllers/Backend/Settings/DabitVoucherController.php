<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Customer;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Transection;
use helper;
use App\Services\Settings\DabitVoucherService;
use App\Transformers\ExpenseTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class DabitVoucherController extends Controller
{


    private $systemService;


    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param DabitVoucherService $systemService
     * @param ExpenseTransformer $systemTransformer
     */
    public function __construct(DabitVoucherService $DabitVoucherService, ExpenseTransformer $expenseTransformer)
    {
        $this->systemService = $DabitVoucherService;
        $this->systemTransformer = $expenseTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $title = 'Debit Voucher List';
        return view('backend.pages.settings.dabit_voucher.index', get_defined_vars());
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
        $title = 'Add New Debit Voucher';
        $branches = Branch::get()->where('status', 'Active');
        $creditaccountheas = ChartOfAccount::whereIn('id', [16, 17])->get();
        $accounts = ChartOfAccount::where('parent_id', 0)->get();
        $dabitvoucher = DabitVoucher::get();
        $projects = Project::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();

        $dabitvoucherLastData = DabitVoucher::latest('id')->first();
        if ($dabitvoucherLastData) :
            $dabitvoucherData = $dabitvoucherLastData->id + 1;
        else :
            $dabitvoucherData = 1;
        endif;
        $invoice_no = 'DV' . str_pad($dabitvoucherData, 5, "0", STR_PAD_LEFT);

        return view('backend.pages.settings.dabit_voucher.create', get_defined_vars());
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
        return redirect()->route('settings.dabit.voucher.index');
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

        $title = 'Edit Debit Voucher';
        $branches = Branch::get()->where('status', 'Active');
        $creditaccountheas = ChartOfAccount::whereIn('id', [16, 17])->get();
        $accounts = ChartOfAccount::where('parent_id', 0)->whereNotIn('id', [7])->get();
        $dabitvoucher = DabitVoucher::get();
        $projects = Project::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        return view('backend.pages.settings.dabit_voucher.edit', get_defined_vars());
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
        $local = DabitVoucherDetails::find($id);
        $accountsdf =  AccountTransaction::where('type', 5)->where('table_id', $local->dabit_voucher_id)->whereNull('debit')->first();

        $account =   AccountTransaction::where('type', 5)->where('table_id', $local->dabit_voucher_id)
            ->where('account_id', $local->account_id)->where('debit', $local->amount)->first();

        $accountsdf->credit = $accountsdf->credit - $account->debit;
        $accountsdf->save();

        $account->delete();
        $local->delete();
        session()->flash('success', 'Data successfully Delete!!');
        return redirect()->route('settings.dabit.voucher.index');
    }

    public function getSubCategory(Request $request)
    {
        $category_id = $request->catId;
        $subcetegoris = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', $category_id);
        if ($subcetegoris) {
            return view('backend.pages.settings.dabit_voucher.subcategory', get_defined_vars());
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
                $total = AccountTransaction::where('invoice', $payinvouce->invoice ?? "")->whereIn('account_id', $paymentid)->selectRaw('SUM(credit) as credit')->first();
                $checkamount = $value->debit - $total->credit;

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
        $employeeedger = $employeeedger->whereIn('account_id', $paymentid)->where('type', '!=', 5)->whereIn('invoice', $invoice)->selectRaw('SUM(debit) as debit,SUM(credit) as credit,account_id,payment_invoice,invoice');
        $employeeedger =  $employeeedger->where('employee_id', $employee_id);
        $employeeedger =  $employeeedger->groupBy('invoice')->get();
        $data = "";

        if (!$employeeedger->isEmpty()) {
            $data .= '<option selected disabled>Select Voucher</option>';
            foreach ($employeeedger as $value) {
                $payinvouce = AccountTransaction::where('payment_invoice', $value->invoice)->selectRaw('invoice')->first();
                $total = AccountTransaction::where('invoice', $payinvouce->invoice ?? "")->whereIn('account_id', $paymentid)->selectRaw('SUM(credit) as credit')->first();
                $checkamount = $value->debit - $total->credit;
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
                $total = AccountTransaction::where('invoice', $payinvouce->invoice ?? "")->whereIn('account_id', $paymentid)->selectRaw('SUM(credit) as credit')->first();
                $checkamount = $value->debit - $total->credit;

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
     * Approve Debit Voucher
     *
     * @author itwaybd
     * @contributor Sajjad
     * @param int $id
     */
    public function approve($id)
    {

        $approved =  $this->systemService->approve($id);

       

        if ($approved) {
            session()->flash('success', 'This vourcher approve successfully.');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $account_transactions = AccountTransaction::where('table_id', $id)->where('type', 5)->get();
        $title = "Debit Voucher";
        $debitVoucher = DabitVoucher::findOrFail($id);
        if ($debitVoucher && count($account_transactions) != 0 && Auth::user()->type == 'Admin') {
            if ($debitVoucher->viewed == 0) {
                $debitVoucher->viewed = 1;
                $debitVoucher->save();
            }
        }

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.settings.dabit_voucher.debit_voucher_show', get_defined_vars());
    }
    /* 
    public function checkBillByBill(Request $request)
    {
        $accountId = $request->input('account_id');
        $account = ChartOfAccount::find($accountId);

        $type = "debit";

        if (in_array(getFirstAccount($account->parent_id) ?? 0, [getAccountByUniqueID(9)->id ?? 9, getAccountByUniqueID(17)->id ?? 17])) {
            $type = "credit";
        }

        $anothertype =  $type == "debit" ? "credit" : "debit";

        if ($account) {
            $billByBill = $account->bill_by_bill;
            $paymentInvoicesDetails = [];
            if ($billByBill) {
                $paymentInvoices = AccountTransaction::where('account_id', $accountId);
                if ($type) {
                    $paymentInvoices = $paymentInvoices->whereNotNull($type);
                }
                $paymentInvoices = $paymentInvoices->get(['id', 'invoice','created_at',$type]);
            }

            foreach ($paymentInvoices as $item) {

                $trans = AccountTransaction::where('account_id', $accountId)->where("payment_invoice", $item->invoice);
                if ($type) {
                    $trans = $trans->whereNotNull($anothertype);
                }
                $trans = $trans->sum($anothertype);
                if ($item->$type > $trans) {
                    $paymentInvoicesDetails[] = [
                        "invoice" => $item->invoice,
                        "date" => date("Y-m-d",strtotime($item->created_at)),
                        "amount" => $item->$type - $trans,
                    ];
                }
            }

            return response()->json([
                'bill_by_bill' => $billByBill,
                'payment_invoices' => $paymentInvoicesDetails
            ]);
        }

        return response()->json(['bill_by_bill' => false, 'payment_invoices' => []]);
    }

     */


    /*   public function checkBillByBill(Request $request)
    {
        $accountId = $request->input('account_id');
        $account   = ChartOfAccount::find($accountId);

        if (!$account || !$account->bill_by_bill) {
            return response()->json(['bill_by_bill' => false, 'payment_invoices' => []]);
        }

        $paymentInvoicesDetails = [];

        // ==================== update logic ====================
        $transactions = AccountTransaction::where('account_id', $accountId)
            ->whereNotNull('debit')           // Purchase এ debit থাকবে
            ->where('debit', '>', 0)
            ->select('invoice', 'created_at', 'debit', 'supplier_id', 'customer_id', 'party_type')
            ->get();

        foreach ($transactions as $item) {

            // এই ইনভয়েসের বিপরীতে কত টাকা পেমেন্ট (Credit) হয়েছে
            $paid = AccountTransaction::where('payment_invoice', $item->invoice)
                ->where('account_id', $accountId)
                ->sum('credit');

            $due = $item->debit - $paid;

            if ($due > 0.01) {
                $paymentInvoicesDetails[] = [
                    "invoice" => $item->invoice,
                    "date"    => date("Y-m-d", strtotime($item->created_at)),
                    "amount"  => round($due, 2),
                ];
            }
        }

        // যদি উপরের লজিকে কিছু না পাওয়া যায়, তাহলে Credit দিয়েও চেক করো (Customer এর ক্ষেত্রে)
        if (count($paymentInvoicesDetails) == 0) {
            $transactions2 = AccountTransaction::where('account_id', $accountId)
                ->whereNotNull('credit')
                ->where('credit', '>', 0)
                ->select('invoice', 'created_at', 'credit', 'supplier_id', 'customer_id', 'party_type')
                ->get();

            foreach ($transactions2 as $item) {
                $paid = AccountTransaction::where('payment_invoice', $item->invoice)
                    ->where('account_id', $accountId)
                    ->sum('debit');   // এখানে debit দিয়ে পেমেন্ট হতে পারে

                $due = $item->credit - $paid;

                if ($due > 0.01) {
                    $paymentInvoicesDetails[] = [
                        "invoice" => $item->invoice,
                        "date"    => date("Y-m-d", strtotime($item->created_at)),
                        "amount"  => round($due, 2),
                    ];
                }
            }
        }

        return response()->json([
            'bill_by_bill'     => true,
            'payment_invoices' => $paymentInvoicesDetails
        ]);
    } */

    public function checkBillByBill(Request $request)
    {
        $accountId = $request->input('account_id');
        $account = ChartOfAccount::find($accountId);

        if (!$account || !$account->bill_by_bill) {
            return response()->json(['bill_by_bill' => false, 'payment_invoices' => []]);
        }

        $details = [];

        // Party Account-এ Credit Entry থাকে (Purchase এর সময়)
        $purchases = AccountTransaction::where('account_id', $accountId)
            ->whereNotNull('credit')
            ->where('credit', '>', 0)
            ->select('invoice', 'created_at', 'credit as amount')
            ->distinct('invoice')
            ->get();

        foreach ($purchases as $pur) {

            // এই ইনভয়েসের বিপরীতে মোট পেমেন্ট হয়েছে (Debit Voucher-এ Debit হয় Party Account-এ)
            $totalPaid = AccountTransaction::where('payment_invoice', $pur->invoice)
                ->where('account_id', $accountId)
                ->where('debit', '>', 0)          // Payment-এ Debit হয়
                ->sum('debit');

            $due = $pur->amount - $totalPaid;

            if ($due > 0.01) {
                $details[] = [
                    "invoice" => $pur->invoice,
                    "date"    => date("Y-m-d", strtotime($pur->created_at)),
                    "amount"  => round($due, 2),
                ];
            }
        }

        return response()->json([
            'bill_by_bill'     => true,
            'payment_invoices' => $details
        ]);
    }
   
}
