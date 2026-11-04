<?php

namespace App\Services\InventorySetup;

use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Repositories\InventorySetup\PurchaseReturnRepositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PurchaseReturnService
{
    protected $purchaseReturnRepo;

    public function __construct(PurchaseReturnRepositories $purchaseReturnRepo)
    {
        $this->purchaseReturnRepo = $purchaseReturnRepo;
    }

    public function getList($request)
    {
        return $this->purchaseReturnRepo->getList($request);
    }

    
    public function storeValidation(Request $request)
    {
        return [
            'return_date'     => 'required|date',
            'branch_id'       => 'required|exists:branches,id',
            'warehouse_id'    => 'required|exists:warehouses,id',
            'project_id'      => 'nullable|exists:projects,id',
            'ledger_id'       => 'required|exists:chart_of_accounts,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'remarks'         => 'nullable|string|max:500',
            'product_id'      => 'required|array|min:1',
            'product_id.*'    => 'required|exists:products,id',
            'returned_qty'    => 'required|array|min:1',
            'returned_qty.*'  => 'required|numeric|min:0.01',
            'unit_price'      => 'required|array|min:1',
            'unit_price.*'    => 'required|numeric|min:0',
            'purchasetype'    => 'required|array|min:1',
            'purchasetype.*'  => 'required|in:local,imported',
            'reason'          => 'nullable|array',
            'reason.*'        => 'nullable|string|max:255',
        ];
    }
    

    public function store($request)
    {
        return $this->purchaseReturnRepo->store($request);
    }

    public function updateValidation($request, $id)
    {
      
         return [
            'return_date'     => 'required|date',
            'branch_id'       => 'required|exists:branches,id',
            'warehouse_id'    => 'required|exists:warehouses,id',
            'project_id'      => 'nullable|exists:projects,id',
            'ledger_id'       => 'required|exists:chart_of_accounts,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'remarks'         => 'nullable|string|max:500',
            'product_id'      => 'required|array|min:1',
            'product_id.*'    => 'required|exists:products,id',
            'returned_qty'    => 'required|array|min:1',
            'returned_qty.*'  => 'required|numeric|min:0.01',
            'unit_price'      => 'required|array|min:1',
            'unit_price.*'    => 'required|numeric|min:0',
            'purchasetype'    => 'required|array|min:1',
            'purchasetype.*'  => 'required|in:local,imported',
            'reason'          => 'nullable|array',
            'reason.*'        => 'nullable|string|max:255',
        ];
    }




    public function update($request, $id)
    {
        return $this->purchaseReturnRepo->update($request, $id);
    }

    public function edit($id)
    {
        return $this->purchaseReturnRepo->edit($id);
    }

    public function destroy($id)
    {
        return $this->purchaseReturnRepo->destroy($id);
    }

    /**
     * Products of one category for the create/edit row picker.
     * Returns plain [{id, name}] JSON — no dependency on the
     * purchase module's getProductList endpoint.
     */
    public function searchProducts(Request $request)
    {
        $query = Product::where('status', 'Active')->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('term')) {
            $query->where('name', 'like', '%' . $request->term . '%');
        }

        return $query->limit(100)->get(['id', 'name']);
    }

    /**
     * The ledger account linked to a supplier: chart_of_accounts rows
     * with accountable_type = Supplier and accountable_id = the
     * supplier id (the same link the purchase form's ledger list uses).
     * Used to auto-fill the ledger select when a supplier is picked.
     * Returns null when the supplier has no linked ledger — then the
     * user picks the ledger by hand on the form.
     */
    public function getSupplierLedger(Request $request)
    {
        $account = ChartOfAccount::where('accountable_type', \App\Models\Supplier::class)
            ->where('accountable_id', $request->supplier_id)
            ->where('status', 'Active')
            ->orderBy('id')
            ->first();

        return $account
            ? ['ledger_id' => $account->id, 'account_name' => $account->account_name]
            : null;
    }

    
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $purchaseReturn = PurchaseReturn::where('id', $id)->lockForUpdate()->firstOrFail();

           

            if ($purchaseReturn->status !== 'pending') {
                throw new \Exception('Only pending returns can be approved.');
            }

            $details = PurchaseReturnDetail::where('purchase_return_id', $purchaseReturn->id)->get();

            if ($details->isEmpty()) {
                throw new \Exception('This return has no items.');
            }

            $ledger = ChartOfAccount::where('id', $purchaseReturn->ledger_id)
                ->where('status', 'Active')
                ->first();

            if (!$ledger) {
                throw new \Exception('Supplier ledger not found or inactive (ID: ' . $purchaseReturn->ledger_id . ').');
            }

            // The contra account is found by name + Active, never hardcoded.
            $creditAccount = ChartOfAccount::where('account_name', 'Purchase Return')
                ->where('status', 'Active')
                ->first();

            if (!$creditAccount) {
                throw new \Exception("Chart account 'Purchase Return' not found or inactive. Create it under Purchase first.");
            }

            foreach ($details as $detail) {
                $this->decreaseStock($purchaseReturn, $detail);
            }

            $amount = round((float) $purchaseReturn->grand_total, 2);

            
            // Dr: the selected supplier's ledger, against that supplier.
            AccountTransaction::create([
                'invoice'      => $purchaseReturn->return_no,
                'table_id'     => $purchaseReturn->id,
                'account_id'   => $ledger->id,
                'type'         => 'purchase_return',
                'branch_id'    => $purchaseReturn->branch_id,
                'warehouse_id' => $purchaseReturn->warehouse_id,
                'debit'        => $amount,
                'remark'       => $purchaseReturn->remarks ?? '',
                'voucher_date' => $purchaseReturn->return_date,
                'created_by'   => Auth::id(),
                'supplier_id'  => $purchaseReturn->supplier_id ?: 0,
                'created_at'   => $purchaseReturn->return_date,
            ]);

            // Cr: Purchase Return contra account.
            AccountTransaction::create([
                'invoice'      => $purchaseReturn->return_no,
                'table_id'     => $purchaseReturn->id,
                'account_id'   => $creditAccount->id,
                'type'         => 'purchase_return',
                'branch_id'    => $purchaseReturn->branch_id,
                'warehouse_id' => $purchaseReturn->warehouse_id,
                'credit'       => $amount,
                'remark'       => $purchaseReturn->remarks ?? '',
                'voucher_date' => $purchaseReturn->return_date,
                'created_by'   => Auth::id(),
                'supplier_id'  => $purchaseReturn->supplier_id ?: 0,
                'created_at'   => $purchaseReturn->return_date,
            ]);

            $purchaseReturn->status      = 'approved';
            $purchaseReturn->approved_by = Auth::id();
            $purchaseReturn->approved_at = now();
            $purchaseReturn->save();

            DB::commit();

            return $purchaseReturn;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reject(Request $request, $id)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($id);

        if ($purchaseReturn->status !== 'pending') {
            throw new \Exception('Only pending returns can be rejected.');
        }

        $purchaseReturn->status           = 'rejected';
        $purchaseReturn->rejection_reason = $request->rejection_reason;
        $purchaseReturn->updated_by       = Auth::id();
        $purchaseReturn->save();

        return $purchaseReturn;
    }

    /**
     * Decrement exactly one bucket for one return line — this is where
     * the stock goes down.
     *
     * Bucket order:
     *  1. Project bucket (when the return names a project) — both live
     *     key shapes are tried: shape 1 (branch_id = project id) then
     *     shape 2 (project_id column + branch_id 0).
     *  2. Branch bucket of the selected warehouse.
     *  3. For project returns only: the destination Branch bucket of
     *     the most recent accepted project->warehouse transfer of this
     *     product — the transfer voucher is the proof the goods are
     *     there now. project_to_project transfers are NOT followed.
     *
     * The guard never lets a summary go negative. Quantities are
     * integers in the DB, so the decrement is rounded in PHP to match
     * exactly what gets stored.
     */
    protected function decreaseStock(PurchaseReturn $purchaseReturn, PurchaseReturnDetail $detail)
    {
        $productId    = (int) $detail->product_id;
        $qty          = (float) $detail->returned_qty;
        $purchasetype = $detail->purchasetype
            ?: optional($detail->purchaseDetail)->purchasetype
            ?: 'local';
        $projectId   = (int) ($purchaseReturn->project_id ?? 0);
        $branchId    = (int) $purchaseReturn->branch_id;
        $warehouseId = $purchaseReturn->warehouse_id;

        $summary          = null;
        $stockBranchId    = $branchId;
        $stockWarehouseId = $warehouseId;
        $stockProjectId   = null;

        $branchSummaryQuery = function () use ($productId, $purchasetype, $warehouseId) {
            return StockSummary::where('product_id', $productId)
                ->where('purchasetype', $purchasetype)
                ->where('type', 'Branch')
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->first();
        };

        // 1. Project bucket (both key shapes).
        if ($projectId > 0) {
            $summary = StockSummary::where('product_id', $productId)
                ->where('purchasetype', $purchasetype)
                ->where('type', 'Project')
                ->where('branch_id', $projectId)
                ->lockForUpdate()
                ->first();

            if (!$summary) {
                $summary = StockSummary::where('product_id', $productId)
                    ->where('purchasetype', $purchasetype)
                    ->where('type', 'Project')
                    ->where('project_id', $projectId)
                    ->where('branch_id', 0)
                    ->lockForUpdate()
                    ->first();
            }

            if ($summary && (float) $summary->quantity >= $qty) {
                $stockBranchId    = $projectId;
                $stockWarehouseId = null;
                $stockProjectId   = $projectId;
            } else {
                $summary = null;
            }
        }

        // 2. Branch bucket of the selected warehouse.
        if (!$summary) {
            $candidate = $branchSummaryQuery();

            if ($candidate && (float) $candidate->quantity >= $qty) {
                $summary = $candidate;
            }
        }

        // 3. Accepted-transfer destination bucket (project returns only).
        if (!$summary && $projectId > 0) {
            $transfers = DB::table('project_transfer_details as ptd')
                ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
                ->where('ptd.product_id', $productId)
                ->whereIn('pt.transfer_type', ['project_to_branch', 'project_to_warehouse'])
                ->where('ptd.status', 'Accepted')
                ->where('pt.from_project_id', $projectId)
                ->orderByDesc('pt.id')
                ->select('ptd.warehouse_id', 'ptd.branch_id')
                ->get();

            foreach ($transfers as $transfer) {
                $candidate = StockSummary::where('product_id', $productId)
                    ->where('purchasetype', $purchasetype)
                    ->where('type', 'Branch')
                    ->where('warehouse_id', $transfer->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                if ($candidate && (float) $candidate->quantity >= $qty) {
                    $summary          = $candidate;
                    $stockBranchId    = (int) ($transfer->branch_id ?: $branchId);
                    $stockWarehouseId = $transfer->warehouse_id;
                    $stockProjectId   = null;
                    break;
                }
            }
        }

        $available = $summary ? (float) $summary->quantity : 0;

        if (! $summary) {
            $short = StockSummary::where('product_id', $productId)
                ->where('purchasetype', $purchasetype)
                ->where('type', 'Branch')
                ->where('warehouse_id', $warehouseId)
                ->orderByDesc('quantity')
                ->first();

            if ($short) {
                $available = (float) $short->quantity;
            }
        }

        if (!$summary || $available < $qty) {
            throw new \Exception(
                'Insufficient stock for product ID ' . $productId
                . '. Available: ' . $available
                . ', requested return: ' . $qty . '.'
            );
        }

        $summary->quantity = (int) round($available - $qty);
        $summary->save();

        $stock                = new Stock();
        $stock->invoice_no    = $purchaseReturn->return_no;
        $stock->product_id    = $productId;
        $stock->quantity      = (int) round($qty);
        $stock->project_id    = $stockProjectId;
        $stock->branch_id     = $stockBranchId;
        $stock->warehouse_id  = $stockWarehouseId;
        $stock->unit_price    = $detail->unit_price;
        $stock->total_price   = $detail->line_amount;
        $stock->general_id    = $purchaseReturn->id;
        $stock->date          = $purchaseReturn->return_date;
        $stock->status        = 'Purchase Return';
        $stock->created_by    = Auth::id();
        $stock->save();
    }
}