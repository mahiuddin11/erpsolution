<?php

namespace App\Http\Controllers\Backend\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\CashReq;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Company;
use App\Models\Customer;
use App\Models\customerLedger;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Grn;
use App\Models\Invoice;
use App\Models\LeaveApplication;
use App\Models\Production;
use App\Models\ProductOpeningStockDetails;
use App\Models\ProductUse;
use App\Models\ProjectTransfer;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectMoney;
use App\Models\ProjectReturn;
use App\Models\ProjectTransferDetails;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Purchases;
use App\Models\PurchasesDetails;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\sales_Details;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\supplierLedger;
use App\Models\Transection;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{

    public $types;
    public function __construct()
    {
        $this->types = [
            1 => "Opening Balance",
            2 => "Balance Transfer",
            3 => "Transfer Receive",
            4 => "Expense",
            5 => "Customer Opening Balance",
            6 => "Supplier Payment",
            7 => "Balance Transrfer",
            8 => "Customer Payment",
            9 => "Supplier Payment",
            10 => "Cash Sale",
            11 => "Cash Purchase",
            12 => "Project Money",
        ];
    }

    public function purchase(Request $request)
    {

        $branch_id = '';
        $product_id = '';
        $supplier_id = '';
        $project_id = '';
        $type = '';
        if ($request->method() == 'POST') {

            $purchaseDetails = '';
            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;
            $supplier_id = $request->ledger_id;
            $project_id = $request->project_id;
            $type = $request->type;

            $purchaseDetails = Purchases::with(["branch", "supplier"])->where("type", $type)->whereBetween('date', [$from_date, $to_date]);


            // $purchaseDetails = Purchases::with(["branch", "supplier"])->where("type", $type)->whereBetween('date', [$from_date, $to_date]);

            if ($type == "Branch") {
                if ($branch_id != 'all') {
                    $purchaseDetails =   $purchaseDetails->where('branch_id', $branch_id);
                }
            } else {
                if ($project_id != 'all') {
                    $purchaseDetails =   $purchaseDetails->where('project_id', $project_id);
                }
            }

            if ($supplier_id != 'all') {
                $purchaseDetails = $purchaseDetails->where('ledger_id', $supplier_id);
            }

            if ($product_id != 'all') {
                $purchaseDetails = $purchaseDetails->whereHas('details', function ($query) use ($product_id) {
                    $query->where('product_id', $product_id);
                });
            }

            $purchaseDetails =  $purchaseDetails->get();
        }

        $title = 'Purchase Report';
        $companyInfo = Company::latest('id')->first();
        $ledgers = ChartOfAccount::where('parent_id', 0)->get();
        $projects = Project::get();
        $branch = Branch::where('status', 'Active')->where('parent_id', "!=", 0)->get();
        $supplier = Supplier::get()->where('status', 'Active');
        $product = Product::get()->where('status', 'Active');

        return view('backend.pages.reports.purchase', get_defined_vars());
    }

    public function empSalary(Request $request)
    {
        $title = 'Employee Ledger Report';
        $employee = Employee::all();
        $employeeLedger = '';
        $employee_id  = '';
        $account_id  = '';
        if ($request->method() == 'POST') {
            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));
            $account_id = $request->account_id;
            $employeeLedger = AccountTransaction::with('employee')->whereBetween('created_at', [$from_date, $to_date]);

            if ($account_id != 'all') {
                $employeeLedger =  $employeeLedger->where('account_id', $account_id);
            }

            if ($employee_id != 'all') {
                $employeeLedger =  $employeeLedger->where('employee_id', $employee_id);
            } else {
                $employeeLedger =  $employeeLedger->whereNotNull('employee_id');
            }
            $employeeLedger =  $employeeLedger->get();
        }
        $title = 'Suplier Ledger Report';
        $companyInfo = Company::latest('id')->first();
        $accounts = ChartOfAccount::get();

        return view('backend.pages.reports.emp_salary', get_defined_vars());
    }

    public function sale(Request $request)
    {
        $branch_id = '';
        $product_id = '';
        $supplier_id = '';
        $project_id = '';
        $type = '';

        if ($request->method() == 'POST') {

            $salesDetails = '';
            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));
            $branch_id = $request->branch_id;
            $product_id = $request->product_id;
            $supplier_id = $request->ledger_id;
            $project_id = $request->project_id;
            $type = $request->type;

            $salesDetails = Sale::with("branch", "customer", 'details')->whereBetween('date', [$from_date, $to_date]);

            if ($branch_id != 'all') {
                $salesDetails =   $salesDetails->where('branch_id', $branch_id);
            }

            if ($supplier_id != 'all') {
                $salesDetails = $salesDetails->where('ledger_id', $supplier_id);
            }

            if ($product_id != 'all') {
                $salesDetails = $salesDetails->whereHas('details', function ($query) use ($product_id) {
                    $query->where('product_id', $product_id);
                });
            }

            $salesDetails =  $salesDetails->get();
        }

        $title = 'Purchase Report';
        $companyInfo = Company::latest('id')->first();
        $ledgers = ChartOfAccount::where('parent_id', 0)->get();
        $projects = Project::get();
        $branch = Branch::where('status', 'Active')->where('parent_id', "!=", 0)->get();
        $supplier = Supplier::get()->where('status', 'Active');
        $product = Product::get()->where('status', 'Active');

        return view('backend.pages.reports.sale', get_defined_vars());
    }


    public function expense(Request $request)
    {
        $title = 'Expence Report';

        $branch_id = '';
        $category_id = '';

        if ($request->method() == 'POST') {
            $expenseDetails = '';

            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $category_id = $request->category_id;

            $expenseDetails = Expense::join('chart_of_accounts', 'expenses.chartofaccount_id', '=', 'chart_of_accounts.id')
                ->join('branches', 'expenses.branch_id', '=', 'branches.id')
                ->whereBetween('expenses.date', [$from_date, $to_date]);

            if ($branch_id != 'all') {
                $expenseDetails =   $expenseDetails->where('expenses.branch_id', $branch_id);
            }
            if ($category_id != 'all') {
                $expenseDetails =   $expenseDetails->where('expenses.expensecategorie_id', $category_id);
            }

            $expenseDetails =  $expenseDetails->get();
            // dd($expenseDetails);
        }

        $companyInfo = Company::latest('id')->first();

        $branch = Branch::where('status', 'Active')->get();
        $categorys = ExpenseCategory::where('status', 'Active')->get();
        $customer = Customer::get()->where('status', 'Active');
        $product = Product::get()->where('status', 'Active');
        return view('backend.pages.reports.expense', get_defined_vars());
    }

    public function projectexpence(Request $request)
    {
        $title = 'Project Expence Report';

        $project_id = '';

        if ($request->method() == 'POST') {
            $projectExpense = '';

            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $project_id = $request->project_id;

            $projectExpense = ProjectExpense::join('expense_categories', 'project_expenses.categorie_id', '=', 'expense_categories.id')
                ->join('projects', 'project_expenses.project_id', '=', 'projects.id')
                ->whereBetween('project_expenses.date', [$from_date, $to_date]);

            if ($project_id != 'all') {
                $projectExpense =   $projectExpense->where('project_expenses.project_id', $project_id);
            }

            $projectExpense =  $projectExpense->get(['project_expenses.amount as ammount', 'project_expenses.date as date', 'expense_categories.name as categorynm', 'projects.projectCode as prcode', 'projects.name as prname',]);
        }

        $companyInfo = Company::latest('id')->first();

        $projects = Project::where('status', 'Active')->get();
        $product = Product::get()->where('status', 'Active');
        return view('backend.pages.reports.projectexpence', get_defined_vars());
    }


    public function transfer(Request $request)
    {
        $title = 'Transfer Report';
        $branch_id = '';
        if ($request->method() == 'POST') {
            $transferDetails = '';
            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));
            $branch_id = $request->branch_id;
            $transferDetails = Transfer::whereBetween('transfers.date', [$from_date, $to_date]);
            if ($branch_id != 'all') {
                $transferDetails =   $transferDetails->where('transfers.from_branch_id', $branch_id);
            }
            $transferDetails =  $transferDetails->get();
        }
        $companyInfo = Company::latest('id')->first();
        $branch = Branch::where('status', 'Active')->get();
        return view('backend.pages.reports.transfer', get_defined_vars());
    }


    public function project(Request $request)
    {

        // dd('project', $request->all());
        $title = 'Project Report';
        $project_id = '';
        if ($request->method() == 'POST') {

            if ($request->project_id ==  0) {
                return Redirect::back()->withErrors(['msg' => 'Project Can not be empty!']);
            }

            $projectDetails = '';
            $projectExpense = '';
            $productUses = '';
            $productReturn = '';
            $productIssue = '';

            $project_id = $request->project_id;
            $projectDetails = Project::join('users', 'users.id', '=', 'projects.manager_id')
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
                    'projects.projectCode'
                ]);
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
        }

        $companyInfo = Company::latest('id')->first();
        $project = Project::where('status', 'Active')->get();
        return view('backend.pages.reports.project', get_defined_vars());
    }

    public function supledger(Request $request)
    {
        $supplierLedger = '';
        $branch_id = '';
        $supplier_id  = '';
        if ($request->method() == 'POST') {

            $datas = explode('-', $request->dateRange);
            $supplier_id = $request->supplier_id;
            $supplierLedger = AccountTransaction::with('supplier');
            $from_date = "";
            $to_date = "";

            if ($request->dateRange) {
                $from_date = date('Y-m-d', strtotime($datas[0]));
                $to_date = date('Y-m-d', strtotime($datas[1]));
                $supplierLedger = $supplierLedger->whereBetween('created_at', [$from_date, $to_date]);
            }

            if ($supplier_id != 'all') {
                $supplierLedger =  $supplierLedger->where('supplier_id', $supplier_id);
            } else {
                $supplierLedger =  $supplierLedger->whereNotIn('supplier_id', ["null", "0"]);
            }
            $supplierLedger =  $supplierLedger->get();
        }

        $branch = Branch::where('status', 'Active')->get();
        $supplier = Supplier::where('status', 'Active')->get();
        $title = 'Suplier Ledger Report';
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.reports.supledger', get_defined_vars());
    }

    public function custledger(Request $request)
    {
        $title = 'Customer Ledger  Report';
        $customerLedger = '';
        $branch_id = '';
        $customer_id  = '';

        if ($request->method() == 'POST') {
            $datas = explode('-', $request->dateRange);
            $from_date = "";
            $to_date = "";
            $customer_id = $request->customer_id;
            $customerLedger = AccountTransaction::with('customer');

            if ($request->dateRange) {
                $from_date = date('Y-m-d', strtotime($datas[0]));
                $to_date = date('Y-m-d', strtotime($datas[1]));
                $customerLedger = $customerLedger->whereBetween('created_at', [$from_date, $to_date]);
            }

            if ($customer_id != 'all') {
                $customerLedger =  $customerLedger->where('customer_id', $customer_id);
            } else {
                $customerLedger =  $customerLedger->whereNotIn('customer_id', ["null", "0"]);
            }
            $customerLedger =  $customerLedger->get();
        }
        $branch = Branch::where('status', 'Active')->get();
        $customer = Customer::where('status', 'Active')->get();
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.reports.custledger', get_defined_vars());
    }

    public function account(Request $request)
    {
        $title = 'Account Report';

        $branch_id = '';
        $account_id = '';

        if ($request->method() == 'POST') {
            $accountDetails = '';

            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $account_id = $request->accounts_id;

            $picDataContidon = [1, 2, 3, 4, 6, 7, 8, 10, 11, 12, 13];

            $accountDetails = Transection::join('branches', 'transections.branch_id', '=', 'branches.id')
                ->join('chart_of_accounts', 'transections.account_id', '=', 'chart_of_accounts.id')
                ->whereBetween('transections.date', [$from_date, $to_date])
                ->whereIn("transections.type", $picDataContidon);
            if ($branch_id != 'all') {
                $accountDetails =   $accountDetails->where('transections.branch_id', $branch_id);
            }
            if ($account_id != 'all') {
                $accountDetails =   $accountDetails->where('transections.account_id', $account_id);
            }

            $accountDetails =  $accountDetails->get();
            //dd($accountDetails);
        }

        $companyInfo = Company::latest('id')->first();

        $branch = Branch::where('status', 'Active')->get();
        $accounts = ChartOfAccount::get()->where('status', 'Active');

        return view('backend.pages.reports.account', get_defined_vars());
    }

    public function cashbook(Request $request)
    {
        $title = 'Cash Book Report';

        $accounts = ChartOfAccount::where("id", getAccountByUniqueID(6)->id)->get();
        $selectedAccountId = $request->input('account_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $ledgerEntries = [];
        $openingBalance = 0;
        $runningBalance = 0;
        $account = null;

        if ($selectedAccountId) {

            $account = ChartOfAccount::findOrFail($selectedAccountId);

            // Calculate the opening balance as of the start date
            $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
                ->whereDate('created_at', '<', $startDate)
                ->sum('debit');

            $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
                ->whereDate('created_at', '<', $startDate)
                ->sum('credit');

            // Adjust opening balance based on the balance type
            if ($account->balance_type === 'debit') {
                $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
            } else {
                $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
            }

            $runningBalance = $openingBalance;

            $transactions = AccountTransaction::where('account_id', $selectedAccountId)
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->orderBy('created_at')
                ->get();

            foreach ($transactions as $transaction) {
                $relatedAccountTransaction = AccountTransaction::where('invoice', $transaction->invoice)
                    ->where('account_id', '!=', $selectedAccountId)
                    ->first();

                $debit = $transaction->debit ?? 0;
                $credit = $transaction->credit ?? 0;

                if ($account->balance_type == "debit") {
                    $runningBalance += $debit - $credit;
                } else {
                    $runningBalance += $credit -  $debit;
                }

                $ledgerEntries[] = [
                    'date' => $transaction->created_at,
                    'invoice' => $transaction->invoice,
                    'description' => $transaction->remark,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $runningBalance,
                    'account_name' => $relatedAccountTransaction->account->account_name ?? ''
                ];
            }
        }
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.reports.cashbook', get_defined_vars());
    }
    public function reqcashbook(Request $request)
    {
        $title = 'Cash Requisition Report';
        $employees = Employee::get();
        if ($request->method() == 'POST') {

            $startDate = $request->input('start_date');

            $endDate = $request->input('end_date');

            $reqests = CashReq::orderBy("id", "DESC")->whereDate('created_at', ">=", $startDate)->whereDate('created_at', "<=", $endDate);

            if ($request->employee_id != "All") {
                $reqests = $reqests->where("employee_id", $request->employee_id);
            }
            if ($request->status != "All") {
                $reqests = $reqests->where("status", $request->status);
            }
            $reqests = $reqests->get();
        }
        return view('backend.pages.reports.cashreq', get_defined_vars());
    }

    public function leave(Request $request)
    {
        $title = 'Leave Report';
        $employees = Employee::get();
        if ($request->method() == 'POST') {

            $startDate = $request->input('start_date');

            $endDate = $request->input('end_date');

            $reqests = LeaveApplication::with('employee')->orderBy("id", "DESC")->whereDate('created_at', ">=", $startDate)->whereDate('created_at', "<=", $endDate);

            if ($request->employee_id != "All") {
                $reqests = $reqests->where("employee_id", $request->employee_id);
            }
            if ($request->status != "All") {
                $reqests = $reqests->where("status", $request->status);
            }
            $reqests = $reqests->get();
        }
        return view('backend.pages.reports.leave', get_defined_vars());
    }

    public function newexpense(Request $request)
    {
        $title = 'Expense Book Report';

        if ($request->isMethod('post')) {
            $findreports = new AccountTransaction();
            if ($request->from_date) {
                $findreports = $findreports->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $findreports = $findreports->whereDate('created_at', '<=', $request->to_date);
            }

            // Retrieve specific account IDs and invoices
            $accountid = getOldAccount([19])->pluck('id')->toArray();

            $getaccountInv = AccountTransaction::whereIn('account_id', $accountid)->pluck('invoice', 'id')->toArray();

            // Select necessary fields
            // $findreports = $findreports->select('debit', 'credit', 'account_id', 'created_at', 'invoice', 'remark', 'type', 'supplier_id', 'customer_id', 'employee_id', 'project_id')->get();
            //  $findreports = $findreports->select('debit', 'account_id', 'created_at', 'invoice', 'remark', 'type', 'supplier_id', 'customer_id', 'employee_id', 'project_id')->get();

            $findreports = $findreports->where('debit', '>', 0)
                ->select(
                    'debit',
                    'credit',
                    'account_id',
                    'created_at',
                    'invoice',
                    'remark',
                    'type',
                    'supplier_id',
                    'customer_id',
                    'employee_id',
                    'project_id'
                )->get();

            // Opening check for balance calculations
            $opeingcheck = AccountTransaction::where('account_id', '!=', 16)
                ->select('debit', 'credit', 'account_id', 'created_at', 'invoice', 'remark', 'type', 'supplier_id', 'customer_id', 'employee_id', 'project_id');


            if ($request->from_date) {
                $opeingcheck = $opeingcheck->whereDate('created_at', '<=', $request->from_date);
            }
        }

        // Retrieve the latest company information
        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.newexpense', get_defined_vars());
    }

    public function cashflow(Request $request)
    {
        $title = 'Cash Flow Report';
        $startDate = $request->from_date ?? date('Y-01-01');
        $toDate = $request->to_date ?? date('Y-m-d');
        if ($request->method() == 'POST') {
            $getOpeningBalance =  Transection::where('account_id', 16)->where('type', 1)->first() ?? 0;
            $newOpeningBalance =  $getOpeningBalance->amount ?? 0;

            $prefindreports = new AccountTransaction();
            if ($request->from_date) {
                $prefindreports = $prefindreports->whereDate('created_at', '>=', $getOpeningBalance->created_at ?? date('Y-m-d'));
            }
            if ($request->to_date) {
                $prefindreports = $prefindreports->whereDate('created_at', '<=', $startDate);
            }
            $pregetaccountInv = AccountTransaction::where('account_id', 16)->pluck('invoice')->toArray();
            $prefindreports = $prefindreports->whereIn('invoice', $pregetaccountInv);
            $precashaccount = $prefindreports->where('account_id', "!=", 16);
            $preaccountlists = $precashaccount->selectRaw('sum(debit) as debit , sum(credit) as credit , account_id')->groupBy('account_id')->get();

            foreach ($preaccountlists as $ite) {
                $newOpeningBalance += $ite->credit - $ite->debit;
            }

            $findreports = new AccountTransaction();

            if ($request->from_date) {
                $findreports = $findreports->whereDate('created_at', '>=', $startDate);
            }

            if ($request->to_date) {
                $findreports = $findreports->whereDate('created_at', '<=', $toDate);
            }
            $getaccountInv = AccountTransaction::where('account_id', 16)->pluck('invoice')->toArray();

            $findreports = $findreports->whereIn('invoice', $getaccountInv);
            $cashaccount = $findreports->where('account_id', "!=", 16);
            $accountbycroupby = $cashaccount->where('account_id', "!=", 0)->selectRaw('sum(debit) as debit , sum(credit) as credit , account_id')->groupBy('account_id')->get();
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        }
        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.cashflow', get_defined_vars());
    }

    public function retainedearning(Request $request)
    {
        $title = 'Retained Earning Report';

        $account = new ChartOfAccount();
        $year = $request->year ?? date('Y');

        $expenseHeadIds = $account->getaccount(6)->pluck('id');
        $incomeHeadIds = $account->getaccount(8)->pluck('id');

        $incomes = AccountTransaction::selectRaw('SUM(credit) as credit, YEAR(created_at) as year')
            ->whereYear('created_at', '<=', $year)
            ->whereIn('account_id', $incomeHeadIds)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $expenses = AccountTransaction::selectRaw('SUM(debit) as debit, YEAR(created_at) as year')
            ->whereYear('created_at', '<=', $year)
            ->whereIn('account_id', $expenseHeadIds)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.retainedearning', get_defined_vars());
    }

    public function bankbook(Request $request)
    {
        $title = 'Bank Book Report';

        $accounts = ChartOfAccount::where("id", getAccountByUniqueID(6)->id)->get();
        $selectedAccountId = $request->input('account_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $ledgerEntries = [];
        $openingBalance = 0;
        $runningBalance = 0;
        $account = null;
        if ($selectedAccountId) {
            $account = ChartOfAccount::findOrFail($selectedAccountId);

            // Calculate the opening balance as of the start date
            $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
                ->whereDate('created_at', '<', $startDate)
                ->sum('debit');

            $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
                ->whereDate('created_at', '<', $startDate)
                ->sum('credit');

            // Adjust opening balance based on the balance type
            if ($account->balance_type === 'debit') {
                $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
            } else {
                $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
            }

            $runningBalance = $openingBalance;


            $transactions = AccountTransaction::where('account_id', $selectedAccountId)
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->orderBy('created_at')
                ->get();

            foreach ($transactions as $transaction) {
                $relatedAccountTransaction = AccountTransaction::where('invoice', $transaction->invoice)
                    ->where('account_id', '!=', $selectedAccountId)
                    ->first();

                $debit = $transaction->debit ?? 0;
                $credit = $transaction->credit ?? 0;

                if ($account->balance_type == "debit") {
                    $runningBalance += $debit - $credit;
                } else {
                    $runningBalance += $credit -  $debit;
                }

                $ledgerEntries[] = [
                    'date' => $transaction->created_at,
                    'invoice' => $transaction->invoice,
                    'description' => $transaction->remark,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $runningBalance,
                    'account_name' => $relatedAccountTransaction->account->account_name ?? ''
                ];
            }
        }
        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.bankbook', get_defined_vars());
    }

    public function daybook(Request $request)
    {
        $title = 'Day Book Report';

        $companyInfo = Company::latest('id')->first();

        $dateSplite = explode(" - ", $request->input('date'));

        $startDate = date("Y-m-d", strtotime(empty($dateSplite[0]) ? date("Y-m-d") : $dateSplite[0])); // Default to today's date if no date is provided
        $endDate = date("Y-m-d", strtotime($dateSplite[1] ?? date("Y-m-d"))); // Default to today's date if no date is provided
        // Fetch transactions for the selected date
        $transactions = AccountTransaction::whereDate('created_at', ">=", $startDate)->whereDate('created_at', "<=", $endDate)
            ->orderBy('created_at')
            ->get();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');

        return view('backend.pages.reports.daybook', get_defined_vars());
    }

    // public function ledger(Request $request)
    // {
    //     $title = 'Ledger Report';

    //     $accounts = ChartOfAccount::where("parent_id", 0)->get();
    //     $companyInfo = Company::latest('id')->first();


    //     $selectedAccountId = $request->input('account_id');
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');


    //     $ledgerEntries = [];
    //     $openingBalance = 0;
    //     $runningBalance = 0;
    //     $account = null;
    //     if ($selectedAccountId) {
    //         $account = ChartOfAccount::findOrFail($selectedAccountId);


    //         // Calculate the opening balance as of the start date
    //         $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('debit');

    //         $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('credit');

    //         // Adjust opening balance
    //         if ($account->balance_type === 'debit') {
    //             $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    //         } else {
    //             $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
    //         }

    //         $runningBalance = $openingBalance;
    //         $totalDebit = 0;
    //         $totalCredit = 0;
    //         $ledgerEntries = [];

    //         $transactions = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->when($startDate, function ($query) use ($startDate) {
    //                 return $query->whereDate('created_at', '>=', $startDate);
    //             })
    //             ->when($endDate, function ($query) use ($endDate) {
    //                 return $query->whereDate('created_at', '<=', $endDate);
    //             })
    //             ->orderBy('created_at')
    //             ->get();


    //         foreach ($transactions as $transaction) {
    //             $invoice = $transaction->invoice;
    //             if ($transaction->type == "purchase") {
    //                 $item = DB::table("purchases")->find($transaction->table_id);
    //                 $invoice = $item->invoice_no ?? "";
    //             }


    //             $relatedAccountTransaction = AccountTransaction::where('invoice', $transaction->invoice)
    //                 ->where('account_id', '!=', $selectedAccountId);

    //             if ($transaction->debit) {

    //                 $relatedAccountTransaction = $relatedAccountTransaction->whereNotNull('credit');
    //             }
    //             if ($transaction->credit) {
    //                 $relatedAccountTransaction = $relatedAccountTransaction->whereNotNull('debit');
    //             }
    //             $relatedAccountTransaction = $relatedAccountTransaction->first();


    //             $debit = $transaction->debit ?? 0;
    //             $credit = $transaction->credit ?? 0;

    //             $totalDebit += $debit;
    //             $totalCredit += $credit;

    //             if ($account->balance_type == "debit") {
    //                 $runningBalance += $debit - $credit;
    //             } else {
    //                 $runningBalance += $credit -  $debit;
    //             }

    //             $ledgerEntries[] = [
    //                 'date' => $transaction->created_at,
    //                 'invoice' => $invoice,
    //                 'description' => $transaction->remark,
    //                 'debit' => $debit,
    //                 'credit' => $credit,
    //                 'balance' => $runningBalance,
    //                 'account_name' => (($relatedAccountTransaction->account->account_name ?? '') . " " . ($relatedAccountTransaction->account->bank_name ?? "") . ' ' . ($relatedAccountTransaction->account->account_code ?? ""))
    //             ];
    //         }

    //         // Add subtotals if needed
    //         $ledgerSummary = [
    //             'opening_balance' => $openingBalance,
    //             'total_debit' => $totalDebit,
    //             'total_credit' => $totalCredit,
    //             'closing_balance' => $runningBalance,
    //         ];
    //     }

    //     return view('backend.pages.reports.ledger', get_defined_vars());
    // }


    // public function ledger(Request $request)
    // {
    //     $title = 'Ledger Report';
    //     $accounts = ChartOfAccount::where("parent_id", 0)->get();
    //     $companyInfo = Company::latest('id')->first();

    //     $selectedAccountId = $request->input('account_id');
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     $ledgerEntries = [];
    //     $openingBalance = 0;
    //     $runningBalance = 0;
    //     $account = null;

    //     if ($selectedAccountId) {
    //         $account = ChartOfAccount::findOrFail($selectedAccountId);

    //         // Opening Balance Calculation (আগের কোড ঠিক আছে)
    //         $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('debit');

    //         $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('credit');

    //         if ($account->balance_type === 'debit') {
    //             $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    //         } else {
    //             $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
    //         }

    //         $runningBalance = $openingBalance;
    //         $totalDebit = 0;
    //         $totalCredit = 0;

    //         $transactions = AccountTransaction::with(['supplier', 'customer', 'account'])
    //             ->where('account_id', $selectedAccountId)
    //             ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
    //             ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
    //             ->orderBy('created_at')
    //             ->get();

    //         foreach ($transactions as $transaction) {


    //             $oppositeName = 'N/A';

    //             if ($transaction->supplier_id && $transaction->supplier) {
    //                 $oppositeName = $transaction->supplier->name;
    //             } elseif ($transaction->customer_id && $transaction->customer) {
    //                 $oppositeName = $transaction->customer->name;
    //             } elseif ($transaction->remark) {
    //                 $oppositeName = $transaction->remark;
    //             }

    //             $debit = $transaction->debit ?? 0;
    //             $credit = $transaction->credit ?? 0;

    //             $totalDebit += $debit;
    //             $totalCredit += $credit;

    //             if ($account->balance_type == "debit") {
    //                 $runningBalance += $debit - $credit;
    //             } else {
    //                 $runningBalance += $credit - $debit;
    //             }

    //             $ledgerEntries[] = [
    //                 'date'          => $transaction->created_at,
    //                 'invoice'       => $transaction->invoice ?? $transaction->payment_invoice,
    //                 'description'   => $transaction->remark ?? 'Purchase Voucher',
    //                 'debit'         => $debit,
    //                 'credit'        => $credit,
    //                 'balance'       => $runningBalance,
    //                 'account_name'  => $oppositeName,           // 
    //             ];
    //         }

    //         $ledgerSummary = [
    //             'opening_balance' => $openingBalance,
    //             'total_debit'     => $totalDebit,
    //             'total_credit'    => $totalCredit,
    //             'closing_balance' => $runningBalance,
    //         ];
    //     }

    //     return view('backend.pages.reports.ledger', get_defined_vars());
    // }

    // public function ledger(Request $request)
    // {
    //     $title = 'Ledger Report';
    //     $accounts = ChartOfAccount::where("parent_id", 0)->get();
    //     $companyInfo = Company::latest('id')->first();

    //     $selectedAccountId = $request->input('account_id');
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     $ledgerEntries = [];
    //     $openingBalance = 0;
    //     $runningBalance = 0;
    //     $account = null;

    //     if ($selectedAccountId) {
    //         $account = ChartOfAccount::findOrFail($selectedAccountId);

    //         // Opening Balance Calculation 
    //         $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('debit');

    //         $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('credit');

    //         if ($account->balance_type === 'debit') {
    //             $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    //         } else {
    //             $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
    //         }

    //         $runningBalance = $openingBalance;
    //         $totalDebit = 0;
    //         $totalCredit = 0;

    //         $transactions = AccountTransaction::with(['supplier', 'customer', 'account'])
    //             ->where('account_id', $selectedAccountId)
    //             ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
    //             ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
    //             ->orderBy('created_at')
    //             ->get();

    //         $allInvoices = $transactions->pluck('invoice')->filter()->unique()->values()->toArray();

    //         $invoiceTransactions = AccountTransaction::with('account')
    //             ->whereIn('invoice', $allInvoices)
    //             ->get()
    //             ->groupBy('invoice');

    //         foreach ($transactions as $transaction) {

    //             $oppositeName = 'N/A';

    //             if ($transaction->supplier_id && $transaction->supplier) {
    //                 $oppositeName = $transaction->supplier->name;
    //             } elseif ($transaction->customer_id && $transaction->customer) {
    //                 $oppositeName = $transaction->customer->name;
    //             } elseif ($transaction->invoice && isset($invoiceTransactions[$transaction->invoice])) {

    //                 $sameInvoiceGroup = $invoiceTransactions[$transaction->invoice];

    //                 $isDebit  = $transaction->debit > 0;
    //                 $amount   = $isDebit ? $transaction->debit : $transaction->credit;

    //                 $opposite = $sameInvoiceGroup
    //                     ->where('id', '!=', $transaction->id)
    //                     ->where('account_id', '!=', $transaction->account_id)
    //                     ->when($isDebit, function ($col) use ($amount) {
    //                         return $col->where('credit', $amount);
    //                     }, function ($col) use ($amount) {
    //                         return $col->where('debit', $amount);
    //                     })
    //                     ->first();

    //                 if ($opposite && $opposite->account) {
    //                     $oppositeName = $opposite->account->account_name;
    //                 }
    //             } elseif ($transaction->remark) {
    //                 $oppositeName = explode(' - ', $transaction->remark)[0] ?? $transaction->remark;
    //             }

    //             $debit  = (float) ($transaction->debit  ?? 0);
    //             $credit = (float) ($transaction->credit ?? 0);

    //             $totalDebit  += $debit;
    //             $totalCredit += $credit;

    //             $runningBalance += $account->balance_type === 'debit'
    //                 ? ($debit - $credit)
    //                 : ($credit - $debit);

    //             $ledgerEntries[] = [
    //                 'date'         => $transaction->created_at,
    //                 'invoice'      => $transaction->invoice ?? $transaction->payment_invoice ?? 'N/A',
    //                 'account_name' => $oppositeName,
    //                 'description'  => $transaction->remark ?? 'N/A',
    //                 'debit'        => $debit,
    //                 'credit'       => $credit,
    //                 'balance'      => $runningBalance,
    //             ];
    //         }

    //         $ledgerSummary = [
    //             'opening_balance' => $openingBalance,
    //             'total_debit'     => $totalDebit,
    //             'total_credit'    => $totalCredit,
    //             'closing_balance' => $runningBalance,
    //         ];
    //     }

    //     return view('backend.pages.reports.ledger', get_defined_vars());
    // }

    // public function ledger(Request $request)
    // {
    //     $title       = 'Ledger Report';
    //     $accounts    = ChartOfAccount::where("parent_id", 0)->get();
    //     $companyInfo = Company::latest('id')->first();

    //     $selectedAccountId = $request->input('account_id');
    //     $startDate         = $request->input('start_date');
    //     $endDate           = $request->input('end_date');

    //     $ledgerEntries  = [];
    //     $openingBalance = 0;
    //     $runningBalance = 0;
    //     $account        = null;
    //     $ledgerSummary  = [];

    //     if ($selectedAccountId) {
    //         $account = ChartOfAccount::findOrFail($selectedAccountId);

    //         // ── Opening Balance ──
    //         $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('debit');

    //         $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('credit');

    //         if ($account->balance_type === 'debit') {
    //             $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    //         } else {
    //             $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
    //         }

    //         $runningBalance = $openingBalance;
    //         $totalDebit     = 0;
    //         $totalCredit    = 0;

    //         $transactions = AccountTransaction::with(['supplier', 'customer', 'account'])
    //             ->where('account_id', $selectedAccountId)
    //             ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
    //             ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
    //             ->orderBy('created_at')
    //             ->get();

    //         // ── Same invoice এর সব transaction load ──
    //         $allInvoices = $transactions->pluck('invoice')->filter()->unique()->values()->toArray();

    //         $invoiceTransactions = AccountTransaction::with('account')
    //             ->whereIn('invoice', $allInvoices)
    //             ->get()
    //             ->groupBy('invoice');

    //         foreach ($transactions as $transaction) {

    //             $oppositeName = 'N/A';

    //             if ($transaction->invoice && isset($invoiceTransactions[$transaction->invoice])) {

    //                 $sameInvoiceGroup = $invoiceTransactions[$transaction->invoice];

    //                 $isDebit = (float)$transaction->debit > 0;
    //                 $amount  = $isDebit ? (float)$transaction->debit : (float)$transaction->credit;

    //                 // ✅ Current debit → same invoice + same amount এর credit record খুঁজো
    //                 // ✅ Current credit → same invoice + same amount এর debit record খুঁজো
    //                 $opposite = $sameInvoiceGroup
    //                     ->where('id', '!=', $transaction->id)
    //                     ->where('account_id', '!=', $transaction->account_id)
    //                     ->first(function ($item) use ($isDebit, $amount) {
    //                         if ($isDebit) {
    //                             // debit transaction → opposite এ credit হবে same amount
    //                             return (float)$item->credit == $amount && (float)$item->debit == 0;
    //                         } else {
    //                             // credit transaction → opposite এ debit হবে same amount
    //                             return (float)$item->debit == $amount && (float)$item->credit == 0;
    //                         }
    //                     });

    //                 if ($opposite && $opposite->account) {
    //                     // ✅ Opposite record এর account_id এর account_name
    //                     $oppositeName = $opposite->account->account_name;
    //                 }
    //             }

    //             // ── Opposite পাওয়া না গেলে supplier/customer/remark fallback ──
    //             if ($oppositeName === 'N/A') {
    //                 if (
    //                     $transaction->party_type === 'supplier' &&
    //                     (int)$transaction->supplier_id > 0 &&
    //                     $transaction->supplier
    //                 ) {
    //                     $oppositeName = $transaction->supplier->name;
    //                 } elseif (
    //                     $transaction->party_type === 'customer' &&
    //                     (int)$transaction->customer_id > 0
    //                 ) {
    //                     $oppositeName = $transaction->customer?->name
    //                         ?? \App\Models\Customer::find($transaction->customer_id)?->name
    //                         ?? 'N/A';
    //                 } elseif (
    //                     (int)$transaction->supplier_id > 0 &&
    //                     $transaction->supplier
    //                 ) {
    //                     $oppositeName = $transaction->supplier->name;
    //                 } elseif ((int)$transaction->customer_id > 0) {
    //                     $oppositeName = $transaction->customer?->name
    //                         ?? \App\Models\Customer::find($transaction->customer_id)?->name
    //                         ?? 'N/A';
    //                 } elseif ($transaction->remark) {
    //                     $oppositeName = explode(' - ', $transaction->remark)[0] ?? $transaction->remark;
    //                 }
    //             }

    //             $debit  = (float) ($transaction->debit  ?? 0);
    //             $credit = (float) ($transaction->credit ?? 0);

    //             $totalDebit  += $debit;
    //             $totalCredit += $credit;

    //             $runningBalance += $account->balance_type === 'debit'
    //                 ? ($debit - $credit)
    //                 : ($credit - $debit);

    //             $ledgerEntries[] = [
    //                 'date'         => $transaction->created_at,
    //                 'invoice'      => $transaction->invoice ?? $transaction->payment_invoice ?? 'N/A',
    //                 'account_name' => $oppositeName,
    //                 'description'  => $transaction->remark ?? 'N/A',
    //                 'debit'        => $debit,
    //                 'credit'       => $credit,
    //                 'balance'      => $runningBalance,
    //             ];
    //         }

    //         $ledgerSummary = [
    //             'opening_balance' => $openingBalance,
    //             'total_debit'     => $totalDebit,
    //             'total_credit'    => $totalCredit,
    //             'closing_balance' => $runningBalance,
    //         ];
    //     }

    //     return view('backend.pages.reports.ledger', get_defined_vars());
    // }

    // public function ledger(Request $request)
    // {
    //     $title       = 'Ledger Report';
    //     $accounts    = ChartOfAccount::where("parent_id", 0)->get();
    //     $companyInfo = Company::latest('id')->first();

    //     $selectedAccountId = $request->input('account_id');
    //     $startDate         = $request->input('start_date');
    //     $endDate           = $request->input('end_date');

    //     $ledgerEntries  = [];
    //     $openingBalance = 0;
    //     $runningBalance = 0;
    //     $account        = null;
    //     $ledgerSummary  = [];

    //     if ($selectedAccountId) {
    //         $account = ChartOfAccount::findOrFail($selectedAccountId);

    //         // ── Opening Balance ──
    //         $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('debit');

    //         $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('credit');

    //         if ($account->balance_type === 'debit') {
    //             $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    //         } else {
    //             $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
    //         }

    //         $runningBalance = $openingBalance;
    //         $totalDebit     = 0;
    //         $totalCredit    = 0;

    //         $transactions = AccountTransaction::with(['supplier', 'customer', 'account'])
    //             ->where('account_id', $selectedAccountId)
    //             ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
    //             ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
    //             ->orderBy('created_at')
    //             ->get();

    //         // ── Same invoice এর সব transaction load ──
    //         $allInvoices = $transactions->pluck('invoice')->filter()->unique()->values()->toArray();

    //         $invoiceTransactions = AccountTransaction::with('account')
    //             ->whereIn('invoice', $allInvoices)
    //             ->get()
    //             ->groupBy('invoice');

    //         foreach ($transactions as $transaction) {

    //             $oppositeName = 'N/A';

    //             if ($transaction->invoice && isset($invoiceTransactions[$transaction->invoice])) {

    //                 $sameInvoiceGroup = $invoiceTransactions[$transaction->invoice];

    //                 $isDebit = (float)$transaction->debit > 0;
    //                 $amount  = $isDebit ? (float)$transaction->debit : (float)$transaction->credit;

    //                 $opposite = $sameInvoiceGroup
    //                     ->where('id', '!=', $transaction->id)
    //                     ->where('account_id', '!=', $transaction->account_id)
    //                     // ✅ same date filter
    //                     ->filter(function ($item) use ($transaction) {
    //                         return $item->created_at->toDateString() === $transaction->created_at->toDateString();
    //                     })
    //                     // ✅ same amount + correct debit/credit side
    //                     ->first(function ($item) use ($isDebit, $amount) {
    //                         if ($isDebit) {
    //                             return (float)$item->credit == $amount && (float)$item->debit == 0;
    //                         } else {
    //                             return (float)$item->debit == $amount && (float)$item->credit == 0;
    //                         }
    //                     });

    //                 if ($opposite && $opposite->account) {
    //                     $oppositeName = $opposite->account->account_name;
    //                 }
    //             }

    //             // ── Opposite পাওয়া না গেলে fallback ──
    //             if ($oppositeName === 'N/A') {

    //                 if (
    //                     $transaction->party_type === 'supplier' &&
    //                     (int)$transaction->supplier_id > 0 &&
    //                     $transaction->supplier
    //                 ) {
    //                     $oppositeName = $transaction->supplier->name;
    //                 } elseif (
    //                     $transaction->party_type === 'customer' &&
    //                     (int)$transaction->customer_id > 0
    //                 ) {
    //                     $oppositeName = $transaction->customer?->name
    //                         ?? \App\Models\Customer::find($transaction->customer_id)?->name
    //                         ?? 'N/A';
    //                 } elseif (
    //                     (int)$transaction->supplier_id > 0 &&
    //                     $transaction->supplier
    //                 ) {
    //                     $oppositeName = $transaction->supplier->name;
    //                 } elseif ((int)$transaction->customer_id > 0) {
    //                     $oppositeName = $transaction->customer?->name
    //                         ?? \App\Models\Customer::find($transaction->customer_id)?->name
    //                         ?? 'N/A';
    //                 } elseif ($transaction->remark) {
    //                     $oppositeName = explode(' - ', $transaction->remark)[0] ?? $transaction->remark;
    //                 }
    //             }

    //             $debit  = (float) ($transaction->debit  ?? 0);
    //             $credit = (float) ($transaction->credit ?? 0);

    //             $totalDebit  += $debit;
    //             $totalCredit += $credit;

    //             $runningBalance += $account->balance_type === 'debit'
    //                 ? ($debit - $credit)
    //                 : ($credit - $debit);

    //             $ledgerEntries[] = [
    //                 'date'         => $transaction->created_at,
    //                 'invoice'      => $transaction->invoice ?? $transaction->payment_invoice ?? 'N/A',
    //                 'account_name' => $oppositeName,
    //                 'description'  => $transaction->remark ?? 'N/A',
    //                 'debit'        => $debit,
    //                 'credit'       => $credit,
    //                 'balance'      => $runningBalance,
    //             ];
    //         }

    //         $ledgerSummary = [
    //             'opening_balance' => $openingBalance,
    //             'total_debit'     => $totalDebit,
    //             'total_credit'    => $totalCredit,
    //             'closing_balance' => $runningBalance,
    //         ];
    //     }

    //     return view('backend.pages.reports.ledger', get_defined_vars());
    // }


    // public function ledger(Request $request)
    // {
    //     $title       = 'Ledger Report';
    //     $accounts    = ChartOfAccount::where("parent_id", 0)->get();
    //     $companyInfo = Company::latest('id')->first();

    //     $selectedAccountId = $request->input('account_id');
    //     $startDate         = $request->input('start_date');
    //     $endDate           = $request->input('end_date');

    //     $ledgerEntries  = [];
    //     $openingBalance = 0;
    //     $runningBalance = 0;
    //     $account        = null;
    //     $ledgerSummary  = [];

    //     if ($selectedAccountId) {
    //         $account = ChartOfAccount::findOrFail($selectedAccountId);

    //         // ── Opening Balance ──
    //         $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('debit');

    //         $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
    //             ->whereDate('created_at', '<', $startDate)
    //             ->sum('credit');

    //         if ($account->balance_type === 'debit') {
    //             $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    //         } else {
    //             $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
    //         }

    //         $runningBalance = $openingBalance;
    //         $totalDebit     = 0;
    //         $totalCredit    = 0;

    //         $transactions = AccountTransaction::with(['supplier', 'customer', 'account'])
    //             ->where('account_id', $selectedAccountId)
    //             ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
    //             ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
    //             ->orderBy('created_at')
    //             ->get();

    //         // ── Same invoice এর সব transaction load ──
    //         $allInvoices = $transactions->pluck('invoice')->filter()->unique()->values()->toArray();

    //         $invoiceTransactions = AccountTransaction::with('account')
    //             ->whereIn('invoice', $allInvoices)
    //             ->get()
    //             ->groupBy('invoice');

    //         foreach ($transactions as $transaction) {

    //             $oppositeName = 'N/A';

    //             if ($transaction->invoice && isset($invoiceTransactions[$transaction->invoice])) {

    //                 $sameInvoiceGroup = $invoiceTransactions[$transaction->invoice];

    //                 $isDebit = (float)$transaction->debit > 0;
    //                 $amount  = $isDebit ? (float)$transaction->debit : (float)$transaction->credit;

    //                 $opposite = $sameInvoiceGroup
    //                     ->where('id', '!=', $transaction->id)
    //                     ->where('account_id', '!=', $transaction->account_id)
    //                     // ✅ same date filter
    //                     ->filter(function ($item) use ($transaction) {
    //                         return $item->created_at->toDateString() === $transaction->created_at->toDateString();
    //                     })
    //                     // ✅ same amount + correct debit/credit side
    //                     ->first(function ($item) use ($isDebit, $amount) {
    //                         if ($isDebit) {
    //                             return (float)$item->credit == $amount && (float)$item->debit == 0;
    //                         } else {
    //                             return (float)$item->debit == $amount && (float)$item->credit == 0;
    //                         }
    //                     });

    //                 if ($opposite && $opposite->account) {
    //                     $oppositeName = $opposite->account->account_name;
    //                 }
    //             }

    //             // ── Opposite পাওয়া না গেলে fallback ──
    //             // ✅ FIX: ?-> (nullsafe) সরিয়ে PHP 7.x compatible করা হয়েছে
    //             if ($oppositeName === 'N/A') {

    //                 if (
    //                     $transaction->party_type === 'supplier' &&
    //                     (int)$transaction->supplier_id > 0 &&
    //                     $transaction->supplier
    //                 ) {
    //                     $oppositeName = $transaction->supplier->name;
    //                 } elseif (
    //                     $transaction->party_type === 'customer' &&
    //                     (int)$transaction->customer_id > 0
    //                 ) {
    //                     if ($transaction->customer && $transaction->customer->name) {
    //                         $oppositeName = $transaction->customer->name;
    //                     } else {
    //                         $customer = \App\Models\Customer::find($transaction->customer_id);
    //                         $oppositeName = ($customer && $customer->name) ? $customer->name : 'N/A';
    //                     }
    //                 } elseif (
    //                     (int)$transaction->supplier_id > 0 &&
    //                     $transaction->supplier
    //                 ) {
    //                     $oppositeName = $transaction->supplier->name;
    //                 } elseif ((int)$transaction->customer_id > 0) {
    //                     if ($transaction->customer && $transaction->customer->name) {
    //                         $oppositeName = $transaction->customer->name;
    //                     } else {
    //                         $customer = \App\Models\Customer::find($transaction->customer_id);
    //                         $oppositeName = ($customer && $customer->name) ? $customer->name : 'N/A';
    //                     }
    //                 } elseif ($transaction->remark) {
    //                     $oppositeName = explode(' - ', $transaction->remark)[0] ?? $transaction->remark;
    //                 }
    //             }

    //             $debit  = (float) ($transaction->debit  ?? 0);
    //             $credit = (float) ($transaction->credit ?? 0);

    //             $totalDebit  += $debit;
    //             $totalCredit += $credit;

    //             $runningBalance += $account->balance_type === 'debit'
    //                 ? ($debit - $credit)
    //                 : ($credit - $debit);

    //             $ledgerEntries[] = [
    //                 'date'         => $transaction->created_at,
    //                 'invoice'      => $transaction->invoice ?? $transaction->payment_invoice ?? 'N/A',
    //                 'account_name' => $oppositeName,
    //                 'description'  => $transaction->remark ?? 'N/A',
    //                 'debit'        => $debit,
    //                 'credit'       => $credit,
    //                 'balance'      => $runningBalance,
    //             ];
    //         }

    //         $ledgerSummary = [
    //             'opening_balance' => $openingBalance,
    //             'total_debit'     => $totalDebit,
    //             'total_credit'    => $totalCredit,
    //             'closing_balance' => $runningBalance,
    //         ];
    //     }

    //     return view('backend.pages.reports.ledger', get_defined_vars());
    // }


    public function ledger(Request $request)
    {

        $title       = 'Ledger Report';
        $accounts    = ChartOfAccount::where("parent_id", 0)->where("status", "Active")->get();
        $companyInfo = Company::latest('id')->first();

        $selectedAccountId = $request->input('account_id');
        $startDate         = $request->input('start_date');
        $endDate           = $request->input('end_date');

        $ledgerEntries  = [];
        $openingBalance = 0;
        $runningBalance = 0;
        $account        = null;
        $ledgerSummary  = [];

        if ($selectedAccountId) {
            $account = ChartOfAccount::findOrFail($selectedAccountId);

            // ── Opening Balance ──
            $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
                ->whereDate('created_at', '<', $startDate)
                ->sum('debit');

            $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
                ->whereDate('created_at', '<', $startDate)
                ->sum('credit');

            if ($account->balance_type === 'debit') {
                $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
            } else {
                $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
            }

            $runningBalance = $openingBalance;
            $totalDebit     = 0;
            $totalCredit    = 0;

            $transactions = AccountTransaction::with(['supplier', 'customer', 'account'])
                ->where('account_id', $selectedAccountId)
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->orderBy('created_at')
                ->get();

            // ── Same invoice এর সব transaction load ──
            $allInvoices = $transactions->pluck('invoice')->filter()->unique()->values()->toArray();

            $invoiceTransactions = AccountTransaction::with('account')
                ->whereIn('invoice', $allInvoices)
                ->get()
                ->groupBy('invoice');

            foreach ($transactions as $transaction) {

                $oppositeName = 'N/A';

                if ($transaction->invoice && isset($invoiceTransactions[$transaction->invoice])) {

                    $sameInvoiceGroup = $invoiceTransactions[$transaction->invoice];

                    $isDebit = (float)$transaction->debit > 0;
                    $amount  = $isDebit ? (float)$transaction->debit : (float)$transaction->credit;

                    // ── Step 1: Exact amount match ──
                    $opposite = $sameInvoiceGroup
                        ->where('id', '!=', $transaction->id)
                        ->where('account_id', '!=', $transaction->account_id)
                        ->filter(function ($item) use ($transaction) {
                            return $item->created_at->toDateString() === $transaction->created_at->toDateString();
                        })
                        ->first(function ($item) use ($isDebit, $amount) {
                            if ($isDebit) {
                                return (float)$item->credit == $amount && (float)$item->debit == 0;
                            } else {
                                return (float)$item->debit == $amount && (float)$item->credit == 0;
                            }
                        });

                    // ── Step 2: Split entry 
                    if (!$opposite) {
                        $opposite = $sameInvoiceGroup
                            ->where('id', '!=', $transaction->id)
                            ->where('account_id', '!=', $transaction->account_id)
                            ->filter(function ($item) use ($transaction) {
                                return $item->created_at->toDateString() === $transaction->created_at->toDateString();
                            })
                            ->first(function ($item) use ($isDebit) {
                                if ($isDebit) {
                                    return (float)$item->credit > 0;
                                } else {
                                    return (float)$item->debit > 0;
                                }
                            });
                    }

                    if ($opposite && $opposite->account) {
                        $oppositeName = $opposite->account->account_name;
                    }
                }

                // ── Opposite পাওয়া না গেলে fallback ──
                if ($oppositeName === 'N/A') {

                    if (
                        $transaction->party_type === 'supplier' &&
                        (int)$transaction->supplier_id > 0 &&
                        $transaction->supplier
                    ) {
                        $oppositeName = $transaction->supplier->name;
                    } elseif (
                        $transaction->party_type === 'customer' &&
                        (int)$transaction->customer_id > 0
                    ) {
                        if ($transaction->customer && $transaction->customer->name) {
                            $oppositeName = $transaction->customer->name;
                        } else {
                            $customer = \App\Models\Customer::find($transaction->customer_id);
                            $oppositeName = ($customer && $customer->name) ? $customer->name : 'N/A';
                        }
                    } elseif (
                        (int)$transaction->supplier_id > 0 &&
                        $transaction->supplier
                    ) {
                        $oppositeName = $transaction->supplier->name;
                    } elseif ((int)$transaction->customer_id > 0) {
                        if ($transaction->customer && $transaction->customer->name) {
                            $oppositeName = $transaction->customer->name;
                        } else {
                            $customer = \App\Models\Customer::find($transaction->customer_id);
                            $oppositeName = ($customer && $customer->name) ? $customer->name : 'N/A';
                        }
                    }
                }

                $debit  = (float) ($transaction->debit  ?? 0);
                $credit = (float) ($transaction->credit ?? 0);

                $totalDebit  += $debit;
                $totalCredit += $credit;

                $runningBalance += $account->balance_type === 'debit'
                    ? ($debit - $credit)
                    : ($credit - $debit);

                $ledgerEntries[] = [
                    'date'         => $transaction->created_at,
                    'invoice'      => $transaction->invoice ?? 'N/A',
                    'account_name' => $oppositeName,
                    'description'  => $transaction->remark ?? 'N/A',
                    'debit'        => $debit,
                    'credit'       => $credit,
                    'balance'      => $runningBalance,
                ];
            }

            $ledgerSummary = [
                'opening_balance' => $openingBalance,
                'total_debit'     => $totalDebit,
                'total_credit'    => $totalCredit,
                'closing_balance' => $runningBalance,
            ];
        }

        return view('backend.pages.reports.ledger', get_defined_vars());
    }

    public function groupledgerList(Request $request)
    {
        $title = 'Group Ledger';
        $companyInfo = Company::latest('id')->first();

        $mainGroups = ChartOfAccount::whereNull('parent_id')
            ->orWhere('parent_id', 0)
            ->where('status', 1)
            ->orderBy('account_name')
            ->get();

        $account    = null;
        $subLedgers = collect();
        $summary    = ['total_debit' => 0, 'total_credit' => 0];

        $startDate = $request->start_date ?? date('Y-m-d', strtotime('-30 days'));
        $endDate   = $request->end_date   ?? date('Y-m-d');

        if ($request->account_id) {

            $account = ChartOfAccount::find($request->account_id);

            // Selected group এর direct children আনো
            $children = ChartOfAccount::where('parent_id', $request->account_id)
                ->where('status', 1)
                ->orderBy('account_name')
                ->get();

            // প্রতিটা child এর balance calculate করো
            $subLedgers = $children->map(function ($child) use ($startDate, $endDate) {

                // এই child এর নিচে সব nested account IDs
                $childIds   = ChartOfAccount::getTypeOfAccount([$child->id]);
                $childIds[] = $child->id;

                // Opening Balance — start_date এর আগের transactions
                $openingDebit  = AccountTransaction::whereIn('account_id', $childIds)
                    ->where('created_at', '<', $startDate)
                    ->sum('debit');

                $openingCredit = AccountTransaction::whereIn('account_id', $childIds)
                    ->where('created_at', '<', $startDate)
                    ->sum('credit');

                $child->opening_balance = $openingDebit - $openingCredit;

                // Period Debit & Credit
                $child->period_debit = AccountTransaction::whereIn('account_id', $childIds)
                    ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                    ->sum('debit');

                $child->period_credit = AccountTransaction::whereIn('account_id', $childIds)
                    ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                    ->sum('credit');

                // Closing Balance
                $child->closing_balance = $child->opening_balance
                    + $child->period_debit
                    - $child->period_credit;

                return $child;
            });

            $summary['total_debit']  = $subLedgers->sum('period_debit');
            $summary['total_credit'] = $subLedgers->sum('period_credit');
        }

        return view('backend.pages.reports.legergrouplist', get_defined_vars());
    }

    public function groupLedgerData(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-d', strtotime('-30 days'));
        $endDate   = $request->end_date   ?? date('Y-m-d');

        $children = ChartOfAccount::where('parent_id', $request->account_id)
            ->where('status', 1)
            ->orderBy('account_name')
            ->get();

        $subLedgers = $children->map(function ($child) use ($startDate, $endDate) {

            $childIds   = ChartOfAccount::getTypeOfAccount([$child->id]);
            $childIds[] = $child->id;

            $openingDebit  = AccountTransaction::whereIn('account_id', $childIds)
                ->where('created_at', '<', $startDate)
                ->sum('debit');

            $openingCredit = AccountTransaction::whereIn('account_id', $childIds)
                ->where('created_at', '<', $startDate)
                ->sum('credit');

            $child->opening_balance = $openingDebit - $openingCredit;

            $child->period_debit = AccountTransaction::whereIn('account_id', $childIds)
                ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                ->sum('debit');

            $child->period_credit = AccountTransaction::whereIn('account_id', $childIds)
                ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                ->sum('credit');

            $child->closing_balance = $child->opening_balance + $child->period_debit - $child->period_credit;
            return $child;
        });

        return response()->json([
            'subLedgers' => $subLedgers->values(),
            'summary' => [
                'total_debit'  => $subLedgers->sum('period_debit'),
                'total_credit' => $subLedgers->sum('period_credit'),
            ]
        ]);
    }

    public function getSubGroups(Request $request)
    {

        $groups = ChartOfAccount::where('parent_id', $request->parent_id)
            ->where('status', 1)
            ->orderBy('account_name')
            ->get();
        return response()->json($groups);
    }



    public function groupledger(Request $request)
    {
        $title = 'Group Ledger Report';

        $companyInfo = Company::latest('id')->first();

        // Get all accounts (you can filter by parent_id if needed)
        $accounts = ChartOfAccount::get();

        $groupLedgerData = [];
        $totalDebitBalance = 0;
        $totalCreditBalance = 0;

        foreach ($accounts as $account) {
            // Use the EXACT same calculation as your original ledger function

            // Calculate opening balance (from beginning of time)
            $debitSumBeforeToday = AccountTransaction::where('account_id', $account->id)->sum('debit');
            $creditSumBeforeToday = AccountTransaction::where('account_id', $account->id)->sum('credit');

            // Calculate opening balance based on account balance type (same as original)
            if ($account->balance_type === 'debit') {
                $openingBalance = $account->opening_balance + $debitSumBeforeToday - $creditSumBeforeToday;
            } else {
                $openingBalance = $account->opening_balance + $creditSumBeforeToday - $debitSumBeforeToday;
            }

            // Get ALL transactions for this account (same as original ledger logic)
            $transactions = AccountTransaction::where('account_id', $account->id)
                ->orderBy('created_at')
                ->get();

            // Calculate running balance exactly like in your original ledger
            $runningBalance = $account->opening_balance; // Start with opening balance
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($transactions as $transaction) {
                $debit = $transaction->debit ?? 0;
                $credit = $transaction->credit ?? 0;

                $totalDebit += $debit;
                $totalCredit += $credit;

                // Use EXACT same running balance calculation as original ledger
                if ($account->balance_type == "debit") {
                    $runningBalance += $debit - $credit;
                } else {
                    $runningBalance += $credit - $debit;
                }
            }

            // Final closing balance is the runningBalance (same as original ledger)
            $closingBalance = $runningBalance;

            // Only include accounts with non-zero closing balances or that have had transactions
            if ($closingBalance != 0 || $totalDebit > 0 || $totalCredit > 0) {
                $groupLedgerData[] = [
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'bank_name' => $account->bank_name,
                    'balance_type' => $account->balance_type,
                    'closing_balance' => $closingBalance,
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                ];

                // Add to totals based on balance type and positive balances only
                if ($account->balance_type === 'debit' && $closingBalance > 0) {
                    $totalDebitBalance += $closingBalance;
                } elseif ($account->balance_type === 'credit' && $closingBalance > 0) {
                    $totalCreditBalance += $closingBalance;
                } elseif ($account->balance_type === 'debit' && $closingBalance < 0) {
                    // Negative debit balance acts like credit
                    $totalCreditBalance += abs($closingBalance);
                } elseif ($account->balance_type === 'credit' && $closingBalance < 0) {
                    // Negative credit balance acts like debit
                    $totalDebitBalance += abs($closingBalance);
                }
            }
        }

        // Sort by account code or name
        usort($groupLedgerData, function ($a, $b) {
            return strcmp($a['account_code'], $b['account_code']);
        });

        return view('backend.pages.reports.groupledger', get_defined_vars());
    }
    public function accountledger(Request $request)
    {
        $title = 'Ledger Report';
        $accounts = ChartOfAccount::find($request->account_id);

        $findreports = AccountTransaction::selectRaw('debit,credit,account_id,created_at,invoice,remark,type,supplier_id,customer_id,employee_id')->where('type', "!=", 12);

        $newOpeningBalance =  Transection::where('account_id', $request->account_id)->where('type', 1)->pluck('amount')->first() ?? 0;

        $findreports = $findreports->where('account_id', $request->account_id);
        $findreports = $findreports->get();

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.accountledger', get_defined_vars());
    }

    // public function trialbalance(Request $request)
    // {
    //     $title = 'Trial Balance Report';

    //     $startDate = $request->input('start_date') ?? date("Y-m-d");
    //     $endDate = $request->input('end_date') ?? date("Y-m-d");

    //     // Fetch names for main heads from the database
    //     $parentAccounts = ChartOfAccount::whereIn('id', [1, 9, 17, 19])->get();
    //     $parentIds = [
    //         'Asset' => getAccountByUniqueID(1)->id,
    //         'Liabilities' => getAccountByUniqueID(9)->id,
    //         'Income' => getAccountByUniqueID(17)->id,
    //         'Expenses' => getAccountByUniqueID(19)->id,
    //     ];
    //     $parentNames = [
    //         'Asset' => $parentAccounts->where('id', 1)->first()->account_name ?? 'Asset',
    //         'Liabilities' => $parentAccounts->where('id', 9)->first()->account_name ?? 'Liabilities',
    //         'Income' => $parentAccounts->where('id', 17)->first()->account_name ?? 'Income',
    //         'Expenses' => $parentAccounts->where('id', 19)->first()->account_name ?? 'Expenses',
    //     ];

    //     $groupedTrialBalance = [
    //         'Asset' => [],
    //         'Liabilities' => [],
    //         'Income' => [],
    //         'Expenses' => []
    //     ];

    //     foreach ($parentIds as $key => $parentId) {
    //         $accounts = getAllSubAccounts($parentId);

    //         foreach ($accounts as $account) {
    //             $openingDebit = $account->balance_type === 'debit' ? $account->opening_balance : 0;
    //             $openingCredit = $account->balance_type === 'credit' ? $account->opening_balance : 0;

    //             $openingTransactionDebit = AccountTransaction::where('account_id', $account->id)
    //                 ->whereDate('created_at', '<', $startDate)
    //                 ->sum('debit');

    //             $openingTransactionCredit = AccountTransaction::where('account_id', $account->id)
    //                 ->whereDate('created_at', '<', $startDate)
    //                 ->sum('credit');

    //             if (getFirstAccount($account->id) == $parentIds['Asset'] || getFirstAccount($account->id) == $parentIds['Expenses']) {
    //                 $openingDebit += $openingTransactionDebit - $openingTransactionCredit;
    //                 $openingCredit += 0;
    //             } elseif (getFirstAccount($account->id) == $parentIds['Liabilities'] || getFirstAccount($account->id) == $parentIds['Income']) {
    //                 $openingDebit += 0;
    //                 $openingCredit += $openingTransactionCredit - $openingTransactionDebit;
    //             }


    //             // if (getFirstAccount($account->id) == $parentIds['Income'] || getFirstAccount($account->id) == $parentIds['Expenses']) {
    //             //     $openingDebit = 0;
    //             //     $openingCredit = 0;
    //             // }

    //             // Calculate transactions within the period
    //             $transactionDebit = AccountTransaction::where('account_id', $account->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->sum('debit');

    //             $transactionCredit = AccountTransaction::where('account_id', $account->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->sum('credit');

    //             // Determine closing balances based on account type

    //             $closingDebit = $openingDebit + $transactionDebit - $transactionCredit;
    //             $closingCredit = $openingCredit + $transactionCredit - $transactionDebit;
    //             // Adjust closing balance logic based on account type

    //             if (getFirstAccount($account->id) == $parentIds['Asset'] || getFirstAccount($account->id) == $parentIds['Expenses']) {
    //                 $closingDebit = $closingDebit  ? $closingDebit : 0;
    //                 $closingCredit = $closingDebit == 0 ? abs($closingDebit) : 0;
    //             } elseif (getFirstAccount($account->id) == $parentIds['Liabilities'] || getFirstAccount($account->id) == $parentIds['Income']) {
    //                 $closingCredit = $closingCredit ? $closingCredit : 0;
    //                 $closingDebit = $closingCredit == 0 ? abs($closingCredit) : 0;
    //             }

    //             // Only add the entry if it has a non-zero opening balance or transactions within the period
    //             if ($openingDebit != 0 || $openingCredit != 0 || $transactionDebit != 0 || $transactionCredit != 0) {
    //                 $entry = [
    //                     'account_name' => $account->account_name,
    //                     'opening_debit' => $openingDebit,
    //                     'opening_credit' => $openingCredit,
    //                     'transaction_debit' => $transactionDebit,
    //                     'transaction_credit' => $transactionCredit,
    //                     'closing_debit' => $closingDebit,
    //                     'closing_credit' => $closingCredit,
    //                     'parent_id' => $account->parent_id,
    //                 ];

    //                 // Group accounts by parent ID
    //                 if ($key == 'Asset') {
    //                     $groupedTrialBalance['Asset'][] = $entry;
    //                 } elseif ($key == 'Liabilities') {
    //                     $groupedTrialBalance['Liabilities'][] = $entry;
    //                 } elseif ($key == 'Income') {
    //                     $groupedTrialBalance['Income'][] = $entry;
    //                 } elseif ($key == 'Expenses') {
    //                     $groupedTrialBalance['Expenses'][] = $entry;
    //                 }
    //             }
    //         }
    //     }

    //     $companyInfo = Company::latest('id')->first();

    //     return view('backend.pages.reports.trialbalance', get_defined_vars());
    // }

    public function trialbalance(Request $request)
    {
        $title = 'Trial Balance Report';

        $startDate = $request->input('start_date') ?? date("Y-m-d");
        $endDate   = $request->input('end_date')   ?? date("Y-m-d");

        // Bug 1 & 2 Fix: getAccountByUniqueID দিয়ে সব নাও, hardcoded id ব্যবহার করো না - Modified: 2026-06-29
        $assetAccount       = getAccountByUniqueID(1);
        $liabilitiesAccount = getAccountByUniqueID(9);
        $incomeAccount      = getAccountByUniqueID(17);
        $expensesAccount    = getAccountByUniqueID(19);

        $parentIds = [
            'Asset'       => $assetAccount->id,
            'Liabilities' => $liabilitiesAccount->id,
            'Income'      => $incomeAccount->id,
            'Expenses'    => $expensesAccount->id,
        ];

        $parentNames = [
            'Asset'       => $assetAccount->account_name,
            'Liabilities' => $liabilitiesAccount->account_name,
            'Income'      => $incomeAccount->account_name,
            'Expenses'    => $expensesAccount->account_name,
        ];

        $groupedTrialBalance = [
            'Asset'       => [],
            'Liabilities' => [],
            'Income'      => [],
            'Expenses'    => [],
        ];

        foreach ($parentIds as $key => $parentId) {
            $accounts = getAllSubAccounts($parentId);

            // Bug 5 Fix: সব account-এর transaction একসাথে ২টা query-তে নাও - Added: 2026-06-29
            $allAccountIds = collect($accounts)->pluck('id')->toArray();

            $openingTransactions = AccountTransaction::whereIn('account_id', $allAccountIds)
                ->whereDate('created_at', '<', $startDate)
                ->selectRaw('account_id, SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->groupBy('account_id')
                ->get()
                ->keyBy('account_id');

            $periodTransactions = AccountTransaction::whereIn('account_id', $allAccountIds)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->selectRaw('account_id, SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->groupBy('account_id')
                ->get()
                ->keyBy('account_id');

            foreach ($accounts as $account) {

                // Bug 6 Fix: getFirstAccount() একবারই call করো - Modified: 2026-06-29
                $firstAccountId = getFirstAccount($account->id);
                $isDebitNature  = (
                    $firstAccountId == $parentIds['Asset'] ||
                    $firstAccountId == $parentIds['Expenses']
                );

                // chart_of_accounts opening balance
                $obDebit  = $account->balance_type === 'debit'  ? (float) $account->opening_balance : 0;
                $obCredit = $account->balance_type === 'credit' ? (float) $account->opening_balance : 0;

                // start date  transactions
                $openTxn       = $openingTransactions->get($account->id);
                $openTxnDebit  = $openTxn ? (float) $openTxn->total_debit  : 0;
                $openTxnCredit = $openTxn ? (float) $openTxn->total_credit : 0;

                // Bug 4 Fix: net opening  calculate  - Modified: 2026-06-29
                if ($isDebitNature) {
                    $netOpening    = ($obDebit - $obCredit) + ($openTxnDebit - $openTxnCredit);
                    $openingDebit  = $netOpening >= 0 ? $netOpening : 0;
                    $openingCredit = $netOpening <  0 ? abs($netOpening) : 0;
                } else {
                    $netOpening    = ($obCredit - $obDebit) + ($openTxnCredit - $openTxnDebit);
                    $openingCredit = $netOpening >= 0 ? $netOpening : 0;
                    $openingDebit  = $netOpening <  0 ? abs($netOpening) : 0;
                }

                // period transactions
                $periodTxn         = $periodTransactions->get($account->id);
                $transactionDebit  = $periodTxn ? (float) $periodTxn->total_debit  : 0;
                $transactionCredit = $periodTxn ? (float) $periodTxn->total_credit : 0;

                // Bug 3 Fix: closing balance  calculate - Modified: 2026-06-29
                if ($isDebitNature) {
                    $netClosing    = $netOpening + ($transactionDebit - $transactionCredit);
                    $closingDebit  = $netClosing >= 0 ? $netClosing : 0;
                    $closingCredit = $netClosing <  0 ? abs($netClosing) : 0;
                } else {
                    $netClosing    = $netOpening + ($transactionCredit - $transactionDebit);
                    $closingCredit = $netClosing >= 0 ? $netClosing : 0;
                    $closingDebit  = $netClosing <  0 ? abs($netClosing) : 0;
                }

                // zero balance account skip 
                if (
                    $openingDebit     == 0 &&
                    $openingCredit    == 0 &&
                    $transactionDebit == 0 &&
                    $transactionCredit == 0
                ) {
                    continue;
                }

                $groupedTrialBalance[$key][] = [
                    'account_name'       => $account->account_name,
                    'opening_debit'      => $openingDebit,
                    'opening_credit'     => $openingCredit,
                    'transaction_debit'  => $transactionDebit,
                    'transaction_credit' => $transactionCredit,
                    'closing_debit'      => $closingDebit,
                    'closing_credit'     => $closingCredit,
                ];
            }
        }

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.trialbalance', get_defined_vars());
    }

    public function dashboardtrialbalance(Request $request)
    {
        $title = 'Bank Balance Report';

        $startDate = $request->input('start_date') ?? date("Y-m-d");
        $endDate = $request->input('end_date') ?? date("Y-m-d");

        // Fetch names for main heads from the database
        $parentAccounts = ChartOfAccount::whereIn('id', [1])->get();
        $parentIds = [
            'Bank' => getAccountByUniqueID(1)->id,
        ];
        $parentNames = [
            'Bank' => $parentAccounts->where('id', 1)->first()->account_name ?? 'Bank',
        ];

        $groupedTrialBalance = [
            'Bank' => [],
        ];

        foreach ($parentIds as $key => $parentId) {
            $accounts = ChartOfAccount::getaccount(8)->get();
            foreach ($accounts as $account) {
                $openingDebit = $account->balance_type === 'debit' ? $account->opening_balance : 0;
                $openingCredit = $account->balance_type === 'credit' ? $account->opening_balance : 0;

                $openingTransactionDebit = AccountTransaction::where('account_id', $account->id)
                    ->whereDate('created_at', '<', $startDate)
                    ->sum('debit');

                $openingTransactionCredit = AccountTransaction::where('account_id', $account->id)
                    ->whereDate('created_at', '<', $startDate)
                    ->sum('credit');

                if (getFirstAccount($account->id) == $parentIds['Bank']) {
                    $openingDebit += $openingTransactionDebit - $openingTransactionCredit;
                    $openingCredit += 0;
                }

                // if (getFirstAccount($account->id) == $parentIds['Income'] || getFirstAccount($account->id) == $parentIds['Expenses']) {
                //     $openingDebit = 0;
                //     $openingCredit = 0;
                // }

                // Calculate transactions within the period
                $transactionDebit = AccountTransaction::where('account_id', $account->id)
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->sum('debit');

                $transactionCredit = AccountTransaction::where('account_id', $account->id)
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->sum('credit');

                // Determine closing balances based on account type

                $closingDebit = $openingDebit + $transactionDebit - $transactionCredit;
                $closingCredit = $openingCredit + $transactionCredit - $transactionDebit;
                // Adjust closing balance logic based on account type

                if (getFirstAccount($account->id) == $parentIds['Bank']) {
                    $closingDebit = $closingDebit  ? $closingDebit : 0;
                    $closingCredit = $closingDebit == 0 ? abs($closingDebit) : 0;
                }

                // Only add the entry if it has a non-zero opening balance or transactions within the period
                $entry = [
                    'account_name' => $account->account_name,
                    'opening_debit' => $openingDebit,
                    'opening_credit' => $openingCredit,
                    'transaction_debit' => $transactionDebit,
                    'transaction_credit' => $transactionCredit,
                    'closing_debit' => $closingDebit,
                    'closing_credit' => $closingCredit,
                    'parent_id' => $account->parent_id,
                ];

                // Group accounts by parent ID
                if ($key == 'Bank') {
                    $groupedTrialBalance['Bank'][] = $entry;
                }
            }
        }

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.DbTrialbalance', get_defined_vars());
    }

    // public function incomestatement(Request $request)
    // {
    //     $title = 'Income Statement Report';
    //     $account = new  ChartOfAccount();

    //     $startDate = $request->input('from_date', date('Y-m-01')); // Default to the start of the current month
    //     $endDate = $request->input('to_date', date('Y-m-t')); // Default to the end of the current month

    //     // Calculate Revenue
    //     $revenue = DB::table('account_transactions')
    //         ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //         ->whereIn('chart_of_accounts.id', getAccountIdsToArray(getOldAccount([getAccountByUniqueID(25)->id]), 18))
    //         ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
    //         ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
    //         ->first();

    //     // Calculate COGS
    //     $cogs = DB::table('account_transactions')
    //         ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //         ->whereIn('chart_of_accounts.id', getOldAccount(0, 20)->pluck("id"))
    //         ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
    //         ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
    //         ->first();


    //     // Calculate Operating Expenses
    //     $operatingExpenses = DB::table('account_transactions')
    //         ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //         ->whereIn('chart_of_accounts.id', getOldAccount(0, 21)->pluck("id"))
    //         ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
    //         ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
    //         ->first();

    //     // Calculate Non-Operating Income
    //     $nonOperatingIncome = DB::table('account_transactions')
    //         ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //         ->whereIn('chart_of_accounts.id', getOldAccount(0, 25)->pluck("id"))
    //         ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
    //         ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
    //         ->first();

    //     // Prepare the income statement data
    //     $incomeStatement = [
    //         'Revenue' => ($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0),
    //         'COGS' => ($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0),
    //         'Gross Profit' => (($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0)) - (($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0)),
    //         'Operating Expenses' => ($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0),
    //         'Operating Income' => (($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0)) - (($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0)) - (($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0)),
    //         'Non-Operating Income' => ($nonOperatingIncome->total_credit ?? 0) - ($nonOperatingIncome->total_debit ?? 0),
    //         'Net Income' => (
    //             (($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0))
    //             - (($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0))
    //         ) - (($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0))
    //             + (($nonOperatingIncome->total_credit ?? 0) - ($nonOperatingIncome->total_debit ?? 0))
    //     ];

    //     $companyInfo = Company::latest('id')->first();

    //     return view('backend.pages.reports.incomestatement', get_defined_vars());
    // }

    // public function incomestatement(Request $request)
    // {
    //     $title = 'Income Statement Report';

    //     $startDate = $request->input('from_date', date('Y-m-01'));
    //     $endDate   = $request->input('to_date',   date('Y-m-t'));

    //     // Added: 2026-06-29 — সঠিক account ID দিয়ে data নেওয়া
    //     // Revenue = Sales (ID:18) + Direct Income (ID:19)
    //     $salesIds        = getOldAccount(0, 18)->pluck('id')->toArray();
    //     $directIncomeIds = getOldAccount(0, 19)->pluck('id')->toArray();
    //     $revenueIds      = array_merge($salesIds, $directIncomeIds);

    //     // COGS = Direct Expenses (ID:22) + Purchase (ID:24)
    //     $directExpenseIds = getOldAccount(0, 22)->pluck('id')->toArray();
    //     $purchaseIds      = getOldAccount(0, 24)->pluck('id')->toArray();
    //     $cogsIds          = array_merge($directExpenseIds, $purchaseIds);

    //     // Operating Expenses = Indirect Expenses (ID:23)
    //     $opexIds = getOldAccount(0, 23)->pluck('id')->toArray();

    //     // Non-Operating Income = Indirect Income (ID:20)
    //     $nonOpIncomeIds = getOldAccount(0, 20)->pluck('id')->toArray();

    //     // Added: 2026-06-29 — whereDate দিয়ে time issue fix, একসাথে query
    //     $revenue = AccountTransaction::whereIn('account_id', $revenueIds)
    //         ->whereDate('created_at', '>=', $startDate)
    //         ->whereDate('created_at', '<=', $endDate)
    //         ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
    //         ->first();

    //     $cogs = AccountTransaction::whereIn('account_id', $cogsIds)
    //         ->whereDate('created_at', '>=', $startDate)
    //         ->whereDate('created_at', '<=', $endDate)
    //         ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
    //         ->first();

    //     $operatingExpenses = AccountTransaction::whereIn('account_id', $opexIds)
    //         ->whereDate('created_at', '>=', $startDate)
    //         ->whereDate('created_at', '<=', $endDate)
    //         ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
    //         ->first();

    //     $nonOperatingIncome = AccountTransaction::whereIn('account_id', $nonOpIncomeIds)
    //         ->whereDate('created_at', '>=', $startDate)
    //         ->whereDate('created_at', '<=', $endDate)
    //         ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
    //         ->first();

    //     // Added: 2026-06-29 — সব calculation আলাদা variable এ রাখা
    //     $totalRevenue     = ($revenue->total_credit         ?? 0) - ($revenue->total_debit         ?? 0);
    //     $totalCOGS        = ($cogs->total_debit             ?? 0) - ($cogs->total_credit           ?? 0);
    //     $grossProfit      = $totalRevenue - $totalCOGS;
    //     $totalOpex        = ($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0);
    //     $operatingIncome  = $grossProfit - $totalOpex;
    //     $totalNonOpIncome = ($nonOperatingIncome->total_credit ?? 0) - ($nonOperatingIncome->total_debit ?? 0);
    //     $netIncome        = $operatingIncome + $totalNonOpIncome;

    //     $companyInfo = Company::latest('id')->first();

    //     return view('backend.pages.reports.incomestatement', get_defined_vars());
    // }
    public function incomestatement(Request $request)
    {
        $title = 'Income Statement Report';

        $startDate = $request->input('from_date', date('Y-m-01'));
        $endDate   = $request->input('to_date',   date('Y-m-t'));

        $salesIds        = getOldAccount(0, 18)->pluck('id')->toArray();
        $directIncomeIds = getOldAccount(0, 19)->pluck('id')->toArray();
        $revenueIds      = array_merge($salesIds, $directIncomeIds);

        $directExpenseIds = getOldAccount(0, 22)->pluck('id')->toArray();
        $purchaseIds      = getOldAccount(0, 24)->pluck('id')->toArray();
        $cogsIds          = array_merge($directExpenseIds, $purchaseIds);

        $opexIds = getOldAccount(0, 23)->pluck('id')->toArray();

        $nonOpIncomeIds = getOldAccount(0, 20)->pluck('id')->toArray();

        // $revenue = AccountTransaction::whereIn('account_id', $revenueIds)
        //     ->whereDate('created_at', '>=', $startDate)
        //     ->whereDate('created_at', '<=', $endDate)
        //     ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
        //     ->first();

        $revenue = AccountTransaction::whereIn('account_id', $revenueIds)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $cogs = AccountTransaction::whereIn('account_id', $cogsIds)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $operatingExpenses = AccountTransaction::whereIn('account_id', $opexIds)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $nonOperatingIncome = AccountTransaction::whereIn('account_id', $nonOpIncomeIds)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $totalRevenue = ($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0);
        // $totalRevenue     = ($revenue->total_credit         ?? 0) - ($revenue->total_debit         ?? 0);
        $totalCOGS        = ($cogs->total_debit             ?? 0) - ($cogs->total_credit           ?? 0);
        $grossProfit      = $totalRevenue - $totalCOGS;
        $totalOpex        = ($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0);
        $operatingIncome  = $grossProfit - $totalOpex;
        $totalNonOpIncome = ($nonOperatingIncome->total_credit ?? 0) - ($nonOperatingIncome->total_debit ?? 0);
        $netIncome        = $operatingIncome + $totalNonOpIncome;

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.incomestatement', get_defined_vars());
    }


    public function incomestatementDetails(Request $request)
    {
        $category  = $request->input('category');
        $startDate = $request->input('from_date');
        $endDate   = $request->input('to_date');

        switch ($category) {
            case 'revenue':
                $ids = array_merge(
                    getOldAccount(0, 18)->pluck('id')->toArray(),
                    getOldAccount(0, 19)->pluck('id')->toArray()
                );
                $natureCredit = true;
                break;
            case 'cogs':
                $ids = array_merge(
                    getOldAccount(0, 22)->pluck('id')->toArray(),
                    getOldAccount(0, 24)->pluck('id')->toArray()
                );
                $natureCredit = false;
                break;
            case 'opex':
                $ids = getOldAccount(0, 23)->pluck('id')->toArray();
                $natureCredit = false;
                break;
            case 'nonop':
                $ids = getOldAccount(0, 20)->pluck('id')->toArray();
                $natureCredit = true;
                break;
            default:
                return response('<p class="text-danger">Invalid category</p>', 400);
        }

        $transactions = AccountTransaction::with('account:id,account_name')
            ->whereIn('account_id', $ids)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'asc')
            ->get();

        $total = 0;
        $rows = '';

        if ($transactions->isEmpty()) {
            $rows = '<tr><td colspan="5" class="text-center text-muted">NO Record Found</td></tr>';
        } else {
            foreach ($transactions as $txn) {
                $value = $natureCredit
                    ? ($txn->credit - $txn->debit)
                    : ($txn->debit - $txn->credit);
                $total += $value;

                $date       = \Carbon\Carbon::parse($txn->created_at)->format('d-M-Y');
                $accName    = $txn->account->account_name ?? 'N/A';
                $voucherNo  = $txn->invoice ?? '-';
                $debit      = $txn->debit > 0  ? number_format($txn->debit, 2)  : '-';
                $credit     = $txn->credit > 0 ? number_format($txn->credit, 2) : '-';

                $rows .= "<tr>
                <td>{$date}</td>
                <td>{$accName}</td>
                <td>{$voucherNo}</td>
                <td class='text-right'>{$debit}</td>
                <td class='text-right'>{$credit}</td>
            </tr>";
            }
        }

        $totalFormatted = number_format(abs($total), 2);

        // Added: 2026-06-30 — total 
        $debitTotalCell  = $natureCredit ? "<td class='text-right'>-</td>" : "<td class='text-right'>{$totalFormatted}</td>";
        $creditTotalCell = $natureCredit ? "<td class='text-right'>{$totalFormatted}</td>" : "<td class='text-right'>-</td>";

        $html = "
    <table class='table table-bordered table-sm'>
        <thead>
            <tr style='background:#f4f4f4;'>
                <th>Date</th>
                <th>Account Name</th>
                <th>Voucher No</th>
                <th class='text-right'>Debit</th>
                <th class='text-right'>Credit</th>
            </tr>
        </thead>
        <tbody>{$rows}</tbody>
        <tfoot>
            <tr style='background:#e9ecef; font-weight:600;'>
                <td colspan='3'>Net Total</td>
                {$debitTotalCell}
                {$creditTotalCell}
            </tr>
        </tfoot>
    </table>";

        return response($html);
    }





    // function incomeDetails(Request $req)
    // {
    //     $sanitizedCategory = $req->input('category');
    //     $startDate = $req->from_date;
    //     $endDate = $req->to_date;
    //     if ($sanitizedCategory == "Revenue") {
    //         // Fetch transactions for the matched account IDs
    //         $transactions = DB::table('account_transactions')
    //             ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //             ->whereIn('chart_of_accounts.id', getAccountIdsToArray(getOldAccount([getAccountByUniqueID(25)->id]), 18))
    //             ->whereBetween('account_transactions.created_at', [$startDate, $endDate])

    //             ->get();
    //     } elseif ($sanitizedCategory == "COGS") {
    //         $transactions = DB::table('account_transactions')
    //             ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //             ->whereIn('chart_of_accounts.id', getOldAccount(0, 20)->pluck("id"))
    //             ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
    //             ->get();
    //     } elseif ($sanitizedCategory == "Operating_Expenses") {
    //         $transactions = DB::table('account_transactions')
    //             ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //             ->whereIn('chart_of_accounts.id', getOldAccount(0, 21)->pluck("id"))
    //             ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
    //             ->get();
    //     } elseif ($sanitizedCategory == "Non_Operating_Income") {
    //         $transactions = DB::table('account_transactions')
    //             ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
    //             ->whereIn('chart_of_accounts.id', getOldAccount(0, 25)->pluck("id"))
    //             ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
    //             ->get();
    //     }

    //     // Apply different calculations based on the category
    //     $totalDebit = $transactions->sum('debit');
    //     $totalCredit = $transactions->sum('credit');

    //     if (in_array($sanitizedCategory, ['Revenue', "Non_Operating_Income"])) {
    //         $amount = $totalCredit - $totalDebit;
    //     } elseif (in_array($sanitizedCategory, ['COGS'])) {
    //         $amount = $totalDebit - $totalCredit;
    //     } else {
    //         $amount = $totalDebit - $totalCredit;
    //     }

    //     // Return a view with the transactions data and calculated amount
    //     return view('backend/pages/reports/transaction-details', get_defined_vars());
    // }

    /* public function balancesheet(Request $request)
    {
        $title = 'Balance Sheet Report';
        $startDate = $request->from_date ?? date('Y-01-01');
        $endDate = $request->to_date ?? date('Y-m-d');

        // Get all accounts
        $accounts = DB::table('chart_of_accounts')->get();

        // Get all transactions within the date range
        $transactions = DB::table('account_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Initialize balance sheet data
        $balanceSheet = [
            'assets' => [],
            'liabilities' => [],
            'equity' => [],
            'total_assets' => 0,
            'total_liabilities' => 0,
            'total_equity' => 0,
        ];

        // Calculate balances
        foreach ($accounts as $account) {
            $accountTransactions = $transactions->where('account_id', $account->id);

            // Calculate the total debit and credit for each account
            $totalDebit = $accountTransactions->sum('debit');
            $totalCredit = $accountTransactions->sum('credit');

            // Calculate the ending balance
            $balance = $account->opening_balance + ($totalDebit - $totalCredit);

            // Categorize the accounts into Assets, Liabilities, and Equity
            if (in_array($account->unique_identifier, [1, 3, 4, 5, 6, 7, 8])) { // Assets identifiers
                $balanceSheet['assets'][] = [
                    'name' => $account->account_name,
                    'balance' => $balance,
                ];
                $balanceSheet['total_assets'] += $balance;
            } elseif (in_array($account->unique_identifier, [9, 10, 11, 12, 13, 14, 15, 16])) { // Liabilities identifiers
                $balanceSheet['liabilities'][] = [
                    'name' => $account->account_name,
                    'balance' => $balance,
                ];
                $balanceSheet['total_liabilities'] += $balance;
            } elseif (in_array($account->unique_identifier, [17, 18, 19, 20, 21, 22, 23, 24, 25])) { // Equity identifiers
                $balanceSheet['equity'][] = [
                    'name' => $account->account_name,
                    'balance' => $balance,
                ];
                $balanceSheet['total_equity'] += $balance;
            }
        }

        // Calculate total liabilities and equity
        $balanceSheet['total_liabilities_and_equity'] = $balanceSheet['total_liabilities'] + $balanceSheet['total_equity'];


        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.balancesheet', get_defined_vars());
    } */


    private $categoryAnchors = [
        1  => 'asset',
        9  => 'liability',
        10 => 'equity',
        14 => 'liability',
        15 => 'liability',
        17 => 'income',
        21 => 'expense',
    ];


    private function resolveAccountType($accountId, $accountsById)
    {
        $currentId = $accountId;
        $visited = [];

        while (isset($accountsById[$currentId])) {
            if (isset($this->categoryAnchors[$currentId])) {
                return $this->categoryAnchors[$currentId];
            }

            if (in_array($currentId, $visited)) {
                break; // circular reference guard
            }
            $visited[] = $currentId;

            $parentId = $accountsById[$currentId]->parent_id;
            if (!$parentId) {
                break;
            }
            $currentId = $parentId;
        }

        return null; // resolve করা গেলো না — manual review লাগবে
    }

    private function verifyBalanceSheet($balanceSheet, $unresolvedAccounts = [])
    {
        $difference = round($balanceSheet['total_assets'] - $balanceSheet['total_liabilities_and_equity'], 2);
        $isBalanced = abs($difference) < 0.01;

        $result = [
            'is_balanced'         => $isBalanced,
            'difference'          => $difference,
            'unresolved_accounts' => $unresolvedAccounts,
        ];

        if (!$isBalanced || !empty($unresolvedAccounts)) {
            $result['message'] = !$isBalanced
                ? 'Balance Sheet does NOT balance. Difference: ' . number_format($difference, 2)
                : 'Balance হচ্ছে, কিন্তু কিছু account category resolve হয়নি — চেক করো।';

            \Log::warning('Balance Sheet correction check', $result);
        } else {
            $result['message'] = 'Balance Sheet is balanced.';
        }

        return $result;
    }

    public function balancesheet(Request $request)
    {
        $title   = 'Balance Sheet Report';
        $asOfDate = $request->as_of_date ?? date('Y-m-d');


        $fiscalYearStart = date('Y', strtotime($asOfDate)) . '-01-01';
        // $accounts     = DB::table('chart_of_accounts')->whereNull('deleted_at')->get();
        $accounts = DB::table('chart_of_accounts')
            ->whereNull('deleted_at')
            ->select('*')
            ->get();

        $accountsById = $accounts->keyBy('id');


        $balanceSheetTotals = DB::table('account_transactions')
            ->whereDate('created_at', '<=', $asOfDate)
            ->select(
                'account_id',
                DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(credit), 0) as total_credit')
            )
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');


        $periodTotals = DB::table('account_transactions')
            ->whereDate('created_at', '>=', $fiscalYearStart)
            ->whereDate('created_at', '<=', $asOfDate)
            ->select(
                'account_id',
                DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(credit), 0) as total_credit')
            )
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $balanceSheet = [
            'current_assets'      => [],
            'fixed_assets'        => [],
            'advance_to_suppliers' => [],
            'current_liabilities' => [],
            'long_term_liabilities' => [],
            'equity'              => [],
            'advance_from_customers' => [],
            'total_current_assets'        => 0,
            'total_fixed_assets'          => 0,
            'total_advance_to_suppliers'  => 0,
            'total_assets'                => 0,
            'total_current_liabilities'   => 0,
            'total_long_term_liabilities' => 0,
            'total_advance_from_customers' => 0,
            'total_liabilities'           => 0,
            'total_equity'                => 0,
        ];

        $currentYearProfit  = 0;
        $priorYearAdjustment = 0;
        $unresolvedAccounts = [];
        $skipIds = [9, 25];

        foreach ($accounts as $account) {

            if (in_array($account->id, $skipIds)) {
                continue;
            }

            $accountType = $this->resolveAccountType($account->id, $accountsById);

            if ($accountType === null) {
                $unresolvedAccounts[] = $account->account_name . ' (id: ' . $account->id . ')';
                continue;
            }

            if (in_array($accountType, ['asset', 'liability', 'equity'])) {
                $totals      = $balanceSheetTotals->get($account->id);
                $totalDebit  = $totals ? (float)$totals->total_debit  : 0;
                $totalCredit = $totals ? (float)$totals->total_credit : 0;


                $openingBalance = (float)$account->opening_balance;
                if ($account->balance_type === 'credit') {
                    $openingBalance = -$openingBalance;
                }


                $runningBalance = $openingBalance + ($totalDebit - $totalCredit);


                if ($accountType === 'asset') {
                    $balance = $runningBalance; // Asset: debit = positive
                } else {
                    $balance = -$runningBalance; // Liability/Equity: credit = positive
                }

                if ($balance == 0) {
                    continue; // zero balance account skip
                }

                $row = [
                    'name'       => $account->account_name,
                    'balance'    => $balance,
                    'account_id' => $account->id,
                ];

                // Added: 2026-07-02 — Current Assets vs Fixed Assets 
                if ($accountType === 'asset') {

                    // $subType = $this->resolveAssetSubType($account->id, $accountsById);
                    // if ($subType === 'fixed') {
                    //     $balanceSheet['fixed_assets'][] = $row;
                    //     $balanceSheet['total_fixed_assets'] += $balance;
                    // } else {
                    //     $balanceSheet['current_assets'][] = $row;
                    //     $balanceSheet['total_current_assets'] += $balance;
                    // }
                    // $balanceSheet['total_assets'] += $balance;

                    $subType = $this->resolveAssetSubType($account->id, $accountsById);

                    $isReceivable = $this->isUnderAnchor($account->id, 5, $accountsById);
                    $isCustomerAccount = $account->accountable_type === 'App\\Models\\Customer';

                    if ($isReceivable && $balance < 0 && $isCustomerAccount) {
                        $advanceRow = [
                            'name'       => $row['name'],
                            'balance'    => abs($balance),
                            'account_id' => $row['account_id'],
                        ];
                        $balanceSheet['advance_from_customers'][] = $advanceRow;
                        $balanceSheet['total_advance_from_customers'] += abs($balance);
                        $balanceSheet['total_liabilities'] += abs($balance);
                        continue;
                    }

                    if ($subType === 'fixed') {
                        $balanceSheet['fixed_assets'][] = $row;
                        $balanceSheet['total_fixed_assets'] += $balance;
                    } else {
                        $balanceSheet['current_assets'][] = $row;
                        $balanceSheet['total_current_assets'] += $balance;
                    }
                    $balanceSheet['total_assets'] += $balance;
                } elseif ($accountType === 'liability') {

                    // $parentId = $account->parent_id;
                    // $isLongTerm = $this->isUnderAnchor($account->id, 14, $accountsById);
                    // if ($isLongTerm) {
                    //     $balanceSheet['long_term_liabilities'][] = $row;
                    //     $balanceSheet['total_long_term_liabilities'] += $balance;
                    // } else {
                    //     $balanceSheet['current_liabilities'][] = $row;
                    //     $balanceSheet['total_current_liabilities'] += $balance;
                    // }
                    // $balanceSheet['total_liabilities'] += $balance;

                    $isPayable = $this->isUnderAnchor($account->id, 16, $accountsById);
                    $isSupplierAccount = $account->accountable_type === 'App\\Models\\Supplier';

                    if ($isPayable && $balance < 0 && $isSupplierAccount) {
                        $advanceRow = [
                            'name'       => $row['name'],
                            'balance'    => abs($balance), // positive  asset
                            'account_id' => $row['account_id'],
                        ];
                        $balanceSheet['advance_to_suppliers'][] = $advanceRow;
                        $balanceSheet['total_advance_to_suppliers'] += abs($balance);
                        $balanceSheet['total_assets'] += abs($balance); // Total Assets-
                        continue; // 
                    }

                    // Added: 2026-07-02 — Current vs Long Term 
                    $parentId = $account->parent_id;
                    $isLongTerm = $this->isUnderAnchor($account->id, 14, $accountsById);
                    if ($isLongTerm) {
                        $balanceSheet['long_term_liabilities'][] = $row;
                        $balanceSheet['total_long_term_liabilities'] += $balance;
                    } else {
                        $balanceSheet['current_liabilities'][] = $row;
                        $balanceSheet['total_current_liabilities'] += $balance;
                    }
                    $balanceSheet['total_liabilities'] += $balance;
                } else {
                    $balanceSheet['equity'][] = $row;
                    $balanceSheet['total_equity'] += $balance;
                }
            } elseif (in_array($accountType, ['income', 'expense'])) {


                $allTimeTotals    = $balanceSheetTotals->get($account->id);
                $allTimeDebit     = $allTimeTotals ? (float)$allTimeTotals->total_debit  : 0;
                $allTimeCredit    = $allTimeTotals ? (float)$allTimeTotals->total_credit : 0;

                // শুধু এই fiscal year-এর total
                $periodTotal      = $periodTotals->get($account->id);
                $periodDebit      = $periodTotal ? (float)$periodTotal->total_debit  : 0;
                $periodCredit     = $periodTotal ? (float)$periodTotal->total_credit : 0;

                // Prior-year part = all-time - current-year
                $priorDebit  = $allTimeDebit  - $periodDebit;
                $priorCredit = $allTimeCredit - $periodCredit;

                $openingBalance = (float)$account->opening_balance;

                if ($accountType === 'income') {
                    // Current year profit-এ শুধু এই বছরের অংশ
                    $currentYearProfit += ($periodCredit - $periodDebit);

                    // Prior-year অংশ Retained Earnings adjustment-এ যাবে
                    $priorYearAdjustment += ($priorCredit - $priorDebit);

                    if ($account->balance_type === 'credit') {
                        $priorYearAdjustment += $openingBalance;
                    }
                } else {
                    $currentYearProfit -= ($periodDebit - $periodCredit);
                    $priorYearAdjustment -= ($priorDebit - $priorCredit);

                    if ($account->balance_type === 'debit') {
                        $priorYearAdjustment -= $openingBalance;
                    }
                }
            }
        }

        // Net Profit/Loss equity-তে যোগ করা — Added: 2026-07-02
        // Net Profit/Loss equity-তে যোগ করা — Added: 2026-07-02
        $balanceSheet['equity'][] = [
            'name'       => 'Current Year ' . ($currentYearProfit >= 0 ? 'Profit' : 'Loss'),
            'balance'    => $currentYearProfit,
            'account_id' => null,
        ];
        $balanceSheet['total_equity'] += $currentYearProfit;

        // Added: 2026-07-02 — Prior years-এর unclosed income/expense কে
        // Retained Earnings adjustment হিসেবে দেখানো হচ্ছে, যাতে balance sheet balance হয়
        // if (abs($priorYearAdjustment) > 0.01) {
        //     $balanceSheet['equity'][] = [
        //         'name'       => 'Retained Earnings Adjustment (Prior Years)',
        //         'balance'    => $priorYearAdjustment,
        //         'account_id' => null,
        //     ];
        //     $balanceSheet['total_equity'] += $priorYearAdjustment;
        // }

        $balanceSheet['total_liabilities_and_equity'] =
            $balanceSheet['total_liabilities'] + $balanceSheet['total_equity'];

        $balanceCheck = $this->verifyBalanceSheet($balanceSheet, $unresolvedAccounts);

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.balancesheet', compact(
            'title',
            'asOfDate',
            'balanceSheet',
            'companyInfo',
            'balanceCheck'
        ));
    }


    private function resolveAssetSubType($accountId, $accountsById)
    {

        return $this->isUnderAnchor($accountId, 2, $accountsById) ? 'fixed' : 'current';
    }


    private function isUnderAnchor($accountId, $anchorId, $accountsById)
    {
        $currentId = $accountId;
        $visited   = [];

        while (isset($accountsById[$currentId])) {
            if ($currentId == $anchorId) {
                return true;
            }
            if (in_array($currentId, $visited)) {
                break;
            }
            $visited[]  = $currentId;
            $currentId  = $accountsById[$currentId]->parent_id ?? null;
            if (!$currentId) break;
        }
        return false;
    }





    // public function stock(Request $request)
    // {

    //     $title = 'Stock Detail';

    //     $branch_id = '';
    //     $product_id = '';

    //     if ($request->method() == 'POST') {
    //         $StockDetails = '';
    //         $datas = explode('-', $request->dateRange);
    //         $from_date = date('Y-m-d', strtotime($datas[0]));
    //         $to_date = date('Y-m-d', strtotime($datas[1]));

    //         $branch_id = $request->branch_id;
    //         $product_id = $request->product_id;


    //         // dd($request->all());
    //         if ($product_id ==  '---Select Product---' || $product_id == null) {
    //             return Redirect::back()->withErrors(['msg' => 'Product Can not be empty!']);
    //         }

    //         // $currentSrock = StockSummary::orderBy('stock_summaries.id', 'desc')
    //         //     ->select('stock_summaries.*', 'stock_summaries.quantity as stock_qty', 'purchases_details.*', DB::raw("avg(purchases_details.unit_price) as avgPrice"))
    //         //     ->join('purchases_details', 'purchases_details.product_id', '=', 'stock_summaries.product_id')
    //         //     ->orderBy("stock_summaries.product_id", "ASC")
    //         //     ->groupBy('stock_summaries.product_id')
    //         //     ->get();

    //         $StockDetails = Stock::join('branches', 'stocks.branch_id', '=', 'branches.id')
    //             ->select('products.*', 'purchases_details.*', DB::raw("avg(purchases_details.unit_price) as avgPrice"))
    //             ->join('purchases_details', 'purchases_details.product_id', '=', 'stocks.product_id')
    //             ->join('products', 'stocks.product_id', '=', 'products.id')
    //             ->whereBetween('stocks.date', [$from_date, $to_date]);

    //         if ($branch_id != 'all') {
    //             $StockDetails =   $StockDetails->where('stocks.branch_id', $branch_id);
    //         }
    //         $StockDetails =   $StockDetails->where('stocks.product_id', $product_id);

    //         $StockDetails =  $StockDetails->get();
    //         // dd(count($StockDetails));
    //     }

    //     $companyInfo = Company::latest('id')->first();

    //     $branch = Branch::where('status', 'Active')->get();
    //     $accounts = ChartOfAccount::get()->where('status', 'Active');
    //     $category_info = Category::where('status', 'Active')->get();
    //     return view('backend.pages.reports.stock', get_defined_vars());
    // }

    public function stock(Request $request)
    {
        $title = 'Stock Details Report';

        $branch_id   = $request->branch_id ?? 'all';
        $product_id  = $request->product_id;
        $from_date   = null;
        $to_date     = null;

        // ==================== Date Range Processing (Fixed) ====================
        if ($request->filled('dateRange')) {
            $dateStr = trim($request->dateRange);
            // Remove extra spaces and split
            $dateStr = str_replace(' ', '', $dateStr);
            $datas   = explode('-', $dateStr);

            if (count($datas) == 2) {
                $from_date = date('Y-m-d', strtotime(trim($datas[0])));
                $to_date   = date('Y-m-d', strtotime(trim($datas[1])));
            }
        }

        $StockDetails = collect();

        if ($request->isMethod('POST')) {

            if (empty($product_id) || $product_id == '---Select Product---') {
                return redirect()->back()->withErrors(['msg' => 'Product is required!']);
            }

            // ==================== Main Query ====================
            $query = Stock::select(
                'stocks.*',
                'products.productCode',
                'products.name as product_name',
                'branches.branchCode',
                'branches.name as branch_name'
            )
                ->leftJoin('products', 'products.id', '=', 'stocks.product_id')
                ->leftJoin('branches', 'branches.id', '=', 'stocks.branch_id')
                ->where('stocks.product_id', $product_id)
                ->orderBy('stocks.date', 'asc')
                ->orderBy('stocks.id', 'asc');

            if ($branch_id != 'all') {
                $query->where('stocks.branch_id', $branch_id);
            }

            // ==================== Date Filter ====================
            if ($from_date && $to_date) {
                $query->whereBetween('stocks.date', [$from_date, $to_date]);
            }

            $StockDetails = $query->get();

            // ==================== Running Balance Calculation ====================
            $runningBalance = 0;
            foreach ($StockDetails as $item) {

                $positiveStatuses = [
                    'Opening',
                    'Purchase',
                    'Manual Purchase',
                    'Production',
                    'Gain',
                    'Loss',
                    'Transfer In',
                    'Project In',
                    'Return',
                    'Purchase Return'
                ];

                $isIn = in_array($item->status, $positiveStatuses);

                if ($isIn) {
                    $runningBalance += $item->quantity ?? 0;
                    $item->in_qty  = $item->quantity ?? 0;
                    $item->out_qty = 0;
                } else {
                    $runningBalance -= $item->quantity ?? 0;
                    $item->in_qty  = 0;
                    $item->out_qty = $item->quantity ?? 0;
                }

                $item->running_balance = $runningBalance;
            }
        }

        $companyInfo   = Company::latest('id')->first();
        $branch        = Branch::where('status', 'Active')->get();
        $category_info = Category::where('status', 'Active')->get();

        return view('backend.pages.reports.stock', get_defined_vars());
    }

    // public function stocksummery(Request $request)
    // {
    //     $title = 'Stock Summery';
    //     $branch_id = '';
    //     $product_id = '';
    //     $category_id = '';


    //     if ($request->method() == 'POST') {
    //         $StocksumDetails = '';

    //         $branch_id = $request->branch_id;
    //         $product_id = $request->product_id;
    //         $category_id = $request->category;


    //         $inPro = array('Purchase', 'Manual Purchase', 'Production', 'Gain', 'Transfer In', 'Project In', 'Return');
    //         $outPro = array('Production Sale', 'Production Out', 'Sale', 'Damage', 'Lost', 'Transfer Out', 'Project Out', 'Project Use');


    //         // $StocksumDetails = Stock::orderBy('stocks.product_id', 'asc')
    //         //     ->select('stocks.branch_id', 'stocks.product_id', 'stocks.status', 'branches.branchCode', 'branches.name as bname', 'categories.name as catname', 'products.name as proname', 'products.productCode', 'products.category_id')
    //         //     ->join('products', 'products.id', '=', 'stocks.product_id')
    //         //     ->join('categories', 'categories.id', '=', 'products.category_id')
    //         //     ->join('branches', 'branches.id', '=', 'stocks.branch_id')
    //         //     // ->wherein('status', $inPro)
    //         //     ->orderBy("stocks.product_id", "ASC")
    //         //     ->groupBy('stocks.product_id');

    //         $StocksumDetails = Stock::select(
    //             'stocks.product_id',
    //             'products.productCode',
    //             'products.name as proname',
    //             'categories.name as catname',
    //             'branches.branchCode',
    //             'branches.name as bname',

    //             DB::raw('SUM(CASE WHEN stocks.status IN ("Opening") THEN stocks.quantity ELSE 0 END) as opening_stock'),
    //             DB::raw('SUM(CASE WHEN stocks.status IN ("Purchase", "Manual Purchase", "Production", "Gain", "Transfer In", "Project In", "Return") THEN stocks.quantity ELSE 0 END) as total_in'),
    //             DB::raw('SUM(CASE WHEN stocks.status IN ("Production Sale", "Production Out", "Sale", "Damage", "Lost", "Transfer Out", "Project Out", "Project Use") THEN stocks.quantity ELSE 0 END) as total_out'),
    //             DB::raw('SUM(stocks.quantity) as current_stock'),
    //             DB::raw('AVG(stocks.unit_price) as avg_unit_price'),
    //             DB::raw('SUM(stocks.total_price) as total_value')
    //         )
    //             ->join('products', 'products.id', '=', 'stocks.product_id')
    //             ->join('categories', 'categories.id', '=', 'products.category_id')
    //             ->join('branches', 'branches.id', '=', 'stocks.branch_id')
    //             ->groupBy('stocks.product_id', 'products.productCode', 'products.name', 'categories.name', 'branches.branchCode', 'branches.name');

    //         if ($branch_id != 'all') {
    //             $StocksumDetails =   $StocksumDetails->where('stocks.branch_id', $branch_id);
    //         }
    //         if ($product_id != 'all') {
    //             $StocksumDetails =   $StocksumDetails->where('stocks.product_id', $product_id);
    //         }
    //         if ($category_id != 'all') {
    //             $StocksumDetails =   $StocksumDetails->where('products.category_id', $category_id);
    //         }

    //         $StocksumDetails =  $StocksumDetails->get();

    //         // echo '<pre>';
    //         // print_r($StocksumDetails->toArray());
    //         // die();

    //         // dd($StocksumDetails);


    //         // StockSummary::join('branches', 'stock_summaries.branch_id', '=', 'branches.id')
    //         //     ->select('products.*', 'branches.branchCode as brcode', 'branches.name as brname', 'stock_summaries.*', 'stock_summaries.quantity as stock_qty')
    //         //     // ->join('purchases_details', 'purchases_details.product_id', '=', 'stock_summaries.product_id')
    //         //     ->join('products', 'stock_summaries.product_id', '=', 'products.id');

    //         // if ($branch_id != 'all') {
    //         //     $StocksumDetails =   $StocksumDetails->where('stock_summaries.branch_id', $branch_id);
    //         // }
    //         // if ($product_id != 'all') {
    //         //     $StocksumDetails =   $StocksumDetails->where('stock_summaries.branch_id', $branch_id);
    //         // }
    //         // if ($category_id != 'all') {
    //         //     $StocksumDetails =   $StocksumDetails->where('stock_summaries.branch_id', $branch_id);
    //         // }


    //         // $StocksumDetails =   $StocksumDetails->where('stock_summaries.product_id', $product_id);

    //         // $StocksumDetails =  $StocksumDetails->get();
    //         // dd($StocksumDetails);
    //     }

    //     $companyInfo = Company::latest('id')->first();

    //     $branch = Branch::where('status', 'Active')->get();
    //     $accounts = ChartOfAccount::get()->where('status', 'Active');
    //     $category_info = Category::where('status', 'Active')->get();
    //     return view('backend.pages.reports.stocksummery', get_defined_vars());
    // }

    // public function stocksummery(Request $request)
    // {
    //     $title = 'Stock Summery';

    //     // ফিল্টারের জন্য ভেরিয়েবল
    //     $branch_id   = $request->branch_id;
    //     $product_id  = $request->product_id;
    //     $category_id = $request->category;

    //     if ($request->method() == 'POST') {

    //         // ==================== Main Query ====================
    //         $StocksumDetails = Stock::select(
    //             'stocks.product_id',
    //             'products.productCode',
    //             'products.name as proname',
    //             'categories.name as catname',
    //             'branches.branchCode',
    //             'branches.name as bname',

    //             // Opening Stock
    //             DB::raw('SUM(CASE WHEN stocks.status IN ("Opening") THEN stocks.quantity ELSE 0 END) as opening_stock'),

    //             // Stock In
    //             DB::raw('SUM(CASE WHEN stocks.status IN ("Purchase", "Manual Purchase", "Production", "Gain", "Transfer In", "Project In", "Return") 
    //                  THEN stocks.quantity ELSE 0 END) as total_in'),

    //             // Stock Out
    //             DB::raw('SUM(CASE WHEN stocks.status IN ("Production Sale", "Production Out", "Sale", "Damage", "Lost", "Transfer Out", "Project Out", "Project Use") 
    //                  THEN stocks.quantity ELSE 0 END) as total_out'),

    //             // Current Stock
    //             DB::raw('SUM(stocks.quantity) as current_stock'),

    //             DB::raw('AVG(stocks.unit_price) as avg_unit_price'),
    //             DB::raw('SUM(stocks.total_price) as total_value')
    //         )
    //             ->join('products', 'products.id', '=', 'stocks.product_id')
    //             ->join('categories', 'categories.id', '=', 'products.category_id')
    //             ->join('branches', 'branches.id', '=', 'stocks.branch_id')
    //             ->groupBy(
    //                 'stocks.product_id',
    //                 'products.productCode',
    //                 'products.name',
    //                 'categories.name',
    //                 'branches.branchCode',
    //                 'branches.name'
    //             );

    //         // ==================== Filters ====================
    //         if ($branch_id != 'all') {
    //             $StocksumDetails = $StocksumDetails->where('stocks.branch_id', $branch_id);
    //         }

    //         if ($product_id != 'all') {
    //             $StocksumDetails = $StocksumDetails->where('stocks.product_id', $product_id);
    //         }

    //         if ($category_id != 'all') {
    //             $StocksumDetails = $StocksumDetails->where('products.category_id', $category_id);
    //         }

    //         $StocksumDetails = $StocksumDetails->get();
    //     }

    //     // View এর জন্য ডাটা
    //     $companyInfo   = Company::latest('id')->first();
    //     $branch        = Branch::where('status', 'Active')->get();
    //     $category_info = Category::where('status', 'Active')->get();

    //     return view('backend.pages.reports.stocksummery', get_defined_vars());
    // }

    public function stocksummery(Request $request)
    {
        $title = 'Stock Summary Report';

        $branch_id   = $request->branch_id ?? 'all';
        $product_id  = $request->product_id ?? 'all';
        $category_id = $request->category ?? 'all';

        $StocksumDetails = collect();

        if ($request->isMethod('POST')) {


            $StocksumDetails = Stock::select(
                'stocks.product_id',
                'products.productCode',
                'products.name as proname',
                'categories.name as catname',
                'branches.branchCode',
                'branches.name as bname',

                DB::raw('SUM(CASE WHEN stocks.status = "Opening" THEN stocks.quantity ELSE 0 END) as opening_stock'),

                DB::raw('SUM(CASE WHEN stocks.status IN ("Purchase", "Manual Purchase", "Production", "Gain", "Transfer In", "Project In", "Return", "Purchase Return") 
             THEN stocks.quantity ELSE 0 END) as total_in'),

                DB::raw('SUM(CASE WHEN stocks.status IN ("Production Sale", "Production Out", "Sale", "Damage", "Lost", "Transfer Out", "Project Out", "Project Use", "Sale Return") 
             THEN stocks.quantity ELSE 0 END) as total_out'),


                DB::raw('
        SUM(
            CASE 
                WHEN stocks.status = "Opening" THEN stocks.quantity 
                WHEN stocks.status IN ("Purchase", "Manual Purchase", "Production", "Gain", "Transfer In", "Project In", "Return", "Purchase Return") THEN stocks.quantity 
                WHEN stocks.status IN ("Production Sale", "Production Out", "Sale", "Damage", "Lost", "Transfer Out", "Project Out", "Project Use", "Sale Return") THEN -stocks.quantity 
                ELSE 0 
            END
        ) as current_stock
    '),


                // DB::raw('AVG(CASE WHEN stocks.status IN ("Purchase", "Opening", "Manual Purchase") THEN stocks.unit_price END) as avg_unit_price'),
                DB::raw('
ROUND(
    SUM(
        CASE
            WHEN stocks.status IN ("Opening","Purchase","Manual Purchase")
            THEN stocks.unit_price * stocks.quantity
            ELSE 0
        END
    )
    /
    NULLIF(
        SUM(
            CASE
                WHEN stocks.status IN ("Opening","Purchase","Manual Purchase")
                THEN stocks.quantity
                ELSE 0
            END
        ),
        0
    ),
2) as avg_unit_price
'),

                DB::raw('
                ROUND(
                (
                    SUM(
                        CASE
                            WHEN stocks.status = "Opening" THEN stocks.quantity
                            WHEN stocks.status IN (
                                "Purchase",
                                "Manual Purchase",
                                "Production",
                                "Gain",
                                "Transfer In",
                                "Project In",
                                "Return",
                                "Purchase Return"
                            ) THEN stocks.quantity

                            WHEN stocks.status IN (
                                "Production Sale",
                                "Production Out",
                                "Sale",
                                "Damage",
                                "Lost",
                                "Transfer Out",
                                "Project Out",
                                "Project Use",
                                "Sale Return"
                            ) THEN -stocks.quantity

                            ELSE 0
                        END
                    )
                )
                *
                (
                    SUM(
                        CASE
                            WHEN stocks.status IN (
                                "Opening",
                                "Purchase",
                                "Manual Purchase"
                            )
                            THEN stocks.unit_price * stocks.quantity
                            ELSE 0
                        END
                    )
                    /
                    NULLIF(
                        SUM(
                            CASE
                                WHEN stocks.status IN (
                                    "Opening",
                                    "Purchase",
                                    "Manual Purchase"
                                )
                                THEN stocks.quantity
                                ELSE 0
                            END
                        ),
                        0
                    )
                ),
                2) as total_value
            ')
            )
                ->join('products', 'products.id', '=', 'stocks.product_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('branches', 'branches.id', '=', 'stocks.branch_id')
                ->groupBy('stocks.product_id', 'products.productCode', 'products.name', 'categories.name', 'branches.branchCode', 'branches.name');

            // Filters
            if ($branch_id != 'all') {
                $StocksumDetails->where('stocks.branch_id', $branch_id);
            }
            if ($product_id != 'all') {
                $StocksumDetails->where('stocks.product_id', $product_id);
            }
            if ($category_id != 'all') {
                $StocksumDetails->where('products.category_id', $category_id);
            }

            $StocksumDetails = $StocksumDetails->get();
        }

        $companyInfo   = Company::latest('id')->first();
        $branch        = Branch::where('status', 'Active')->get();
        $category_info = Category::where('status', 'Active')->get();

        return view('backend.pages.reports.stocksummery', get_defined_vars());
    }

    public function purchasereq(Request $request)
    {
        $title = 'Purchase Requisition Report';

        $branch_id = '';
        $product_id = '';

        if ($request->method() == 'POST') {
            $requisitionDetails = '';

            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;

            $requisitionDetails = PurchaseRequisition::join('pr_details', 'purchase_requisitions.id', '=', 'pr_details.pr_id')
                ->join('products', 'pr_details.product_id', '=', 'products.id')
                ->join('branches', 'purchase_requisitions.branch_id', '=', 'branches.id')
                ->whereBetween('purchase_requisitions.date', [$from_date, $to_date]);

            if ($branch_id != 'all') {
                $requisitionDetails =   $requisitionDetails->where('purchase_requisitions.branch_id', $branch_id);
            }
            $requisitionDetails =   $requisitionDetails->where('pr_details.product_id', $product_id);

            $requisitionDetails =  $requisitionDetails->get([
                'pr_details.*',
                'branches.branchCode as brcode',
                'branches.branchCode as brname',
                'products.name as prname',
                'products.productCode as productCode',
                'purchase_requisitions.date as prdate'
            ]);
            // dd($requisitionDetails);
        }

        $companyInfo = Company::latest('id')->first();
        $branch = Branch::where('status', 'Active')->get();
        $accounts = ChartOfAccount::get()->where('status', 'Active');
        $category_info = Category::where('status', 'Active')->get();
        return view('backend.pages.reports.purrequisition', get_defined_vars());
    }

    public function purchaseorder(Request $request)
    {
        $title = 'Purchase Order Report';

        $branch_id = '';
        $product_id = '';

        if ($request->method() == 'POST') {
            $orderDetails = '';

            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;

            $orderDetails = PurchaseOrder::join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
                ->join('products', 'purchase_order_details.product_id', '=', 'products.id')
                ->join('branches', 'purchase_orders.branch_id', '=', 'branches.id')
                ->whereBetween('purchase_orders.order_date', [$from_date, $to_date]);

            if ($branch_id != 'all') {
                $orderDetails =   $orderDetails->where('purchase_orders.branch_id', $branch_id);
            }
            $orderDetails =   $orderDetails->where('purchase_order_details.product_id', $product_id);

            $orderDetails =  $orderDetails->get([
                'purchase_order_details.*',
                'branches.branchCode as brcode',
                'branches.branchCode as brname',
                'products.name as prname',
                'products.productCode as productCode',
                'purchase_orders.order_date as order_date'
            ]);
            // dd($orderDetails);
        }

        $companyInfo = Company::latest('id')->first();
        $branch = Branch::where('status', 'Active')->get();
        $accounts = ChartOfAccount::get()->where('status', 'Active');
        $category_info = Category::where('status', 'Active')->get();
        return view('backend.pages.reports.purorder', get_defined_vars());
    }

    public function goodrcvnote(Request $request)
    {
        $title = 'Good Recive Note Report';

        $branch_id = '';
        $product_id = '';

        if ($request->method() == 'POST') {
            $grnDetails = '';

            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;

            $grnDetails = Grn::join('grn_details', 'grns.id', '=', 'grn_details.good_rcv_note_id')
                ->join('products', 'grn_details.product_id', '=', 'products.id')
                ->join('branches', 'grns.branch_id', '=', 'branches.id')
                ->whereBetween('grns.date', [$from_date, $to_date]);

            if ($branch_id != 'all') {
                $grnDetails =   $grnDetails->where('grns.branch_id', $branch_id);
            }
            $grnDetails =   $grnDetails->where('grn_details.product_id', $product_id);

            $grnDetails =  $grnDetails->get([
                'grn_details.*',
                'branches.branchCode as brcode',
                'branches.name as brname',
                'products.name as prname',
                'products.productCode as productCode',
                'grns.date as order_date'
            ]);
            // dd($grnDetails);
        }

        $companyInfo = Company::latest('id')->first();
        $branch = Branch::where('status', 'Active')->get();
        $accounts = ChartOfAccount::get()->where('status', 'Active');
        $category_info = Category::where('status', 'Active')->get();
        return view('backend.pages.reports.goodrcvnote', get_defined_vars());
    }

    public function production(Request $request)
    {
        $title = 'Production Report';
        $branch_id = '';
        $product_id = '';
        $category_id = '';

        if ($request->method() == 'POST') {
            $productionDetails = '';

            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;
            $category_id = $request->category_id;

            $productionDetails = Production::join('products', 'productions.product_id', '=', 'products.id')
                ->join('branches', 'productions.branch_id', '=', 'branches.id')
                ->whereBetween('productions.date', [$from_date, $to_date]);

            if ($product_id != 'all') {
                $productionDetails =   $productionDetails->where('productions.product_id', $product_id);
            }
            if ($branch_id != 'all') {
                $productionDetails =   $productionDetails->where('productions.branch_id', $branch_id);
            }

            $productionDetails =  $productionDetails->get([
                'branches.branchCode as bCode',
                'branches.name as bName',
                'products.name as pName',
                'products.productCode as pCode',
                'productions.date as productionDate'
            ]);

            // pops($productionDetails);
        }
        $product = Product::get()->where('status', 'Active');
        $companyInfo = Company::latest('id')->first();
        $branch = Branch::where('status', 'Active')->get();
        $category_info = Category::where('status', 'Active')->get();
        return view('backend.pages.reports.production', get_defined_vars());
    }

    // public function productledger(Request $request)
    // {

    //     $title = 'Product Ledger';
    //     $branch_id = '';
    //     $product_id = '';
    //     $datas = [];
    //     $from_date = null;
    //     $to_date = null;

    //     if ($request->isMethod('POST')) {
    //         $dates = explode('-', $request->dateRange);
    //         $from_date = date('Y-m-d');
    //         $to_date = date('Y-m-d');

    //         $branch_id = $request->branch_id;
    //         $product_id = $request->product_id;

    //         // Fetch the product ledger data
    //         $datas = $this->getProductLedgerData($product_id, $branch_id, $from_date, $to_date);
    //     }
    //     $products = Product::where('status', 'Active')->get();
    //     $companyInfo = Company::latest('id')->first();
    //     $branches = Branch::where('status', 'Active')->get();

    //     return view('backend.pages.reports.productledger', compact(
    //         'title',
    //         'branch_id',
    //         'product_id',
    //         'datas',
    //         'from_date',
    //         'request',
    //         'to_date',
    //         'products',
    //         'companyInfo',
    //         'branches'
    //     ));
    // }

    public function productledger(Request $request)
    {
        $title      = 'Product Ledger';
        $branch_id  = $request->branch_id ?? 'all';
        $product_id = $request->product_id ?? null;
        $from_date  = $request->from_date ?? date('Y-01-01');
        $to_date    = $request->to_date   ?? date('Y-m-d');
        $datas      = [];

        if ($request->isMethod('POST') && $product_id) {
            $datas = $this->getProductLedgerData($product_id, $branch_id, $from_date, $to_date);
        }

        $products    = Product::where('status', 'Active')->orderBy('name')->get(['id', 'productCode', 'name']);
        $companyInfo = Company::latest('id')->first();
        $branches    = Branch::where('status', 'Active')->orderBy('name')->get(['id', 'branchCode', 'name']);

        return view('backend.pages.reports.productledger', compact(
            'title',
            'branch_id',
            'product_id',
            'datas',
            'from_date',
            'to_date',
            'products',
            'companyInfo',
            'branches',
            'request'
        ));
    }

    public function product_update(Request $request)
    {
        $error_mess = null;
        if (empty($request->branch_id) || $request->branch_id == "all") {
            $error_mess .= "Please Select a specific Branch";
        }

        if ((empty($request->branch_id) || $request->branch_id == "all") && empty($request->product_id)) {
            $error_mess .= " & ";
        }

        if (empty($request->product_id)) {
            $error_mess .= "Please Select a specific Product";
        }

        if ($error_mess) {
            session()->flash('error', $error_mess);
            return redirect()->back();
        }

        //
        StockSummary::where('product_id', $request->product_id)->where('branch_id', $request->branch_id)->where('purchasetype', $request->type)->where('type', "Branch")->update(array('quantity' => $request->qty));

        session()->flash('success', "Stock Adjust Successfully");
        return redirect()->back();
    }

    // private function getProductLedgerData($product_id, $branch_id, $from_date, $to_date)
    // {
    //     $ledgerData = [];

    //     // Fetch stock-in details (Opening Stock + Purchases)
    //     $stockIn = ProductOpeningStockDetails::where('product_id', $product_id)
    //         ->when($branch_id !== 'all', function ($query) use ($branch_id) {
    //             return $query->where('branch_id', $branch_id);
    //         })
    //         ->get()
    //         ->map(function ($item) {
    //             $item->type = 'Opening Stock'; // Add dynamic type
    //             $item->invoice = $item->ProductOpeningStock->invoice_no ?? ""; // Add dynamic type
    //             return $item;
    //         })
    //         ->merge(
    //             PurchasesDetails::where('product_id', $product_id)
    //                 ->when($branch_id !== 'all', function ($query) use ($branch_id) {
    //                     return $query->where('branch_id', $branch_id);
    //                 })
    //                 ->get()
    //                 ->map(function ($item) {
    //                     $item->type = 'Purchase'; // Add dynamic type
    //                     $item->invoice = $item->purchase->invoice_no ?? ""; // Add dynamic type
    //                     return $item;
    //                 })
    //         );

    //     // Fetch stock-out details (Sales + Project Transfers)
    //     $stockOut = sales_Details::where('product_id', $product_id)
    //         ->when($branch_id !== 'all', function ($query) use ($branch_id) {
    //             return $query->where('branch_id', $branch_id);
    //         })
    //         ->get()
    //         ->map(function ($item) {
    //             $item->type = 'Sales'; // Add dynamic type
    //             $item->quantity = $item->qty; // Add dynamic type
    //             $item->invoice = $item->sales->invoice_no ?? ""; // Add dynamic type
    //             return $item;
    //         })
    //         ->merge(
    //             ProjectTransferDetails::where('product_id', $product_id)
    //                 ->when($branch_id !== 'all', function ($query) use ($branch_id) {
    //                     return $query->where('branch_id', $branch_id);
    //                 })
    //                 ->get()
    //                 ->map(function ($item) {
    //                     $item->type = 'Project Transfer'; // Add dynamic type
    //                     $item->invoice = $item->project_transfer->invoice_no ?? ""; // Add dynamic type
    //                     return $item;
    //                 })
    //         );

    //     // Combine and sort data
    //     $allData = $stockIn->merge($stockOut)->sortBy('date');
    //     $remainingStock = 0;
    //     $ledgerData = [];
    //     foreach ($allData as $key => $entry) {
    //         $in = in_array($entry->type, ['Opening Stock', 'Purchase']) ? $entry->quantity : 0;
    //         $out = in_array($entry->type, ['Sales', 'Project Transfer']) ? $entry->quantity : 0;
    //         $remainingStock += ($in - $out);

    //         $ledgerData[] = [
    //             'sl' => $key + 1,
    //             'date' => $entry->date,
    //             'invoice' => $entry->invoice ?? "",
    //             'branch' => $entry->branch->name ?? 'N/A',
    //             'product' => $entry->product->name ?? 'N/A',
    //             'status' => $entry->type,
    //             'in' => $in,
    //             'out' => $out,
    //             'remaining' => $remainingStock,
    //         ];
    //     }
    //     return $ledgerData;
    // }



    private function getProductLedgerData($product_id, $branch_id, $from_date, $to_date): array
    {
        $isAllBranch = ($branch_id === 'all' || empty($branch_id));

        // ── 1. Opening Stock ──────────────────────────────────────────────
        $openingRows = ProductOpeningStockDetails::with(['branch:id,name', 'product:id,name', 'ProductOpeningStock:id,invoice_no'])
            ->where('product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date ?? '0000-00-00',
                'invoice'    => $item->ProductOpeningStock->invoice_no ?? '—',
                'branch'     => $item->branch->name ?? 'N/A',
                'product'    => $item->product->name ?? 'N/A',
                'type'       => 'Opening Stock',
                'quantity'   => (int) $item->quantity,
                'in'         => (int) $item->quantity,
                'out'        => 0,
                'sort_key'   => '0',
                'created_at' => $item->created_at,
            ]);

        // ── 2. Purchases ──────────────────────────────────────────────────
        $purchaseRowsRaw = PurchasesDetails::with([
            'branch:id,name',
            'product:id,name',
            'purchase:id,invoice_no,type,purchase_type,project_id',
            'purchase.project:id,name',
        ])
            ->where('product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
            ->whereBetween('date', [$from_date, $to_date])
            ->get();

        $purchaseRows = collect();

        foreach ($purchaseRowsRaw as $item) {
            $isProjectManual = (
                optional($item->purchase)->type === 'Project' &&
                optional($item->purchase)->purchase_type === 'Manual'
            );

            // Stock IN — 
            $purchaseRows->push([
                'date'       => $item->date,
                'invoice'    => $item->purchase->invoice_no ?? '—',
                'branch'     => $item->branch->name
                    ?? optional($item->purchase?->project)->name
                    ?? 'N/A',
                'product'    => $item->product->name ?? 'N/A',
                'type'       => 'Purchase (' . ucfirst($item->purchasetype) . ')',
                'quantity'   => (int) $item->quantity,
                'in'         => (int) $item->quantity,
                'out'        => 0,
                'sort_key'   => '1',
                'created_at' => $item->created_at,
            ]);

            // Stock OUT — 
            if ($isProjectManual) {
                $purchaseRows->push([
                    'date'       => $item->date,
                    'invoice'    => $item->purchase->invoice_no ?? '—',
                    'branch'     => $item->branch->name
                        ?? optional($item->purchase?->project)->name
                        ?? 'N/A',
                    'product'    => $item->product->name ?? 'N/A',
                    'type'       => 'Project Consume (Manual)',
                    'quantity'   => (int) $item->quantity,
                    'in'         => 0,
                    'out'        => (int) $item->quantity,
                    'sort_key'   => '1',
                    'created_at' => $item->created_at,
                ]);
            }
        }

        // ── 3. Stock Adjustments ──────────────────────────────────────────
        $adjustRows = DB::table('stock_ajdustment_detailsts as sad')
            ->join('stock_ajdustments as sa', 'sa.id', '=', 'sad.purchases_id')
            ->leftJoin('branches as b', 'b.id', '=', 'sad.branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'sad.product_id')
            ->where('sad.product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('sad.branch_id', $branch_id))
            ->whereNotNull('sad.date')
            ->where('sad.date', '>=', $from_date)
            ->where('sad.date', '<=', $to_date)
            ->select(
                'sad.id',
                'sad.date',
                'sad.quantity',
                'sad.purchases_id',
                'sad.status',
                'sad.created_at',
                'sa.invoice_no',
                'sa.adjustment_type',
                'sa.note',
                'b.name as branch_name',
                'p.name as product_name'
            )
            ->orderBy('sad.created_at')
            ->get()
            ->map(function ($item) {
                $isGain = $item->adjustment_type === 'Gain';
                $qty    = abs((int) $item->quantity);
                $label  = match ($item->adjustment_type) {
                    'Gain'   => 'Adjustment (Gain)',
                    'Loss'   => 'Adjustment (Loss)',
                    'Damage' => 'Adjustment (Damage)',
                    'Others' => 'Adjustment (Others)',
                    default  => 'Adjustment',
                };
                return [
                    'date'       => $item->date,
                    'invoice'    => $item->invoice_no ?? ('ADJ-' . $item->purchases_id),
                    'branch'     => $item->branch_name  ?? 'N/A',
                    'product'    => $item->product_name ?? 'N/A',
                    'type'       => $label,
                    'quantity'   => $qty,
                    'in'         => $isGain ? $qty : 0,
                    'out'        => $isGain ? 0 : $qty,
                    'sort_key'   => '2',
                    'created_at' => $item->created_at,
                ];
            });

        // ── 4. Transfer In ────────────────────────────────────────────────
        $transferInRows = DB::table('transfer_details as td')
            ->leftJoin('branches as b', 'b.id', '=', 'td.to_branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'td.product_id')
            ->where('td.product_id', $product_id)
            ->where('td.status', 'Approved')
            ->when(!$isAllBranch, fn($q) => $q->where('td.to_branch_id', $branch_id))
            ->whereNull('td.deleted_at')
            ->whereBetween('td.date', [$from_date, $to_date])
            ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'b.name as branch_name', 'p.name as product_name')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => 'TR-' . $item->transfer_id,
                'branch'     => $item->branch_name ?? 'N/A',
                'product'    => $item->product_name ?? 'N/A',
                'type'       => 'Transfer In',
                'quantity'   => (int) $item->approve_qty,
                'in'         => (int) $item->approve_qty,
                'out'        => 0,
                'sort_key'   => '3',
                'created_at' => $item->created_at,
            ]);

        // ── 5. Transfer Out ───────────────────────────────────────────────
        $transferOutRows = DB::table('transfer_details as td')
            ->leftJoin('branches as b', 'b.id', '=', 'td.from_branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'td.product_id')
            ->where('td.product_id', $product_id)
            ->where('td.status', 'Approved')
            ->when(!$isAllBranch, fn($q) => $q->where('td.from_branch_id', $branch_id))
            ->whereNull('td.deleted_at')
            ->whereBetween('td.date', [$from_date, $to_date])
            ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'b.name as branch_name', 'p.name as product_name')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => 'TR-' . $item->transfer_id,
                'branch'     => $item->branch_name ?? 'N/A',
                'product'    => $item->product_name ?? 'N/A',
                'type'       => 'Transfer Out',
                'quantity'   => (int) $item->approve_qty,
                'in'         => 0,
                'out'        => (int) $item->approve_qty,
                'sort_key'   => '4',
                'created_at' => $item->created_at,
            ]);

        // ── 6. Sales ──────────────────────────────────────────────────────
        $salesRows = sales_Details::with(['branch:id,name', 'product:id,name', 'sales:id,invoice_no'])
            ->where('product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
            ->whereBetween('date', [$from_date, $to_date])
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => $item->sales->invoice_no ?? '—',
                'branch'     => $item->branch->name ?? 'N/A',
                'product'    => $item->product->name ?? 'N/A',
                'type'       => 'Sale',
                'quantity'   => (int) $item->qty,
                'in'         => 0,
                'out'        => (int) $item->qty,
                'sort_key'   => '5',
                'created_at' => $item->created_at,
            ]);

        // ── Merge + Sort ──────────────────────────────────────────────────
        $allRows = collect()
            ->merge($openingRows)
            ->merge($purchaseRows)
            ->merge($adjustRows)
            ->merge($transferInRows)
            ->merge($transferOutRows)
            ->merge($salesRows)
            ->sortBy('created_at')
            ->values();

        // ── Running balance ───────────────────────────────────────────────
        $remaining = 0;
        return $allRows->map(function ($row, $index) use (&$remaining) {
            $remaining += ($row['in'] - $row['out']);
            return array_merge($row, [
                'sl'        => $index + 1,
                'remaining' => $remaining,
            ]);
        })->toArray();
    }


    public function lowstocks(Request $request)
    {
        $title = 'Low Stock Report';


        $StockDetails = '';

        $StockDetails = StockSummary::join('products', 'stock_summaries.product_id', '=', 'products.id')
            ->join('branches', 'stock_summaries.branch_id', '=', 'branches.id')
            ->get(['products.name as pname', 'products.productCode as pcode', 'products.low_stock', 'stock_summaries.quantity', 'branches.name as bname', 'branches.branchCode as bcode']);
        // pops($StockDetails);

        $category_info = Category::where('status', 'Active')->get();
        return view('backend.pages.reports.lowstocks', get_defined_vars());
    }

    function voucher($invoice)
    {
        $account_transactions = AccountTransaction::where('invoice', $invoice)->get();
        $title = "Debit Voucher";

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.reports.voucher', get_defined_vars());
    }
}
