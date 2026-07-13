<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Backend\Chart\ChartController;
use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialStatementController extends Controller
{

    public function notesToFinancialStatements(Request $request)
    {
        $title = 'Notes to the Financial Statements';
        $companyInfo = Company::first();

        $from_date = $request->from_date;
        $to_date   = $request->to_date;

        if ($request->method() != 'POST' || !$from_date) {
            return view('backend.pages.reports.FinancialStatement', get_defined_vars());
        }

        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        // ============================================================
        // CONFIG — confirmed against Chart of Accounts 
        // ============================================================
        $fixedAssetRootId    = 2;         // Fixed Asset tree root
        $inventoryRootId     = null;     // TODO: still unresolved — no dedicated Inventory account found
        $receivableRootId    = 5;       // Accounts Receivable
        $payableRootId       = 16;     // Accounts Payable
        $cashBankRootId      = 6;     // Cash and Cash Equivalents (parent of Cash in Hand id=7, Cash at Bank id=8)
        $shareCapitalId      = 11;
        $reserveSurplusId    = 12;
        $incomeRootId        = 17;
        $expenseRootId       = 21;

        // ============================================================
        // Note: Property, Plant & Equipment 
        // ============================================================
        $fixedAssetSchedule = $this->buildFixedAssetSchedule($fixedAssetRootId, $from_date, $to_date);

        // ============================================================
        // Note: Reserve and Surplus 
        // ============================================================
        $reserveTreeIds     = $this->collectAccountTree($reserveSurplusId);
        $reserveOpening     = $this->getAccountTreeBalanceAsOf($reserveTreeIds, $from_date, false);
        $reserveClosing     = $this->getAccountTreeBalanceAsOf($reserveTreeIds, $to_date, true);
        $reserveMovements   = $this->getYearlyProfitLossRows($reserveTreeIds, $from_date, $to_date);
        $reserveMissingYears = $this->detectMissingClosingYears($reserveMovements, $from_date, $to_date);

        // ============================================================
        // Note: Share Capital
        // ============================================================
        $shareCapitalTreeIds = $this->collectAccountTree($shareCapitalId);
        $shareCapitalBalance = $this->getAccountTreeBalanceAsOf($shareCapitalTreeIds, $to_date, true);

        $incomeTreeIds = $this->collectAccountTree($incomeRootId);
        $revenueByYear = $this->getYearlyMovementRows($incomeTreeIds, $from_date, $to_date);

        // ============================================================
        // Note: Accounts Receivable Ageing 
        // ============================================================
        $receivableAgeing = $receivableRootId
            ? $this->buildAgeingSchedule($receivableRootId, $to_date, 'debit')
            : null;


        $payableBalance = $payableRootId
            ? $this->getAccountTreeBalanceAsOf($this->collectAccountTree($payableRootId), $to_date, true)
            : null;
        $payableAbnormalSign = !is_null($payableBalance) && $payableBalance < 0;


        $cashBankBreakdown = $cashBankRootId
            ? $this->buildLeafBreakdown($cashBankRootId, $to_date, [7, 8])
            : null;

        return view('backend.pages.reports.FinancialStatement', get_defined_vars());
    }

    // ================================================================
    // HELPERS
    // ================================================================

    private function collectAccountTree($rootId)
    {
        $ids = [$rootId];
        $children = ChartOfAccount::where('parent_id', $rootId)->pluck('id');

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->collectAccountTree($childId));
        }

        return $ids;
    }


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


    private function getYearlyProfitLossRows(array $accountIds, $fromDate, $toDate)
    {
        $rows = DB::table('account_transactions')
            ->whereIn('account_id', $accountIds)
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->select('invoice', 'remark', 'credit', 'debit', 'created_at')
            ->orderBy('created_at')
            ->get();

        $yearly = [];

        foreach ($rows as $row) {
            $net = (float) $row->credit - (float) $row->debit;
            if ($net == 0) continue;

            $postingDate = Carbon::parse($row->created_at);
            $representedYear = ($postingDate->month == 1 && $postingDate->day == 1)
                ? $postingDate->year - 1
                : $postingDate->year;

            $yearly[$representedYear] = ($yearly[$representedYear] ?? 0) + $net;
        }

        ksort($yearly);

        $movements = [];
        foreach ($yearly as $year => $amount) {
            $movements[] = [
                'label'  => $amount >= 0 ? "Profit for FY {$year}" : "Loss for FY {$year}",
                'amount' => $amount,
                'year'   => $year,
            ];
        }

        return $movements;
    }

    private function getYearlyMovementRows(array $accountIds, $fromDate, $toDate)
    {
        // balance_type per account so debit/credit direction is resolved correctly
        $accounts = ChartOfAccount::whereIn('id', $accountIds)->get(['id', 'balance_type'])->keyBy('id');

        $rowsWithAccount = DB::table('account_transactions')
            ->whereIn('account_id', $accountIds)
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->select('account_id', 'credit', 'debit', 'created_at')
            ->get();

        $yearly = [];

        foreach ($rowsWithAccount as $row) {
            $acc = $accounts->get($row->account_id);
            $balanceType = $acc->balance_type ?? 'credit';

            $net = ($balanceType === 'debit')
                ? ((float) $row->debit - (float) $row->credit)
                : ((float) $row->credit - (float) $row->debit);

            if ($net == 0) continue;

            $year = Carbon::parse($row->created_at)->year;
            $yearly[$year] = ($yearly[$year] ?? 0) + $net;
        }

        ksort($yearly);
        return array_map(fn($v) => round($v), $yearly); // ['2023' => 1234, '2024' => ...]
    }


    private function detectMissingClosingYears(array $movements, $fromDate, $toDate)
    {
        $coveredYears = collect($movements)->pluck('year')->all();

        $startYear = Carbon::parse($fromDate)->year;
        $lastCompletableYear = Carbon::parse($toDate)->year - 1;

        // If the range itself starts and ends within the same year,
        // there's no completed fiscal year to expect a closing JV for.
        if ($lastCompletableYear < $startYear) {
            return [];
        }

        $missing = [];
        for ($year = $startYear; $year <= $lastCompletableYear; $year++) {
            if (!in_array($year, $coveredYears)) {
                $missing[] = $year;
            }
        }

        return $missing;
    }

    private function buildFixedAssetSchedule($rootId, $fromDate, $toDate)
    {
        $categories = ChartOfAccount::where('parent_id', $rootId)->get(['id', 'account_name']);

        $schedule = [];
        $totals = ['opening' => 0, 'addition' => 0, 'disposal' => 0, 'closing' => 0];
        $flaggedRows = [];

        foreach ($categories as $cat) {
            $treeIds = $this->collectAccountTree($cat->id);

            $opening = $this->getAccountTreeBalanceAsOf($treeIds, $fromDate, false);
            $closing = $this->getAccountTreeBalanceAsOf($treeIds, $toDate, true);

            // Split movement into positive (addition) and negative (disposal/depreciation)
            $accounts = ChartOfAccount::whereIn('id', $treeIds)->get(['id', 'balance_type']);
            $addition = 0;
            $disposal = 0;

            foreach ($accounts as $acc) {
                $rows = DB::table('account_transactions')
                    ->where('account_id', $acc->id)
                    ->whereDate('created_at', '>=', $fromDate)
                    ->whereDate('created_at', '<=', $toDate)
                    ->select('debit', 'credit')
                    ->get();

                foreach ($rows as $r) {
                    $net = ($acc->balance_type === 'debit')
                        ? ((float) $r->debit - (float) $r->credit)
                        : ((float) $r->credit - (float) $r->debit);

                    if ($net >= 0) {
                        $addition += $net;
                    } else {
                        $disposal += $net; // negative
                    }
                }
            }


            $isSuspicious = ($opening == 0 && $addition == 0 && $disposal < 0);
            if ($isSuspicious) {
                $flaggedRows[] = $cat->account_name;
            }

            $schedule[] = [
                'label'      => $cat->account_name,
                'opening'    => $opening,
                'addition'   => $addition,
                'disposal'   => $disposal,
                'closing'    => $closing,
                'suspicious' => $isSuspicious,
                'negative_closing' => $closing < 0,
            ];

            $totals['opening']  += $opening;
            $totals['addition'] += $addition;
            $totals['disposal'] += $disposal;
            $totals['closing']  += $closing;
        }

        return [
            'rows'         => $schedule,
            'totals'       => $totals,
            'flaggedRows'  => $flaggedRows,
            'totalNegative' => $totals['closing'] < 0,
        ];
    }

    private function buildAgeingSchedule($rootId, $asOfDate, $normalSide = 'debit')
    {
        $treeIds = $this->collectAccountTree($rootId);
        $asOf = Carbon::parse($asOfDate);

        $buckets = ['0-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0];

        $rows = DB::table('account_transactions')
            ->whereIn('account_id', $treeIds)
            ->whereDate('created_at', '<=', $asOfDate)
            ->select('debit', 'credit', 'created_at')
            ->get();

        foreach ($rows as $r) {
            $net = $normalSide === 'debit'
                ? ((float) $r->debit - (float) $r->credit)
                : ((float) $r->credit - (float) $r->debit);

            if ($net == 0) continue;

            $age = Carbon::parse($r->created_at)->diffInDays($asOf);

            if ($age <= 30) $buckets['0-30'] += $net;
            elseif ($age <= 60) $buckets['31-60'] += $net;
            elseif ($age <= 90) $buckets['61-90'] += $net;
            else $buckets['90+'] += $net;
        }

        return $buckets;
    }


    private function buildLeafBreakdown($rootId, $asOfDate, array $expectedChildIds = [])
    {
        $leaves = ChartOfAccount::where('parent_id', $rootId)->get(['id', 'account_name']);

        $result = [];
        foreach ($leaves as $leaf) {
            $treeIds = $this->collectAccountTree($leaf->id);
            $result[] = [
                'label'      => $leaf->account_name,
                'balance'    => $this->getAccountTreeBalanceAsOf($treeIds, $asOfDate, true),
                'unexpected' => !empty($expectedChildIds) && !in_array($leaf->id, $expectedChildIds),
            ];
        }

        return $result;
    }
}
