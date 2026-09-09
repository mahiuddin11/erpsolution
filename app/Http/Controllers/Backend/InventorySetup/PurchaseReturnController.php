<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{

    public function index(Request $request)
    {
        $title = 'Purchase Return';
        return view('backend.pages.inventories.purchase.purchasereturn', get_defined_vars());
    }
}
