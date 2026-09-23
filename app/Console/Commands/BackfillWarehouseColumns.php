<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillWarehouseColumns extends Command
{
    protected $signature = 'warehouse:backfill-columns
                            {--dry-run : Show what would happen without changing anything}
                            {--force : Skip the confirmation prompt}';

    protected $description = 'Add warehouse_id + backup_branch_id to listed tables, then backfill: '
        . 'backup_branch_id = original branch_id (never overwritten once set). '
        . 'From backup_branch_id we look up branches: warehouse_id = branches.warehouse_id, '
        . 'and if the row is a warehouse-type branch (parent_id != 0) branch_id = branches.parent_id '
        . '(the real branch). Safe to rerun: it always recalculates from backup_branch_id.';

    protected array $tables = [
        'account_transactions',
        'dabit_vouchers',
        'dabit_voucher_details',
        'credit_vouchers',
        'credit_voucher_details',
        'stocks',
        'product_opening_stocks',
        'product_opening_stock_details',
        'stock_ajdustments',
        'stock_ajdustment_detailsts',
        'stock_summaries',
        'sales',
        'sales__details',
        'sale_returns',
        'sale_return_details',
        'purchases',
        'purchases_details',
        'project_transfers',
        'project_transfer_details',
        'journal_vouchers',
        'journal_voucher_details',
    ];

    /** Real branch id for a joined `branches b` row. */
    private const TARGET_BRANCH = 'IF(COALESCE(b.parent_id, 0) != 0, b.parent_id, b.id)';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if (empty($this->tables)) {
            $this->warn('The $tables list is empty — add table names in the command class and rerun.');
            return self::SUCCESS;
        }

        $this->info($dryRun ? 'DRY RUN — no database changes will be made.' : 'Starting warehouse column backfill...');

        // Pre-flight: bad data in `branches` itself would break the mapping
        $problems = $this->preflight();
        if ($problems > 0 && !$dryRun) {
            $this->error('Fix the branches data issues above and rerun.');
            return self::FAILURE;
        }

        $this->line('Tables to process: ' . implode(', ', $this->tables));
        $this->newLine();

        $plan = [];
        foreach ($this->tables as $tableName) {
            $plan[$tableName] = $this->inspectTable($tableName);
        }

        $this->table(
            ['Table', 'branch_id exists', 'warehouse_id', 'backup_branch_id', 'Rows to backup', 'Rows to fix (branch/warehouse)'],
            collect($plan)->map(function ($p, $table) {
                return [
                    $table,
                    $p['has_branch_id'] ? 'yes' : 'MISSING',
                    $p['has_warehouse_id'] ? 'exists' : 'will add',
                    $p['has_backup_branch_id'] ? 'exists' : 'will add',
                    $p['has_branch_id'] ? $p['rows_to_backup'] : '-',
                    $p['has_branch_id'] ? $p['rows_to_map'] : '-',
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Dry run complete. No changes were made.');
            return self::SUCCESS;
        }

        $processable = array_filter($plan, fn($p) => $p['has_branch_id']);

        if (empty($processable)) {
            $this->warn('No listed table has a branch_id column — nothing to do.');
            return self::SUCCESS;
        }

        if (!$force && !$this->confirm('Apply the changes shown above?')) {
            $this->warn('Aborted — no changes made.');
            return self::SUCCESS;
        }

        foreach ($processable as $tableName => $info) {
            $this->newLine();
            $this->info("Processing {$tableName}...");

            try {
                $this->ensureColumns($tableName, $info);
                $this->backfill($tableName);
                $this->info("Done: {$tableName}");
            } catch (\Throwable $e) {
                $this->error("Failed on {$tableName}: " . $e->getMessage());
                $this->warn('Earlier tables in this run (if any) already committed and are unaffected. Fix the issue and rerun — the backfill recalculates from backup_branch_id, so rerunning is safe.');
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('All listed tables processed successfully.');

        return self::SUCCESS;
    }

    /**
     * Checks the `branches` table for data that would make the mapping wrong.
     * Returns the number of problems found.
     */
    private function preflight(): int
    {
        $problems = 0;

        // branches.warehouse_id pointing to a warehouse that doesn't exist -> FK error later
        $orphanWarehouses = DB::table('branches as b')
            ->leftJoin('warehouses as w', 'w.id', '=', 'b.warehouse_id')
            ->whereNotNull('b.warehouse_id')
            ->whereNull('w.id')
            ->pluck('b.id');

        if ($orphanWarehouses->isNotEmpty()) {
            $problems++;
            $this->error('branches.warehouse_id points to a missing warehouse. branches.id: ' . $orphanWarehouses->implode(', '));
        }

        // parent_id points to a missing branch, or to another warehouse-type branch (nested)
        $badParents = DB::table('branches as b')
            ->leftJoin('branches as p', 'p.id', '=', 'b.parent_id')
            ->whereRaw('COALESCE(b.parent_id, 0) != 0')
            ->where(function ($q) {
                $q->whereNull('p.id')
                    ->orWhereRaw('COALESCE(p.parent_id, 0) != 0');
            })
            ->pluck('b.id');

        if ($badParents->isNotEmpty()) {
            $problems++;
            $this->error('branches.parent_id is missing or points to another warehouse-type branch. branches.id: ' . $badParents->implode(', '));
        }

        return $problems;
    }

    private function inspectTable(string $tableName): array
    {
        $hasBranchId = $this->columnExists($tableName, 'branch_id');
        $hasWarehouseId = $this->columnExists($tableName, 'warehouse_id');
        $hasBackupBranchId = $this->columnExists($tableName, 'backup_branch_id');

        $rowsToBackup = 0;
        $rowsToMap = 0;

        if ($hasBranchId) {

            $backupQuery = DB::table($tableName . ' as t')
                ->join('branches as b', 'b.id', '=', 't.branch_id');

            if ($hasBackupBranchId) {
                $backupQuery->whereNull('t.backup_branch_id');
            }

            $rowsToBackup = $backupQuery->count();

            // Rows to fix: always calculated from the ORIGINAL branch id
            // (backup_branch_id if already there, otherwise the current branch_id)
            $src = $hasBackupBranchId ? 'COALESCE(t.backup_branch_id, t.branch_id)' : 't.branch_id';

            $mapQuery = DB::table($tableName . ' as t')
                ->join('branches as b', DB::raw($src), '=', 'b.id');

            if ($hasWarehouseId) {
                $mapQuery->whereRaw(
                    '(NOT (t.warehouse_id <=> b.warehouse_id) OR NOT (t.branch_id <=> ' . self::TARGET_BRANCH . '))'
                );
            } else {
                // warehouse_id column will be new, so every warehouse-type row changes
                $mapQuery->whereRaw('(COALESCE(b.parent_id, 0) != 0 OR b.warehouse_id IS NOT NULL)');
            }

            $rowsToMap = $mapQuery->count();
        }

        return [
            'has_branch_id'        => $hasBranchId,
            'has_warehouse_id'     => $hasWarehouseId,
            'has_backup_branch_id' => $hasBackupBranchId,
            'rows_to_backup'       => $rowsToBackup,
            'rows_to_map'          => $rowsToMap,
        ];
    }

    private function columnExists(string $table, string $column): bool
    {
        $row = DB::selectOne("
            SELECT COLUMN_NAME
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ", [$table, $column]);

        return (bool) $row;
    }

    private function ensureColumns(string $tableName, array $info): void
    {
        if (!$info['has_warehouse_id']) {
            DB::statement("
                ALTER TABLE `{$tableName}`
                ADD COLUMN `warehouse_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `branch_id`,
                ADD INDEX `idx_{$tableName}_warehouse_id` (`warehouse_id`),
                ADD CONSTRAINT `fk_{$tableName}_warehouse_id`
                    FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`)
                    ON DELETE SET NULL
            ");
            $this->line("  + added warehouse_id (indexed, FK -> warehouses.id)");
        }

        if (!$this->columnExists($tableName, 'backup_branch_id')) {
            DB::statement("
                ALTER TABLE `{$tableName}`
                ADD COLUMN `backup_branch_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `warehouse_id`,
                ADD INDEX `idx_{$tableName}_backup_branch_id` (`backup_branch_id`),
                ADD CONSTRAINT `fk_{$tableName}_backup_branch_id`
                    FOREIGN KEY (`backup_branch_id`) REFERENCES `branches`(`id`)
                    ON DELETE SET NULL
            ");
            $this->line("  + added backup_branch_id (indexed, FK -> branches.id)");
        }
    }

    private function backfill(string $tableName): void
    {
        DB::beginTransaction();

        try {
            // Step 1: preserve the original branch_id — only for rows not yet backed up,
            // and only when branch_id is a VALID branches.id. Corrupted values are
            // skipped (backup_branch_id stays NULL) so they don't violate the FK
            // and can be fixed manually later. Existing backups are never overwritten.
            $backedUp = DB::update("
                UPDATE `{$tableName}` t
                INNER JOIN `branches` b ON b.id = t.branch_id
                SET t.backup_branch_id = t.branch_id
                WHERE t.backup_branch_id IS NULL
            ");
            $this->line("  backed up branch_id on {$backedUp} row(s)");

            $skipped = DB::table($tableName . ' as t')
                ->leftJoin('branches as b', 'b.id', '=', 't.branch_id')
                ->whereNull('b.id')
                ->whereNull('t.backup_branch_id')
                ->count();

            if ($skipped > 0) {
                $this->warn("  skipped {$skipped} row(s) with invalid/corrupted branch_id (no matching branches.id) — left untouched for manual fix");
            }

            // Step 2: always calculate from the ORIGINAL branch id (backup_branch_id).
            //   warehouse_id = branches.warehouse_id
            //   branch_id    = branches.parent_id if the row is warehouse-type
            //                  (parent_id != 0), otherwise the branch itself.
            // Only rows whose values would actually change are updated, so this also
            // repairs rows written wrongly by the earlier version, and is safe to rerun.
            $target = self::TARGET_BRANCH;

            $mapped = DB::update("
                UPDATE `{$tableName}` t
                INNER JOIN `branches` b ON b.id = t.backup_branch_id
                SET t.warehouse_id = b.warehouse_id,
                    t.branch_id = {$target}
                WHERE NOT (t.warehouse_id <=> b.warehouse_id)
                   OR NOT (t.branch_id <=> {$target})
            ");
            $this->line("  fixed warehouse_id/branch_id on {$mapped} row(s)");

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
