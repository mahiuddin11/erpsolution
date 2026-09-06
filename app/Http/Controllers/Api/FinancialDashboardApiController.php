<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialDashboardApiController extends Controller
{

    private function resolveRange(string $range)
    {
        $now = Carbon::now();
        switch ($range) {
            case 'today':
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDay()->startOfDay();
                $prevTo = $now->copy()->subDay()->endOfDay();
                break;
            case 'month':
                $from = $now->copy()->startOfMonth();
                $to = $now->copy()->endOfMonth();
                $prevFrom = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $prevTo = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'year':
                $from = $now->copy()->startOfYear();
                $to = $now->copy()->endOfYear();
                $prevFrom = $now->copy()->subYear()->startOfYear();
                $prevTo = $now->copy()->subYear()->endOfYear();
                break;
            case 'all':
                $from = Carbon::create(2000, 1, 1)->startOfDay(); // TODO-CONFIRM: earliest txn date diye replace koro
                $to = $now->copy()->endOfDay();
                $prevFrom = null;
                $prevTo = null;
                break;
            case '7d':
            default:
                $from = $now->copy()->subDays(6)->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDays(13)->startOfDay();
                $prevTo = $now->copy()->subDays(7)->endOfDay();
                break;
        }
        return compact('from', 'to', 'prevFrom', 'prevTo');
    }


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


    public function kpis(Request $request)
    {
        $range = $request->input('range', '7d');
        ['from' => $from, 'to' => $to, 'prevFrom' => $prevFrom, 'prevTo' => $prevTo] = $this->resolveRange($range);
        $current = $this->financialSummary($from->format('Y-m-d'), $to->format('Y-m-d'));
        $previous = ($prevFrom && $prevTo)
            ? $this->financialSummary($prevFrom->format('Y-m-d'), $prevTo->format('Y-m-d'))
            : ['total_income' => 0, 'total_expenses' => 0, 'net_profit' => 0];

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
            'total_income'       => $current['total_income'],
            'income_change'      => $pctChange($current['total_income'], $previous['total_income']),
            'total_expenses'     => $current['total_expenses'],
            'expenses_change'    => $pctChange($current['total_expenses'], $previous['total_expenses']),
            'net_profit'         => $current['net_profit'],
            'net_profit_change'  => $pctChange($current['net_profit'], $previous['net_profit']),
            'pending_payments'   => $pendingPayments,
            'overdue_count'      => 0, // TODO-CONFIRM: invoice/overdue table na thakle 0
        ]);
    }


    public function kpiDetails(Request $request)
    {
        $type = $request->input('type');
        $range = $request->input('range', '7d');
        $perPage = (int) $request->input('per_page', 100);
        $all = $request->boolean('all');
        $g = $this->accountGroups();


        ['from' => $rangeFrom, 'to' => $rangeTo] = $this->resolveRange($range);


        $from = null;
        $to = null;

        switch ($type) {
            case 'total_income':
                $accountIds = $g['revenue_ids'];
                $from = $rangeFrom;
                $to = $rangeTo;
                $direction = 'income';
                break;

            case 'total_expenses':
                $accountIds = array_merge($g['cogs_ids'], $g['opex_ids']);
                $from = $rangeFrom;
                $to = $rangeTo;
                $direction = 'expense';
                break;

            case 'net_profit':

                $accountIds = array_merge(
                    $g['revenue_ids'],
                    $g['cogs_ids'],
                    $g['opex_ids'],
                    $g['non_op_income_ids']
                );
                $from = $rangeFrom;
                $to = $rangeTo;
                $direction = 'mixed';
                break;

            case 'pending_payments':
                // AR-er current balance -- range apply hoy na, purnango list-i dekhano hoy
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

    public function arApAging()
    {
        return response()->json([
            'receivable' => $this->buildAgingReport(5, true),
            'payable'    => $this->buildAgingReport(16, false),
        ]);
    }


    private function buildAgingReport($parentId, $isAr)
    {
        $children = \App\Models\Accounts::where('parent_id', $parentId)
            ->get(['id', 'account_name']);

        $rows = [];

        foreach ($children as $acc) {
            $ids = getOldAccount(0, $acc->id)->pluck('id')->toArray();

            $txns = AccountTransaction::whereIn('account_id', $ids)
                ->select('debit', 'credit', 'created_at')
                ->get();

            if ($txns->isEmpty()) continue;

            $buckets = ['current' => 0, 'd1_30' => 0, 'd31_60' => 0, 'd61_90' => 0, 'd91_plus' => 0];

            foreach ($txns as $t) {
                $amount = $isAr ? ($t->debit - $t->credit) : ($t->credit - $t->debit);
                $days = Carbon::parse($t->created_at)->diffInDays(Carbon::now());

                if ($days <= 0) {
                    $buckets['current'] += $amount;
                } elseif ($days <= 30) {
                    $buckets['d1_30'] += $amount;
                } elseif ($days <= 60) {
                    $buckets['d31_60'] += $amount;
                } elseif ($days <= 90) {
                    $buckets['d61_90'] += $amount;
                } else {
                    $buckets['d91_plus'] += $amount;
                }
            }

            $total = array_sum($buckets);

            if (round($total, 2) == 0) continue; // fully settled -- table-e dekhanor dorkar nai

            $rows[] = [
                'name'     => $acc->account_name,
                'current'  => round($buckets['current'], 2),
                'd1_30'    => round($buckets['d1_30'], 2),
                'd31_60'   => round($buckets['d31_60'], 2),
                'd61_90'   => round($buckets['d61_90'], 2),
                'd91_plus' => round($buckets['d91_plus'], 2),
                'total'    => round($total, 2),
            ];
        }

        usort($rows, fn($a, $b) => $b['total'] <=> $a['total']);

        $grand = [
            'current'  => round(array_sum(array_column($rows, 'current')), 2),
            'd1_30'    => round(array_sum(array_column($rows, 'd1_30')), 2),
            'd31_60'   => round(array_sum(array_column($rows, 'd31_60')), 2),
            'd61_90'   => round(array_sum(array_column($rows, 'd61_90')), 2),
            'd91_plus' => round(array_sum(array_column($rows, 'd91_plus')), 2),
            'total'    => round(array_sum(array_column($rows, 'total')), 2),
        ];

        $top10 = collect($rows)->take(10)->map(fn($r) => [
            'label'  => $r['name'],
            'amount' => $r['total'],
        ])->values();

        return [
            'kpis' => [
                'unpaid_amount'   => $grand['total'],
                'overdue_amount'  => round($grand['d1_30'] + $grand['d31_60'] + $grand['d61_90'] + $grand['d91_plus'], 2),
                'overdue_30_plus' => round($grand['d31_60'] + $grand['d61_90'] + $grand['d91_plus'], 2),
                'overdue_90_plus' => $grand['d91_plus'],
            ],
            'top10' => $top10,
            'rows'  => $rows,
            'grand' => $grand,
        ];
    }



    public function bankCashBalance()
    {
        $cashAccounts = $this->buildBankCashRows(7, 'Cash in Hand');   // Cash in Hand children
        $bankAccounts = $this->buildBankCashRows(8, 'Bank');   // Cash at Bank children

        $allAccounts = $cashAccounts->concat($bankAccounts)->sortByDesc('balance')->values();

        $totalCash  = round($cashAccounts->sum('balance'), 2);
        $totalBank  = round($bankAccounts->sum('balance'), 2);
        $grandTotal = round($totalCash + $totalBank, 2);


        $allIds = array_merge(
            getOldAccount(0, 7)->pluck('id')->toArray(),
            getOldAccount(0, 8)->pluck('id')->toArray()
        );

        $todayTxns = AccountTransaction::whereIn('account_id', $allIds)
            ->whereDate('created_at', Carbon::today())
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(debit),0)+COALESCE(SUM(credit),0) as amt')
            ->first();

        return response()->json([
            'total_cash'                => $totalCash,
            'total_bank'                => $totalBank,
            'grand_total'               => $grandTotal,
            'cash_accounts_count'       => $cashAccounts->count(),
            'bank_accounts_count'       => $bankAccounts->count(),
            'today_transactions_count'  => (int) $todayTxns->cnt,
            'today_transactions_amount' => round($todayTxns->amt, 2),
            'accounts'                  => $allAccounts,
        ]);
    }

    private function buildBankCashRows($parentId, $type)
    {
        $children = ChartOfAccount::where('parent_id', $parentId)
            ->get(['id', 'account_name', 'account_code', 'status', 'opening_balance', 'balance_type']);

        if ($children->isEmpty()) {
            $parentAcc = ChartOfAccount::find($parentId);
            if ($parentAcc) {
                $children = collect([$parentAcc]);
            }
        }

        return $children->map(function ($acc) use ($type) {
            $ids = getOldAccount(0, $acc->id)->pluck('id')->toArray();

            $sum = AccountTransaction::whereIn('account_id', $ids)
                ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
                ->first();

            $txnBalance = $sum->total_debit - $sum->total_credit;


            $opening = $acc->balance_type === 'credit'
                ? -1 * $acc->opening_balance
                : $acc->opening_balance;

            return [
                'id'             => $acc->id,
                'name'           => $acc->account_name,
                'account_number' => $acc->account_code ?? '-',
                'type'           => $type,
                'status'         => $acc->status,
                'balance'        => round($opening + $txnBalance, 2),
            ];
        });
    }

    // private function buildBankCashRows($parentId, $type)
    // {
    //     $children = ChartOfAccount::where('parent_id', $parentId)
    //         ->get(['id', 'account_name', 'account_code', 'status']);

    //     return $children->map(function ($acc) use ($type) {
    //         $ids = getOldAccount(0, $acc->id)->pluck('id')->toArray();

    //         $sum = AccountTransaction::whereIn('account_id', $ids)
    //             ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
    //             ->first();

    //         return [
    //             'id'             => $acc->id,
    //             'name'           => $acc->account_name,
    //             'account_number' => $acc->account_code ?? '-',
    //             'type'           => $type,
    //             'status'         => $acc->status,
    //             'balance'        => round($sum->total_debit - $sum->total_credit, 2),
    //         ];
    //     });
    // }


    // Range filter apply hoy na -- eta nijer alada range-selector (This Year/Last Year/Last 6 Months) niye rakhe
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
    public function expenseBreakdown(Request $request)
    {
        $range = $request->input('range');

        if ($range) {

            ['from' => $fromDate, 'to' => $toDate] = $this->resolveRange($range);
            $from = $fromDate->format('Y-m-d');
            $to   = $toDate->format('Y-m-d');
        } else {
            // range na thakle purono from_date/to_date behavior (backward-compatible)
            $from = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $to   = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        }

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
            $query->where(function ($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%");
            });
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
                    ?? '-',
                'amount'    => $amount ?? '',
                'date'      => $t->created_at->format('d M Y'),
            ];
        });

        return response()->json($data);
    }


    public function invoices(Request $request)
    {
        return response()->json([]);
    }
}
