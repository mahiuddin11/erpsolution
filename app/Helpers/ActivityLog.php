<?php

/**
 * ╔══════════════════════════════════════════════════════════════════╗
 * ║           activity_log() — Global Helper Function               ║
 * ║   যেকোনো Controller / Repository থেকে এক লাইনে call করো       ║
 * ╚══════════════════════════════════════════════════════════════════╝
 *
 * composer.json → autoload → files → ["app/Helpers/extrafunction.php"]
 */

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

if (!function_exists('activity_log')) {

    /**
     * @param string      $action      'create' | 'update' | 'delete' | 'login' | 'logout' | 'export' | 'failed'
     * @param string      $table       DB table name  (e.g. 'product_opening_stocks')
     * @param array       $newData     নতুন data  (create / update এর জন্য)
     * @param array       $oldData     পুরনো data (update / delete এর জন্য)
     * @param string|null $description Custom message (না দিলে auto তৈরি হবে)
     */
    function activity_log(
        string  $action,
        string  $table,
        array   $newData     = [],
        array   $oldData     = [],
        ?string $description = null
    ): void {

        try {
            // ── 1. Module resolve ──────────────────────────────────────
            [$module, $subModule] = _log_resolve_module($table);

            // ── 2. Record ID ───────────────────────────────────────────
            $recordId = $newData['id'] ?? $oldData['id'] ?? null;

            // ── 3. Diff (update হলে কোন fields বদলেছে) ────────────────
            $changedFields = [];
            $storedOld     = [];
            $storedNew     = [];

            $action = strtolower($action);

            if ($action === 'update' && !empty($oldData)) {
                [$changedFields, $storedOld, $storedNew] = _log_diff($oldData, $newData);

                // কিছুই change না হলে log দরকার নেই
                if (empty($changedFields)) return;
            } elseif ($action === 'create') {
                $storedNew = _log_sanitize($newData);
            } elseif ($action === 'delete') {
                $storedOld = _log_sanitize($oldData);
            }

            // ── 4. Auto description ────────────────────────────────────
            $userName   = Auth::check() ? Auth::user()->name : 'System';
            $recordType = _log_table_label($table);

            // invoice_no / voucher_no খোঁজার জন্য newData + oldData merge করে pass
            $allData     = array_merge($oldData, $newData);
            $description = $description ?? _log_description(
                $action,
                $recordType,
                $recordId,
                $userName,
                $changedFields,
                $allData
            );

            // ── 5. Save ────────────────────────────────────────────────
            ActivityLog::create([
                'user_id'        => Auth::id(),
                'user_name'      => $userName,
                'action'         => strtoupper($action),
                'module'         => $module,
                'sub_module'     => $subModule,
                'record_id'      => $recordId,
                'record_type'    => $recordType,
                'description'    => $description,
                'old_values'     => !empty($storedOld)     ? json_encode($storedOld,     JSON_UNESCAPED_UNICODE) : null,
                'new_values'     => !empty($storedNew)     ? json_encode($storedNew,     JSON_UNESCAPED_UNICODE) : null,
                'changed_fields' => !empty($changedFields) ? json_encode($changedFields, JSON_UNESCAPED_UNICODE) : null,
                'ip_address'     => _log_ip(),
                'user_agent'     => Request::userAgent(),
                'url'            => Request::fullUrl(),
                'method'         => Request::method(),
                'status'         => 'SUCCESS',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            // Log fail হলে main flow break হবে না
            dd($e->getMessage());
        }
    }
}


// ════════════════════════════════════════════════════════════════════
//  PRIVATE HELPER FUNCTIONS  (prefix: _log_)
// ════════════════════════════════════════════════════════════════════

if (!function_exists('_log_resolve_module')) {

    /** Table name → [module, sub_module] */
    function _log_resolve_module(string $table): array
    {
        $map = [
            // ── Inventory Setup ──
            'product_opening_stocks'         => ['Inventory',  'Opening Stock'],
            'product_opening_stock_details'  => ['Inventory',  'Opening Stock Details'],
            'stock_adjustments'              => ['Inventory',  'Stock Adjustment'],
            'stock_adjustment_details'       => ['Inventory',  'Stock Adjustment Details'],
            'stock_summaries'                => ['Inventory',  'Stock Summary'],
            'stock_transfer'                 => ['Inventory',  'Stock Transfer'],
            'stocks'                         => ['Inventory',  'Stock Ledger'],
            'products'                       => ['Inventory',  'Product'],
            'product_categories'             => ['Inventory',  'Category'],
            'warehouses'                     => ['Inventory',  'Warehouse'],

            'purchase_orders'                => ['Project',  'Purchase Order'],
            'purchase_orders_update'         => ['Project',  'Purchase Order update'],
            'purchase_order_aprove'          => ['Project',  'Purchase Order Aprove'],
            'purchase_order_details'         => ['Inventory',  'Purchase Order Details'],

            'grns'                           => ['Project',  'GRN'],

            //Direct_purchase
            'derect_purchase'                => ['Direct Purchase',  'Direct Purchase'],
            'menual_purchase'                => ['Menual Purchase',  'Menual Purchase'],

            // ── HR ──
            'employees'                      => ['HR',         'Employee'],
            'departments'                    => ['HR',         'Department'],
            'designations'                   => ['HR',         'Designation'],
            'leaves'                         => ['HR',         'Leave'],
            'attendances'                    => ['HR',         'Attendance'],
            'payrolls'                       => ['HR',         'Payroll'],


            // ── Project ──
            'projects'                        => ['Project',         'Project'],
            'purchase_requisitions'           => ['Project',         'Purchase Requisitions'],


            // ── Accounts ──
            'invoices'                       => ['Accounts',   'Invoice'],
            'payments'                       => ['Accounts',   'Payment'],
            'expenses'                       => ['Accounts',   'Expense'],
            'account_transactions'           => ['Accounts',   'Transaction'],
            'journal_entries'                => ['Accounts',   'Journal Entry'],
            'chart_of_accounts'              => ['Accounts',   'Chart of Accounts'],

            // ── Sales ──
            'sales_orders'                   => ['Sales',      'Sales Order'],
            'Sales'                          => ['Sales',      'Sales'],
            'quotations'                     => ['Sales',      'Quotation'],
            'leads'                          => ['Sales',      'CRM Lead'],

            // ── Settings / Auth ──
            'users'                          => ['Settings',   'User Management'],
            'roles'                          => ['Settings',   'Role & Permission'],
            'settings'                       => ['Settings',   'System Settings'],
            'brands'                         => ['Settings',   'Brand'],
        ];

        return $map[$table] ?? ['General', _log_table_label($table)];
    }
}

if (!function_exists('_log_diff')) {

    /** old vs new তুলনা করে changed fields বের করা */
    function _log_diff(array $old, array $new): array
    {
        $skip   = ['password', 'remember_token', 'api_key', 'token', 'updated_at'];
        $changed = [];
        $oldVals = [];
        $newVals = [];

        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($keys as $key) {
            if (in_array($key, $skip)) continue;

            $o = $old[$key] ?? null;
            $n = $new[$key] ?? null;

            if ((string)$o !== (string)$n) {
                $changed[]    = $key;
                $oldVals[$key] = $o;
                $newVals[$key] = $n;
            }
        }

        return [$changed, $oldVals, $newVals];
    }
}

if (!function_exists('_log_sanitize')) {

    /** Sensitive fields বাদ দিয়ে data return */
    function _log_sanitize(array $data): array
    {
        $skip = ['password', 'remember_token', 'api_key', 'token'];
        return array_diff_key($data, array_flip($skip));
    }
}

if (!function_exists('_log_table_label')) {

    /** product_opening_stocks → Product Opening Stock */
    function _log_table_label(string $table): string
    {
        return ucwords(str_replace('_', ' ', rtrim($table, 's')));
    }
}

if (!function_exists('_log_description')) {

    /** Human-readable auto description */
    function _log_description(
        string  $action,
        string  $recordType,
        ?string $recordId,
        string  $userName,
        array   $changedFields,
        array   $data = []          // invoice_no বা অন্য identifier খোঁজার জন্য
    ): string {
        // invoice_no থাকলে সেটা দেখাবে, না থাকলে record id
        $ref = null;

        if (!empty($data['invoice_no'])) {
            $ref = "Invoice: {$data['invoice_no']}";
        } elseif (!empty($data['voucher_no'])) {
            $ref = "Voucher: {$data['voucher_no']}";
        } elseif (!empty($data['order_no'])) {
            $ref = "Order: {$data['order_no']}";
        } elseif ($recordId) {
            $ref = "#{$recordId}";
        }
        $refStr = $ref ? " ({$ref})" : '';

        if (!empty($data['branch_id'])) {
            $branch = Branch::find($data['branch_id'])->name;
        }

        if (!empty($data['project_id'])) {
            $project  = Project::find($data['project_id'])->name;
        }

        return match ($action) {
            'create' => "New {$recordType} created{$refStr}",
            'update' => "{$recordType} updated{$refStr}"
                . (!empty($changedFields) ? ' — changed: ' . implode(', ', $changedFields) : ''),
            'delete' => "{$recordType} deleted{$refStr}",
            'login'  => "User logged in successfully",
            'logout' => "User logged out",
            'failed' => "Failed login attempt",
            default  => strtoupper($action) . " performed on {$recordType}{$refStr}",
        };
    }
}

if (!function_exists('_log_ip')) {

    function _log_ip(): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            $val = request()->server($key);
            if ($val) {
                $ip = trim(explode(',', $val)[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return request()->ip() ?? '0.0.0.0';
    }
}
