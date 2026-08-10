<?php
// Rebuilt: 2025-08-08 -- Quick action fix (Admin fallback), paginated 
// print/export modal for products+receivables+supplier due, single-sale
// invoice modal, product consumption search+code, sales performance -> 
// "Top Seller" placeholder-aware panel

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PosDashboardApiController extends Controller
{
    protected string $salesTable           = 'sales';
    protected string $saleDetailsTable     = 'sales__details';
    protected string $purchaseTable        = 'purchases';
    protected string $purchaseDetailsTable = 'purchases_details';
    protected string $transactionsTable    = 'account_transactions';
    protected string $accountsTable        = 'chart_of_accounts';
    protected string $projectsTable        = 'projects';
    protected string $suppliersTable       = 'suppliers';
    protected string $productsTable        = 'products';

    protected function resolveDateRange(Request $request): array
    {
        $filter = $request->query('filter', 'today');

        switch ($filter) {
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end   = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                $end   = Carbon::now()->endOfYear();
                break;
            case 'today':
            default:
                $start  = Carbon::now()->startOfDay();
                $end    = Carbon::now()->endOfDay();
                $filter = 'today';
                break;
        }

        return ['start' => $start, 'end' => $end, 'filter' => $filter];
    }

    /**
     * Generic paginator -- 'all' hole full data, na hole page/per_page onujayi slice
     */
    protected function paginateBuilder($query, Request $request, int $defaultPerPage = 100): array
    {
        $perPageParam = $request->query('per_page', $defaultPerPage);
        $page = max(1, (int) $request->query('page', 1));

        $total = (clone $query)->count();

        if ($perPageParam === 'all') {
            $rows = $query->get();
            return ['data' => $rows, 'total' => $total, 'page' => 1, 'per_page' => max($total, 1)];
        }

        $perPage = (int) $perPageParam ?: $defaultPerPage;
        $rows = $query->forPage($page, $perPage)->get();

        return ['data' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function quickActions(): JsonResponse
    {
        $user = Auth::user();
        $isAdmin = ($user->type ?? null) === 'Admin';
        $actions = [];

        $canSale = $isAdmin;
        $canPurchase = $isAdmin;

        if (!$isAdmin && method_exists($user, 'can')) {
            try {
                $canSale = $user->can('sale-create');
            } catch (\Throwable $e) {
            }
            try {
                $canPurchase = $user->can('purchase-create');
            } catch (\Throwable $e) {
            }
        }

        if ($canSale) {
            try {
                $actions[] = ['label' => 'New Sale', 'icon' => 'bi-cart-plus-fill', 'url' => route('sale.sale.create')];
            } catch (\Throwable $e) {
            }
        }

        if ($canPurchase) {
            try {
                $actions[] = ['label' => 'New Purchase', 'icon' => 'bi-bag-plus-fill', 'url' => route('inventorySetup.purchase.create')];
            } catch (\Throwable $e) {
            }
        }

        return response()->json($actions);
    }

    public function kpis(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $sales = DB::table($this->salesTable)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->selectRaw('COUNT(id) as cnt, COALESCE(SUM(grand_total),0) as amount')
            ->first();

        $purchases = DB::table($this->purchaseTable)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->selectRaw('COUNT(id) as cnt, COALESCE(SUM(grand_total),0) as amount')
            ->first();

        $ledgerRows = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->select('debit', 'payment_invoice')
            ->get();

        $received = 0;
        $receivable = 0;
        foreach ($ledgerRows as $row) {
            if (!empty(trim((string) $row->payment_invoice))) {
                $received += (float) $row->debit;
            } else {
                $receivable += (float) $row->debit;
            }
        }

        return response()->json([
            'filter' => $range['filter'],
            'total_sales_count'     => (int) $sales->cnt,
            'total_sales_amount'    => round((float) $sales->amount, 2),
            'total_purchase_count'  => (int) $purchases->cnt,
            'total_purchase_amount' => round((float) $purchases->amount, 2),
            'received_amount'       => round($received, 2),
            'receivable_amount'     => round($receivable, 2),
        ]);
    }

    public function salesPurchaseTrend(Request $request): JsonResponse
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $salesRows = DB::table($this->salesTable)
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as m, COALESCE(SUM(grand_total),0) as amount')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('amount', 'm');

        $purchaseRows = DB::table($this->purchaseTable)
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as m, COALESCE(SUM(grand_total),0) as amount')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('amount', 'm');

        $labels = [];
        $salesData = [];
        $purchaseData = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = $monthNames[$m - 1];
            $salesData[] = isset($salesRows[$m]) ? (float) $salesRows[$m] : 0;
            $purchaseData[] = isset($purchaseRows[$m]) ? (float) $purchaseRows[$m] : 0;
        }

        return response()->json(['year' => $year, 'labels' => $labels, 'sales' => $salesData, 'purchases' => $purchaseData]);
    }

    public function paymentBreakdown(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $rows = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->select('debit', 'payment_invoice')
            ->get();

        $paid = 0;
        $due = 0;
        foreach ($rows as $row) {
            if (!empty(trim((string) $row->payment_invoice))) {
                $paid += (float) $row->debit;
            } else {
                $due += (float) $row->debit;
            }
        }

        $total = $paid + $due;
        $paidPercent = $total > 0 ? round(($paid / $total) * 100, 1) : 0;

        return response()->json(['paid' => round($paid, 2), 'due' => round($due, 2), 'total' => round($total, 2), 'paid_percent' => $paidPercent]);
    }

    public function topProducts(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $products = DB::table($this->saleDetailsTable)
            ->join($this->salesTable, $this->salesTable . '.id', '=', $this->saleDetailsTable . '.sale_id')
            ->join($this->productsTable, $this->productsTable . '.id', '=', $this->saleDetailsTable . '.product_id')
            ->whereBetween($this->salesTable . '.created_at', [$range['start'], $range['end']])
            ->select(
                $this->productsTable . '.id',
                $this->productsTable . '.name',
                DB::raw('COALESCE(SUM(' . $this->saleDetailsTable . '.qty), 0) as total_qty'),
                DB::raw('COALESCE(SUM(' . $this->saleDetailsTable . '.qty * ' . $this->saleDetailsTable . '.price), 0) as total_amount')
            )
            ->groupBy($this->productsTable . '.id', $this->productsTable . '.name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    /**
     * Modified: pagination (100/page or 'all') jog kora holo -- print/export
     * er jonno
     */
    public function productInvoices(Request $request, $productId): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $query = DB::table($this->saleDetailsTable)
            ->join($this->salesTable, $this->salesTable . '.id', '=', $this->saleDetailsTable . '.sale_id')
            ->where($this->saleDetailsTable . '.product_id', $productId)
            ->whereBetween($this->salesTable . '.created_at', [$range['start'], $range['end']])
            ->select(
                $this->salesTable . '.id as sale_id',
                $this->salesTable . '.invoice_no',
                $this->salesTable . '.created_at',
                $this->saleDetailsTable . '.qty',
                $this->saleDetailsTable . '.price',
                DB::raw($this->saleDetailsTable . '.qty * ' . $this->saleDetailsTable . '.price as line_total')
            )
            ->orderByDesc($this->salesTable . '.created_at');

        $result = $this->paginateBuilder($query, $request);

        $result['data']->transform(function ($r) {
            try {
                $r->print_url = route('sale.sale.show', $r->sale_id);
            } catch (\Throwable $e) {
                $r->print_url = null;
            }
            return $r;
        });

        return response()->json($result);
    }

    /**
     * Added: Single sale er full invoice detail (Recent Transactions row click)
     */
    public function saleInvoiceDetail($saleId): JsonResponse
    {
        $sale = DB::table($this->salesTable)->where('id', $saleId)->first();
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        $items = DB::table($this->saleDetailsTable)
            ->leftJoin($this->productsTable, $this->productsTable . '.id', '=', $this->saleDetailsTable . '.product_id')
            ->where($this->saleDetailsTable . '.sale_id', $saleId)
            ->select(
                DB::raw($this->productsTable . '.name as product_name'),
                $this->saleDetailsTable . '.qty',
                $this->saleDetailsTable . '.price',
                DB::raw($this->saleDetailsTable . '.qty * ' . $this->saleDetailsTable . '.price as line_total')
            )
            ->get();

        $ledger = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->where('invoice', $sale->invoice_no)
            ->select('payment_invoice', 'account_id')
            ->first();

        $customerName = 'Walk-in Customer';
        $paymentStatus = 'Due';

        if ($ledger) {
            $paymentStatus = !empty(trim((string) $ledger->payment_invoice)) ? 'Paid' : 'Due';
            if ($ledger->account_id) {
                $acc = DB::table($this->accountsTable)->where('id', $ledger->account_id)->first();
                $customerName = $acc->account_name ?? $customerName;
            }
        }

        try {
            $printUrl = route('sale.sale.show', $sale->id);
        } catch (\Throwable $e) {
            $printUrl = null;
        }

        return response()->json([
            'sale'           => $sale,
            'items'          => $items,
            'customer_name'  => $customerName,
            'payment_status' => $paymentStatus,
            'print_url'      => $printUrl,
        ]);
    }

    public function recentTransactions(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $transactions = DB::table($this->salesTable)
            ->whereBetween($this->salesTable . '.created_at', [$range['start'], $range['end']])
            ->select($this->salesTable . '.id', $this->salesTable . '.invoice_no', $this->salesTable . '.grand_total', $this->salesTable . '.created_at')
            ->orderByDesc($this->salesTable . '.created_at')
            ->limit(15)
            ->get();

        if ($transactions->isEmpty()) return response()->json($transactions);

        $invoiceNos = $transactions->pluck('invoice_no')->filter()->all();

        $ledgerMap = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->whereIn('invoice', $invoiceNos)
            ->select('invoice', 'payment_invoice', 'account_id')
            ->get()
            ->keyBy('invoice');

        $accountIds = $ledgerMap->pluck('account_id')->filter()->unique()->all();
        $accountNames = DB::table($this->accountsTable)->whereIn('id', $accountIds)->pluck('account_name', 'id');

        $transactions->transform(function ($t) use ($ledgerMap, $accountNames) {
            $ledgerRow = $ledgerMap->get($t->invoice_no);
            $t->customer_name = 'Walk-in Customer';
            $t->payment_status = 'Due';

            if ($ledgerRow) {
                $t->payment_status = !empty(trim((string) $ledgerRow->payment_invoice)) ? 'Paid' : 'Due';
                if ($ledgerRow->account_id && isset($accountNames[$ledgerRow->account_id])) {
                    $t->customer_name = $accountNames[$ledgerRow->account_id];
                }
            }
            return $t;
        });

        return response()->json($transactions);
    }


    public function productConsumption(Request $request): JsonResponse
    {


        $range = $this->resolveDateRange($request);
        $projectId = $request->query('project_id');
        $productId = $request->query('product_id');
        $search = trim((string) $request->query('search', ''));

        $query = DB::table($this->purchaseDetailsTable)
            ->join($this->purchaseTable, $this->purchaseTable . '.id', '=', $this->purchaseDetailsTable . '.purchases_id')
            ->leftJoin($this->projectsTable, $this->projectsTable . '.id', '=', $this->purchaseDetailsTable . '.project_id')
            ->leftJoin($this->productsTable, $this->productsTable . '.id', '=', $this->purchaseDetailsTable . '.product_id')
            ->whereNotNull($this->purchaseDetailsTable . '.project_id')
            ->whereBetween($this->purchaseTable . '.created_at', [$range['start'], $range['end']]);

        if ($projectId) $query->where($this->purchaseDetailsTable . '.project_id', $projectId);
        if ($productId) $query->where($this->purchaseDetailsTable . '.product_id', $productId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where($this->productsTable . '.name', 'like', "%{$search}%")
                    ->orWhere($this->productsTable . '.productCode', 'like', "%{$search}%")
                    ->orWhere($this->projectsTable . '.name', 'like', "%{$search}%")
                    ->orWhere($this->purchaseTable . '.invoice_no', 'like', "%{$search}%");
            });
        }

        $rows = $query->select(
            $this->projectsTable . '.name as project_name',
            $this->productsTable . '.name as product_name',
            $this->productsTable . '.productCode as product_code',
            $this->purchaseTable . '.invoice_no',
            $this->purchaseTable . '.created_at',
            DB::raw('COALESCE(SUM(' . $this->purchaseDetailsTable . '.quantity), 0) as total_qty'),
            DB::raw('COALESCE(SUM(' . $this->purchaseDetailsTable . '.total_price), 0) as total_amount')
        )
            ->groupBy(
                $this->projectsTable . '.name',
                $this->productsTable . '.name',
                $this->productsTable . '.productCode',
                $this->purchaseTable . '.invoice_no',
                $this->purchaseTable . '.created_at'
            )
            ->orderByDesc($this->purchaseTable . '.created_at')
            ->limit(100)
            ->get();

        return response()->json($rows);
    }

    public function projectOptions(): JsonResponse
    {
        return response()->json(DB::table($this->projectsTable)->select('id', 'name')->orderBy('name')->get());
    }

    /**
     * Modified: product code o pathano hocche dropdown label er jonno
     */
    public function productOptions(): JsonResponse
    {
        return response()->json(
            DB::table($this->productsTable)->select('id', 'name', 'code')->orderBy('name')->get() // TODO-CONFIRM: code column
        );
    }

    /**
     * Modified: Renamed conceptually to "Top Seller" -- future e target,
     * commission, incentive track hobe. Ekhon shudhu sale volume onujayi rank.
     */
    public function salesPerformance(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $performance = DB::table($this->salesTable)
            ->join('users', 'users.id', '=', $this->salesTable . '.created_by')
            ->whereBetween($this->salesTable . '.created_at', [$range['start'], $range['end']])
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(' . $this->salesTable . '.id) as txn_count'),
                DB::raw('COALESCE(SUM(' . $this->salesTable . '.grand_total), 0) as total_sales')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return response()->json($performance);
    }

    public function topReceivables(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $rows = DB::table($this->transactionsTable)
            ->join($this->accountsTable, $this->accountsTable . '.id', '=', $this->transactionsTable . '.account_id')
            ->where($this->transactionsTable . '.type', 'sale')
            ->whereNotNull($this->transactionsTable . '.debit')
            ->whereBetween($this->transactionsTable . '.created_at', [$range['start'], $range['end']])
            ->where(function ($q) {
                $q->whereNull($this->transactionsTable . '.payment_invoice')->orWhere($this->transactionsTable . '.payment_invoice', '');
            })
            ->select(
                $this->accountsTable . '.id',
                $this->accountsTable . '.account_name as name',
                DB::raw('COALESCE(SUM(' . $this->transactionsTable . '.debit), 0) as due_amount')
            )
            ->groupBy($this->accountsTable . '.id', $this->accountsTable . '.account_name')
            ->orderByDesc('due_amount')
            ->limit(10)
            ->get();

        return response()->json($rows);
    }

    /**
     * Added: ekjon customer (account) er shob due invoice -- pagination
     * shoho, print/export er jonno
     */
    public function receivableInvoices(Request $request, $accountId): JsonResponse
    {
        $query = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->where('account_id', $accountId)
            ->where(function ($q) {
                $q->whereNull('payment_invoice')->orWhere('payment_invoice', '');
            })
            ->select('invoice', 'debit', 'created_at')
            ->orderByDesc('created_at');

        return response()->json($this->paginateBuilder($query, $request));
    }

    public function supplierDue(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $rows = DB::table($this->transactionsTable)
            ->join($this->suppliersTable, $this->suppliersTable . '.id', '=', $this->transactionsTable . '.supplier_id')
            ->where($this->transactionsTable . '.type', 'purchase')
            ->whereNotNull($this->transactionsTable . '.credit')
            ->whereBetween($this->transactionsTable . '.created_at', [$range['start'], $range['end']])
            ->where(function ($q) {
                $q->whereNull($this->transactionsTable . '.payment_invoice')->orWhere($this->transactionsTable . '.payment_invoice', '');
            })
            ->select(
                $this->suppliersTable . '.id',
                $this->suppliersTable . '.name',
                DB::raw('COALESCE(SUM(' . $this->transactionsTable . '.credit), 0) as due_amount')
            )
            ->groupBy($this->suppliersTable . '.id', $this->suppliersTable . '.name')
            ->orderByDesc('due_amount')
            ->limit(10)
            ->get();

        return response()->json($rows);
    }

    /**
     * Added: ekjon supplier er shob due voucher -- pagination shoho
     */
    public function supplierDueInvoices(Request $request, $supplierId): JsonResponse
    {
        $query = DB::table($this->transactionsTable)
            ->where('type', 'purchase')
            ->whereNotNull('credit')
            ->where('supplier_id', $supplierId)
            ->where(function ($q) {
                $q->whereNull('payment_invoice')->orWhere('payment_invoice', '');
            })
            ->select('invoice', 'credit', 'created_at')
            ->orderByDesc('created_at');

        return response()->json($this->paginateBuilder($query, $request));
    }
}
