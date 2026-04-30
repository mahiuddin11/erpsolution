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

    public function ledger(Request $request)
    {
        $title = 'Ledger Report';

        $accounts = ChartOfAccount::where("parent_id", 0)->get();
        $companyInfo = Company::latest('id')->first();


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

            // Adjust opening balance
            if ($account->balance_type === 'debit') {
                $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
            } else {
                $openingBalance = $account->opening_balance + $creditSumBeforeStartDate - $debitSumBeforeStartDate;
            }

            $runningBalance = $openingBalance;
            $totalDebit = 0;
            $totalCredit = 0;
            $ledgerEntries = [];

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
                $invoice = $transaction->invoice;
                if ($transaction->type == "purchase") {
                    $item = DB::table("purchases")->find($transaction->table_id);
                    $invoice = $item->invoice_no ?? "";
                }


                $relatedAccountTransaction = AccountTransaction::where('invoice', $transaction->invoice)
                    ->where('account_id', '!=', $selectedAccountId);

                if ($transaction->debit) {

                    $relatedAccountTransaction = $relatedAccountTransaction->whereNotNull('credit');
                }
                if ($transaction->credit) {
                    $relatedAccountTransaction = $relatedAccountTransaction->whereNotNull('debit');
                }
                $relatedAccountTransaction = $relatedAccountTransaction->first();


                $debit = $transaction->debit ?? 0;
                $credit = $transaction->credit ?? 0;

                $totalDebit += $debit;
                $totalCredit += $credit;

                if ($account->balance_type == "debit") {
                    $runningBalance += $debit - $credit;
                } else {
                    $runningBalance += $credit -  $debit;
                }

                $ledgerEntries[] = [
                    'date' => $transaction->created_at,
                    'invoice' => $invoice,
                    'description' => $transaction->remark,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $runningBalance,
                    'account_name' => (($relatedAccountTransaction->account->account_name ?? '') . " " . ($relatedAccountTransaction->account->bank_name ?? "") . ' ' . ($relatedAccountTransaction->account->account_code ?? ""))
                ];
            }

            // Add subtotals if needed
            $ledgerSummary = [
                'opening_balance' => $openingBalance,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
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

    public function trialbalance(Request $request)
    {
        $title = 'Trial Balance Report';

        $startDate = $request->input('start_date') ?? date("Y-m-d");
        $endDate = $request->input('end_date') ?? date("Y-m-d");

        // Fetch names for main heads from the database
        $parentAccounts = ChartOfAccount::whereIn('id', [1, 9, 17, 19])->get();
        $parentIds = [
            'Asset' => getAccountByUniqueID(1)->id,
            'Liabilities' => getAccountByUniqueID(9)->id,
            'Income' => getAccountByUniqueID(17)->id,
            'Expenses' => getAccountByUniqueID(19)->id,
        ];
        $parentNames = [
            'Asset' => $parentAccounts->where('id', 1)->first()->account_name ?? 'Asset',
            'Liabilities' => $parentAccounts->where('id', 9)->first()->account_name ?? 'Liabilities',
            'Income' => $parentAccounts->where('id', 17)->first()->account_name ?? 'Income',
            'Expenses' => $parentAccounts->where('id', 19)->first()->account_name ?? 'Expenses',
        ];

        $groupedTrialBalance = [
            'Asset' => [],
            'Liabilities' => [],
            'Income' => [],
            'Expenses' => []
        ];

        foreach ($parentIds as $key => $parentId) {
            $accounts = getAllSubAccounts($parentId);

            foreach ($accounts as $account) {
                $openingDebit = $account->balance_type === 'debit' ? $account->opening_balance : 0;
                $openingCredit = $account->balance_type === 'credit' ? $account->opening_balance : 0;

                $openingTransactionDebit = AccountTransaction::where('account_id', $account->id)
                    ->whereDate('created_at', '<', $startDate)
                    ->sum('debit');

                $openingTransactionCredit = AccountTransaction::where('account_id', $account->id)
                    ->whereDate('created_at', '<', $startDate)
                    ->sum('credit');

                if (getFirstAccount($account->id) == $parentIds['Asset'] || getFirstAccount($account->id) == $parentIds['Expenses']) {
                    $openingDebit += $openingTransactionDebit - $openingTransactionCredit;
                    $openingCredit += 0;
                } elseif (getFirstAccount($account->id) == $parentIds['Liabilities'] || getFirstAccount($account->id) == $parentIds['Income']) {
                    $openingDebit += 0;
                    $openingCredit += $openingTransactionCredit - $openingTransactionDebit;
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

                if (getFirstAccount($account->id) == $parentIds['Asset'] || getFirstAccount($account->id) == $parentIds['Expenses']) {
                    $closingDebit = $closingDebit  ? $closingDebit : 0;
                    $closingCredit = $closingDebit == 0 ? abs($closingDebit) : 0;
                } elseif (getFirstAccount($account->id) == $parentIds['Liabilities'] || getFirstAccount($account->id) == $parentIds['Income']) {
                    $closingCredit = $closingCredit ? $closingCredit : 0;
                    $closingDebit = $closingCredit == 0 ? abs($closingCredit) : 0;
                }

                // Only add the entry if it has a non-zero opening balance or transactions within the period
                if ($openingDebit != 0 || $openingCredit != 0 || $transactionDebit != 0 || $transactionCredit != 0) {
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
                    if ($key == 'Asset') {
                        $groupedTrialBalance['Asset'][] = $entry;
                    } elseif ($key == 'Liabilities') {
                        $groupedTrialBalance['Liabilities'][] = $entry;
                    } elseif ($key == 'Income') {
                        $groupedTrialBalance['Income'][] = $entry;
                    } elseif ($key == 'Expenses') {
                        $groupedTrialBalance['Expenses'][] = $entry;
                    }
                }
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

    public function incomestatement(Request $request)
    {
        $title = 'Income Statement Report';
        $account = new  ChartOfAccount();

        $startDate = $request->input('from_date', date('Y-m-01')); // Default to the start of the current month
        $endDate = $request->input('to_date', date('Y-m-t')); // Default to the end of the current month

        // Calculate Revenue
        $revenue = DB::table('account_transactions')
            ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereIn('chart_of_accounts.id', getAccountIdsToArray(getOldAccount([getAccountByUniqueID(25)->id]), 18))
            ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->first();

        // Calculate COGS
        $cogs = DB::table('account_transactions')
            ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereIn('chart_of_accounts.id', getOldAccount(0, 20)->pluck("id"))
            ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->first();


        // Calculate Operating Expenses
        $operatingExpenses = DB::table('account_transactions')
            ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereIn('chart_of_accounts.id', getOldAccount(0, 21)->pluck("id"))
            ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->first();

        // Calculate Non-Operating Income
        $nonOperatingIncome = DB::table('account_transactions')
            ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereIn('chart_of_accounts.id', getOldAccount(0, 25)->pluck("id"))
            ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->first();

        // Prepare the income statement data
        $incomeStatement = [
            'Revenue' => ($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0),
            'COGS' => ($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0),
            'Gross Profit' => (($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0)) - (($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0)),
            'Operating Expenses' => ($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0),
            'Operating Income' => (($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0)) - (($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0)) - (($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0)),
            'Non-Operating Income' => ($nonOperatingIncome->total_credit ?? 0) - ($nonOperatingIncome->total_debit ?? 0),
            'Net Income' => (
                (($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0))
                - (($cogs->total_debit ?? 0) - ($cogs->total_credit ?? 0))
            ) - (($operatingExpenses->total_debit ?? 0) - ($operatingExpenses->total_credit ?? 0))
                + (($nonOperatingIncome->total_credit ?? 0) - ($nonOperatingIncome->total_debit ?? 0))
        ];

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.reports.incomestatement', get_defined_vars());
    }



    function incomeDetails(Request $req)
    {
        $sanitizedCategory = $req->input('category');
        $startDate = $req->from_date;
        $endDate = $req->to_date;
        if ($sanitizedCategory == "Revenue") {
            // Fetch transactions for the matched account IDs
            $transactions = DB::table('account_transactions')
                ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
                ->whereIn('chart_of_accounts.id', getAccountIdsToArray(getOldAccount([getAccountByUniqueID(25)->id]), 18))
                ->whereBetween('account_transactions.created_at', [$startDate, $endDate])

                ->get();
        } elseif ($sanitizedCategory == "COGS") {
            $transactions = DB::table('account_transactions')
                ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
                ->whereIn('chart_of_accounts.id', getOldAccount(0, 20)->pluck("id"))
                ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
                ->get();
        } elseif ($sanitizedCategory == "Operating_Expenses") {
            $transactions = DB::table('account_transactions')
                ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
                ->whereIn('chart_of_accounts.id', getOldAccount(0, 21)->pluck("id"))
                ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
                ->get();
        } elseif ($sanitizedCategory == "Non_Operating_Income") {
            $transactions = DB::table('account_transactions')
                ->join('chart_of_accounts', 'account_transactions.account_id', '=', 'chart_of_accounts.id')
                ->whereIn('chart_of_accounts.id', getOldAccount(0, 25)->pluck("id"))
                ->whereBetween('account_transactions.created_at', [$startDate, $endDate])
                ->get();
        }

        // Apply different calculations based on the category
        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');

        if (in_array($sanitizedCategory, ['Revenue', "Non_Operating_Income"])) {
            $amount = $totalCredit - $totalDebit;
        } elseif (in_array($sanitizedCategory, ['COGS'])) {
            $amount = $totalDebit - $totalCredit;
        } else {
            $amount = $totalDebit - $totalCredit;
        }

        // Return a view with the transactions data and calculated amount
        return view('backend/pages/reports/transaction-details', get_defined_vars());
    }

    public function balancesheet(Request $request)
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
    }

    public function stock(Request $request)
    {

        $title = 'Stock Detail';

        $branch_id = '';
        $product_id = '';

        if ($request->method() == 'POST') {
            $StockDetails = '';
            $datas = explode('-', $request->dateRange);
            $from_date = date('Y-m-d', strtotime($datas[0]));
            $to_date = date('Y-m-d', strtotime($datas[1]));

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;


            // dd($request->all());
            if ($product_id ==  '---Select Product---' || $product_id == null) {
                return Redirect::back()->withErrors(['msg' => 'Product Can not be empty!']);
            }

            // $currentSrock = StockSummary::orderBy('stock_summaries.id', 'desc')
            //     ->select('stock_summaries.*', 'stock_summaries.quantity as stock_qty', 'purchases_details.*', DB::raw("avg(purchases_details.unit_price) as avgPrice"))
            //     ->join('purchases_details', 'purchases_details.product_id', '=', 'stock_summaries.product_id')
            //     ->orderBy("stock_summaries.product_id", "ASC")
            //     ->groupBy('stock_summaries.product_id')
            //     ->get();

            $StockDetails = Stock::join('branches', 'stocks.branch_id', '=', 'branches.id')
                ->select('products.*', 'purchases_details.*', DB::raw("avg(purchases_details.unit_price) as avgPrice"))
                ->join('purchases_details', 'purchases_details.product_id', '=', 'stocks.product_id')
                ->join('products', 'stocks.product_id', '=', 'products.id')
                ->whereBetween('stocks.date', [$from_date, $to_date]);

            if ($branch_id != 'all') {
                $StockDetails =   $StockDetails->where('stocks.branch_id', $branch_id);
            }
            $StockDetails =   $StockDetails->where('stocks.product_id', $product_id);

            $StockDetails =  $StockDetails->get();
            // dd(count($StockDetails));
        }

        $companyInfo = Company::latest('id')->first();

        $branch = Branch::where('status', 'Active')->get();
        $accounts = ChartOfAccount::get()->where('status', 'Active');
        $category_info = Category::where('status', 'Active')->get();
        return view('backend.pages.reports.stock', get_defined_vars());
    }

    public function stocksummery(Request $request)
    {
        $title = 'Stock Summery';
        $branch_id = '';
        $product_id = '';
        $category_id = '';


        if ($request->method() == 'POST') {
            $StocksumDetails = '';

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;
            $category_id = $request->category;


            $inPro = array('Purchase', 'Manual Purchase', 'Production', 'Gain', 'Transfer In', 'Project In', 'Return');
            $outPro = array('Production Sale', 'Production Out', 'Sale', 'Damage', 'Lost', 'Transfer Out', 'Project Out', 'Project Use');


            $StocksumDetails = Stock::orderBy('stocks.product_id', 'asc')
                ->select('stocks.branch_id', 'stocks.product_id', 'stocks.status', 'branches.branchCode', 'branches.name as bname', 'categories.name as catname', 'products.name as proname', 'products.productCode', 'products.category_id')
                ->join('products', 'products.id', '=', 'stocks.product_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('branches', 'branches.id', '=', 'stocks.branch_id')
                // ->wherein('status', $inPro)
                ->orderBy("stocks.product_id", "ASC")
                ->groupBy('stocks.product_id');

            if ($branch_id != 'all') {
                $StocksumDetails =   $StocksumDetails->where('stocks.branch_id', $branch_id);
            }
            if ($product_id != 'all') {
                $StocksumDetails =   $StocksumDetails->where('stocks.product_id', $product_id);
            }
            if ($category_id != 'all') {
                $StocksumDetails =   $StocksumDetails->where('products.category_id', $category_id);
            }

            $StocksumDetails =  $StocksumDetails->get();

            // echo '<pre>';
            // print_r($StocksumDetails->toArray());
            // die();

            // dd($StocksumDetails);


            // StockSummary::join('branches', 'stock_summaries.branch_id', '=', 'branches.id')
            //     ->select('products.*', 'branches.branchCode as brcode', 'branches.name as brname', 'stock_summaries.*', 'stock_summaries.quantity as stock_qty')
            //     // ->join('purchases_details', 'purchases_details.product_id', '=', 'stock_summaries.product_id')
            //     ->join('products', 'stock_summaries.product_id', '=', 'products.id');

            // if ($branch_id != 'all') {
            //     $StocksumDetails =   $StocksumDetails->where('stock_summaries.branch_id', $branch_id);
            // }
            // if ($product_id != 'all') {
            //     $StocksumDetails =   $StocksumDetails->where('stock_summaries.branch_id', $branch_id);
            // }
            // if ($category_id != 'all') {
            //     $StocksumDetails =   $StocksumDetails->where('stock_summaries.branch_id', $branch_id);
            // }


            // $StocksumDetails =   $StocksumDetails->where('stock_summaries.product_id', $product_id);

            // $StocksumDetails =  $StocksumDetails->get();
            // dd($StocksumDetails);
        }

        $companyInfo = Company::latest('id')->first();

        $branch = Branch::where('status', 'Active')->get();
        $accounts = ChartOfAccount::get()->where('status', 'Active');
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

    public function productledger(Request $request)
    {
        
        $title = 'Product Ledger';
        $branch_id = '';
        $product_id = '';
        $datas = [];
        $from_date = null;
        $to_date = null;

        if ($request->isMethod('POST')) {
            $dates = explode('-', $request->dateRange);
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');

            $branch_id = $request->branch_id;
            $product_id = $request->product_id;

            // Fetch the product ledger data
            $datas = $this->getProductLedgerData($product_id, $branch_id, $from_date, $to_date);
        }
        $products = Product::where('status', 'Active')->get();
        $companyInfo = Company::latest('id')->first();
        $branches = Branch::where('status', 'Active')->get();

        return view('backend.pages.reports.productledger', compact(
            'title',
            'branch_id',
            'product_id',
            'datas',
            'from_date',
            'request',
            'to_date',
            'products',
            'companyInfo',
            'branches'
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

    private function getProductLedgerData($product_id, $branch_id, $from_date, $to_date)
    {
        $ledgerData = [];

        // Fetch stock-in details (Opening Stock + Purchases)
        $stockIn = ProductOpeningStockDetails::where('product_id', $product_id)
            ->when($branch_id !== 'all', function ($query) use ($branch_id) {
                return $query->where('branch_id', $branch_id);
            })
            ->get()
            ->map(function ($item) {
                $item->type = 'Opening Stock'; // Add dynamic type
                $item->invoice = $item->ProductOpeningStock->invoice_no ?? ""; // Add dynamic type
                return $item;
            })
            ->merge(
                PurchasesDetails::where('product_id', $product_id)
                    ->when($branch_id !== 'all', function ($query) use ($branch_id) {
                        return $query->where('branch_id', $branch_id);
                    })
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'Purchase'; // Add dynamic type
                        $item->invoice = $item->purchase->invoice_no ?? ""; // Add dynamic type
                        return $item;
                    })
            );

        // Fetch stock-out details (Sales + Project Transfers)
        $stockOut = sales_Details::where('product_id', $product_id)
            ->when($branch_id !== 'all', function ($query) use ($branch_id) {
                return $query->where('branch_id', $branch_id);
            })
            ->get()
            ->map(function ($item) {
                $item->type = 'Sales'; // Add dynamic type
                $item->quantity = $item->qty; // Add dynamic type
                $item->invoice = $item->sales->invoice_no ?? ""; // Add dynamic type
                return $item;
            })
            ->merge(
                ProjectTransferDetails::where('product_id', $product_id)
                    ->when($branch_id !== 'all', function ($query) use ($branch_id) {
                        return $query->where('branch_id', $branch_id);
                    })
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'Project Transfer'; // Add dynamic type
                        $item->invoice = $item->project_transfer->invoice_no ?? ""; // Add dynamic type
                        return $item;
                    })
            );

        // Combine and sort data
        $allData = $stockIn->merge($stockOut)->sortBy('date');
        $remainingStock = 0;
        $ledgerData = [];
        foreach ($allData as $key => $entry) {
            $in = in_array($entry->type, ['Opening Stock', 'Purchase']) ? $entry->quantity : 0;
            $out = in_array($entry->type, ['Sales', 'Project Transfer']) ? $entry->quantity : 0;
            $remainingStock += ($in - $out);

            $ledgerData[] = [
                'sl' => $key + 1,
                'date' => $entry->date,
                'invoice' => $entry->invoice ?? "",
                'branch' => $entry->branch->name ?? 'N/A',
                'product' => $entry->product->name ?? 'N/A',
                'status' => $entry->type,
                'in' => $in,
                'out' => $out,
                'remaining' => $remainingStock,
            ];
        }
        return $ledgerData;
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
