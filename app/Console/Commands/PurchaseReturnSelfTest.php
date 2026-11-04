<?php

namespace App\Console\Commands;

use App\Models\AccountTransaction;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Project;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventorySetup\PurchaseReturnService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseReturnSelfTest extends Command
{
    protected $signature = 'purchase-return:selftest {--force : Run even when APP_ENV=production}';

    protected $description = 'Self-test the Purchase Return flow (create, update, approve, reject, destroy) inside one rolled-back transaction. The database is left unchanged.';

    protected $failures = [];
    protected $checks = 0;
    protected $current = '';

    public function handle(PurchaseReturnService $service)
    {
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Refusing to run on production. Run this on your LOCAL copy, or pass --force.');
            return 1;
        }

        $this->info('Purchase Return self-test — all changes are rolled back at the end.');
        $this->loginFirstUser();

        $branch = Branch::where('status', 'Active')->where('parent_id', 0)->orderBy('id')->first();
        $warehouses = Warehouse::where('status', 'Active')->orderBy('id')->get();
        $ledger = ChartOfAccount::where('status', 'Active')
            ->where('accountable_type', Supplier::class)->orderBy('id')->first()
            ?? ChartOfAccount::where('status', 'Active')->orderBy('id')->first();
        $credit = ChartOfAccount::where('account_name', 'Purchase Return')
            ->where('status', 'Active')->first();

        if (! $branch || $warehouses->isEmpty() || ! $ledger || ! $credit) {
            $this->error('Missing fixtures: need 1 Active branch (parent_id 0), 1 Active warehouse, 1 Active ledger, and the Active "Purchase Return" chart account.');
            return 1;
        }

        // Scan every Active warehouse, not just the first one.
        $warehouse = null;
        $summaryRow = null;
        $product = null;
        foreach ($warehouses as $wh) {
            foreach (StockSummary::where('type', 'Branch')->where('warehouse_id', $wh->id)
                ->where('quantity', '>', 0)->orderBy('id')->limit(20)->get() as $row) {
                $p = Product::find($row->product_id);
                if ($p && $p->status === 'Active') {
                    $warehouse = $wh;
                    $summaryRow = $row;
                    $product = $p;
                    break 2;
                }
            }
        }

        if (! $summaryRow) {
            $this->error('No Branch stock summary with quantity > 0 in any Active warehouse. Receive stock first (purchase/GRN), then re-run.');
            return 1;
        }

        $purchasetype = $summaryRow->purchasetype ?: 'local';
        $beforeQty = (float) $summaryRow->quantity;
        $expectedSupplier = ($ledger->accountable_type === Supplier::class) ? (int) $ledger->accountable_id : 0;
        $today = date('Y-m-d');
        $unitPrice = 100.00;
        $qty = 1;

        $this->info("Fixtures: branch #{$branch->id}, warehouse #{$warehouse->id}, ledger #{$ledger->id} ({$ledger->account_name}), credit account #{$credit->id}, product #{$product->id} ({$product->name}), type {$purchasetype}, stock {$beforeQty}, expected supplier_id {$expectedSupplier}.");

        DB::beginTransaction();

        try {
            // ── A. STORE: pending, nothing posted ──
            $this->current = 'store';
            $ret = $this->storeReturn($service, $branch, $warehouse, $ledger, $product, $qty, $unitPrice, $purchasetype, 'selftest');
            $this->check('store: status pending', $ret->status === 'pending', $ret->status);
            $this->check('store: return_no = PRT + id', $ret->return_no === 'PRT' . str_pad($ret->id, 5, '0', STR_PAD_LEFT), (string) $ret->return_no);
            $this->check('store: grand_total = qty x price', $this->moneyEq($ret->grand_total, $qty * $unitPrice), (string) $ret->grand_total);
            $this->check('store: supplier_id derived from ledger', (int) $ret->supplier_id === $expectedSupplier, (string) $ret->supplier_id);
            $detail = PurchaseReturnDetail::where('purchase_return_id', $ret->id)->first();
            $this->check('store: 1 detail line with correct fields', $detail && (int) $detail->product_id === (int) $product->id && (float) $detail->returned_qty == $qty && $this->moneyEq($detail->unit_price, $unitPrice) && $this->moneyEq($detail->line_amount, $qty * $unitPrice) && $detail->purchasetype === $purchasetype);
            $this->check('store: stock unchanged', (float) StockSummary::find($summaryRow->id)->quantity === $beforeQty);
            $this->check('store: no account postings', AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->count() === 0);
            $this->check('store: no stock rows', Stock::where('invoice_no', $ret->return_no)->count() === 0);

            // ── B. UPDATE: recalculates, still pending, still no posting ──
            $this->current = 'update';
            $updReq = Request::create('/', 'PUT', $this->payload($branch, $warehouse, $ledger, $product, $qty, 150.00, $purchasetype, 'selftest update'));
            $this->validateOrFail($service->updateValidation($updReq, $ret->id), $updReq, 'update');
            $ret = $service->update($updReq, $ret->id);
            $expectedAmount = 150.00;
            $this->check('update: grand_total recalculated', $this->moneyEq($ret->grand_total, $expectedAmount), (string) $ret->grand_total);
            $this->check('update: return_no unchanged', $ret->return_no === 'PRT' . str_pad($ret->id, 5, '0', STR_PAD_LEFT));
            $this->check('update: supplier_id still derived from ledger', (int) $ret->supplier_id === $expectedSupplier, (string) $ret->supplier_id);
            $this->check('update: still pending, still no postings', $ret->status === 'pending' && AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->count() === 0 && Stock::where('invoice_no', $ret->return_no)->count() === 0);

            // ── C. PROJECT: update without project_id must preserve it ──
            $this->current = 'project-preserved';
            $project = Project::where('condition', 'One Going')->orderBy('id')->first();
            if ($project) {
                $retP = $this->storeReturn($service, $branch, $warehouse, $ledger, $product, $qty, $unitPrice, $purchasetype, 'selftest project', $project->id);
                $this->check('project: stored with project_id', (int) $retP->project_id === (int) $project->id, (string) $retP->project_id);
                $updPReq = Request::create('/', 'PUT', $this->payload($branch, $warehouse, $ledger, $product, $qty, $unitPrice, $purchasetype, 'selftest project update'));
                $this->validateOrFail($service->updateValidation($updPReq, $retP->id), $updPReq, 'project-update');
                $retP2 = $service->update($updPReq, $retP->id);
                $this->check('project: update without project_id preserves it', (int) $retP2->project_id === (int) $project->id, (string) $retP2->project_id);
            } else {
                $this->info('SKIP project-preserved: no "One Going" project found.');
            }

            // ── D. APPROVE: stock OUT + balanced Dr/Cr ──
            $this->current = 'approve';
            $service->approve($ret->id);
            $fresh = $ret->fresh();
            $this->check('approve: status approved', $fresh->status === 'approved', $fresh->status);
            $this->check('approve: approved_by/at set', ! empty($fresh->approved_by) && ! empty($fresh->approved_at));
            $this->check('approve: stock decreased by qty', (float) StockSummary::find($summaryRow->id)->quantity === $beforeQty - $qty);
            $txns = AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->orderBy('id')->get();
            $this->check('approve: exactly 2 account rows', $txns->count() === 2, (string) $txns->count());
            if ($txns->count() === 2) {
                [$dr, $cr] = [$txns[0], $txns[1]];
                $this->check('approve: Dr = header ledger, amount = grand_total', (int) $dr->account_id === (int) $ledger->id && $this->moneyEq($dr->debit, $expectedAmount) && $this->moneyEq($dr->credit ?? 0, 0));
                $this->check('approve: Cr = Purchase Return account, amount = grand_total', (int) $cr->account_id === (int) $credit->id && $this->moneyEq($cr->credit, $expectedAmount) && $this->moneyEq($cr->debit ?? 0, 0));
                foreach (['Dr' => $dr, 'Cr' => $cr] as $label => $t) {
    $this->check("approve: {$label} invoice", $t->invoice === $ret->return_no, (string) $t->invoice);
    $this->check("approve: {$label} table_id", (int) $t->table_id === (int) $ret->id, (string) $t->table_id);
    $this->check("approve: {$label} branch_id", (int) $t->branch_id === (int) $branch->id, (string) $t->branch_id);
    $this->check("approve: {$label} warehouse_id", (int) $t->warehouse_id === (int) $warehouse->id, (string) $t->warehouse_id);
    $this->check("approve: {$label} supplier_id", (int) $t->supplier_id === $expectedSupplier, (string) $t->supplier_id);
    $this->check("approve: {$label} voucher_date", substr((string) $t->voucher_date, 0, 10) === $today, (string) $t->voucher_date);
    $this->check("approve: {$label} remark", $t->remark === ($ret->remarks ?? ''), (string) $t->remark);

}
            }
            $stocks = Stock::where('invoice_no', $ret->return_no)->where('status', 'Purchase Return')->get();
            $this->check('approve: exactly 1 stock OUT row', $stocks->count() === 1, (string) $stocks->count());
            if ($stocks->count() === 1) {
                $s = $stocks[0];
                $this->check('approve: stock row fields', (int) $s->product_id === (int) $product->id && (int) $s->quantity === $qty && (int) $s->general_id === (int) $ret->id && (int) $s->branch_id === (int) $branch->id && (int) $s->warehouse_id === (int) $warehouse->id && $s->project_id === null && $this->moneyEq($s->unit_price, 150.00) && $this->moneyEq($s->total_price, $expectedAmount) && substr((string) $s->date, 0, 10) === $today);
            }
            $doubleFailed = false;
            try {
                $service->approve($ret->id);
            } catch (\Throwable $e) {
                $doubleFailed = str_contains($e->getMessage(), 'Only pending');
            }
            $this->check('approve: second approve is rejected', $doubleFailed);
            $this->check('approve: still 2 account rows after double-approve', AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->count() === 2);

            // ── E. INSUFFICIENT STOCK: approve must fail, nothing posted ──
            $this->current = 'insufficient-stock';
            $availNow = (float) StockSummary::find($summaryRow->id)->quantity;
            $retBig = $this->storeReturn($service, $branch, $warehouse, $ledger, $product, $availNow + 5, $unitPrice, $purchasetype, 'selftest big');
            $threw = false;
            try {
                $service->approve($retBig->id);
            } catch (\Throwable $e) {
                $threw = str_contains($e->getMessage(), 'Insufficient stock');
                $this->info('  approve error (expected): ' . $e->getMessage());
            }
            $this->check('insufficient: approve throws Insufficient stock', $threw);
            $this->check('insufficient: still pending, nothing posted, stock unchanged', $retBig->fresh()->status === 'pending' && AccountTransaction::where('type', 'purchase_return')->where('table_id', $retBig->id)->count() === 0 && (float) StockSummary::find($summaryRow->id)->quantity === $availNow);

            // ── F. REJECT + DESTROY ──
            $this->current = 'reject-destroy';
            $retR = $this->storeReturn($service, $branch, $warehouse, $ledger, $product, $qty, $unitPrice, $purchasetype, 'selftest reject');
            $service->reject(Request::create('/', 'POST', ['rejection_reason' => 'selftest reject']), $retR->id);
            $freshR = $retR->fresh();
            $this->check('reject: status rejected + reason saved', $freshR->status === 'rejected' && $freshR->rejection_reason === 'selftest reject');
            $this->check('reject: nothing posted', AccountTransaction::where('type', 'purchase_return')->where('table_id', $retR->id)->count() === 0 && Stock::where('invoice_no', $retR->return_no)->count() === 0);
            $service->destroy($retR->id);
            $trashed = PurchaseReturn::withTrashed()->find($retR->id);
            $this->check('destroy: header soft-deleted, details hard-deleted', $trashed && $trashed->deleted_at !== null && PurchaseReturnDetail::where('purchase_return_id', $retR->id)->count() === 0);

            // ── G. APPROVED RETURN MUST NOT BE DELETABLE ──
            $this->current = 'destroy-approved';
            $destroyFailed = false;
            try {
                $service->destroy($ret->id);
            } catch (\Throwable $e) {
                $destroyFailed = true;
            }
            $this->check('destroy: approved return cannot be deleted', $destroyFailed && PurchaseReturn::withTrashed()->find($ret->id)->deleted_at === null);
        } catch (\Throwable $e) {
            $this->failures[] = 'EXCEPTION in [' . $this->current . ']: ' . $e->getMessage();
            $this->error('EXCEPTION in [' . $this->current . ']: ' . $e->getMessage());
        }

        DB::rollBack();
        $this->info('ALL TEST CHANGES ROLLED BACK — database unchanged.');

        if ($this->failures) {
            $this->error('FAILED: ' . count($this->failures) . ' of ' . $this->checks . ' checks failed.');
            foreach ($this->failures as $f) {
                $this->error(' - ' . $f);
            }
            return 1;
        }

        $this->info('ALL ' . $this->checks . ' CHECKS PASSED.');
        return 0;
    }

    protected function loginFirstUser()
    {
        try {
            if (class_exists(User::class)) {
                $user = User::orderBy('id')->first();
                if ($user) {
                    Auth::login($user);
                }
            }
        } catch (\Throwable $e) {
            // created_by / approved_by are nullable — the test can run without a user.
        }
    }

    protected function payload($branch, $warehouse, $ledger, $product, $qty, $price, $type, $remarks, $projectId = null)
    {
        $data = [
            'return_date' => date('Y-m-d'),
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'ledger_id' => $ledger->id,
            'remarks' => $remarks,
            'product_id' => [$product->id],
            'returned_qty' => [$qty],
            'unit_price' => [$price],
            'purchasetype' => [$type],
            'reason' => ['selftest'],
        ];
        if ($projectId) {
            $data['project_id'] = $projectId;
        }
        return $data;
    }

    protected function storeReturn(PurchaseReturnService $service, $branch, $warehouse, $ledger, $product, $qty, $price, $type, $remarks, $projectId = null)
    {
        $req = Request::create('/', 'POST', $this->payload($branch, $warehouse, $ledger, $product, $qty, $price, $type, $remarks, $projectId));
        $this->validateOrFail($service->storeValidation($req), $req, 'store');
        return $service->store($req);
    }

    protected function validateOrFail(array $rules, Request $request, $where)
    {
        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            $this->error('VALIDATION FAILED at ' . $where . ': ' . json_encode($e->errors()));
            throw $e;
        }
    }

    protected function moneyEq($a, $b)
    {
        return abs((float) $a - (float) $b) < 0.005;
    }

    protected function check($name, $condition, $detail = '')
    {
        $this->checks++;
        if ($condition) {
            $this->info('  PASS: ' . $name);
        } else {
            $this->failures[] = $name . ($detail !== '' ? ' (got: ' . $detail . ')' : '');
            $this->error('  FAIL: ' . $name . ($detail !== '' ? ' — got: ' . $detail : ''));
        }
    }
}
