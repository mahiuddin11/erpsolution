<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerMergeController extends Controller
{
    // ----------------------------------------------------------------
    // account_id level — chart_of_accounts.id দিয়ে linked tables
    // ----------------------------------------------------------------
    protected $accountTables = [
        ['table' => 'account_transactions',    'column' => 'account_id'],
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

    // ----------------------------------------------------------------
    // supplier_id level — accountable_id দিয়ে linked tables
    // (purchases, purchase_orders ইত্যাদিতে supplier_id column)
    // ----------------------------------------------------------------
    protected $supplierTables = [
        ['table' => 'purchases',              'column' => 'supplier_id'],
        ['table' => 'purchases_details',      'column' => 'supplier_id'],
        ['table' => 'purchase_orders',        'column' => 'supplier_id'],
        ['table' => 'supplier_select_prices', 'column' => 'supplier_id'],
    ];

    // ----------------------------------------------------------------
    // Merge form page
    // ----------------------------------------------------------------
    public function index()
    {
        return view('backend.pages.ledgermarg');
    }

    // ----------------------------------------------------------------
    // Preview — merge করলে কতটা row affected হবে দেখাবে
    // ----------------------------------------------------------------
    public function preview(Request $request)
    {
        $request->validate([
            'keep_id'   => 'required|integer|exists:chart_of_accounts,id',
            'remove_id' => 'required|integer|exists:chart_of_accounts,id',
        ]);

        $keepId   = (int) $request->keep_id;
        $removeId = (int) $request->remove_id;

        if ($keepId === $removeId) {
            return response()->json(['error' => 'Keep এবং Remove Account একই হতে পারবে না!'], 422);
        }

        $keepAccount   = DB::table('chart_of_accounts')->where('id', $keepId)->first();
        $removeAccount = DB::table('chart_of_accounts')->where('id', $removeId)->first();

        // Keep account অবশ্যই Active হতে হবে
        if ($keepAccount->status !== 'Active') {
            return response()->json(['error' => 'Keep Account টি Active নয়!'], 422);
        }

        // ── Level 1: account_id দিয়ে affected rows ──────────────────
        $accountPreview    = [];
        $totalAccountAffected = 0;

        foreach ($this->accountTables as $item) {
            $count = DB::table($item['table'])
                ->where($item['column'], $removeId)
                ->count();

            if ($count > 0) {
                $accountPreview[] = [
                    'table'    => $item['table'],
                    'column'   => $item['column'],
                    'affected' => $count,
                ];
                $totalAccountAffected += $count;
            }
        }

        // ── Level 2: supplier_id (accountable_id) দিয়ে affected rows ─
        $removeAccountableId = $removeAccount->accountable_id ?? null;
        $keepAccountableId   = $keepAccount->accountable_id   ?? null;

        $supplierPreview       = [];
        $totalSupplierAffected = 0;
        $supplierMergePossible = false;

        if ($removeAccountableId && $keepAccountableId && $removeAccountableId !== $keepAccountableId) {
            $supplierMergePossible = true;

            foreach ($this->supplierTables as $item) {
                $count = DB::table($item['table'])
                    ->where($item['column'], $removeAccountableId)
                    ->count();

                if ($count > 0) {
                    $supplierPreview[] = [
                        'table'    => $item['table'],
                        'column'   => $item['column'],
                        'affected' => $count,
                    ];
                    $totalSupplierAffected += $count;
                }
            }
        }

        return response()->json([
            'keep_account'   => $keepAccount,
            'remove_account' => $removeAccount,

            // account_id level
            'account_preview'          => $accountPreview,
            'total_account_affected'   => $totalAccountAffected,

            // supplier_id (accountable_id) level
            'supplier_preview'         => $supplierPreview,
            'total_supplier_affected'  => $totalSupplierAffected,
            'supplier_merge_possible'  => $supplierMergePossible,
            'remove_accountable_id'    => $removeAccountableId,
            'keep_accountable_id'      => $keepAccountableId,

            // grand total
            'total_affected' => $totalAccountAffected + $totalSupplierAffected,
        ]);
    }

    // ----------------------------------------------------------------
    // Merge execute
    // ----------------------------------------------------------------
    public function merge(Request $request)
    {
        $request->validate([
            'keep_id'   => 'required|integer|exists:chart_of_accounts,id',
            'remove_id' => 'required|integer|exists:chart_of_accounts,id',
        ]);

        $keepId   = (int) $request->keep_id;
        $removeId = (int) $request->remove_id;

        if ($keepId === $removeId) {
            return response()->json(['error' => 'Keep এবং Remove Account একই হতে পারবে না!'], 422);
        }

        $keepAccount   = DB::table('chart_of_accounts')->where('id', $keepId)->first();
        $removeAccount = DB::table('chart_of_accounts')->where('id', $removeId)->first();

        if ($keepAccount->status !== 'Active') {
            return response()->json(['error' => 'Keep Account টি Active নয়!'], 422);
        }

        $removeAccountableId = $removeAccount->accountable_id ?? null;
        $keepAccountableId   = $keepAccount->accountable_id   ?? null;

        DB::beginTransaction();
        try {
            $updated = [];

            // ── Level 1: account_id update ───────────────────────────
            foreach ($this->accountTables as $item) {
                $affected = DB::table($item['table'])
                    ->where($item['column'], $removeId)
                    ->update([$item['column'] => $keepId]);

                if ($affected > 0) {
                    $updated[] = [
                        'level'    => 'account_id',
                        'table'    => $item['table'],
                        'column'   => $item['column'],
                        'affected' => $affected,
                    ];
                }
            }

            // ── Level 2: supplier_id (accountable_id) update ─────────
            if ($removeAccountableId && $keepAccountableId && $removeAccountableId !== $keepAccountableId) {
                foreach ($this->supplierTables as $item) {

                    $affected = DB::table($item['table'])
                        ->where($item['column'], $removeAccountableId)
                        ->update([$item['column'] => $keepAccountableId]);

                    if ($affected > 0) {
                        $updated[] = [
                            'level'    => 'supplier_id',
                            'table'    => $item['table'],
                            'column'   => $item['column'],
                            'affected' => $affected,
                        ];
                    }
                }
            }

            // ── Remove account Inactive করো ──────────────────────────
            DB::table('chart_of_accounts')
                ->where('id', $removeId)
                ->update([
                    'status'     => 'Inactive',
                    'updated_at' => now(),
                ]);

            // ── Activity log ─────────────────────────────────────────
            if (function_exists('activity_log')) {
                activity_log(
                    'Ledger Merge',
                    "Ledger [{$removeId}] (accountable: {$removeAccountableId}) merged into [{$keepId}] (accountable: {$keepAccountableId})"
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Ledger [{$removeId}] সফলভাবে [{$keepId}]-এ merge হয়েছে!",
                'updated' => $updated,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Merge fail: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ----------------------------------------------------------------
    // AJAX account search — 
    // ----------------------------------------------------------------
    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $accounts = DB::table('chart_of_accounts')
            ->where('status', 'Active')
            ->where(function ($query) use ($q) {
                $query->where('account_name', 'LIKE', "%{$q}%")
                    ->orWhere('accountCode',  'LIKE', "%{$q}%");
            })
            ->select(
                'id',
                DB::raw("CONCAT('[', accountCode, '] ', account_name) as text"),
                'accountCode as code'
            )
            ->limit(30)
            ->get();

        return response()->json($accounts);
    }
}
