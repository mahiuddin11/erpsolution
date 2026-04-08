<?php

namespace App\Http\Controllers\Backend\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\customerLedger;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\LeaveApplication;
use App\Models\Lone;
use App\Models\ProductUse;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectRequisition;
use App\Models\ProjectReturn;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Purchases;
use App\Models\Sale;
use App\Models\Service;
use App\Models\StockAjdustment;
use App\Models\Supplier;
use App\Models\supplierLedger;
use App\Models\Transection;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        $user = Auth::user();
        getUnderAccount(8);
        
        $purchase =  getUnderAccount(22);

        $totdayPurchase = getUnderAccount(22, date('Y-m-d'));

        $sale = getUnderAccount(18, null, false);

        if ($user->branch_id == null) {
            $totalService = Service::sum('amount');
        } else {
            $totalService = Service::where('branch_id', $user->branch_id)->sum('amount');
        }

        $todaySale = getUnderAccount(18, date('Y-m-d'), false);

        $expense = getUnderAccount(19);
        $todayExpense = getUnderAccount(19, date('Y-m-d'));


        if ($user->branch_id == null) {
            $suppplierPayment = supplierLedger::where('payment_type', 'Collect')->sum('credit');
        } else {
            $suppplierPayment = supplierLedger::where('payment_type', 'Collect')->where('branch_id', $user->branch_id)->sum('credit');
        }

        
        $totalcashbalance =  getUnderAccount(7);
        $totalbankbalance =  getUnderAccount(8);
        // dd( $totalcashbalance,  $totalbankbalance );

        $totalemployee = Employee::where('employee_status','present')->count();
        $todayattendance = Attendance::whereDate("date", date("Y-m-d"))->count();

        $customerDue = customerDue();

        $supplierDue = supplierDue();


        // $customerPayment = customerLedger::where('payment_type', 'Collect')->sum('debit');
        $customerPayment = customerLedger::sum('debit');
        // dd($customerPayment);
        $userrole = UserRole::where('status', 'Active')->count();
        $customer = Customer::where('status', 'Active')->count();
        $Supplier = Supplier::where('status', 'Active')->count();
        $prjectDetails = Project::where('manager_id', $user->id)->first();

        //pending branch
        $pvConditon = array(
            'status' => 'Pending',
            'purchase_type' => 'Manual',
        );
        if ($user->branch_id == null) {
            $prPending = PurchaseRequisition::where('status', 'Pending')->count();
        } else {
            $prPending = PurchaseRequisition::where('status', 'Pending')->where('branch_id', $user->branch_id)->count();
        }
        if ($user->branch_id == null) {
            $poPending = PurchaseOrder::where('status', 'Pending')->count();
        } else {
            $poPending = PurchaseOrder::where('status', 'Pending')->where('branch_id', $user->branch_id)->count();
        }
        if ($user->branch_id == null) {
            $pvPending = Purchases::where($pvConditon)->count();
        } else {
            $pvPending = Purchases::where($pvConditon)->where('branch_id', $user->branch_id)->count();
        }
        if ($user->branch_id == null) {
            $trPending = Transfer::where('status', 'Pending')->count();
        } else {
            $trPending = Transfer::where('status', 'Pending')->where('from_branch_id', $user->branch_id)->count();
        }

        if ($user->branch_id == null) {
            $adjPending = StockAjdustment::where('status', 'Pending')->count();
        } else {
            $adjPending = StockAjdustment::where('status', 'Pending')->where('branch_id', $user->branch_id)->count();
        }

        if ($user->type == "Project") {

            $totalprojectexpence = ProjectExpense::where('project_id', $prjectDetails->id)->sum('amount');
            $todayprojectexpence = ProjectExpense::where('project_id', $prjectDetails->id)->where('date', today())->sum('amount');

            $productreq = ProjectRequisition::where('project_id', $prjectDetails->id)->sum('approve_qty');
            $productreqtoday = ProjectRequisition::where('project_id', $prjectDetails->id)->where('date', today())->sum('approve_qty');

            $useproduct = ProductUse::where('project_id', $prjectDetails->id)->sum('use_total');
            $usetotaltoday = ProductUse::where('project_id', $prjectDetails->id)->where('date', today())->sum('use_total');

            $returntotal = ProjectReturn::where('project_id', $prjectDetails->id)->sum('return_total');
            $returntoday = ProjectReturn::where('project_id', $prjectDetails->id)->where('date', today())->sum('return_total');
        }


        // Count records created today and approved
        $leave_aplication = LeaveApplication::whereDate('created_at', today())
            ->where('status', 'approved')
            ->count();
        $attendance_aplication =  Attendance::whereDate('date',date("Y-m-d"))->count();

        $activeEmployeeCount = Employee::where('status', 'Active')->where('employee_status',"present")->count();
        $total_absent = $activeEmployeeCount - $attendance_aplication;

        $approved_leave = LeaveApplication::where('status', 'approved')->count();

        $startDate = Carbon::now()->subMonth(); // One month ago
        $endDate = Carbon::now();
        $monthly_created_employee = \App\Models\Employee::where('status', 'Active')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $monthly_lone_approved = Lone::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $monthly_lone = Lone::whereBetween('created_at', [$startDate, $endDate])->count();

        // dd(date('m-d-Y'));
        //pending project
        if ($user->branch_id == null) {
            $PendingReturn = ProjectReturn::where('status', 'Pending')->count();
        } else {
            $PendingReturn = ProjectReturn::where('status', 'Pending')->where('branch_id', $user->branch_id)->count();
        }
        if ($user->branch_id == null) {
            $PendingReq = ProjectRequisition::where('status', 'Pending')->count();
        } else {
            $PendingReq = ProjectRequisition::where('status', 'Pending')->where('branch_id', $user->branch_id)->count();
        }

        $rollper = auth()->user()->userRole->dashboard_id;
        $rollper = explode(',', $rollper);

        return view('backend.pages.dashboard.index', get_defined_vars());
    }
}
