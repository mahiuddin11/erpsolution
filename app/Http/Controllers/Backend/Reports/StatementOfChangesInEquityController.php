<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatementOfChangesInEquityController extends Controller
{
    public function changesInEquity(Request $request)
    {
        $title       = 'Statement of Changes in Equity';
        $companyInfo = Company::first();

        $from_date = $request->from_date;
        $to_date   = $request->to_date;

        if ($request->method() != 'POST' || !$from_date) {
            return view('backend.pages.reports.soce', get_defined_vars());
        }

        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        $equityMap = [
            'share_capital'       => ['label' => 'Share Capital',       'account_id' => 11],
            'reserve_and_surplus' => ['label' => 'Reserve and Surplus', 'account_id' => 12],
        ];
        $equityRootId  = 10;
        $incomeRootId  = 17;
        $expenseRootId = 21;

        $components   = [];
        $openingTotal = 0;
        $closingTotal = 0;

        foreach ($equityMap as $key => $meta) {
            $treeIds = $this->collectAccountTree($meta['account_id']);

            $opening = $this->getAccountTreeBalanceAsOf($treeIds, $from_date, false);
            $closing = $this->getAccountTreeBalanceAsOf($treeIds, $to_date, true);

            $components[$key] = [
                'label'   => $meta['label'],
                'opening' => $opening,
                'closing' => $closing,
            ];

            $openingTotal += $opening;
            $closingTotal += $closing;
        }

        // ============================================================
        // Movement rows = ACTUAL posted transactions in the Reserve and
        // Surplus tree during the period (e.g. JV05000 profit transfer).
        // This replaces the disconnected accrual figure so that
        // Opening + Movements = Closing always holds true.
        // Added: 2026-07-11
        // ============================================================
        $reserveTreeIds  = $this->collectAccountTree($equityMap['reserve_and_surplus']['account_id']);
        $actualMovements = $this->getYearlyProfitLossRows($reserveTreeIds, $from_date, $to_date); // ← function বদলানো হলো

        $movementRows = [];
        foreach ($actualMovements as $m) {
            $movementRows[] = [
                'label'               => $m['label'],
                'share_capital'       => 0,
                'reserve_and_surplus' => $m['amount'],
            ];
        }

        // Additional Share Capital injected during the period
        $shareCapitalTreeIds = $this->collectAccountTree($equityMap['share_capital']['account_id']);
        $capitalMovement     = $this->getAccountTreeMovement($shareCapitalTreeIds, $from_date, $to_date);

        if ($capitalMovement != 0) {
            $movementRows[] = [
                'label'               => 'Additional Capital Injected',
                'share_capital'       => $capitalMovement,
                'reserve_and_surplus' => 0,
            ];
        }

        // ============================================================
        // Memo only — accrued (unposted) Net Profit for the period,
        // NOT included in the movement rows / table math above.
        // Shown separately in the view as a footnote.
        // Added: 2026-07-11
        // ============================================================
        $accruedNetProfit = $this->calculateNetProfit($from_date, $to_date, $incomeRootId, $expenseRootId);

        // Reconciliation against Balance Sheet Equity section (id=10 tree)
        $balanceSheetEquityTreeIds = $this->collectAccountTree($equityRootId);
        $balanceSheetEquityTotal   = $this->getAccountTreeBalanceAsOf($balanceSheetEquityTreeIds, $to_date, true);
        $reconDifference           = $closingTotal - $balanceSheetEquityTotal;

        return view('backend.pages.reports.soce', get_defined_vars());
    }

    private function collectAccountTree($rootId)
    {
        $ids = [$rootId];
        $children = ChartOfAccount::where('parent_id', $rootId)->pluck('id');

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->collectAccountTree($childId));
        }

        return $ids;
    }

    /**
     * STOCK balance as of a given date — account.opening_balance + all
     * transactions up to that date, direction resolved per balance_type.
     */
    private function getAccountTreeBalanceAsOf(array $accountIds, $toDate, $inclusive = false)
    {
        $accounts = ChartOfAccount::whereIn('id', $accountIds)->get(['id', 'opening_balance', 'balance_type']);

        $total = 0.0;
        $operator = $inclusive ? '<=' : '<';

        foreach ($accounts as $acc) {
            $row = DB::table('account_transactions')
                ->where('account_id', $acc->id)
                ->whereDate('created_at', $operator, $toDate)
                ->selectRaw('COALESCE(SUM(debit),0) as d, COALESCE(SUM(credit),0) as c')
                ->first();

            $debit   = (float) $row->d;
            $credit  = (float) $row->c;
            $opening = (float) ($acc->opening_balance ?? 0);

            $balance = ($acc->balance_type === 'debit')
                ? ($opening + $debit - $credit)
                : ($opening + $credit - $debit);

            $total += $balance;
        }

        return $total;
    }

    /**
     * FLOW (movement only, no opening_balance) within a date range.
     */
    private function getAccountTreeMovement(array $accountIds, $fromDate, $toDate)
    {
        $accounts = ChartOfAccount::whereIn('id', $accountIds)->get(['id', 'balance_type']);

        $total = 0.0;

        foreach ($accounts as $acc) {
            $row = DB::table('account_transactions')
                ->where('account_id', $acc->id)
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate)
                ->selectRaw('COALESCE(SUM(debit),0) as d, COALESCE(SUM(credit),0) as c')
                ->first();

            $debit  = (float) $row->d;
            $credit = (float) $row->c;

            $balance = ($acc->balance_type === 'debit')
                ? ($debit - $credit)
                : ($credit - $debit);

            $total += $balance;
        }

        return $total;
    }

    /**
     * Actual posted transactions grouped by voucher, within the period,
     * for a given account tree. Each distinct voucher becomes its own
     * labeled movement row — this IS the true equity movement.
     */
    private function getYearlyProfitLossRows(array $accountIds, $fromDate, $toDate)
    {
        $rows = DB::table('account_transactions')
            ->whereIn('account_id', $accountIds)
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->select('invoice', 'remark', 'credit', 'debit', 'created_at')
            ->orderBy('created_at')
            ->get();

        $yearly = []; // [fiscal_year => net_amount]

        foreach ($rows as $row) {
            $net = (float) $row->credit - (float) $row->debit;

            if ($net == 0) {
                continue;
            }

            $postingDate = \Carbon\Carbon::parse($row->created_at);

            // Closing JV posted on Jan 1 represents the PRIOR year's result.
            // If posted exactly on Jan 1, represented year = posting year - 1.
            // (If posted mid-year for some other reason, treat as current year.)
            $representedYear = ($postingDate->month == 1 && $postingDate->day == 1)
                ? $postingDate->year - 1
                : $postingDate->year;

            if (!isset($yearly[$representedYear])) {
                $yearly[$representedYear] = 0;
            }

            $yearly[$representedYear] += $net;
        }

        ksort($yearly); // chronological order

        $movements = [];
        foreach ($yearly as $year => $amount) {
            $label = $amount >= 0
                ? "Profit for Fiscal Year {$year}"
                : "Loss for Fiscal Year {$year}";

            $movements[] = [
                'label'  => $label,
                'amount' => $amount,
                'year'   => $year,
            ];
        }

        return $movements;
    }

    /**
     * Memo-only accrued Net Profit (Income - Expense) for the period.
     * NOT posted to the ledger yet — used purely as an informational
     * footnote in the view, separate from the actual movement rows.
     */
    private function calculateNetProfit($from_date, $to_date, $incomeRootId, $expenseRootId)
    {
        $incomeTreeIds  = $this->collectAccountTree($incomeRootId);
        $expenseTreeIds = $this->collectAccountTree($expenseRootId);

        $totalIncome  = $this->getAccountTreeMovement($incomeTreeIds, $from_date, $to_date);
        $totalExpense = $this->getAccountTreeMovement($expenseTreeIds, $from_date, $to_date);

        return $totalIncome - $totalExpense;
    }
}
