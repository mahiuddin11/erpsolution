<?php

namespace App\Console\Commands;

use App\Models\ProductOpeningStockDetails;
use App\Models\PurchasesDetails;
use App\Models\sales_Details;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * php artisan stock:diagnose {product_id} {--branch=all} {--from=2000-01-01} {--to=2100-01-01}
 *
 * Compares the "stocks" table (used by stock() and stocksummery() reports)
 * against the actual source tables (used by getProductLedgerData()) for a
 * single product, and prints where the two disagree.
 *
 * NOTE: model namespaces/table names below are guessed to match the code
 * you pasted (App\Models\Stock, App\Models\PurchasesDetails, etc). Adjust
 * the `use` statements at the top if your actual namespaces differ.
 */
class DiagnoseStockMismatch extends Command
{
    protected $signature = 'stock:diagnose
                            {product_id : The product to check}
                            {--branch=all : Branch id, or "all"}
                            {--from=2000-01-01 : From date (Y-m-d)}
                            {--to=2100-01-01 : To date (Y-m-d)}';

    protected $description = 'Compare stocks table totals vs source-table (ledger) totals for a product';

    public function handle()
    {
        $productId = $this->argument('product_id');
        $branchId  = $this->option('branch');
        $from      = $this->option('from');
        $to        = $this->option('to');
        $isAllBranch = ($branchId === 'all' || empty($branchId));

        $this->info("Diagnosing product_id={$productId}  branch=" . ($isAllBranch ? 'ALL' : $branchId) . "  {$from} to {$to}");
        $this->newLine();

        // ==========================================================
        // 1. SOURCE-TABLE TOTALS (the "truth", same tables getProductLedgerData() reads)
        // ==========================================================
        $source = [];

        // Opening stock (no date filter, matches getProductLedgerData behaviour)
        $source['Opening Stock'] = [
            'in'  => ProductOpeningStockDetails::where('product_id', $productId)
                ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branchId))
                ->whereNull('deleted_at')
                ->sum('quantity'),
            'out' => 0,
        ];

        // Purchases (all treated as IN; plus synthetic Project-Manual-Consume OUT)
        $purchaseRows = PurchasesDetails::with('purchase:id,type,purchase_type')
            ->where('product_id', $productId)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('date', [$from, $to])
            ->get();

        $purchaseIn = 0;
        $projectManualConsumeOut = 0;
        foreach ($purchaseRows as $row) {
            $purchaseIn += (int) $row->quantity;
            $isProjectManual = (
                optional($row->purchase)->type === 'Project' &&
                optional($row->purchase)->purchase_type === 'Manual'
            );
            if ($isProjectManual) {
                $projectManualConsumeOut += (int) $row->quantity;
            }
        }
        $source['Purchase'] = ['in' => $purchaseIn, 'out' => 0];
        $source['Project Consume (Manual, synthetic)'] = ['in' => 0, 'out' => $projectManualConsumeOut];

        // Stock adjustments
        $adjRows = DB::table('stock_ajdustment_detailsts as sad')
            ->join('stock_ajdustments as sa', 'sa.id', '=', 'sad.purchases_id')
            ->where('sad.product_id', $productId)
            ->when(!$isAllBranch, fn($q) => $q->where('sad.branch_id', $branchId))
            ->whereNotNull('sad.date')
            ->where('sad.date', '>=', $from)
            ->where('sad.date', '<=', $to)
            ->select('sad.quantity', 'sa.adjustment_type')
            ->get();

        $adjIn = 0;
        $adjOut = 0;
        foreach ($adjRows as $row) {
            $qty = abs((int) $row->quantity);
            if ($row->adjustment_type === 'Gain') {
                $adjIn += $qty;
            } else {
                $adjOut += $qty; // Loss, Damage, Others
            }
        }
        $source['Stock Adjustment'] = ['in' => $adjIn, 'out' => $adjOut];

        // Transfer In / Out
        $source['Transfer In'] = [
            'in' => DB::table('transfer_details')
                ->where('product_id', $productId)
                ->where('status', 'Approved')
                ->when(!$isAllBranch, fn($q) => $q->where('to_branch_id', $branchId))
                ->whereNull('deleted_at')
                ->whereBetween('date', [$from, $to])
                ->sum('approve_qty'),
            'out' => 0,
        ];
        $source['Transfer Out'] = [
            'in' => 0,
            'out' => DB::table('transfer_details')
                ->where('product_id', $productId)
                ->where('status', 'Approved')
                ->when(!$isAllBranch, fn($q) => $q->where('from_branch_id', $branchId))
                ->whereNull('deleted_at')
                ->whereBetween('date', [$from, $to])
                ->sum('approve_qty'),
        ];

        // Sales
        $source['Sale'] = [
            'in' => 0,
            'out' => sales_Details::where('product_id', $productId)
                ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branchId))
                ->whereBetween('date', [$from, $to])
                ->sum('qty'),
        ];

        // Project Transfer Out (Branch -> Project)
        $source['Project Transfer Out'] = [
            'in' => 0,
            'out' => DB::table('project_transfer_details as ptd')
                ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
                ->where('ptd.product_id', $productId)
                ->where('pt.transfer_type', 'branch_to_project')
                ->where('ptd.status', 'Accepted')
                ->when(!$isAllBranch, fn($q) => $q->where('ptd.branch_id', $branchId))
                ->whereBetween('pt.order_date', [$from, $to])
                ->sum('ptd.qty'),
        ];

        // Project Transfer In (Project -> Branch, return)
        $source['Project Transfer In'] = [
            'in' => DB::table('project_transfer_details as ptd')
                ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
                ->where('ptd.product_id', $productId)
                ->where('pt.transfer_type', 'project_to_branch')
                ->where('ptd.status', 'Accepted')
                ->when(!$isAllBranch, fn($q) => $q->where('ptd.branch_id', $branchId))
                ->whereBetween('pt.order_date', [$from, $to])
                ->sum('ptd.qty'),
            'out' => 0,
        ];

        // ==========================================================
        // 2. STOCKS TABLE TOTALS, grouped by raw status string
        //    (this shows us exactly which statuses exist / are missing)
        // ==========================================================
        $stocksRows = Stock::where('product_id', $productId)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('date', [$from, $to]))
            ->select('status', DB::raw('SUM(quantity) as qty'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')
            ->get();

        $stocksummeryIn  = ['Opening', 'Purchase', 'Manual Purchase', 'Production', 'Gain', 'Transfer In', 'Project In', 'Return', 'Purchase Return'];
        $stocksummeryOut = ['Production Sale', 'Production Out', 'Sale', 'Damage', 'Lost', 'Transfer Out', 'Project Out', 'Project Use', 'Sale Return'];
        // stock() positiveStatuses list (note: includes 'Loss' as IN — flagged below)
        $stockControllerIn = ['Opening', 'Purchase', 'Manual Purchase', 'Production', 'Gain', 'Loss', 'Transfer In', 'Project In', 'Return', 'Purchase Return'];

        $this->info('--- Raw status breakdown currently in `stocks` table ---');
        $stocksTableRows = [];
        $unmappedStatuses = [];
        foreach ($stocksRows as $row) {
            $inStocksummery  = in_array($row->status, $stocksummeryIn) ? 'IN' : (in_array($row->status, $stocksummeryOut) ? 'OUT' : 'UNMAPPED');
            $inStockCtrl     = in_array($row->status, $stockControllerIn) ? 'IN' : 'OUT';

            if ($inStocksummery === 'UNMAPPED') {
                $unmappedStatuses[] = $row->status;
            }

            $stocksTableRows[] = [
                $row->status,
                $row->cnt,
                $row->qty,
                $inStocksummery,
                $inStockCtrl,
            ];
        }
        $this->table(
            ['status (raw string)', 'row count', 'qty sum', 'stocksummery() bucket', 'stock() bucket'],
            $stocksTableRows
        );

        if (!empty($unmappedStatuses)) {
            $this->error('UNMAPPED statuses found (silently ignored by stocksummery(), and will fall into the OUT branch by default in stock() since stock() only checks IN membership): ' . implode(', ', array_unique($unmappedStatuses)));
        }


        $this->newLine();
        $this->info('--- Source table (ledger truth) vs `stocks` table, per category ---');


        $stocksInTotal  = 0;
        $stocksOutTotal = 0;
        foreach ($stocksRows as $row) {
            if (in_array($row->status, $stocksummeryIn)) {
                $stocksInTotal += $row->qty;
            } elseif (in_array($row->status, $stocksummeryOut)) {
                $stocksOutTotal += $row->qty;
            }
        }


        $stocksummeryInFixed = array_values(array_diff($stocksummeryIn, ['Project In']));

        $stocksInTotalFixed  = 0;
        $stocksOutTotalFixed = 0;
        foreach ($stocksRows as $row) {
            if (in_array($row->status, $stocksummeryInFixed)) {
                $stocksInTotalFixed += $row->qty;
            } elseif (in_array($row->status, $stocksummeryOut)) {
                $stocksOutTotalFixed += $row->qty;
            }
            // 'Project In' rows: intentionally counted in neither bucket (history-only).
        }

        $sourceInTotal  = 0;
        $sourceOutTotal = 0;
        $compareRows = [];
        foreach ($source as $label => $vals) {
            $sourceInTotal  += $vals['in'];
            $sourceOutTotal += $vals['out'];
            $compareRows[] = [$label, $vals['in'], $vals['out']];
        }
        $this->table(['Source (from real tables)', 'in', 'out'], $compareRows);

        $this->newLine();
        $this->table(
            ['', 'IN total', 'OUT total', 'Net (current stock contribution)'],
            [
                ['Source tables (ledger truth)', $sourceInTotal, $sourceOutTotal, $sourceInTotal - $sourceOutTotal],
                ['`stocks` table — AS-IS (current stock()/stocksummery() logic)', $stocksInTotal, $stocksOutTotal, $stocksInTotal - $stocksOutTotal],
                ['`stocks` table — WITH "Project In" excluded (simulated fix)', $stocksInTotalFixed, $stocksOutTotalFixed, $stocksInTotalFixed - $stocksOutTotalFixed],
                ['DIFFERENCE (AS-IS vs source)', $sourceInTotal - $stocksInTotal, $sourceOutTotal - $stocksOutTotal, ($sourceInTotal - $sourceOutTotal) - ($stocksInTotal - $stocksOutTotal)],
                ['DIFFERENCE (WITH FIX vs source)', $sourceInTotal - $stocksInTotalFixed, $sourceOutTotal - $stocksOutTotalFixed, ($sourceInTotal - $sourceOutTotal) - ($stocksInTotalFixed - $stocksOutTotalFixed)],
            ]
        );

        $this->newLine();
        $this->comment('Interpretation:');
        $this->comment('- If "Project Consume (Manual, synthetic)" out qty > 0 but no matching row exists in `stocks`, that consume is never subtracted in stock()/stocksummery().');
        $this->comment('- Any status shown as UNMAPPED above is invisible to stocksummery() and mis-bucketed in stock().');
        $this->comment('- If a whole category (Adjustment, Project Transfer In/Out) has source qty > 0 but zero matching rows in the `stocks` status breakdown, that module is not writing to `stocks` at all.');

        return 0;
    }
}
