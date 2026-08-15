<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupBranchWarehouses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warehouse:setup-branch-warehouses';

    /**
     * The console command description.
     *
     * @var string
     */

    // php artisan warehouse:setup-branch-warehouses
    protected $description = 'Create warehouses from child branches and update branches.warehouse_id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting branch warehouse setup...');

        DB::beginTransaction();

        try {

            /*
             * parent_id != 0 branches are warehouses.
             */
            $branches = DB::table('branches')
                ->where('parent_id', '!=', 0)
                ->get();

            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($branches as $branch) {

                /*
                 * Warehouse ID MUST be the same as Branch ID.
                 */
                $warehouseId = $branch->id;

                /*
                 * Generate warehouse code from Branch ID.
                 *
                 * Example:
                 * Branch ID 5  = WH00005
                 * Branch ID 25 = WH00025
                 */
                $warehouseCode = 'WH' . str_pad(
                    $branch->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                );

                /*
                 * Check warehouse by ID.
                 */
                $warehouse = DB::table('warehouses')
                    ->where('id', $warehouseId)
                    ->first();

                if (!$warehouse) {

                    /*
                     * Create warehouse with SAME ID as branch.
                     */
                    DB::table('warehouses')->insert([
                        'id'            => $warehouseId,
                        'branch_id'     => $branch->parent_id,
                        'name'          => !empty($branch->name)
                            ? $branch->name
                            : 'Warehouse ' . $branch->id,

                        'warehouseCode' => $warehouseCode,

                        'email'         => $branch->email,
                        'phone'         => $branch->phone,
                        'address'       => $branch->address,

                        'status'        => !empty($branch->status)
                            ? $branch->status
                            : 'Active',

                        'created_by'    => $branch->created_by,
                        'updated_by'    => $branch->updated_by,
                        'deleted_by'    => $branch->deleted_by,

                        'created_at'    => $branch->created_at,
                        'updated_at'    => $branch->updated_at,
                    ]);

                    $created++;
                } else {

                    /*
                     * Warehouse already exists.
                     * No new ID will be generated.
                     */
                    $skipped++;
                }

                /*
                 * Branch warehouse_id = Branch ID.
                 */
                DB::table('branches')
                    ->where('id', $branch->id)
                    ->update([
                        'warehouse_id' => $branch->id,
                    ]);

                $updated++;
            }

            DB::commit();

            $this->newLine();

            $this->info('Warehouse setup completed successfully.');

            $this->table(
                ['Created', 'Branches Updated', 'Already Exists'],
                [
                    [$created, $updated, $skipped]
                ]
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {

            DB::rollBack();

            $this->error('Warehouse setup failed!');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
