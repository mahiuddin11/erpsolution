<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosDashboardController extends Controller
{
    public function index()
    {

        $pgTitle = "POS Dashboard";
        $user    = Auth::user();
        $isAdmin = $user->type == 'Admin';

        return view('backend.pages.dashboard.pos', compact('pgTitle', 'user', 'isAdmin'));
    }
}
