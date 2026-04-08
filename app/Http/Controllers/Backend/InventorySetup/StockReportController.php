<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockSummary;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Stock Summary';
        $companyInfo = Company::latest('id')->first();

        $currentSrock = Stock::orderBy('stocks.product_id', 'asc')
            ->select('stocks.branch_id', 'stocks.product_id', 'stocks.total_price', 'stocks.quantity', 'stocks.status')
            ->get();
    
        $currentSrock = StockSummary::orderBy('stock_summaries.id', 'desc')
            ->select('stock_summaries.*', 'stock_summaries.quantity as stock_qty')
            ->orderBy("stock_summaries.product_id", "ASC");

        if($request->method() == "POST"){
            if($request->category_id != "all"){
                $productid = Product::where('category_id',$request->category_id)->pluck('id');
                $currentSrock = $currentSrock->whereIn('product_id',$productid);
            }
        }
        
        $currentSrock = $currentSrock->get();

        $categorys = Category::get();
        // pops($currentSrock);
        return view('backend.pages.reports.index', get_defined_vars());
    }
}
