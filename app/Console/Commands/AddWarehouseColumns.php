<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddWarehouseColumns extends Command
{
    protected $signature = 'warehouse:add-columns
                            {--dry-run : Show what would happen without changing anything}
                            {--force : Skip the confirmation prompt}';

    protected $description = 'SCHEMA-ONLY: Add warehouse_id + backup_branch_id columns to the '
        . 'listed tables (if missing). Does NOT touch or update any existing row data — '
        . 'use warehouse:backfill-columns separately to populate these columns.';

    protected array $tables = [
        'stocks',
        'stock_summaries',
        // 'purchases',
        // 'purchases_details',
        // 'sales',
        // 'sales__details',
        // 'account_transactions',
        // 'dabit_vouchers',
        // 'dabit_voucher_details',
        // 'projects',
        // 'project_transfers',
        // 'project_transfer_details',
    ];

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if (empty($this->tables)) {
            $this->warn('The $tables list is empty — add table names in the command class and rerun.');
            return self::SUCCESS;
        }

        $this->info($dryRun ? 'DRY RUN — no database changes will be made.' : 'Adding warehouse columns (schema only, no data updates)...');
        $this->line('Tables to process: ' . implode(', ', $this->tables));
        $this->newLine();

        $plan = [];
        foreach ($this->tables as $tableName) {
            $plan[$tableName] = $this->inspectTable($tableName);
        }

        $this->table(
            ['Table', 'exists', 'warehouse_id', 'backup_branch_id'],
            collect($plan)->map(function ($p, $table) {
                return [
                    $table,
                    $p['table_exists'] ? 'yes' : 'MISSING TABLE',
                    !$p['table_exists'] ? '-' : ($p['has_warehouse_id'] ? 'exists' : 'will add'),
                    !$p['table_exists'] ? '-' : ($p['has_backup_branch_id'] ? 'exists' : 'will add'),
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Dry run complete. No changes were made.');
            return self::SUCCESS;
        }

        $processable = array_filter($plan, fn($p) => $p['table_exists']);

        if (empty($processable)) {
            $this->warn('None of the listed tables exist — nothing to do.');
            return self::SUCCESS;
        }

        $needsWork = array_filter($processable, fn($p) => !$p['has_warehouse_id'] || !$p['has_backup_branch_id']);

        if (empty($needsWork)) {
            $this->info('All listed tables already have both columns. Nothing to do.');
            return self::SUCCESS;
        }

        if (!$force && !$this->confirm('Add the missing columns shown above? (No data will be changed)')) {
            $this->warn('Aborted — no changes made.');
            return self::SUCCESS;
        }

        foreach ($processable as $tableName => $info) {
            $this->newLine();
            $this->info("Processing {$tableName}...");

            try {
                $this->ensureColumns($tableName, $info);
                $this->info("Done: {$tableName}");
            } catch (\Throwable $e) {
                $this->error("Failed on {$tableName}: " . $e->getMessage());
                $this->warn('Earlier tables in this run (if any) already committed and are unaffected. Fix the issue and rerun — already-added columns will be skipped automatically.');
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('All listed tables processed successfully. No row data was modified.');

        return self::SUCCESS;
    }

    private function inspectTable(string $tableName): array
    {
        $tableExists = $this->tableExists($tableName);

        return [
            'table_exists'          => $tableExists,
            'has_warehouse_id'      => $tableExists && $this->columnExists($tableName, 'warehouse_id'),
            'has_backup_branch_id'  => $tableExists && $this->columnExists($tableName, 'backup_branch_id'),
        ];
    }

    private function tableExists(string $table): bool
    {
        $row = DB::selectOne("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
        ", [$table]);

        return (bool) $row;
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

    /**
     * DDL ONLY — adds missing columns as NULL. No UPDATE statements anywhere
     * in this class; existing row data is never touched.
     */
    private function ensureColumns(string $tableName, array $info): void
    {
        if (!$info['has_warehouse_id']) {
            $branchIdExists = $this->columnExists($tableName, 'branch_id');

            DB::statement("
                ALTER TABLE `{$tableName}`
                ADD COLUMN `warehouse_id` BIGINT UNSIGNED NULL DEFAULT NULL"
                . ($branchIdExists ? " AFTER `branch_id`" : "") . ",
                ADD INDEX `idx_{$tableName}_warehouse_id` (`warehouse_id`),
                ADD CONSTRAINT `fk_{$tableName}_warehouse_id`
                    FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`)
                    ON DELETE SET NULL
            ");
            $this->line("  + added warehouse_id (indexed, FK -> warehouses.id)");
        } else {
            $this->line("  - warehouse_id already exists, skipped");
        }

        if (!$this->columnExists($tableName, 'backup_branch_id')) {
            $warehouseIdExists = $this->columnExists($tableName, 'warehouse_id');

            DB::statement("
                ALTER TABLE `{$tableName}`
                ADD COLUMN `backup_branch_id` BIGINT UNSIGNED NULL DEFAULT NULL"
                . ($warehouseIdExists ? " AFTER `warehouse_id`" : "") . ",
                ADD INDEX `idx_{$tableName}_backup_branch_id` (`backup_branch_id`)
            ");
            $this->line("  + added backup_branch_id (indexed, no FK — may hold corrupted/legacy values by design)");
        } else {
            $this->line("  - backup_branch_id already exists, skipped");
        }
    }
}
