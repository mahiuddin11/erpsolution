<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// >>> NEW
class BackfillTransferWarehouseColumns extends Command
{
    protected $signature = 'warehouse:backfill-transfers
                            {--dry-run : Show what would happen without changing anything}
                            {--force : Skip the confirmation prompt}';

    protected $description = 'Backfill transfers + transfer_details: keep the original from/to branch id in '
        . 'backup_from_branch_id / backup_to_branch_id (never overwritten once set), then set '
        . 'from/to_warehouse_id = branches.warehouse_id and, for warehouse-type branches (parent_id != 0), '
        . 'from/to_branch_id = branches.parent_id. Safe to rerun: always recalculates from the backup columns.';

    protected array $tables = ['transfers', 'transfer_details'];

    protected array $sides = [
        'from' => [
            'branch'    => 'from_branch_id',
            'warehouse' => 'from_warehouse_id',
            'backup'    => 'backup_from_branch_id',
        ],
        'to' => [
            'branch'    => 'to_branch_id',
            'warehouse' => 'to_warehouse_id',
            'backup'    => 'backup_to_branch_id',
        ],
    ];

    /** Real branch id for a joined `branches b` row. */
    private const TARGET_BRANCH = 'IF(COALESCE(b.parent_id, 0) != 0, b.parent_id, b.id)';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $this->info($dryRun ? 'DRY RUN — no database changes will be made.' : 'Starting transfer warehouse backfill...');

        // The earlier migration must already have added from/to_warehouse_id
        foreach ($this->tables as $tableName) {
            foreach ($this->sides as $side) {
                if (!$this->columnExists($tableName, $side['branch']) || !$this->columnExists($tableName, $side['warehouse'])) {
                    $this->error("{$tableName}: {$side['branch']} or {$side['warehouse']} is missing. Run the warehouse migration first.");
                    return self::FAILURE;
                }
            }
        }

        $problems = $this->preflight();
        if ($problems > 0 && !$dryRun) {
            $this->error('Fix the branches data issues above and rerun.');
            return self::FAILURE;
        }

        $rows = [];
        foreach ($this->tables as $tableName) {
            foreach ($this->sides as $sideName => $side) {
                $info = $this->inspect($tableName, $side);
                $rows[] = [
                    $tableName,
                    $sideName,
                    $info['has_backup'] ? 'exists' : 'will add',
                    $info['rows_to_backup'],
                    $info['rows_to_map'],
                    $info['invalid'],
                ];
            }
        }

        $this->table(
            ['Table', 'Side', 'backup column', 'Rows to backup', 'Rows to fix (branch/warehouse)', 'Invalid branch id'],
            $rows
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Dry run complete. No changes were made.');
            return self::SUCCESS;
        }

        if (!$force && !$this->confirm('Apply the changes shown above?')) {
            $this->warn('Aborted — no changes made.');
            return self::SUCCESS;
        }

