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

                
                    $purchasetype = optional($detail->saleDetail)->purchasetype;

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

         
            $accountBranchId = $sale->branch_id;

          
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
