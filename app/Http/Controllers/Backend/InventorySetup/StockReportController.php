<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SalesDetailsController;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductOpeningStockDetails;
use App\Models\PurchasesDetails;
use App\Models\sales_Details;
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

        if ($request->method() == "POST") {
            if ($request->category_id != "all") {
                $productid = Product::where('category_id', $request->category_id)->pluck('id');
                $currentSrock = $currentSrock->whereIn('product_id', $productid);
            }
        }

        $currentSrock = $currentSrock->get();

        $categorys = Category::get();
        // pops($currentSrock);
        return view('backend.pages.reports.index', get_defined_vars());
    }


    public function productSummarayDemo(Request $request)
    {


        // ── GET: শুধু page load ───────────────────────────────────────────
        if ($request->isMethod('get')) {
            $branches = DB::table('branches')->select('id', 'name')->orderBy('name')->get();
            return view('backend.pages.reports.productSummaryDemo', compact('branches'));
        }

        // ── POST: AJAX data fetch ─────────────────────────────────────────
        $request->validate([
            'product_id' => 'required|integer|min:1',
            'branch_id'  => 'nullable|integer',
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
        ]);

        $productId = (int) $request->product_id;
        $branchId  = $request->branch_id ? (int) $request->branch_id : null;
        $fromDate  = $request->from_date;
        $toDate    = $request->to_date;

        // ── 1. Product info ───────────────────────────────────────────────
        // products: id, name, productCode, unit_id, category_id, brand_id
        // units join করে unit name আনা হচ্ছে
        $product = DB::table('products as p')
            ->leftJoin('product_units as u', 'u.id', '=', 'p.unit_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->select(
                'p.id',
                'p.name',
                'p.productCode as code',
                DB::raw("COALESCE(u.name, 'N/A') as unit"),
                DB::raw("COALESCE(cat.name, 'N/A') as category"),
                'p.purchases_price',
                'p.sale_price',
                'p.status',
                'p.low_stock'
            )
            ->where('p.id', $productId)
            ->whereNull('p.deleted_at')
            ->first();

        if (! $product) {
            return response()->json(['error' => 'Product not found with ID: ' . $productId], 404);
        }

        // ─────────────────────────────────────────────────────────────────
        // 2. OPENING STOCK
        // Table: product_opening_stock_details
        // Columns: product_id, branch_id, quantity, unit_price, total_price, date
        // ─────────────────────────────────────────────────────────────────
        $openingQuery = DB::table('product_opening_stock_details')
            ->where('product_id', $productId)
            ->whereNull('deleted_at');

        if ($branchId) {
            $openingQuery->where('branch_id', $branchId);
        }

        $opening = $openingQuery
            ->select('branch_id', 'quantity', 'unit_price', 'total_price', 'date', 'purchasetype')
            ->orderBy('date')
            ->get();

        // ─────────────────────────────────────────────────────────────────
        // 3. PURCHASES
        // Table: purchases_details
        // Columns: product_id, branch_id, supplier_id, quantity, unit_price,
        //          total_price, date, status, purchasetype, purchases_id
        // NOTE: purchases_details এ সরাসরি সব data আছে, parent join লাগবে না
        // ─────────────────────────────────────────────────────────────────
        $purchaseQuery = DB::table('purchases_details as pd')
            ->leftJoin('suppliers as s', 's.id', '=', 'pd.supplier_id')
            ->where('pd.product_id', $productId)
            ->whereNull('pd.deleted_at')
            ->where('pd.status', 'Active')
            ->whereBetween('pd.date', [$fromDate, $toDate]);

        if ($branchId) {
            $purchaseQuery->where('pd.branch_id', $branchId);
        }

        $purchases = $purchaseQuery
            ->select(
                'pd.date',
                'pd.purchases_id',
                'pd.branch_id',
                'pd.supplier_id',
                DB::raw("COALESCE(s.name, 'N/A') as supplier_name"),
                'pd.quantity',
                'pd.unit_price',
                'pd.total_price',
                'pd.purchasetype',
                'pd.status'
            )
            ->orderBy('pd.date')
            ->get();

        // ─────────────────────────────────────────────────────────────────
        // 4. STOCK ADJUSTMENTS
        // Table: stock_ajdustment_detailsts  (typo — actual table name রাখা হয়েছে)
        // Columns: product_id, branch_id, quantity, unit_price, total_price,
        //          date, approval_date, status, purchases_id
        // NOTE: quantity positive = stock in (adjustment add)
        // ─────────────────────────────────────────────────────────────────
        $adjustQuery = DB::table('stock_ajdustment_detailsts')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->where('status', 'Active')
            ->whereBetween('date', [$fromDate, $toDate]);

        if ($branchId) {
            $adjustQuery->where('branch_id', $branchId);
        }

        $adjustments = $adjustQuery
            ->select('date', 'approval_date', 'branch_id', 'quantity', 'unit_price', 'total_price', 'status', 'purchases_id')
            ->orderBy('date')
            ->get();

        // ─────────────────────────────────────────────────────────────────
        // 5. TRANSFERS
        // Table: transfer_details
        // Columns: product_id, from_branch_id, to_branch_id, qty, approve_qty,
        //          unit_price, total_price, date, status, transfer_id
        // NOTE: approve_qty ব্যবহার করা হচ্ছে (approved মাল)
        // ─────────────────────────────────────────────────────────────────
        $transferQuery = DB::table('transfer_details')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->where('status', 'Approved')
            ->whereBetween('date', [$fromDate, $toDate]);

        if ($branchId) {
            $transferQuery->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                    ->orWhere('to_branch_id', $branchId);
            });
        }

        $transfers = $transferQuery
            ->select('date', 'transfer_id', 'from_branch_id', 'to_branch_id', 'qty', 'approve_qty', 'unit_price', 'total_price', 'status')
            ->orderBy('date')
            ->get();

        // ─────────────────────────────────────────────────────────────────
        // 6. SALES
        // Table: sales__details  (double underscore — actual table name)
        // Columns: product_id, branch_id, qty, rate, price, date, sale_id,
        //          purchasetype, vat, cty_size, gas_qty
        // ─────────────────────────────────────────────────────────────────
        $salesQuery = DB::table('sales__details as sd')
            ->leftJoin('sales as s', 's.id', '=', 'sd.sale_id')
            ->leftJoin('customers as c', 'c.id', '=', 's.customer_id')
            ->where('sd.product_id', $productId)
            ->whereBetween('sd.date', [$fromDate, $toDate]);

        if ($branchId) {
            $salesQuery->where('sd.branch_id', $branchId);
        }

        $sales = $salesQuery
            ->select(
                'sd.date',
                'sd.sale_id',
                'sd.branch_id',
                DB::raw("COALESCE(c.name, 'N/A') as customer_name"),
                'sd.qty',
                'sd.rate',
                'sd.price',
                'sd.purchasetype',
                'sd.vat'
            )
            ->orderBy('sd.date')
            ->get();

        // ─────────────────────────────────────────────────────────────────
        // 7. SUMMARY TOTALS
        // ─────────────────────────────────────────────────────────────────
        $totalOpening  = $opening->sum('quantity');
        $totalPurchase = $purchases->sum('quantity');
        $totalAdj      = $adjustments->sum('quantity');   // + বা - হতে পারে
        $totalSales    = $sales->sum('qty');

        // Transfer: branch filter থাকলে — in/out আলাদা হিসাব
        if ($branchId) {
            $totalTrIn  = $transfers->where('to_branch_id', $branchId)->sum('approve_qty');
            $totalTrOut = $transfers->where('from_branch_id', $branchId)->sum('approve_qty');
        } else {
            // All branch: transfer internally balance হয়, net = 0
            $totalTrIn  = $transfers->sum('approve_qty');
            $totalTrOut = $transfers->sum('approve_qty');
        }

        $closing = $totalOpening + $totalPurchase + $totalTrIn - $totalTrOut + $totalAdj - $totalSales;

        return response()->json([
            'product'     => $product,
            'summary'     => [
                'totalOpening'  => $totalOpening,
                'totalPurchase' => $totalPurchase,
                'totalTrIn'     => $totalTrIn,
                'totalTrOut'    => $totalTrOut,
                'totalAdj'      => $totalAdj,
                'totalSales'    => $totalSales,
                'closing'       => $closing,
            ],
            'opening'     => $opening,
            'purchases'   => $purchases,
            'adjustments' => $adjustments,
            'transfers'   => $transfers,
            'sales'       => $sales,
        ]);
    }

    public function stockMatchingEngine(Request $request)
    {
        $title = 'Stock Matching Engine';
        $result = [];
        $branchId  = $request->branch_id;
        $productId = $request->product_id;
        $fromDate  = $request->from_date;
        $toDate    = $request->to_date;

        // GET → শুধু page load
        if (!$request->isMethod('post')) {
            return view('backend.pages.reports.matchingEngine', compact(
                'result',
                'branchId',
                'productId',
                'fromDate',
                'toDate',
                'title'
            ));
        }

        $request->validate(['product_id' => 'required']);

        try {
            $stocks = Stock::query()->where('product_id', $productId);

            if (!empty($branchId)) $stocks->where('branch_id', $branchId);
            if (!empty($fromDate)) $stocks->whereDate('date', '>=', $fromDate);
            if (!empty($toDate))   $stocks->whereDate('date', '<=', $toDate);

            $stocks = $stocks->orderBy('date')->orderBy('id')->get();

            foreach ($stocks as $stock) {
                $matched = false;

                // Purchase Match
                $purchase = PurchasesDetails::where('purchases_id', $stock->general_id)
                    ->where('product_id', $productId)->first();

                if ($purchase) {
                    $result[] = [
                        'stock_id'           => $stock->id,          // ← checkbox এর জন্য
                        'date'               => $stock->date,
                        'stock_branch_id'    => $stock->branch_id ?? 0,
                        'stock_out_branch_id' => $purchase->branch_id ?? 0,
                        'general_id'         => $stock->general_id,
                        'table_name'         => 'purchases_details',
                        'source_id'          => $purchase->purchases_id,
                        'status'             => $stock->status,
                        'stock_qty'          => $stock->quantity,
                        'quantity'           => $purchase->quantity ?? 0,
                        'match_status'       => 'Matched',
                    ];
                    $matched = true;
                }

                // Sale Match
                if (!$matched) {
                    $sale = sales_Details::where('sale_id', $stock->general_id)
                        ->where('product_id', $productId)->first();

                    if ($sale) {
                        $result[] = [
                            'stock_id'           => $stock->id,
                            'date'               => $stock->date,
                            'stock_branch_id'    => $stock->branch_id ?? 0,
                            'stock_out_branch_id' => $sale->branch_id ?? 0,
                            'general_id'         => $stock->general_id,
                            'table_name'         => 'sales_details',
                            'source_id'          => $sale->sale_id,
                            'status'             => $stock->status,
                            'stock_qty'          => $stock->quantity,
                            'quantity'           => $sale->qty ?? $sale->quantity ?? 0,
                            'match_status'       => 'Matched',
                        ];
                        $matched = true;
                    }
                }

                // Opening Stock Match
                if (!$matched) {
                    $opening = ProductOpeningStockDetails::where(
                        'product_opening_stock_id',
                        $stock->general_id
                    )->where('product_id', $productId)->first();

                    if ($opening) {
                        $result[] = [
                            'stock_id'           => $stock->id,
                            'date'               => $stock->date,
                            'stock_branch_id'    => $stock->branch_id ?? 0,
                            'stock_out_branch_id' => $opening->branch_id ?? 0,
                            'general_id'         => $stock->general_id,
                            'table_name'         => 'product_opening_stock_details',
                            'source_id'          => $opening->product_opening_stock_id,
                            'status'             => $stock->status,
                            'stock_qty'          => $stock->quantity,
                            'quantity'           => $opening->quantity ?? 0,
                            'match_status'       => 'Matched',
                        ];
                        $matched = true;
                    }
                }

                // No Match
                if (!$matched) {
                    $result[] = [
                        'stock_id'           => $stock->id,
                        'date'               => $stock->date,
                        'stock_branch_id'    => $stock->branch_id ?? 0,
                        'stock_out_branch_id' => '-',
                        'general_id'         => $stock->general_id,
                        'table_name'         => 'NO SOURCE FOUND',
                        'source_id'          => '-',
                        'status'             => $stock->status,
                        'stock_qty'          => $stock->quantity,
                        'quantity'           => 0,
                        'match_status'       => 'No Match',
                    ];
                }
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return view('backend.pages.reports.matchingEngine', compact(
            'result',
            'branchId',
            'productId',
            'fromDate',
            'toDate',
            'title'
        ));
    }


    // ── 2. নতুন method — Bulk Status Update (AJAX) ───────────────────────
    public function bulkUpdateStockStatus(Request $request)
    {

        $request->validate([
            'stock_ids' => 'required|array|min:1',
            'stock_ids.*' => 'integer|min:1',
            'status'    => 'required|in:Opening,Purchase,Manual Purchase,Production Sale,Production,Production Out,Sale,Damage,Lost,Gain,Others,Transfer Out,Transfer In,Project,Project In,Project Out,Project Use,Return,Sale Return,Purchase Return',
        ]);

        try {
            $updated = Stock::whereIn('id', $request->stock_ids)
                ->update([
                    'status'     => $request->status,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => $updated . ' টি stock record আপডেট হয়েছে → ' . $request->status,
                'updated_count' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
