<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreDashboardController extends Controller
{
    //

    public function index()
    {
        $pgTitle = "Inventory Dashboard";
        $user    = Auth::user();
        $isAdmin = $user->type == 'Admin';

        return view('backend.pages.dashboard.store', compact('pgTitle', 'user', 'isAdmin'));
    }
}
