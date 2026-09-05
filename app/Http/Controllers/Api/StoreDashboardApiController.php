<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockSummary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreDashboardApiController extends Controller
{
    const IN_STATUSES = [
        'Opening',
        'Purchase',
        'Manual Purchase',
        'Production',
        'Gain',
        'Transfer In',
        'Project In',
        'Return',
        'Sale Return',
    ];

    const OUT_STATUSES = [
        'Production Sale',
        'Production Out',
        'Sale',
        'Damage',
        'Lost',
        'Transfer Out',
        'Project Out',
        'Project Use',
        'Purchase Return',
        'Project',
    ];

    // ---------------------------------------------------------------
    // Branch / Warehouse আলাদা করার জন্য centralized scope
    // parent_id = 0        -> আসল Branch
    // parent_id != 0       -> Warehouse (sub-location)
    // ---------------------------------------------------------------
    private function branchQuery()
    {
        return Branch::where('parent_id', 0);
    }

    private function warehouseQuery()
    {
        return Branch::where('parent_id', '!=', 0);
    }

    private function branchStockByProduct()
    {
        return StockSummary::where('type', 'Branch')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->pluck('total_qty', 'product_id');
    }

    private function avgPriceByProduct()
    {
        $purchases = DB::table('purchases_details')->select('product_id', 'unit_price');
        $opening   = DB::table('product_opening_stock_details')->select('product_id', 'unit_price');

        $union = $purchases->unionAll($opening);

        return DB::table(DB::raw("({$union->toSql()}) as combined_prices"))
            ->mergeBindings($union)
            ->select('product_id', DB::raw('AVG(unit_price) as avg_price'))
            ->groupBy('product_id')
            ->pluck('avg_price', 'product_id');
    }

    public function kpis()
    {
        $today = Carbon::today();

        $stockByProduct = $this->branchStockByProduct();
        $priceByProduct = $this->avgPriceByProduct();
        $totalSku = Product::where('status', 'Active')->count();

        $totalStockValue = 0;
        foreach ($stockByProduct as $productId => $qty) {
            if ($qty <= 0) continue;
            $avgPrice = $priceByProduct[$productId] ?? 0;
            $totalStockValue += round($avgPrice * $qty, 2);
        }

        $lowStockCount = 0;
        $outOfStockCount = 0;

        Product::where('status', 'Active')
            ->whereNotNull('low_stock')
            ->select('id', 'low_stock')
            ->get()
            ->each(function ($p) use ($stockByProduct, &$lowStockCount, &$outOfStockCount) {
                $qty = $stockByProduct[$p->id] ?? 0;
                if ($qty <= 0) {
                    $outOfStockCount++;
                } elseif ($qty <= $p->low_stock) {
                    $lowStockCount++;
                }
            });

        $stockInToday = (int) Stock::whereDate('date', $today)
            ->whereIn('status', self::IN_STATUSES)->sum('quantity');

        $stockOutToday = (int) Stock::whereDate('date', $today)
            ->whereIn('status', self::OUT_STATUSES)->sum('quantity');

        $pendingPr = 0;
        if (class_exists(\App\Models\PurchaseRequisition::class)) {
            $pendingPr = \App\Models\PurchaseRequisition::where('status', 'pending')->count();
        }


        $totalBranches   = $this->branchQuery()->where('status', 'Active')->count();
        $totalWarehouses = $this->warehouseQuery()->where('status', 'Active')->count();

        return response()->json([
            'visible'            => true,
            'total_sku'          => $totalSku,
            'total_stock_value'  => round($totalStockValue),
            'low_stock_count'    => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'stock_in_today'     => $stockInToday,
            'stock_out_today'    => $stockOutToday,
            'branchs'            => $totalBranches,
            'warehouses'         => $totalWarehouses,
            'pending_pr'         => $pendingPr,
        ]);
    }

    // ---------------------------------------------------------------
    // Point 1 (drill-down): KPI click -> detail list
    // ---------------------------------------------------------------
    public function kpiDetails(Request $request)
    {
        $type  = $request->input('type');
        $today = Carbon::today();
        $rows  = [];

        switch ($type) {
            case 'low_stock':
            case 'out_of_stock':
                $stockByProduct = $this->branchStockByProduct();
                $rows = Product::where('status', 'Active')
                    ->whereNotNull('low_stock')
                    ->get(['id', 'name', 'low_stock'])
                    ->map(function ($p) use ($stockByProduct) {
                        return [
                            'name'      => $p->name,
                            'qty'       => $stockByProduct[$p->id] ?? 0,
                            'low_stock' => $p->low_stock,
                        ];
                    })
                    ->filter(fn($p) => $type === 'out_of_stock' ? $p['qty'] <= 0 : ($p['qty'] > 0 && $p['qty'] <= $p['low_stock']))
                    ->sortBy('qty')->take(50)->values()
                    ->map(fn($p) => ['title' => $p['name'], 'subtitle' => "Qty: {$p['qty']} (Reorder: {$p['low_stock']})"]);
                break;

            case 'stock_in_today':
                $rows = Stock::with(['product', 'branch'])
                    ->whereDate('date', $today)->whereIn('status', self::IN_STATUSES)
                    ->take(50)->get()
                    ->map(fn($s) => ['title' => optional($s->product)->name ?? 'N/A', 'subtitle' => $s->status . ' - ' . (optional($s->branch)->name ?? 'N/A')]);
                break;

            case 'stock_out_today':
                $rows = Stock::with(['product', 'branch'])
                    ->whereDate('date', $today)->whereIn('status', self::OUT_STATUSES)
                    ->take(50)->get()
                    ->map(fn($s) => ['title' => optional($s->product)->name ?? 'N/A', 'subtitle' => $s->status . ' - ' . (optional($s->branch)->name ?? 'N/A')]);
                break;

            case 'branches':
                $rows = $this->branchQuery()->where('status', 'Active')
                    ->get(['id', 'name'])
                    ->map(fn($b) => ['title' => $b->name, 'subtitle' => 'Branch']);
                break;

            case 'warehouses':
                $rows = $this->warehouseQuery()->where('status', 'Active')
                    ->get(['id', 'name'])
                    ->map(fn($w) => ['title' => $w->name, 'subtitle' => 'Warehouse']);
                break;

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        return response()->json($rows);
    }
    public function branchDistribution()
    {
        $priceByProduct = $this->avgPriceByProduct();

        $branchIds = $this->branchQuery()->pluck('id');

        $rows = StockSummary::where('type', 'Branch')
            ->whereIn('branch_id', $branchIds)
            ->select('branch_id', 'product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('branch_id', 'product_id')
            ->having('qty', '>', 0)
            ->get();

        $valueByBranch = [];
        foreach ($rows as $r) {
            $avgPrice = $priceByProduct[$r->product_id] ?? 0;
            $value = round($avgPrice * $r->qty, 2);
            $valueByBranch[$r->branch_id] = ($valueByBranch[$r->branch_id] ?? 0) + $value;
        }

        $grandTotal = array_sum($valueByBranch);

        $data = $this->branchQuery()->where('status', 'Active')->get(['id', 'name'])
            ->map(function ($b) use ($valueByBranch, $grandTotal) {
                $val = $valueByBranch[$b->id] ?? 0;
                return [
                    'id'              => $b->id,
                    'name'            => $b->name,
                    'total'           => round($val, 2),
                    'present_percent' => $grandTotal > 0 ? round(($val / $grandTotal) * 100, 1) : 0,
                ];
            });

        return response()->json($data);
    }

    public function warehouseDistribution()
    {
        $priceByProduct = $this->avgPriceByProduct();


        $warehouseIds = $this->warehouseQuery()->pluck('id');

        $rows = StockSummary::where('type', 'Branch')
            ->whereIn('branch_id', $warehouseIds)
            ->select('branch_id', 'product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('branch_id', 'product_id')
            ->having('qty', '>', 0)
            ->get();

        $valueByWarehouse = [];
        foreach ($rows as $r) {
            $avgPrice = $priceByProduct[$r->product_id] ?? 0;
            $value = round($avgPrice * $r->qty, 2);
            $valueByWarehouse[$r->branch_id] = ($valueByWarehouse[$r->branch_id] ?? 0) + $value;
        }

        $grandTotal = array_sum($valueByWarehouse);

        $data = $this->warehouseQuery()->where('status', 'Active')->get(['id', 'name'])
            ->map(function ($w) use ($valueByWarehouse, $grandTotal) {
                $val = $valueByWarehouse[$w->id] ?? 0;
                return [
                    'id'              => $w->id,
                    'name'            => $w->name,
                    'total'           => round($val, 2),
                    'present_percent' => $grandTotal > 0 ? round(($val / $grandTotal) * 100, 1) : 0,
                ];
            });

        return response()->json($data);
    }

    public function warehouseStockDetails(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');

        $priceByProduct = $this->avgPriceByProduct();

        $rows = StockSummary::with('products.category')
            ->where('branch_id', $warehouseId)
            ->where('type', 'Branch')
            ->select('product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->having('qty', '>', 0)
            ->get()
            ->map(function ($r) use ($priceByProduct) {
                $avgPrice = $priceByProduct[$r->product_id] ?? 0;
                $total    = round($avgPrice * $r->qty, 2);
                return [
                    'product'      => optional($r->products)->getRawOriginal('name') ?? 'N/A',
                    'product_code' => optional($r->products)->getRawOriginal('productCode') ?? '',
                    'category'     => optional(optional($r->products)->category)->name ?? 'N/A',
                    'qty'          => $r->qty,
                    'avg_price'    => round($avgPrice, 2),
                    'total'        => $total,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $warehouseName = optional($this->warehouseQuery()->find($warehouseId))->name ?? 'N/A';
        $grandTotal = round($rows->sum('total'), 2);

        return response()->json([
            'warehouse_name' => $warehouseName,
            'rows'           => $rows,
            'grand_total'    => $grandTotal,
        ]);
    }


    public function quickActions()
    {
        $actions = [
            ['key' => 'add_product',    'label' => 'Add Product',          'icon' => 'bi-box-seam',         'route' => 'inventorySetup.product.create'],
            ['key' => 'stock_purchase', 'label' => 'Purchase Entry',       'icon' => 'bi-truck',            'route' => 'inventorySetup.purchase.create'],
            ['key' => 'stock_adjuestment', 'label' => 'Stock Adjustment',       'icon' => 'bi-truck',            'route' => 'inventorySetup.stockAdjustment.create'],
            ['key' => 'stock_transfer', 'label' => 'Stock Transfer',       'icon' => 'bi-arrow-left-right', 'route' => 'inventorySetup.transfer.create'],
            ['key' => 'current_stock',   'label' => 'Current Stock', 'icon' => 'bi-clipboard-data',   'route' => 'inventorySetup.currentStock.index'],
        ];

        $allowed = collect($actions)->filter(function ($action) {
            return \Route::has($action['route']) && Helper::roleAccess($action['route']);
        })->map(function ($action) {
            $action['url'] = route($action['route']);
            return $action;
        })->values();

        return response()->json($allowed);
    }


    public function lowStockItems()
    {
        $stockByProduct = $this->branchStockByProduct();

        $data = Product::where('status', 'Active')
            ->whereNotNull('low_stock')
            ->get(['id', 'name', 'low_stock'])
            ->map(fn($p) => [
                'name'      => $p->name,
                'qty'       => $stockByProduct[$p->id] ?? 0,
                'low_stock' => $p->low_stock,
            ])
            ->filter(fn($p) => $p['qty'] <= $p['low_stock'])
            ->sortBy('qty')->take(10)->values()
            ->map(fn($p) => [
                'name'     => $p['name'],
                'sub'      => $p['qty'] <= 0 ? 'Out of stock' : "Qty: {$p['qty']}",
                'severity' => $p['qty'] <= 0 ? 'out' : 'low',
            ]);

        return response()->json($data);
    }


    public function stockMovement(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->type == 'Admin';
        $today   = Carbon::today();

        $query = Stock::with(['product', 'branch']);

        if ($isAdmin) {
            $query->whereDate('date', $today);
            if ($request->filled('warehouse_id')) {
                $query->where('branch_id', $request->warehouse_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }
        } else {
            $last30 = Carbon::now()->subDays(30)->startOfDay();
            $query->where('branch_id', $user->branch_id)
                ->whereBetween('date', [$last30, $today]);
        }

        $data = $query->latest('date')->take(50)->get()->map(fn($s) => [
            'product'   => optional($s->product)->name ?? 'N/A',
            'branch'    => optional($s->branch)->name ?? 'N/A', // এখানে branch relation টা আসলে warehouse row রিটার্ন করবে, নাম ঠিকই আসবে
            'status'    => $s->status,
            'direction' => in_array($s->status, self::IN_STATUSES) ? 'in' : (in_array($s->status, self::OUT_STATUSES) ? 'out' : 'other'),
            'quantity'  => $s->quantity,
            'date'      => $s->date instanceof Carbon ? $s->date->format('Y-m-d') : $s->date,
        ]);

        return response()->json($data);
    }

    public function warehouseOptions()
    {
        return response()->json(
            $this->warehouseQuery()->where('status', 'Active')->select('id', 'name')->orderBy('name')->get()
        );
    }

    // ---------------------------------------------------------------
    // Point 6: Recent Transactions -- অপরিবর্তিত
    // ---------------------------------------------------------------
    public function recentTransactions(Request $request)
    {
        $from   = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to     = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type   = $request->input('type');
        $search = $request->input('search');

        $query = Stock::with(['product', 'branch'])
            ->whereNotIn('status', ['Project Out', 'Project In'])
            ->whereBetween('date', [$from, $to]);

        if ($type === 'in') {
            $query->whereIn('status', self::IN_STATUSES);
        } elseif ($type === 'out') {
            $query->whereIn('status', self::OUT_STATUSES);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $data = $query->latest('created_at')->take(200)->get()->map(fn($s) => [
            'title'     => optional($s->product)->name ?? 'N/A',
            'voucher'   => $s->invoice_no ?? '-',
            'status'    => $s->status,
            'direction' => in_array($s->status, self::IN_STATUSES) ? 'in' : (in_array($s->status, self::OUT_STATUSES) ? 'out' : 'other'),
            'branch'    => optional($s->branch)->name ?? 'N/A',
            'quantity'  => $s->quantity,
            'date'      => $s->created_at->format('d-M-Y h:i A'),
        ]);

        return response()->json($data);
    }
}
