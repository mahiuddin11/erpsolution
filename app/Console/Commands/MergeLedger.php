<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeLedger extends Command
{
    protected $signature = 'ledger:merge {keep_id} {remove_id}';
    protected $description = 'Merge two ledger accounts — remove_id er shob reference keep_id te update hobe';


    protected $tables = [
        ['table' => 'account_transactions',   'column' => 'account_id'],
        ['table' => 'journal_voucher_details', 'column' => 'account_id'],
        ['table' => 'dabit_voucher_details',   'column' => 'account_id'],
        ['table' => 'credit_vouchers',         'column' => 'account_id'],
        ['table' => 'credit_voucher_details',  'column' => 'account_id'],
        ['table' => 'purchases',               'column' => 'ledger_id'],
        ['table' => 'purchases_details',       'column' => 'ledger_id'],
        ['table' => 'projects',                'column' => 'ledger_id'],
        ['table' => 'supplier_select_prices',  'column' => 'account_id'],
        ['table' => 'purchase_orders',         'column' => 'account_id'],
        ['table' => 'purchase_order_details',  'column' => 'supplier_ledger_id'],
    ];

    public function handle()
    {
        $keepId   = (int) $this->argument('keep_id');
        $removeId = (int) $this->argument('remove_id');

        // --- Validation --- Added: 2026-06-27
        if ($keepId === $removeId) {
            $this->error('keep_id ar remove_id same hote parbe na!');
            return 1;
        }

        // keep_id exist kore kina check
        $keepAccount = DB::table('chart_of_accounts')->where('id', $keepId)->first();
        if (!$keepAccount) {
            $this->error("chart_of_accounts e keep_id={$keepId} pawa jacche na!");
            return 1;
        }

        // remove_id exist kore kina check
        $removeAccount = DB::table('chart_of_accounts')->where('id', $removeId)->first();
        if (!$removeAccount) {
            $this->error("chart_of_accounts e remove_id={$removeId} pawa jacche na!");
            return 1;
        }

        // --- Preview --- Added: 2026-06-27
        $this->info('');
        $this->info('========== MERGE PREVIEW ==========');
        $this->info("KEEP   : [{$keepId}] {$keepAccount->account_name} ({$keepAccount->accountCode})");
        $this->info("REMOVE : [{$removeId}] {$removeAccount->account_name} ({$removeAccount->accountCode})");
        $this->info('');

        $this->info('Affected rows preview:');
        $rows = [];
        foreach ($this->tables as $item) {
            $count = DB::table($item['table'])
                ->where($item['column'], $removeId)
                ->count();
            if ($count > 0) {
                $rows[] = [$item['table'], $item['column'], $count];
            }
        }

        if (empty($rows)) {
            $this->warn("remove_id={$removeId} kono table e use hoyni — merge korar kicho nei.");
            return 0;
        }

        $this->table(['Table', 'Column', 'Affected Rows'], $rows);
        $this->info('');

        // --- Confirmation --- Added: 2026-06-27
        if (!$this->confirm("Uporer shob row update hobe. Chapiye jaben?")) {
            $this->warn('Merge cancel kora hoyeche.');
            return 0;
        }

        // --- Execute inside transaction --- Added: 2026-06-27
        DB::beginTransaction();
        try {
            foreach ($this->tables as $item) {
                $affected = DB::table($item['table'])
                    ->where($item['column'], $removeId)
                    ->update([$item['column'] => $keepId]);

                if ($affected > 0) {
                    $this->line("  ✓ {$item['table']}.{$item['column']} — {$affected} row updated");
                }
            }

            // remove_id wala COA record Inactive kore dao
            DB::table('chart_of_accounts')
                ->where('id', $removeId)
                ->update([
                    'status'     => 'Inactive',
                    'updated_at' => now(),
                ]);

            DB::commit();

            $this->info('');
            $this->info("✅ Merge shafol! Ledger [{$removeId}] [{$keepId}] te merge hoyeche. COA [{$removeId}] Inactive kora hoyeche.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Merge fail! Rollback kora hoyeche.');
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
