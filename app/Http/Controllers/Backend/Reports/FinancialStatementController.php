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

        $year_end_date = $request->year_end_date;

        if ($request->method() != 'POST' || !$year_end_date) {
            return view('backend.pages.reports.FinancialStatement', get_defined_vars());
        }

        $request->validate([
            'year_end_date' => 'required|date',
        ]);

        // ============================================================
        // Derive current FY and comparative prior FY from the single
        // reporting date. Current FY = Jan 1 .. year_end_date (in case
        // year_end_date isn't exactly Dec 31, e.g. interim reporting).
        // Prior FY = the full calendar year immediately before.
        // ============================================================
        $currentYearEnd   = Carbon::parse($year_end_date)->endOfDay();
        $currentYearStart = $currentYearEnd->copy()->startOfYear();
        $priorYearEnd     = $currentYearStart->copy()->subDay()->endOfDay();
        $priorYearStart   = $priorYearEnd->copy()->startOfYear();

        // kept for the Blade header/date labels
        $from_date = $currentYearStart->toDateString();
        $to_date   = $currentYearEnd->toDateString();

        // ============================================================
        // CONFIG — confirmed against WTBL Chart of Accounts (2026-07-12)
        // ============================================================
        $fixedAssetRootId    = 2;   // Fixed Asset tree root
        $inventoryRootId     = null; // TODO: still unresolved — no dedicated Inventory account found
        $receivableRootId    = 5;   // Accounts Receivable
        $payableRootId       = 16;  // Accounts Payable
        $cashBankRootId      = 6;   // Cash and Cash Equivalents (parent of Cash in Hand id=7, Cash at Bank id=8)
        $shareCapitalId      = 11;
        $reserveSurplusId    = 12;
        $incomeRootId        = 17;
        $expenseRootId       = 21;

        // ============================================================
        // Note: Property, Plant & Equipment — current FY schedule,
        // plus a Prior Year Closing comparative figure.
        // ============================================================
        $fixedAssetSchedule = $this->buildFixedAssetSchedule(
            $fixedAssetRootId,
            $currentYearStart->toDateString(),
            $currentYearEnd->toDateString()
        );
        $fixedAssetPriorYearClosing = $this->getAccountTreeBalanceAsOf(
            $this->collectAccountTree($fixedAssetRootId),
            $priorYearEnd->toDateString(),
            true
        );

        // ============================================================
        // Note: Reserve and Surplus — Current FY | Prior FY columns
        // ============================================================
        $reserveTreeIds = $this->collectAccountTree($reserveSurplusId);

        $reserveCurrent = [
            'opening'  => $this->getAccountTreeBalanceAsOf($reserveTreeIds, $priorYearEnd->toDateString(), true),
            'movement' => $this->getAccountTreeMovement($reserveTreeIds, $currentYearStart->toDateString(), $currentYearEnd->toDateString()),
            'closing'  => $this->getAccountTreeBalanceAsOf($reserveTreeIds, $currentYearEnd->toDateString(), true),
            // FIX (2026-07-13): the "movement" queried within calendar year
            // $currentYearEnd->year is actually the closing JV that
            // REPRESENTS the prior fiscal year (posted Jan 1). Store which
            // FY it represents so the view can label it correctly instead
            // of implying it's this year's own result.
            'representedFy' => $currentYearStart->year - 1,
        ];
        $reservePrior = [
            'opening'  => $this->getAccountTreeBalanceAsOf($reserveTreeIds, $priorYearStart->copy()->subDay()->toDateString(), true),
            'movement' => $this->getAccountTreeMovement($reserveTreeIds, $priorYearStart->toDateString(), $priorYearEnd->toDateString()),
            'closing'  => $this->getAccountTreeBalanceAsOf($reserveTreeIds, $priorYearEnd->toDateString(), true),
            'representedFy' => $priorYearStart->year - 1,
        ];

        // FLAG: because closing JVs post on Jan 1 of the FOLLOWING year, the
        // "movement" shown for the year ended $currentYearEnd actually
        // represents the PRIOR fiscal year's result (posted on this year's
        // Jan 1). This year's own closing JV won't post until next Jan 1 —
        // that is expected, not an error, but worth disclosing so it isn't
        // mistaken for a missing entry.
        $reserveClosingLagNote = true;

        $reserveMovementsCurrentRaw = $this->getYearlyProfitLossRows($reserveTreeIds, $currentYearStart->toDateString(), $currentYearEnd->toDateString());
        $representedFyForCurrent = $currentYearStart->year - 1;
        $reserveMissingClosingForPriorFy = !collect($reserveMovementsCurrentRaw)->pluck('year')->contains($representedFyForCurrent);

        // ============================================================
        // Note: Share Capital — Current Year-End | Prior Year-End
        // ============================================================
        $shareCapitalTreeIds = $this->collectAccountTree($shareCapitalId);
        $shareCapitalCurrent = $this->getAccountTreeBalanceAsOf($shareCapitalTreeIds, $currentYearEnd->toDateString(), true);
        $shareCapitalPrior   = $this->getAccountTreeBalanceAsOf($shareCapitalTreeIds, $priorYearEnd->toDateString(), true);

        // FLAG (2026-07-14): Share Capital showing zero in both years is
        // unusual for an operating company with paid-up capital on record.
        // Flagged so it doesn't silently render as a blank "-" with no
        // explanation — either the account ID needs re-confirming or the
        // capital was posted under a different account.
        $shareCapitalMissing = ($shareCapitalCurrent == 0) && ($shareCapitalPrior == 0);

        // ============================================================
        // Note: Revenue — Current FY Total | Prior FY Total
        // ============================================================
        $incomeTreeIds = $this->collectAccountTree($incomeRootId);
        $revenueCurrent = $this->getAccountTreeMovement($incomeTreeIds, $currentYearStart->toDateString(), $currentYearEnd->toDateString());
        $revenuePrior   = $this->getAccountTreeMovement($incomeTreeIds, $priorYearStart->toDateString(), $priorYearEnd->toDateString());

        // ============================================================
        // Note: Accounts Receivable Ageing — current year-end snapshot
        // only (industry norm: ageing is a point-in-time snapshot, not
        // typically shown with a prior-year comparative column).
        // ============================================================
        $receivableAgeing = $receivableRootId
            ? $this->buildAgeingSchedule($receivableRootId, $currentYearEnd->toDateString(), 'debit')
            : null;

        // FLAG (2026-07-14): Receivable is debit-normal (asset). If the
        // total nets negative, the tree has slipped into an overall
        // credit position — e.g. customer overpayments, misapplied credit
        // memos, or a genuine data issue. Flagged the same way Note 6
        // (Payable) already flags its abnormal sign, so this doesn't sit
        // unexplained in the note.
        $receivableTotal = $receivableAgeing ? array_sum($receivableAgeing) : null;
        $receivableAbnormalSign = !is_null($receivableTotal) && $receivableTotal < 0;

        // ============================================================
        // Note: Accounts Payable — Current Year-End | Prior Year-End
        // ============================================================
        $payableBalanceCurrent = $payableRootId
            ? $this->getAccountTreeBalanceAsOf($this->collectAccountTree($payableRootId), $currentYearEnd->toDateString(), true)
            : null;
        $payableBalancePrior = $payableRootId
            ? $this->getAccountTreeBalanceAsOf($this->collectAccountTree($payableRootId), $priorYearEnd->toDateString(), true)
            : null;
        $payableAbnormalSignCurrent = !is_null($payableBalanceCurrent) && $payableBalanceCurrent < 0;
        $payableAbnormalSignPrior   = !is_null($payableBalancePrior) && $payableBalancePrior < 0;
        // kept for backward compatibility with anything referencing the old single-year flag
        $payableAbnormalSign = $payableAbnormalSignCurrent;

        // ============================================================
        // Note: Cash and Bank Balances — Current Year-End | Prior Year-End
        // per line item.
        // ============================================================
        $cashBankBreakdown = $cashBankRootId
            ? $this->buildLeafBreakdownComparative($cashBankRootId, $currentYearEnd->toDateString(), $priorYearEnd->toDateString(), [7, 8])
            : null;

        // FLAG (2026-07-13): Cash in Hand (id=7) going negative in either
        // year is physically impossible — unlike Cash at Bank, which can
        // legitimately go negative under an overdraft facility. Detected
        // here by account id rather than by sign-only heuristics on the
        // breakdown array, so it survives even if the Chart of Accounts
        // ordering changes.
        $cashInHandNegative = false;
        if ($cashBankBreakdown) {
            foreach ($cashBankBreakdown as $item) {
                if ($item['id'] === 7 && ($item['balance'] < 0 || $item['balance_prior'] < 0)) {
                    $cashInHandNegative = true;
                }
            }
        }

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

    /**
     * STOCK balance as of a date — opening_balance + transactions,
     * direction resolved per balance_type. Proven pattern from SOCE.
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
     * Groups Reserve/Retained Earnings movements by the FISCAL YEAR
     * they represent (posting year - 1, since closing JVs post Jan 1).
     * Reused verbatim from the proven SOCE controller.
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

    private function buildFixedAssetSchedule($rootId, $fromDate, $toDate)
    {
        $categories = ChartOfAccount::where('parent_id', $rootId)->get(['id', 'account_name']);

        $schedule = [];
        $totals = ['opening' => 0, 'addition' => 0, 'disposal' => 0, 'closing' => 0];
        $flaggedRows = [];
        $resolvedRows = [];

        foreach ($categories as $cat) {
            $treeIds = $this->collectAccountTree($cat->id);

            $opening = $this->getAccountTreeBalanceAsOf($treeIds, $fromDate, false);
            $closing = $this->getAccountTreeBalanceAsOf($treeIds, $toDate, true);

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
                        $disposal += $net;
                    }
                }
            }

            // FLAG (2026-07-13, widened): flag on negative opening OR
            // negative closing, since a debit-normal asset account should
            // never sit negative at any point.
            //
            // REFINED (2026-07-14): a negative OPENING that has already
            // self-resolved to a non-negative CLOSING within this year
            // (e.g. an asset-under-construction cost transferred out
            // in a prior period, then reconciled with an offsetting entry
            // this year — see Crane Structure case) is a DIFFERENT
            // situation from a balance that is STILL negative at closing.
            // The former is a resolved transitional artifact worth noting
            // but not alarming about; the latter is a live, unresolved
            // data-integrity issue. These are now tracked separately so
            // the banner text doesn't overstate resolved items as if they
            // were still open problems.
            $isUnresolved = $closing < 0;
            $isResolvedTransitional = ($opening < 0) && !$isUnresolved;

            if ($isUnresolved) {
                $flaggedRows[] = $cat->account_name;
            } elseif ($isResolvedTransitional) {
                $resolvedRows[] = $cat->account_name;
            }

            $schedule[] = [
                'label'      => $cat->account_name,
                'opening'    => $opening,
                'addition'   => $addition,
                'disposal'   => $disposal,
                'closing'    => $closing,
                'suspicious' => $isUnresolved,
                'resolved'   => $isResolvedTransitional,
                'negative_closing' => $isUnresolved,
            ];

            $totals['opening']  += $opening;
            $totals['addition'] += $addition;
            $totals['disposal'] += $disposal;
            $totals['closing']  += $closing;
        }

        return [
            'rows'          => $schedule,
            'totals'        => $totals,
            'flaggedRows'   => $flaggedRows,
            'resolvedRows'  => $resolvedRows,
            'totalNegative' => $totals['closing'] < 0,
        ];
    }

    /**
     * Generic ageing schedule (0-30, 31-60, 61-90, 90+) for a receivable
     * or payable tree, based on individual transaction age from $asOfDate.
     */
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

    /**
     * Leaf-level breakdown (e.g. individual bank accounts) under a root,
     * WITH a prior-year comparative balance per line — used for Note 7
     * under Option 2's current/prior year presentation.
     */
    private function buildLeafBreakdownComparative($rootId, $currentAsOf, $priorAsOf, array $expectedChildIds = [])
    {
        $leaves = ChartOfAccount::where('parent_id', $rootId)->get(['id', 'account_name']);

        $result = [];
        foreach ($leaves as $leaf) {
            $treeIds = $this->collectAccountTree($leaf->id);
            $result[] = [
                'id'            => $leaf->id,
                'label'         => $leaf->account_name,
                'balance'       => $this->getAccountTreeBalanceAsOf($treeIds, $currentAsOf, true),
                'balance_prior' => $this->getAccountTreeBalanceAsOf($treeIds, $priorAsOf, true),
                'unexpected'    => !empty($expectedChildIds) && !in_array($leaf->id, $expectedChildIds),
            ];
        }

        return $result;
    }

}
