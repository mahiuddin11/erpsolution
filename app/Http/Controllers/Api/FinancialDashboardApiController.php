<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialDashboardApiController extends Controller
{
    private function accountGroups()
    {
        return [
            'revenue_ids' => array_merge(
                getOldAccount(0, 18)->pluck('id')->toArray(), // Direct Income
                getOldAccount(0, 19)->pluck('id')->toArray()  // Indirect Income
            ),
            'cogs_ids' => array_merge(
                getOldAccount(0, 22)->pluck('id')->toArray(), // Direct Expense
                getOldAccount(0, 24)->pluck('id')->toArray()  // Purchases
            ),
            'opex_ids'          => getOldAccount(0, 23)->pluck('id')->toArray(), // Operating Expenses
            'non_op_income_ids' => getOldAccount(0, 20)->pluck('id')->toArray(), // Non-Operating Income
        ];
    }

    // netProfit($from, $to) -- incomestatement()-er calculation hubohu,
    // shudhu function-e wrap kora holo jate KPI-r "this month" ar
    // "last month" duibar-i call kora jay code duplicate na kore
    private function financialSummary($from, $to)
    {
        $g = $this->accountGroups();

        $revenue = AccountTransaction::whereIn('account_id', $g['revenue_ids'])
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();

        $cogs = AccountTransaction::whereIn('account_id', $g['cogs_ids'])
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();

        $opex = AccountTransaction::whereIn('account_id', $g['opex_ids'])
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();

        $nonOpIncome = AccountTransaction::whereIn('account_id', $g['non_op_income_ids'])
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();

        $totalRevenue = $revenue->total_credit - $revenue->total_debit;
        $totalCOGS    = $cogs->total_debit - $cogs->total_credit;
        $grossProfit  = $totalRevenue - $totalCOGS;
        $totalOpex    = $opex->total_debit - $opex->total_credit;
        $operatingIncome = $grossProfit - $totalOpex;
        $totalNonOp   = $nonOpIncome->total_credit - $nonOpIncome->total_debit;
        $netIncome    = $operatingIncome + $totalNonOp;

        return [
            'total_income'   => round($totalRevenue, 2),
            'total_expenses' => round($totalCOGS + $totalOpex, 2),
            'net_profit'     => round($netIncome, 2),
        ];
    }

    // ---------------------------------------------------------------
    // Point 1: KPI Cards -- this month vs last month % change
    // ---------------------------------------------------------------
    public function kpis()
    {
        $thisMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $thisMonthEnd   = Carbon::now()->endOfMonth()->format('Y-m-d');
        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth()->format('Y-m-d');
        $lastMonthEnd   = Carbon::now()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');

        $thisMonth = $this->financialSummary($thisMonthStart, $thisMonthEnd);
        $lastMonth = $this->financialSummary($lastMonthStart, $lastMonthEnd);

        $pctChange = function ($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / abs($previous)) * 100, 1);
        };

        $arAccountIds = getOldAccount(0, 5)->pluck('id')->toArray();
        $ar = AccountTransaction::whereIn('account_id', $arAccountIds)
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();
        $pendingPayments = round($ar->total_debit - $ar->total_credit, 2);

        return response()->json([
            'total_income'       => $thisMonth['total_income'],
            'income_change'      => $pctChange($thisMonth['total_income'], $lastMonth['total_income']),
            'total_expenses'     => $thisMonth['total_expenses'],
            'expenses_change'    => $pctChange($thisMonth['total_expenses'], $lastMonth['total_expenses']),
            'net_profit'         => $thisMonth['net_profit'],
            'net_profit_change'  => $pctChange($thisMonth['net_profit'], $lastMonth['net_profit']),
            'pending_payments'   => $pendingPayments,
            'overdue_count'      => 0, // TODO-CONFIRM: invoice/overdue table na thakle 0
        ]);
    }


    public function kpiDetails(Request $request)
    {
        $type = $request->input('type');
        $perPage = (int) $request->input('per_page', 100);
        $all = $request->boolean('all');
        $g = $this->accountGroups();

        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd   = Carbon::now()->endOfMonth();

        $from = null;
        $to = null;

        switch ($type) {
            case 'total_income':
                $accountIds = $g['revenue_ids'];
                $from = $thisMonthStart;
                $to = $thisMonthEnd;
                $direction = 'income';
                break;

            case 'total_expenses':
                $accountIds = array_merge($g['cogs_ids'], $g['opex_ids']);
                $from = $thisMonthStart;
                $to = $thisMonthEnd;
                $direction = 'expense';
                break;

            case 'net_profit':

                $accountIds = array_merge(
                    $g['revenue_ids'],
                    $g['cogs_ids'],
                    $g['opex_ids'],
                    $g['non_op_income_ids']
                );
                $from = $thisMonthStart;
                $to = $thisMonthEnd;
                $direction = 'mixed';
                break;

            case 'pending_payments':
                $accountIds = getOldAccount(0, 5)->pluck('id')->toArray(); // AR
                $direction = 'ar';
                break;

            default:
                return response()->json(['message' => 'Invalid KPI type'], 422);
        }

        $query = AccountTransaction::with(['account', 'branch', 'customer'])
            ->whereIn('account_id', $accountIds);

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $query->orderByDesc('created_at');

        if ($all) {
            $collection = $query->get();
        } else {
            $paginated = $query->paginate($perPage);
            $collection = $paginated->getCollection();
        }

        $mapped = $collection->map(function ($t) use ($direction, $g) {
            if ($direction === 'mixed') {
                $isIncome = in_array($t->account_id, array_merge($g['revenue_ids'], $g['non_op_income_ids']));
            } else {
                $isIncome = $direction === 'income';
            }

            $amount = $direction === 'ar'
                ? ($t->debit - $t->credit)
                : ($isIncome ? ($t->credit - $t->debit) : ($t->debit - $t->credit));

            return [
                'title'   => optional($t->account)->account_name
                    ?? optional($t->customer)->name
                    ?? ucwords(str_replace('_', ' ', $t->type)),
                'branch'  => optional($t->branch)->name ?? '-',
                'voucher' => $t->invoice ?? '-',
                'amount'  => round($amount, 2),
                'date'    => optional($t->created_at)->format('d M Y') ?? '-',
            ];
        })->values();

        if ($all) {
            return response()->json([
                'data'         => $mapped,
                'current_page' => 1,
                'last_page'    => 1,
                'total'        => $mapped->count(),
            ]);
        }

        return response()->json([
            'data'         => $mapped,
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'total'        => $paginated->total(),
        ]);
    }

    public function cashFlow(Request $request)
    {
        $range = $request->input('range', 'this_year');

        if ($range === 'last_year') {
            $year = Carbon::now()->subYear()->year;
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();
        } elseif ($range === 'last_6_months') {
            $start = Carbon::now()->subMonths(5)->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
        } else { // this_year
            $start = Carbon::now()->startOfYear();
            $end   = Carbon::now()->endOfYear();
        }

        $g = $this->accountGroups();

        $income = AccountTransaction::whereIn('account_id', $g['revenue_ids'])
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0) as val')
            ->groupBy('y', 'm')->get()->keyBy(fn($r) => $r->y . '-' . $r->m);

        $expense = AccountTransaction::whereIn('account_id', array_merge($g['cogs_ids'], $g['opex_ids']))
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, COALESCE(SUM(debit),0)-COALESCE(SUM(credit),0) as val')
            ->groupBy('y', 'm')->get()->keyBy(fn($r) => $r->y . '-' . $r->m);

        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $netData = [];

        $cursor = $start->copy()->startOfMonth();
        while ($cursor <= $end) {
            $key = $cursor->year . '-' . $cursor->month;
            $inc = round($income[$key]->val ?? 0, 2);
            $exp = round($expense[$key]->val ?? 0, 2);

            $labels[] = $cursor->format('M');
            $incomeData[] = $inc;
            $expenseData[] = $exp;
            $netData[] = round($inc - $exp, 2);

            $cursor->addMonth();
        }

        return response()->json([
            'labels'   => $labels,
            'income'   => $incomeData,
            'expenses' => $expenseData,
            'net'      => $netData,
        ]);
    }

    // ---------------------------------------------------------------
    // Point 3: Expense Breakdown -- Opex-er child account-wise (donut)
    // ---------------------------------------------------------------
    public function expenseBreakdown(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $opexChildren = \App\Models\Accounts::where('parent_id', 23)->get(['id', 'account_name']);

        $colors = ['#059669', '#a3e635', '#ef4444', '#7c3aed', '#f59e0b', '#3b82f6', '#ec4899'];

        $data = $opexChildren->map(function ($acc, $i) use ($from, $to, $colors) {
            $ids = getOldAccount(0, $acc->id)->pluck('id')->toArray();
            $sum = AccountTransaction::whereIn('account_id', $ids)
                ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                ->selectRaw('COALESCE(SUM(debit),0)-COALESCE(SUM(credit),0) as val')
                ->first();

            return [
                'label'  => $acc->account_name,
                'amount' => round($sum->val ?? 0, 2),
                'color'  => $colors[$i % count($colors)],
            ];
        })->filter(fn($d) => $d['amount'] > 0)->values();

        return response()->json($data);
    }

    // ---------------------------------------------------------------
    // Point 4: Revenue Comparison -- this year vs last year, month-wise
    // ---------------------------------------------------------------
    public function revenueComparison()
    {
        $g = $this->accountGroups();
        $thisYear = Carbon::now()->year;
        $lastYear = $thisYear - 1;

        $rows = AccountTransaction::whereIn('account_id', $g['revenue_ids'])
            ->whereIn(DB::raw('YEAR(created_at)'), [$thisYear, $lastYear])
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0) as val')
            ->groupBy('y', 'm')->get();

        $byYearMonth = [];
        foreach ($rows as $r) {
            $byYearMonth[$r->y][$r->m] = round($r->val, 2);
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $thisYearData = [];
        $lastYearData = [];
        for ($m = 1; $m <= 12; $m++) {
            $thisYearData[] = $byYearMonth[$thisYear][$m] ?? null;
            $lastYearData[] = $byYearMonth[$lastYear][$m] ?? null;
        }

        return response()->json([
            'labels'    => $months,
            'this_year' => $thisYearData,
            'last_year' => $lastYearData,
        ]);
    }

    // ---------------------------------------------------------------
    // Point 5: Transactions tab -- income/expense voucher list
    // TODO-CONFIRM: journal_vouchers/debit_vouchers/credit_vouchers
    // real table structure na jana thakay AccountTransaction theke
    // shorashori dekhano hocche, voucher_no field name check korte hobe
    // ---------------------------------------------------------------
    public function transactions(Request $request)
    {
        $from   = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to     = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type   = $request->input('type');
        $search = $request->input('search');
        $g = $this->accountGroups();

        $query = AccountTransaction::with(
            'account',
            'branch',
            'project'
        )
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);

        if ($type === 'income') {
            $query->whereIn('account_id', $g['revenue_ids']);
        } elseif ($type === 'expense') {
            $query->whereIn('account_id', array_merge($g['cogs_ids'], $g['opex_ids']));
        } else {
            $query->whereIn('account_id', array_merge($g['revenue_ids'], $g['cogs_ids'], $g['opex_ids']));
        }

        if ($search) {
            $query->where('payment_invoice', 'like', "%{$search}%");
        }

        $data = $query->latest('created_at')->take(200)->get()->map(function ($t) use ($g) {
            $isIncome = in_array($t->account_id, $g['revenue_ids']);
            $amount = $isIncome ? ($t->credit - $t->debit) : ($t->debit - $t->credit);

            return [
                'type'      => $t->type,
                'direction' => $isIncome ? 'income' : 'expense',
                'descaption' => $t->remark ?? '-',
                'title'     => optional($t->account)->account_name ?? 'N/A',
                'voucher'   => $t->invoice ?? '-',
                'branch' => $t->branch?->name
                    ?? $t->project?->name
                    ?? '-', // TODO-CONFIRM: branch info AccountTransaction e thakle field name din
                'amount'    => $amount ?? '',
                'date'      => $t->created_at->format('d M Y'),
            ];
        });

        return response()->json($data);
    }

    // Point 6: Invoices tab -- TODO: real invoice table na deya porjonto
    // ei endpoint khali array return korbe
    public function invoices(Request $request)
    {
        return response()->json([]);
    }
}