        foreach ($this->tables as $tableName) {
            $this->newLine();
            $this->info("Processing {$tableName}...");

            try {
                foreach ($this->sides as $side) {
                    $this->ensureBackupColumn($tableName, $side);
                }
                $this->backfill($tableName);
                $this->info("Done: {$tableName}");
            } catch (\Throwable $e) {
                $this->error("Failed on {$tableName}: " . $e->getMessage());
                $this->warn('Rerunning is safe — the backfill always recalculates from the backup columns.');
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('transfers + transfer_details processed successfully.');

        return self::SUCCESS;
    }

    private function preflight(): int
    {
        $problems = 0;

        $orphanWarehouses = DB::table('branches as b')
            ->leftJoin('warehouses as w', 'w.id', '=', 'b.warehouse_id')
            ->whereNotNull('b.warehouse_id')
            ->whereNull('w.id')
            ->pluck('b.id');

        if ($orphanWarehouses->isNotEmpty()) {
            $problems++;
            $this->error('branches.warehouse_id points to a missing warehouse. branches.id: ' . $orphanWarehouses->implode(', '));
        }

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

        // Warning only: warehouse-type branch without a warehouse -> warehouse_id would become NULL
        $noWarehouse = DB::table('branches')
            ->whereRaw('COALESCE(parent_id, 0) != 0')
            ->whereNull('warehouse_id')
            ->pluck('id');

        if ($noWarehouse->isNotEmpty()) {
            $this->warn('Warehouse-type branches with no warehouse_id (rows will get branch_id=parent, warehouse_id=NULL). branches.id: ' . $noWarehouse->implode(', '));
        }

        return $problems;
    }

    private function inspect(string $tableName, array $side): array
    {
        $br = $side['branch'];
        $wh = $side['warehouse'];
        $backup = $side['backup'];
        $hasBackup = $this->columnExists($tableName, $backup);

        $backupQuery = DB::table($tableName . ' as t')
            ->join('branches as b', 'b.id', '=', 't.' . $br);
        if ($hasBackup) {
            $backupQuery->whereNull('t.' . $backup);
        }
        $rowsToBackup = $backupQuery->count();

        $src = $hasBackup ? "COALESCE(t.`{$backup}`, t.`{$br}`)" : "t.`{$br}`";
        $target = self::TARGET_BRANCH;

        $rowsToMap = DB::table($tableName . ' as t')
            ->join('branches as b', DB::raw($src), '=', 'b.id')
            ->whereRaw("(NOT (t.`{$wh}` <=> b.warehouse_id) OR NOT (t.`{$br}` <=> {$target}))")
            ->count();

        $invalidQuery = DB::table($tableName . ' as t')
            ->leftJoin('branches as b', 'b.id', '=', 't.' . $br)
            ->whereNull('b.id');
        if ($hasBackup) {
            $invalidQuery->whereNull('t.' . $backup);
        }
        $invalid = $invalidQuery->count();

        return [
            'has_backup'     => $hasBackup,
            'rows_to_backup' => $rowsToBackup,
            'rows_to_map'    => $rowsToMap,
            'invalid'        => $invalid,
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

    private function ensureBackupColumn(string $tableName, array $side): void
    {
        $backup = $side['backup'];

        if ($this->columnExists($tableName, $backup)) {
            return;
        }

        DB::statement("
            ALTER TABLE `{$tableName}`
            ADD COLUMN `{$backup}` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `{$side['warehouse']}`,
            ADD INDEX `idx_{$tableName}_{$backup}` (`{$backup}`),
            ADD CONSTRAINT `fk_{$tableName}_{$backup}`
                FOREIGN KEY (`{$backup}`) REFERENCES `branches`(`id`)
                ON DELETE SET NULL
        ");
        $this->line("  + added {$backup} (indexed, FK -> branches.id)");
    }

    private function backfill(string $tableName): void
    {
        $target = self::TARGET_BRANCH;

        DB::beginTransaction();

        try {
            foreach ($this->sides as $sideName => $side) {
                $br = $side['branch'];
                $wh = $side['warehouse'];
                $backup = $side['backup'];


                $backedUp = DB::update("
                    UPDATE `{$tableName}` t
                    INNER JOIN `branches` b ON b.id = t.`{$br}`
                    SET t.`{$backup}` = t.`{$br}`
                    WHERE t.`{$backup}` IS NULL
                ");
                $this->line("  [{$sideName}] backed up {$br} on {$backedUp} row(s)");

                $skipped = DB::table($tableName . ' as t')
                    ->leftJoin('branches as b', 'b.id', '=', 't.' . $br)
                    ->whereNull('b.id')
                    ->whereNull('t.' . $backup)
                    ->count();

                if ($skipped > 0) {
                    $this->warn("  [{$sideName}] skipped {$skipped} row(s) with invalid {$br} — left untouched for manual fix");
                }

                // Step 2: always recalculate from the ORIGINAL branch id
                $mapped = DB::update("
                    UPDATE `{$tableName}` t
                    INNER JOIN `branches` b ON b.id = t.`{$backup}`
                    SET t.`{$wh}` = b.warehouse_id,
                        t.`{$br}` = {$target}
                    WHERE NOT (t.`{$wh}` <=> b.warehouse_id)
                       OR NOT (t.`{$br}` <=> {$target})
                ");
                $this->line("  [{$sideName}] fixed {$wh}/{$br} on {$mapped} row(s)");
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
