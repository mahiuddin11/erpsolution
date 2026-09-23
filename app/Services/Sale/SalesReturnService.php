<?php

namespace App\Services\Sale;

use App\Models\AccountTransaction;
use App\Models\SaleReturn;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Transection;
use App\Repositories\Sale\SaleReturnRepositories;
use Illuminate\Support\Facades\DB;

class SalesReturnService
{
    private $SalesReturnRepositories;

    public function __construct(SaleReturnRepositories $SalesReturnRepositories)
    {
        $this->SalesReturnRepositories = $SalesReturnRepositories;
    }

    public function getList($request)
    {
        // dd('Sales Service', $request->all());
        return $this->SalesReturnRepositories->getList($request);
    }

    public function storeValidation($request)
    {
        return [
            'return_no'              => 'required|string',
            'original_sale_id'       => 'required|integer|exists:sales,id',
            'return_date'            => 'required|date',
            'sales_person_id'        => 'nullable|integer',
            'branch_id'              => 'required|integer',
            'warehouse_id'           => 'nullable|integer',
            'ledger_id'              => 'required|integer',
            'remarks'                => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.sale_detail_id' => 'required|integer',
            'items.*.returned_qty'   => 'required|numeric|min:0',
            'items.*.condition'      => 'required|in:good,damaged',
            'items.*.reason'         => 'nullable|string',
        ];
    }

    public function store($request)
    {
        return $this->SalesReturnRepositories->store($request);
    }

    public function approve($id)
    {


        $saleReturn = SaleReturn::with(['sale', 'details.saleDetail'])->findOrFail($id);


        if ($saleReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        DB::beginTransaction();
        try {
            $sale = $saleReturn->sale;


            $targetBranchId = $sale->branch_id;

            foreach ($saleReturn->details as $detail) {

                if ($detail->condition === 'good') {

                    $stock = new Stock();
                    $stock->product_id  = $detail->product_id;
                    $stock->quantity    = $detail->returned_qty;
                    $stock->branch_id   = $targetBranchId;
                    $stock->unit_price  = $detail->unit_price;
                    $stock->total_price = $detail->line_amount;
                    $stock->general_id  = $saleReturn->id;      // references THIS return, same convention as general_id=Sale_id on the original
                    $stock->invoice_no  = $saleReturn->return_no;
                    $stock->date        = $saleReturn->return_date;
                    $stock->status      = 'Sale Return';
                    $stock->created_by  = auth()->id();
                    $stock->save();

                    // purchasetype isn't stored on sale_return_details, so pull it from
                    // the original sale line via the sale_detail relationship — needed
                    // to match the correct StockSummary row.
                    $purchasetype = optional($detail->saleDetail)->purchasetype;

                    // >>> NEW: increment, mirroring Sale::store()'s StockSummary lookup
                    // (same product_id/type=Branch/branch_id/purchasetype match), but
                    // reversed direction (+= instead of -=). Unlike the original sale
                    // code, this also creates the summary row if none exists yet —
                    // a defensive addition so a return can never silently vanish if a
                    // matching summary row is somehow missing.
                    $summary = StockSummary::where('product_id', $detail->product_id)
                        ->where('type', 'Branch')
                        ->where('branch_id', $targetBranchId)
                        ->where('purchasetype', $purchasetype)
                        ->first();

                    if ($summary) {
                        $summary->increment('quantity', $detail->returned_qty);
                    } else {
                        StockSummary::create([
                            'product_id'   => $detail->product_id,
                            'branch_id'    => $targetBranchId,
                            'type'         => 'Branch',
                            'purchasetype' => $purchasetype,
                            'quantity'     => $detail->returned_qty,
                        ]);
                    }
                    // <<< END NEW
                }
                // 'damaged' lines intentionally skipped — not restocked (write-off).
                // No Stock/StockSummary entry for these yet; say if you want a
                // separate damage/scrap log later.
            }

            $returnAmount = (float) $saleReturn->grand_total;

            // NOTE: Sale::store() used a separate $accountbranch (the TRUE branch,
            // captured BEFORE the sub_warehouse override) for its AccountTransaction
            // rows — that original value is never persisted anywhere on the Sale
            // record, so it can't be recovered exactly here. Using $sale->branch_id
            // (the resolved value) as the closest available substitute. Flag if this
            // matters for branch-wise financial reports in your case.
            $accountBranchId = $sale->branch_id;

            // >>> NEW: reverses the exact two AccountTransaction rows Sale::store()
            // creates, direction flipped — Revenue account debited (reduced) instead
            // of credited, customer ledger credited (reduced) instead of debited.
            // payment_invoice intentionally left unset here too, matching the
            // established convention that it's never set on sale-type rows.

            AccountTransaction::create([
                'invoice'    => $saleReturn->return_no,
                'table_id'   => $saleReturn->id,
                'account_id' => getAccountByUniqueID(18)->id, // same Sales Revenue account as the original sale
                'type'       => 2,
                'branch_id'  => $accountBranchId,
                'debit'      => $returnAmount,
                'remark'     => 'Sale Return #' . $saleReturn->return_no,
                'created_by' => auth()->id(),
                'created_at' => $saleReturn->return_date,
            ]);

            AccountTransaction::create([
                'invoice'    => $saleReturn->return_no,
                'table_id'   => $saleReturn->id,
                'account_id' => $saleReturn->ledger_id, // same customer ledger the original sale used
                'type'       => 2,
                'branch_id'  => $accountBranchId,
                'credit'     => $returnAmount,
                'remark'     => 'Sale Return #' . $saleReturn->return_no,
                'created_by' => auth()->id(),
                'created_at' => $saleReturn->return_date,
            ]);
            // <<< END NEW

            // >>> NEW — BEST-EFFORT, please review before relying on this:
            // If the original sale was paid in Cash, the customer is now owed a
            // refund. I haven't seen an existing "refund/payable to customer"
            // pattern anywhere in your shared code, so this reconstructs it by
            // mirroring Sale::store()'s Cash-payment Transection block with the
            // direction reversed (credit = money going OUT, instead of debit).
            // If a Due sale is being returned, no Transection is needed — the
            // AccountTransaction credit above already reduces what the customer owes.
            if ($sale->payment_type === 'Cash') {
                $refund = new Transection();
                $refund->date       = $saleReturn->return_date;
                $refund->account_id = $saleReturn->refund_account_id ?? null; // which Cash/Bank account pays the refund — confirm this column exists on sale_returns
                $refund->payment_id = $saleReturn->id;
                $refund->branch_id  = $targetBranchId;
                $refund->type       = 10; // same code Sale::store() used for its Cash payment row — CONFIRM this is appropriate for a refund, may need its own type value
                $refund->note       = 'Refund for Sale Return #' . $saleReturn->return_no;
                $refund->amount     = $returnAmount;
                $refund->credit     = $returnAmount; // opposite of the original debit
                $refund->save();
            }


            $saleReturn->status      = 'approved';
            $saleReturn->approved_by = auth()->id();
            $saleReturn->approved_at = now();
            $saleReturn->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve return: ' . $e->getMessage());
        }

        return redirect()->route('sale.sale.return')->with('success', 'Return #' . $saleReturn->return_no . ' approved successfully.');
    }
}
