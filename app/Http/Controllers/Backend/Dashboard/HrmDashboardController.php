<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HrmDashboardController extends Controller
{
    //



    public function index()
    {
        $pgTitle = "HRM Dashboard";
        $user    = Auth::user();
        $branchId = $user->branch_id;

        //      "role_id" => 1
        // "status" => "Active"
        // "type" => "Admin"

        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();

        if ($user->type != 'Admin') {
            return 'not admin';
        }

        return view('backend.pages.dashboard.hrm', get_defined_vars());
    }
}
