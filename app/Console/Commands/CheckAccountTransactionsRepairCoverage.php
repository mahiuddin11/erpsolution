<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAccountTransactionsRepairCoverage extends Command
{
    protected $signature = 'account-transactions:check-repair-coverage
                            {--map : Run the full coverage check using the $voucherTypeMap below}';

    protected $description = 'Step 1 (default): show distinct voucher_type values among account_transactions '
        . 'rows with invalid branch_id, so the correct table mapping can be filled in below. '
        . 'Step 2 (--map): once $voucherTypeMap is filled in, report how many of those rows have '
        . 'a recoverable branch_id via their source voucher table.';

    protected array $voucherTypeMap = [
        'debit_voucher'   => ['table' => 'dabit_vouchers',   'branch_column' => 'branch_id', 'match_column' => 'voucher_no'],
        'credit_voucher'  => ['table' => 'credit_vouchers',  'branch_column' => 'branch_id', 'match_column' => 'voucher_no'],
        'journal_voucher' => ['table' => 'journal_vouchers', 'branch_column' => 'branch_id', 'match_column' => 'voucher_no'],
        'purchase'        => ['table' => 'purchases',        'branch_column' => 'branch_id', 'match_column' => 'invoice_no'],
        'contra_voucher'  => ['table' => 'contra_vouchers',  'branch_column' => 'branch_id', 'match_column' => 'voucher_no'],
    ];

    protected string $voucherTypeColumn = 'type';
    protected string $voucherIdColumn = 'invoice';

    public function handle()
    {
        if (
            !$this->columnExists('account_transactions', $this->voucherTypeColumn)
            || !$this->columnExists('account_transactions', $this->voucherIdColumn)
        ) {
            $this->error("account_transactions is missing '{$this->voucherTypeColumn}' or '{$this->voucherIdColumn}' — "
                . "update \$voucherTypeColumn/\$voucherIdColumn in this command to the correct column names and rerun.");
            return self::FAILURE;
        }

        return $this->option('map') ? $this->runCoverageCheck() : $this->runVoucherTypeBreakdown();
    }

    /**
     * STEP 1: which voucher_type values appear on invalid-branch_id rows, and how many rows each.
     */
    private function runVoucherTypeBreakdown(): int
    {
        $this->info('STEP 1 — voucher_type breakdown for account_transactions rows with invalid branch_id.');
        $this->newLine();

        $invalid = $this->invalidBranchQuery();

        $breakdown = (clone $invalid)
            ->select($this->voucherTypeColumn, DB::raw('COUNT(*) as row_count'))
            ->groupBy($this->voucherTypeColumn)
            ->orderByDesc('row_count')
            ->get();

        if ($breakdown->isEmpty()) {
            $this->warn('No rows with invalid branch_id found — nothing to check.');
            return self::SUCCESS;
        }

        $this->table(
            ['voucher_type value', 'Row Count'],
            $breakdown->map(fn($r) => [$r->{$this->voucherTypeColumn} ?? '(NULL)', $r->row_count])->toArray()
        );

        $this->newLine();
        $this->line('Next: open this command file, fill in $voucherTypeMap using the values above (map each '
            . 'voucher_type to its real table + branch_id column), then rerun with --map.');

        return self::SUCCESS;
    }

    /**
     * STEP 2: for each mapped voucher_type, how many invalid rows have a source voucher
     * whose own branch_id IS valid (i.e. recoverable), vs not found / also invalid.
     */
    private function runCoverageCheck(): int
    {
        if (empty($this->voucherTypeMap)) {
            $this->error('$voucherTypeMap is empty. Run without --map first, then fill in the mapping in this file.');
            return self::FAILURE;
        }

        $this->info('STEP 2 — repair coverage via source voucher branch_id.');
        $this->newLine();

        $rows = [];
        $totalInvalid = 0;
        $totalRecoverable = 0;

        foreach ($this->voucherTypeMap as $voucherType => $conf) {
            $table = $conf['table'];
            $branchCol = $conf['branch_column'] ?? 'branch_id';
            $matchCol = $conf['match_column'] ?? 'voucher_no';

            if (!$this->columnExists($table, $branchCol)) {
                $this->warn("Skipping '{$voucherType}': {$table}.{$branchCol} does not exist.");
                continue;
            }

            if (!$this->columnExists($table, $matchCol)) {
                $this->warn("Skipping '{$voucherType}': {$table}.{$matchCol} does not exist.");
                continue;
            }

            $invalidForType = (clone $this->invalidBranchQuery())
                ->where($this->voucherTypeColumn, $voucherType);

            $invalidCount = (clone $invalidForType)->count();

            // DIAGNOSTIC: does `invoice` even match v.{matchCol}, ignoring branch validity?
            $voucherFoundCount = DB::table('account_transactions as t')
                ->joinSub(
                    (clone $invalidForType)->select('t.id', "t.{$this->voucherIdColumn}"),
                    'inv',
                    'inv.id',
                    '=',
                    't.id'
                )
                ->join("{$table} as v", "v.{$matchCol}", '=', "inv.{$this->voucherIdColumn}")
                ->count();

            // Recoverable: the source voucher row exists AND its branch_id matches a real branches.id
            $recoverableCount = DB::table('account_transactions as t')
                ->joinSub(
                    (clone $invalidForType)->select('t.id', "t.{$this->voucherIdColumn}"),
                    'inv',
                    'inv.id',
                    '=',
                    't.id'
                )
                ->join("{$table} as v", "v.{$matchCol}", '=', "inv.{$this->voucherIdColumn}")
                ->join('branches as b', 'b.id', '=', "v.{$branchCol}")
                ->count();

            $rows[] = [
                $voucherType,
                $table,
                $invalidCount,
                $voucherFoundCount,
                $recoverableCount,
                $invalidCount > 0 ? round($recoverableCount / $invalidCount * 100, 1) . '%' : '-',
            ];

            $totalInvalid += $invalidCount;
            $totalRecoverable += $recoverableCount;
        }

        $mappedTypes = array_keys($this->voucherTypeMap);
        $unmappedInvalidCount = (clone $this->invalidBranchQuery())
            ->whereNotIn($this->voucherTypeColumn, $mappedTypes)
            ->count();

        $this->table(
            ['voucher_type', 'Source Table', 'Invalid Rows', 'Voucher Found (any)', 'Recoverable via Voucher', 'Coverage %'],
            $rows
        );

        $this->newLine();
        $this->line("Mapped voucher_types — total invalid: {$totalInvalid}, recoverable: {$totalRecoverable}");
        if ($unmappedInvalidCount > 0) {
            $this->warn("{$unmappedInvalidCount} invalid row(s) have a voucher_type NOT in \$voucherTypeMap — add them to Step 1 output and map, or they stay unrecoverable by this method.");
        }

        $grandTotalInvalid = $totalInvalid + $unmappedInvalidCount;
        $overallPct = $grandTotalInvalid > 0 ? round($totalRecoverable / $grandTotalInvalid * 100, 1) : 0;
        $this->info("Overall recoverable: {$totalRecoverable} / {$grandTotalInvalid} ({$overallPct}%)");

        return self::SUCCESS;
    }

    private function invalidBranchQuery()
    {
        return DB::table('account_transactions as t')
            ->leftJoin('branches as b', 'b.id', '=', 't.branch_id')
            ->where(function ($q) {
                $q->whereNull('t.branch_id')
                    ->orWhere('t.branch_id', 0)
                    ->orWhereNull('b.id');
            })
            ->select('t.*');
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
}
