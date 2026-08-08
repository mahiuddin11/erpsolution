<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinanceDashboardController extends Controller
{
    //

    public function index()
    {
        $pgTitle = 'Financ Dashbord';
        return view('backend.pages.dashboard.finance', get_defined_vars());
    }
}
