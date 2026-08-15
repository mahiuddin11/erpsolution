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
        . 'backup_branch_id = old branch_id. For rows whose branch_id points to a warehouse-type '
        . 'branches row (parent_id != 0) that is linked to a warehouses record, warehouse_id gets '
        . 'set to that warehouse\'s id and branch_id gets rewritten to warehouses.branch_id '
        . '(the real branch).';

    protected array $tables = [
        'stocks',
        'stock_summaries',
        'purchases',
        'purchases_details',
        'sales',
        'sales__details',
        'account_transactions',
        'dabit_vouchers',
        'dabit_voucher_details',
        'projects',
        'project_transfers',
        'project_transfer_details',

    ];

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if (empty($this->tables)) {
            $this->warn('The $tables list is empty — add table names in the command class and rerun.');
            return self::SUCCESS;
        }

        $this->info($dryRun ? 'DRY RUN — no database changes will be made.' : 'Starting warehouse column backfill...');
        $this->line('Tables to process: ' . implode(', ', $this->tables));
        $this->newLine();

        $plan = [];
        foreach ($this->tables as $tableName) {
            $plan[$tableName] = $this->inspectTable($tableName);
        }

        $this->table(
            ['Table', 'branch_id exists', 'warehouse_id', 'backup_branch_id', 'Rows to backup', 'Rows -> warehouse mapped'],
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
                $this->warn('Earlier tables in this run (if any) already committed and are unaffected. Fix the issue and rerun — already-processed rows/columns will be skipped automatically.');
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('All listed tables processed successfully.');

        return self::SUCCESS;
    }

    private function inspectTable(string $tableName): array
    {
        $hasBranchId = $this->columnExists($tableName, 'branch_id');
        $hasWarehouseId = $this->columnExists($tableName, 'warehouse_id');
        $hasBackupBranchId = $this->columnExists($tableName, 'backup_branch_id');

        $rowsToBackup = 0;
        $rowsToMap = 0;

        if ($hasBranchId) {
            if ($hasBackupBranchId) {
                $rowsToBackup = DB::table($tableName)->whereNull('backup_branch_id')->count();
            } else {
                $rowsToBackup = DB::table($tableName)->count();
            }

            // Chain: t.branch_id -> branches.id (parent_id != 0) -> branches.warehouse_id -> warehouses.id
            $rowsToMap = DB::table($tableName . ' as t')
                ->join('branches as b', function ($join) {
                    $join->on('b.id', '=', 't.branch_id')
                        ->where('b.parent_id', '!=', 0);
                })
                ->join('warehouses as w', 'w.id', '=', 'b.warehouse_id')
                ->when($hasWarehouseId, fn($q) => $q->whereNull('t.warehouse_id'))
                ->count();
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
            // Step 1: preserve branch_id for rows not yet backed up — but only
            // for rows whose branch_id is a VALID branches.id. Rows with a
            // corrupted branch_id (e.g. a project_id mistakenly stored there)
            // are skipped here and left with backup_branch_id = NULL, so they
            // don't violate the FK and can be fixed manually later.
            $backedUp = DB::update("
            UPDATE `{$tableName}` t
            INNER JOIN `branches` b ON b.id = t.branch_id
            SET t.backup_branch_id = t.branch_id
            WHERE t.backup_branch_id IS NULL
        ");
            $this->line("  backed up branch_id on {$backedUp} row(s)");

            // Track + report how many rows were skipped due to invalid branch_id
            $skipped = DB::table($tableName . ' as t')
                ->leftJoin('branches as b', 'b.id', '=', 't.branch_id')
                ->whereNull('b.id')
                ->whereNull('t.backup_branch_id')
                ->count();

            if ($skipped > 0) {
                $this->warn("  skipped {$skipped} row(s) with invalid/corrupted branch_id (no matching branches.id) — left untouched for manual fix");
            }

            // Step 2: chain through branches -> warehouses using the preserved
            // original value, set warehouse_id, rewrite branch_id to the real branch
            $mapped = DB::update("
            UPDATE `{$tableName}` t
            INNER JOIN `branches` b
                ON b.id = t.backup_branch_id
                AND b.parent_id != 0
            INNER JOIN `warehouses` w
                ON w.id = b.warehouse_id
            SET t.warehouse_id = w.id,
                t.branch_id = w.branch_id
            WHERE t.warehouse_id IS NULL
        ");
            $this->line("  mapped warehouse_id + rewrote branch_id on {$mapped} row(s)");

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
