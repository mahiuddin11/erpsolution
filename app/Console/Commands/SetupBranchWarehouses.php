<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupBranchWarehouses extends Command
{
    /**
     * php artisan warehouse:setup-branch-warehouses
     * php artisan warehouse:setup-branch-warehouses --dry-run
     */
    protected $signature = 'warehouse:setup-branch-warehouses {--dry-run : Preview changes without writing to the database}';

    protected $description = 'Create warehouses from child branches, update branches.warehouse_id, and extend stocks.status ENUM';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun ? 'DRY RUN — no changes will be written.' : 'Starting branch warehouse setup...');

        // ==========================================================
        // STEP 1: branches -> warehouses (safe to wrap in a transaction,
        // no DDL happens here)
        // ==========================================================
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $mismatches = [];

        DB::beginTransaction();

        try {
            /*
             * parent_id != 0 branches are warehouses.
             * Exclude soft-deleted branches — they shouldn't be (re)materialised
             * as active warehouses.
             */
            $branches = DB::table('branches')
                ->where('parent_id', '!=', 0)
                ->whereNull('deleted_at')
                ->get();

            foreach ($branches as $branch) {
                // Warehouse ID MUST be the same as Branch ID.
                $warehouseId = $branch->id;

                // Example: Branch ID 5 -> WH00005
                $warehouseCode = 'WH' . str_pad($branch->id, 5, '0', STR_PAD_LEFT);

                $warehouse = DB::table('warehouses')->where('id', $warehouseId)->first();

                if (!$warehouse) {
                    if (!$dryRun) {
                        DB::table('warehouses')->insert([
                            'id'            => $warehouseId,
                            'branch_id'     => $branch->parent_id,
                            'name'          => !empty($branch->name) ? $branch->name : 'Warehouse ' . $branch->id,
                            'warehouseCode' => $warehouseCode,
                            'email'         => $branch->email,
                            'phone'         => $branch->phone,
                            'address'       => $branch->address,
                            'status'        => !empty($branch->status) ? $branch->status : 'Active',
                            'created_by'    => $branch->created_by,
                            'updated_by'    => $branch->updated_by,
                            'deleted_by'    => $branch->deleted_by,
                            'created_at'    => $branch->created_at,
                            'updated_at'    => $branch->updated_at,
                        ]);
                    }

                    $created++;
                } else {

                    if ((int) $warehouse->branch_id !== (int) $branch->parent_id) {
                        $mismatches[] = [
                            'warehouse_id'       => $warehouseId,
                            'warehouse.branch_id' => $warehouse->branch_id,
                            'branch.parent_id'   => $branch->parent_id,
                        ];
                    }

                    $skipped++;
                }

                if (!$dryRun) {
                    DB::table('branches')
                        ->where('id', $branch->id)
                        ->update(['warehouse_id' => $branch->id]);
                }

                $updated++;
            }

            if ($dryRun) {
                DB::rollBack(); // dry run never writes, regardless of no errors
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->error('Branch/warehouse setup failed — nothing was written.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Step 1 complete: branches -> warehouses');
        $this->table(
            ['Warehouses Created', 'Branches Updated', 'Warehouses Already Existed'],
            [[$created, $updated, $skipped]]
        );

        if (!empty($mismatches)) {
            $this->warn('Found existing warehouses whose branch_id does NOT match the expected parent branch:');
            $this->table(['warehouse_id', 'warehouse.branch_id', 'branch.parent_id'], $mismatches);
            $this->warn('These were left untouched — review manually before proceeding.');
        }


        $this->newLine();
        $this->info('Checking stocks.status...');

        try {
            $column = DB::selectOne("
                SELECT COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'stocks'
                AND COLUMN_NAME = 'status'
            ");

            if (!$column) {
                throw new \Exception('stocks.status column not found.');
            }

            $enumType = $column->COLUMN_TYPE;

            $newStatuses = [
                'Project To Project In',
                'Project To Project Out',
                'Project Transfer In',
                'Project Transfer Out',
                'Project Transfer Out',
                'Branch to Project',
                'Project to Branch',
            ];

            $toAdd = [];
            foreach ($newStatuses as $status) {
                if (!str_contains($enumType, "'" . $status . "'")) {
                    $enumType = substr($enumType, 0, -1) . ",'" . addslashes($status) . "')";
                    $toAdd[] = $status;
                }
            }

            if (empty($toAdd)) {
                $this->info('All target statuses already present — nothing to add.');
            } else {

                $nullClause = strtoupper($column->IS_NULLABLE) === 'NO' ? 'NOT NULL' : 'NULL';

                $defaultClause = '';
                if ($column->COLUMN_DEFAULT !== null) {
                    $cleanDefault = trim($column->COLUMN_DEFAULT, "'");

                    if ($cleanDefault !== '' && !str_contains($enumType, "'" . $cleanDefault . "'")) {
                        $this->warn("Existing default '{$cleanDefault}' is not a valid member of the enum — falling back to the first enum option.");
                        preg_match("/^enum\('([^']+)'/", $enumType, $m);
                        $cleanDefault = $m[1] ?? $cleanDefault;
                    }

                    if ($cleanDefault !== '') {
                        $defaultClause = "DEFAULT '" . addslashes($cleanDefault) . "'";
                    }
                }

                $sql = "ALTER TABLE stocks MODIFY status {$enumType} {$nullClause} {$defaultClause}";

                if ($dryRun) {
                    $this->info('Would run:');
                    $this->line($sql);
                } else {
                    DB::statement($sql);
                    $this->info('Stock status ENUM updated successfully.');
                    $this->table(['Added Status'], array_map(fn($s) => [$s], $toAdd));
                }
            }
        } catch (\Throwable $e) {
            $this->error('Step 2 (status ENUM update) failed:');
            $this->error($e->getMessage());
            $this->warn('Step 1 (branches/warehouses) already committed successfully and is NOT affected by this failure.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info($dryRun ? 'Dry run complete — no changes were written.' : 'All setup completed successfully.');

        return self::SUCCESS;
    }
}
