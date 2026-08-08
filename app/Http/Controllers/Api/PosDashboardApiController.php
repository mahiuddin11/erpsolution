<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PosDashboardApiController extends Controller
{
    protected string $salesTable        = 'sales';
    protected string $saleDetailsTable  = 'sales__details';
    protected string $transactionsTable = 'account_transactions';

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
     * KPI Cards: Total Sales, Paid, Due, Collection Rate
     */
    public function kpis(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        $totalSales = DB::table($this->salesTable)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->selectRaw('COALESCE(SUM(grand_total), 0) as amount, COUNT(id) as txn_count')
            ->first();

        $totalDiscount = DB::table($this->salesTable)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->sum('discount');

        $totalVat = DB::table($this->saleDetailsTable)
            ->join($this->salesTable, $this->salesTable . '.id', '=', $this->saleDetailsTable . '.sale_id')
            ->whereBetween($this->salesTable . '.created_at', [$range['start'], $range['end']])
            ->sum($this->saleDetailsTable . '.vat');

        // Modified: whereNotNull('debit') jog kora holo -- shudhu customer
        // receivable side er row ashbe, credit/revenue side er duplicate row bad jabe
        $ledgerRows = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->select('debit', 'payment_invoice')
            ->get();

        $paidAmount = 0;
        $dueAmount  = 0;

        foreach ($ledgerRows as $row) {
            $isPaid = !empty(trim((string) $row->payment_invoice));
            if ($isPaid) {
                $paidAmount += (float) $row->debit;
            } else {
                $dueAmount += (float) $row->debit;
            }
        }

        $ledgerTotal = $paidAmount + $dueAmount;
        $collectionRate = $ledgerTotal > 0 ? round(($paidAmount / $ledgerTotal) * 100, 1) : 0;

        $avgOrderValue = $totalSales->txn_count > 0
            ? round($totalSales->amount / $totalSales->txn_count, 2)
            : 0;

        return response()->json([
            'filter' => $range['filter'],
            'total_sales_amount'  => round((float) $totalSales->amount, 2),
            'total_transactions'  => (int) $totalSales->txn_count,
            'average_order_value' => $avgOrderValue,
            'total_discount'      => round((float) $totalDiscount, 2),
            'total_vat'           => round((float) $totalVat, 2),
            'paid_amount'         => round($paidAmount, 2),
            'due_amount'          => round($dueAmount, 2),
            'collection_rate'     => $collectionRate,
        ]);
    }

    /**
     * Sales Trend
     */
    public function salesTrend(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        if ($range['filter'] === 'today') {
            $rows = DB::table($this->salesTable)
                ->whereBetween('created_at', [$range['start'], $range['end']])
                ->selectRaw('HOUR(created_at) as label, COALESCE(SUM(grand_total),0) as amount')
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('label')
                ->get()
                ->keyBy('label');

            $labels = [];
            $data = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $data[] = isset($rows[$h]) ? (float) $rows[$h]->amount : 0;
            }
        } elseif ($range['filter'] === 'year') {
            $rows = DB::table($this->salesTable)
                ->whereBetween('created_at', [$range['start'], $range['end']])
                ->selectRaw('MONTH(created_at) as label, COALESCE(SUM(grand_total),0) as amount')
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy('label')
                ->get()
                ->keyBy('label');

            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $labels = [];
            $data = [];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $monthNames[$m - 1];
                $data[] = isset($rows[$m]) ? (float) $rows[$m]->amount : 0;
            }
        } else {
            $rows = DB::table($this->salesTable)
                ->whereBetween('created_at', [$range['start'], $range['end']])
                ->selectRaw('DATE(created_at) as label, COALESCE(SUM(grand_total),0) as amount')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('label')
                ->get()
                ->keyBy('label');

            $labels = [];
            $data = [];
            $period = CarbonPeriod::create($range['start']->copy()->startOfDay(), $range['end']->copy()->startOfDay());
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $data[] = isset($rows[$key]) ? (float) $rows[$key]->amount : 0;
            }
        }

        return response()->json([
            'filter' => $range['filter'],
            'labels' => $labels,
            'values' => $data,
        ]);
    }

    /**
     * Top Selling Products
     */
    public function topProducts(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        // TODO-CONFIRM: 'products' table ebong 'name' column
        $products = DB::table($this->saleDetailsTable)
            ->join($this->salesTable, $this->salesTable . '.id', '=', $this->saleDetailsTable . '.sale_id')
            ->join('products', 'products.id', '=', $this->saleDetailsTable . '.product_id')
            ->whereBetween($this->salesTable . '.created_at', [$range['start'], $range['end']])
            ->select(
                'products.id',
                'products.name',
                DB::raw('COALESCE(SUM(' . $this->saleDetailsTable . '.qty), 0) as total_qty'),
                DB::raw('COALESCE(SUM(' . $this->saleDetailsTable . '.qty * ' . $this->saleDetailsTable . '.price), 0) as total_amount')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    /**
     * Paid vs Due Breakdown
     */
    public function paymentBreakdown(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        // Modified: whereNotNull('debit') jog kora holo
        $rows = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->select('debit', 'payment_invoice')
            ->get();

        $paid = 0;
        $due  = 0;

        foreach ($rows as $row) {
            if (!empty(trim((string) $row->payment_invoice))) {
                $paid += (float) $row->debit;
            } else {
                $due += (float) $row->debit;
            }
        }

        return response()->json([
            ['method' => 'Paid', 'amount' => round($paid, 2)],
            ['method' => 'Due',  'amount' => round($due, 2)],
        ]);
    }

    /**
     * Recent Transactions -- protyekta sale er pashe Paid/Due status badge
     */
    public function recentTransactions(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        // TODO-CONFIRM: 'customers' table ebong 'name' column
        $transactions = DB::table($this->salesTable)
            ->leftJoin('customers', 'customers.id', '=', $this->salesTable . '.customer_id')
            ->whereBetween($this->salesTable . '.created_at', [$range['start'], $range['end']])
            ->select(
                $this->salesTable . '.id',
                $this->salesTable . '.invoice_no',
                $this->salesTable . '.grand_total',
                $this->salesTable . '.created_at',
                DB::raw('COALESCE(customers.name, "Walk-in Customer") as customer_name')
            )
            ->orderByDesc($this->salesTable . '.created_at')
            ->limit(15)
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json($transactions);
        }

        $invoiceNos = $transactions->pluck('invoice_no')->filter()->all();

        // Modified: whereNotNull('debit') jog kora holo -- duplicate row
        // thakle keyBy() shudhu last matching row rakhto, ekhon shothik row-i thakbe
        $ledgerMap = DB::table($this->transactionsTable)
            ->where('type', 'sale')
            ->whereNotNull('debit')
            ->whereIn('invoice', $invoiceNos)
            ->select('invoice', 'payment_invoice')
            ->get()
            ->keyBy('invoice');

        $transactions->transform(function ($t) use ($ledgerMap) {
            $ledgerRow = $ledgerMap->get($t->invoice_no);
            $t->payment_status = ($ledgerRow && !empty(trim((string) $ledgerRow->payment_invoice)))
                ? 'Paid'
                : 'Due';
            return $t;
        });

        return response()->json($transactions);
    }

    /**
     * Cashier / Sales Agent Performance
     */
    public function cashierPerformance(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        // TODO-CONFIRM: 'users' table ebong created_by -> users.id
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
            ->get();

        return response()->json($performance);
    }

    /**
     * Top Due Customers
     */
    public function topDueCustomers(Request $request): JsonResponse
    {
        $range = $this->resolveDateRange($request);

        // TODO-CONFIRM: 'customers' table ebong 'name' column
        // Modified: whereNotNull('debit') jog kora holo
        $dueCustomers = DB::table($this->transactionsTable)
            ->join('customers', 'customers.id', '=', $this->transactionsTable . '.customer_id')
            ->where($this->transactionsTable . '.type', 'sale')
            ->whereNotNull($this->transactionsTable . '.debit')
            ->whereBetween($this->transactionsTable . '.created_at', [$range['start'], $range['end']])
            ->where(function ($q) {
                $q->whereNull($this->transactionsTable . '.payment_invoice')
                    ->orWhere($this->transactionsTable . '.payment_invoice', '');
            })
            ->select(
                'customers.id',
                'customers.name',
                DB::raw('COALESCE(SUM(' . $this->transactionsTable . '.debit), 0) as due_amount')
            )
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('due_amount')
            ->limit(10)
            ->get();

        return response()->json($dueCustomers);
    }
}
