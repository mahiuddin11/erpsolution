<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Holiday;
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
        $isAdmin = $user->type == 'Admin';

        return view('backend.pages.dashboard.hrm', compact('pgTitle', 'user', 'isAdmin'));
    }
}
