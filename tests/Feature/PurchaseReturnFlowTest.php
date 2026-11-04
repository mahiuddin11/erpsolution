<?php

namespace Tests\Feature;

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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PurchaseReturnFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::orderBy('id')->first();
        $this->assertNotNull($user, 'No user found. Create an admin user first.');
        $this->actingAs($user);
    }

    protected function fixtures(): array
    {
        $branch = Branch::where('status', 'Active')->where('parent_id', 0)->orderBy('id')->first();
        $warehouses = Warehouse::where('status', 'Active')->orderBy('id')->get();
        $ledger = ChartOfAccount::where('status', 'Active')->where('accountable_type', Supplier::class)->orderBy('id')->first()
            ?? ChartOfAccount::where('status', 'Active')->orderBy('id')->first();
        $credit = ChartOfAccount::where('account_name', 'Purchase Return')->where('status', 'Active')->first();
        $this->assertNotNull($branch, 'Need 1 Active branch (parent_id 0).');
        $this->assertFalse($warehouses->isEmpty(), 'Need 1 Active warehouse.');
        $this->assertNotNull($ledger, 'Need 1 Active ledger.');
        $this->assertNotNull($credit, 'Need the Active "Purchase Return" chart account.');

        // Scan every Active warehouse, not just the first one.
        $warehouse = null;
        $summaryRow = null;
        $product = null;
        foreach ($warehouses as $wh) {
            foreach (StockSummary::where('type', 'Branch')->where('warehouse_id', $wh->id)->where('quantity', '>', 0)->orderBy('id')->limit(20)->get() as $row) {
                $p = Product::find($row->product_id);
                if ($p && $p->status === 'Active') {
                    $warehouse = $wh;
                    $summaryRow = $row;
                    $product = $p;
                    break 2;
                }
            }
        }
        $this->assertNotNull($summaryRow, 'No Branch stock with quantity > 0 in any Active warehouse. Receive stock first.');

        return [$branch, $warehouse, $ledger, $credit, $product, $summaryRow];
    }

    protected function payload($branch, $warehouse, $ledger, $product, $qty, $price, $type, $projectId = null): array
    {
        $data = [
            'return_date' => date('Y-m-d'),
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'ledger_id' => $ledger->id,
            'remarks' => 'PHPUNIT',
            'product_id' => [$product->id],
            'returned_qty' => [$qty],
            'unit_price' => [$price],
            'purchasetype' => [$type],
            'reason' => ['phpunit'],
        ];
        if ($projectId) {
            $data['project_id'] = $projectId;
        }
        return $data;
    }

    protected function createReturn(array $fx, $qty = 1, $price = 100.00, $projectId = null): PurchaseReturn
    {
        [$branch, $warehouse, $ledger, $credit, $product, $summaryRow] = $fx;
        $type = $summaryRow->purchasetype ?: 'local';
        $this->post(route('inventorySetup.purchase.return.store'), $this->payload($branch, $warehouse, $ledger, $product, $qty, $price, $type, $projectId))
            ->assertRedirect();
        $ret = PurchaseReturn::orderByDesc('id')->first();
        $this->assertNotNull($ret);
        return $ret;
    }

    /** @test */
    public function create_stays_pending_without_posting()
    {
        $fx = $this->fixtures();
        [$branch, $warehouse, $ledger, $credit, $product, $summaryRow] = $fx;
        $beforeQty = (float) $summaryRow->quantity;

        $ret = $this->createReturn($fx);

        $this->assertSame('pending', $ret->status);
        $this->assertSame('PRT' . str_pad($ret->id, 5, '0', STR_PAD_LEFT), $ret->return_no);
        $this->assertEqualsWithDelta(100.00, (float) $ret->grand_total, 0.005);
        $this->assertEquals($beforeQty, (float) StockSummary::find($summaryRow->id)->quantity);
        $this->assertSame(0, AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->count());
        $this->assertSame(0, Stock::where('invoice_no', $ret->return_no)->count());
    }

    /** @test */
    public function store_validation_rejects_empty_items()
    {
        $fx = $this->fixtures();
        [$branch, $warehouse, $ledger, $credit, $product, $summaryRow] = $fx;
        $data = $this->payload($branch, $warehouse, $ledger, $product, 1, 100.00, $summaryRow->purchasetype ?: 'local');
        $data['product_id'] = [];
        $data['returned_qty'] = [];
        $data['unit_price'] = [];
        $data['purchasetype'] = [];

        $this->post(route('inventorySetup.purchase.return.store'), $data)->assertSessionHasErrors('product_id');
    }

    /** @test */
    public function update_recalculates_total_and_preserves_project()
    {
        $fx = $this->fixtures();
        [$branch, $warehouse, $ledger, $credit, $product, $summaryRow] = $fx;
        $project = Project::where('condition', 'One Going')->orderBy('id')->first();
        $this->assertNotNull($project, 'Need 1 "One Going" project for this test.');

        $ret = $this->createReturn($fx, 1, 100.00, $project->id);
        $this->assertSame((int) $project->id, (int) $ret->project_id);

        // No project_id, no supplier_id — like the real edit form.
        $this->put(route('inventorySetup.purchase.return.update', $ret->id),
            $this->payload($branch, $warehouse, $ledger, $product, 1, 150.00, $summaryRow->purchasetype ?: 'local'))
            ->assertRedirect();

        $fresh = $ret->fresh();
        $this->assertEqualsWithDelta(150.00, (float) $fresh->grand_total, 0.005);
        $this->assertSame((int) $project->id, (int) $fresh->project_id);
        $this->assertSame($ret->return_no, $fresh->return_no);
        $this->assertSame('pending', $fresh->status);
    }

    /** @test */
    public function approve_posts_stock_and_balanced_entries()
    {
        $fx = $this->fixtures();
        [$branch, $warehouse, $ledger, $credit, $product, $summaryRow] = $fx;
        $beforeQty = (float) $summaryRow->quantity;
        $expectedSupplier = ($ledger->accountable_type === Supplier::class) ? (int) $ledger->accountable_id : 0;

        $ret = $this->createReturn($fx, 1, 150.00);

        $this->post(route('inventorySetup.purchase.return.approve', $ret->id))
        ->assertRedirect();


        $fresh = $ret->fresh();
        $this->assertSame('approved', $fresh->status);
        $this->assertNotNull($fresh->approved_by);
        $this->assertNotNull($fresh->approved_at);
        $this->assertEquals($beforeQty - 1, (float) StockSummary::find($summaryRow->id)->quantity);

        $txns = AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->orderBy('id')->get();
        $this->assertCount(2, $txns);
        [$dr, $cr] = [$txns[0], $txns[1]];
        $this->assertSame((int) $ledger->id, (int) $dr->account_id);
        $this->assertEqualsWithDelta(150.00, (float) $dr->debit, 0.005);
        $this->assertSame((int) $credit->id, (int) $cr->account_id);
        $this->assertEqualsWithDelta(150.00, (float) $cr->credit, 0.005);
        foreach ([$dr, $cr] as $t) {
            $this->assertSame($ret->return_no, $dr->invoice, 'Dr invoice');
            $this->assertSame((int) $branch->id, (int) $dr->branch_id, 'Dr branch_id');
            $this->assertSame((int) $warehouse->id, (int) $dr->warehouse_id, 'Dr warehouse_id');
            $this->assertSame($expectedSupplier, (int) $dr->supplier_id, 'Dr supplier_id');
            $this->assertSame($ret->remarks ?? '', $dr->remark, 'Dr remark');
            $this->assertSame(date('Y-m-d'), substr((string) $dr->voucher_date, 0, 10), 'Dr voucher_date');
            $this->assertSame($ret->return_no, $cr->invoice, 'Cr invoice');
            $this->assertSame((int) $branch->id, (int) $cr->branch_id, 'Cr branch_id');
            $this->assertSame((int) $warehouse->id, (int) $cr->warehouse_id, 'Cr warehouse_id');
            $this->assertSame($expectedSupplier, (int) $cr->supplier_id, 'Cr supplier_id');
            $this->assertSame($ret->remarks ?? '', $cr->remark, 'Cr remark');
            $this->assertSame(date('Y-m-d'), substr((string) $cr->voucher_date, 0, 10), 'Cr voucher_date');

        }

        $stocks = Stock::where('invoice_no', $ret->return_no)->where('status', 'Purchase Return')->get();
        $this->assertCount(1, $stocks);
        $this->assertSame((int) $product->id, (int) $stocks[0]->product_id);
        $this->assertSame((int) $ret->id, (int) $stocks[0]->general_id);

        // Double approve must not post again.
        $this->post(route('inventorySetup.purchase.return.approve', $ret->id))->assertRedirect();
        $this->assertSame(2, AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->count());
        $this->assertSame('approved', $ret->fresh()->status);
    }

    /** @test */
    public function approve_fails_on_insufficient_stock_without_posting()
    {
        $fx = $this->fixtures();
        [$branch, $warehouse, $ledger, $credit, $product, $summaryRow] = $fx;
        $availNow = (float) $summaryRow->quantity;

        $ret = $this->createReturn($fx, $availNow + 5, 100.00);

        $this->post(route('inventorySetup.purchase.return.approve', $ret->id))->assertRedirect();

        $this->assertSame('pending', $ret->fresh()->status);
        $this->assertSame(0, AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->count());
        $this->assertEquals($availNow, (float) StockSummary::find($summaryRow->id)->quantity);
    }

    /** @test */
    public function reject_and_destroy_behave_correctly()
    {
        $fx = $this->fixtures();
        $ret = $this->createReturn($fx);

        $this->post(route('inventorySetup.purchase.return.reject', $ret->id), ['rejection_reason' => 'phpunit reject'])
    ->assertRedirect();


        $fresh = $ret->fresh();
        $this->assertSame('rejected', $fresh->status);
        $this->assertSame('phpunit reject', $fresh->rejection_reason);
        $this->assertSame(0, AccountTransaction::where('type', 'purchase_return')->where('table_id', $ret->id)->count());

        $this->post(route('inventorySetup.purchase.return.destroy', $ret->id))->assertRedirect();
        $this->assertSoftDeleted('purchase_returns', ['id' => $ret->id]);
        $this->assertSame(0, PurchaseReturnDetail::where('purchase_return_id', $ret->id)->count());

        // An approved return must not be deletable.
        $ret2 = $this->createReturn($fx);
        $this->post(route('inventorySetup.purchase.return.approve', $ret2->id))->assertRedirect();
        $this->post(route('inventorySetup.purchase.return.destroy', $ret2->id))->assertRedirect();
        $this->assertDatabaseHas('purchase_returns', ['id' => $ret2->id, 'deleted_at' => null]);
    }

    /** @test */
    public function pages_load()
    {
        $ret = $this->createReturn($this->fixtures());
        $this->get(route('inventorySetup.purchase.return.show', $ret->id))->assertOk();
        $this->get(route('inventorySetup.purchase.return.edit', $ret->id))->assertOk();
        $this->get(route('inventorySetup.purchase.return.approvePage', $ret->id))->assertOk();
    }
}
