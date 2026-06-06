<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\StockAjdustment;
use App\Models\StockAjdustmentDetailst;
use App\Models\Stock;
use App\Models\StockSummary;
use Illuminate\Support\Facades\DB;

class StockAdjustmentRepositories
{

    private $StockAjdustment;
    private $user_id;
    private $purchases;


    public function __construct(StockAjdustment $StockAjdustment)
    {
        $this->StockAjdustment = $StockAjdustment;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        $result = $this->StockAjdustment::latest()->get();
        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'invoice_no',
        );

        $auth = Auth::user();

        $edit = Helper::roleAccess('inventorySetup.stockAdjustment.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.stockAdjustment.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.stockAdjustment.show')  ? 1 : 0;
        $approve = Helper::roleAccess('inventorySetup.stockAdjustment.approval') && empty($auth->branch_id) ? 1 : 0;
        $ced = $edit + $delete + $view + $approve;

        $totalData = $this->StockAjdustment::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $purchases = $this->StockAjdustment::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->StockAjdustment::count();
        } else {
            $search = $request->input('search.value');
            $purchases = $this->StockAjdustment::where('invoice_no', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->StockAjdustment::where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();

        if ($purchases) {
            foreach ($purchases as $key => $purchase) {

                $nestedData['id'] = $key + 1;
                $nestedData['invoice_no'] = $purchase->invoice_no;
                $nestedData['date'] = $purchase->date;
                $nestedData['branch'] = $purchase->branch->name ?? 'N/A';
                $nestedData['adjustment_type'] = $purchase->adjustment_type;
                $nestedData['subtotal'] = $purchase->subtotal;
                $nestedData['grand_total'] = $purchase->grand_total;
                $nestedData['status'] = "<b>$purchase->status</b>";

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.stockAdjustment.edit', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($approve != 0)
                        $approve_data = '<a href="' . route('inventorySetup.stockAdjustment.approval', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fas fa-check"></i></a>';
                    else
                        $approve_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('inventorySetup.stockAdjustment.show', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.stockAdjustment.destroy', $purchase->id) . '" delete_id="' . $purchase->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $purchase->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';
                    $nestedData['action'] = $edit_data . ' ' . $approve_data . ' ' . $view_data . ' ' . $delete_data;
                else :
                    $nestedData['action'] = '';
                endif;
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return $json_data;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {
        $result = $this->StockAjdustment::find($id);
        return $result;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $StockAjdustment = new $this->StockAjdustment();
            $StockAjdustment->invoice_no = $request->invoice_no;
            $StockAjdustment->date = $request->date;
            $StockAjdustment->branch_id = $request->branch_id;
            $StockAjdustment->quantity = array_sum($request->qty);
            $StockAjdustment->subtotal = array_sum($request->unitprice);
            $StockAjdustment->grand_total = array_sum($request->total);
            $StockAjdustment->status = 'Pending';
            $StockAjdustment->adjustment_type = $request->adjustment_type;
            $StockAjdustment->created_by = Auth::user()->id;
            $StockAjdustment->note = $request->narration;
            $StockAjdustment->save();
            $StockAjdustments_id = $StockAjdustment->id;


            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;
            for ($i = 0; $i < count($category_id); $i++) {
                $purchaseDetail = new StockAjdustmentDetailst();
                $purchaseDetail->product_id = $proName[$i];
                $purchaseDetail->category_id = $category_id[$i];
                $purchaseDetail->quantity = $qty[$i];
                $purchaseDetail->branch_id = $request->branch_id;
                $purchaseDetail->unit_price = $subtotal[$i];
                $purchaseDetail->total_price = $grand_total[$i];
                $purchaseDetail->purchases_id = $StockAjdustments_id;
                $purchaseDetail->date = $request->date;
                $StockAjdustment->status = 'Pending';
                $purchaseDetail->created_by = Auth::user()->id;
                $purchaseDetail->save();
            }

            activity_log(
                'create',
                'stock_adjustments',
                array_merge($StockAjdustment->toArray(), [
                    'invoice_no'      => $request->invoice_no,
                    'adjustment_type' => $request->adjustment_type,
                    'status' => $StockAjdustment->status,
                ]),
                [],
                "Stock Adjustment created (Invoice: {$request->invoice_no}) — Type: {$request->adjustment_type} — Aprovel Status : {$StockAjdustment->status}"
            );


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return $StockAjdustment;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $purchase = $this->StockAjdustment::findOrFail($id);
            $oldData = $purchase->toArray();
            // $purchase->invoice_no = $request->invoice_no;
            $purchase->date = $request->date;
            $purchase->branch_id = $request->branch_id;

            $purchase->quantity = array_sum($request->qty);
            $purchase->subtotal = array_sum($request->unitprice);
            $purchase->grand_total = array_sum($request->total);
            $purchase->status = 'Pending';
            $purchase->adjustment_type = $request->adjustment_type;
            $purchase->updated_by = Auth::user()->id;
            $purchase->note = $request->narration;
            $purchase->save();
            $purchases_id = $purchase->id;


            StockAjdustmentDetailst::where('purchases_id', $purchase->id)->delete();

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;

            for ($i = 0; $i < count($category_id); $i++) {
                $purchaseDetail = new StockAjdustmentDetailst();
                $purchaseDetail->product_id = $proName[$i];
                $purchaseDetail->quantity = $qty[$i];
                $purchaseDetail->category_id = $category_id[$i];
                $purchaseDetail->branch_id = $request->branch_id;
                $purchaseDetail->unit_price = $subtotal[$i];
                $purchaseDetail->total_price = $grand_total[$i];
                $purchaseDetail->purchases_id = $purchases_id;
                $purchaseDetail->date = $request->date;
                $purchaseDetail->status = 'Pending';
                $purchaseDetail->created_by = Auth::user()->id;
                $purchaseDetail->save();
            }

            activity_log(
                'update',
                'stock_adjustments',
                array_merge($purchase->toArray(), [
                    'invoice_no'      => $purchase->invoice_no,
                    'adjustment_type' => $request->adjustment_type,
                ]),
                $oldData,
                "Stock Adjustment updated (Invoice: {$purchase->invoice_no}) — Type: {$request->adjustment_type}"
            );


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return $purchase;
    }

    // public function storeapproval($request, $id)
    // {


    //     if ($request->adjustment_type == 'Lost') {
    //         $adjustment_type = 'Loss';
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $StockAjdustment = $this->StockAjdustment::findOrFail($id);


    //         $oldData = $StockAjdustment->toArray();

    //         // $purchase->invoice_no = $request->invoice_no;
    //         $StockAjdustment->date = $request->date;
    //         $StockAjdustment->branch_id = $request->branch_id;
    //         $StockAjdustment->quantity = array_sum($request->qty);
    //         $StockAjdustment->approval_qty = array_sum($request->qty);
    //         $StockAjdustment->subtotal = array_sum($request->unitprice);
    //         $StockAjdustment->grand_total = array_sum($request->total);
    //         $StockAjdustment->status = 'Active';
    //         $StockAjdustment->adjustment_type = $adjustment_type ?? '';
    //         $StockAjdustment->approve_by = Auth::user()->id;
    //         $StockAjdustment->approval_date = date('Y-m-d');
    //         $StockAjdustment->note = $request->narration;
    //         $StockAjdustment->save();
    //         $StockAjdustment_id = $StockAjdustment->id;

    //         StockAjdustmentDetailst::where('purchases_id', $StockAjdustment->id)->delete();

    //         $stockDetailsId = $request->stockDetailsId;
    //         $category_id = $request->catName;
    //         $proName = $request->proName;
    //         $subtotal = $request->unitprice;
    //         $grand_total = $request->total;
    //         $qty = $request->qty;

    //         for ($i = 0; $i < count($stockDetailsId); $i++) {
    //             $StockAjdustment['product_id'] = $proName[$i];
    //             $StockAjdustment['quantity'] = $qty[$i];
    //             $StockAjdustment['category_id'] = $category_id[$i];
    //             $StockAjdustment['branch_id'] = $request->branch_id;
    //             $StockAjdustment['unit_price'] = $subtotal[$i];
    //             $StockAjdustment['total_price'] = $grand_total[$i];
    //             $StockAjdustment['purchases_id'] = $StockAjdustment_id;
    //             $StockAjdustment['date'] = $request->date;
    //             $StockAjdustment['status'] = 'Active';
    //             $StockAjdustment['approval_date'] = date('Y-m-d');
    //             StockAjdustmentDetailst::where('purchases_id', $stockDetailsId[$i])->update($StockAjdustment);

    //             // $stock = new Stock();
    //             // $stock->general_id = $purchases_id;
    //             // $stock->branch_id = $request->branch_id;
    //             // $stock->product_id = $proName[$i];
    //             // $stock->unit_price = $subtotal[$i];
    //             // $stock->total_price = $grand_total[$i];
    //             // $stock->quantity = $qty[$i];
    //             // $stock->status = $request->adjustment_type;
    //             // $stock->save();



    //             $stock = new Stock();
    //             $stock->general_id     = $StockAjdustment_id;
    //             $stock->date           = $StockAjdustment->date;
    //             $stock->branch_id      = $request->branch_id;
    //             $stock->invoice_no      = $StockAjdustment->invoice_no;
    //             $stock->product_id     = $proName[$i];
    //             $stock->unit_price     = $subtotal[$i];
    //             $stock->total_price    = $grand_total[$i];
    //             $stock->quantity       = $qty[$i];
    //             $stock->status         = $request->adjustment_type;   // Gain / Lost / Damage
    //             $stock->created_by     = Auth::id();
    //             $stock->save();


    //             // if ($request->adjustment_type == 'Lost'  || $request->adjustment_type == 'Damange') {
    //             //     $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->first();
    //             //     if (!empty($existingCheck)) :
    //             //         $newQty = $existingCheck->quantity - $qty[$i];
    //             //         StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQty));
    //             //     endif;
    //             // }
    //             // if ($request->adjustment_type == 'Gain') {
    //             //     $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->first();
    //             //     if (!empty($existingCheck)) :
    //             //         $newQty = $existingCheck->quantity + $qty[$i];
    //             //         StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQty));
    //             //     endif;
    //             // }

    //             // StockSummary updated
    //             $summary = StockSummary::firstOrNew([
    //                 'product_id' => $proName[$i],
    //                 'branch_id'  => $request->branch_id,
    //                 'type'       => 'Branch'
    //             ]);

    //             if ($request->adjustment_type == 'Gain') {
    //                 $summary->quantity += $qty[$i];
    //             } else if (in_array($adjustment_type, ['Loss', 'Damage'])) {
    //                 $summary->quantity -= $qty[$i];
    //             }

    //             $summary->save();


    //             // $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->first();
    //             // if (!empty($existingCheck)) :
    //             //     $newQty = $existingCheck->quantity + $qty[$i];
    //             //     StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQty));
    //             // else :
    //             //     $stockSummary = new StockSummary();
    //             //     $stockSummary->branch_id = $request->branch_id;
    //             //     $stockSummary->product_id = $proName[$i];
    //             //     $stockSummary->quantity = $qty[$i];
    //             //     $stockSummary->type = "Branch";
    //             //     $stockSummary->save();
    //             // endif;
    //         }

    //         activity_log(
    //             'approve',
    //             'stock_adjustments',
    //             $StockAjdustment->toArray(),
    //             $oldData,
    //             "Stock Adjustment Approved by " . Auth::user()->name .
    //                 " (Invoice: {$StockAjdustment->invoice_no}) — Type: {$adjustment_type}"
    //         );


    //         DB::commit();
    //     } catch (\Exception $e) {
    //         dd($e->getMessage(), $e->getLine());
    //         DB::rollback();
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }
    //     return $purchase;
    // }


    public function storeapproval($request, $id)
    {
        if ($request->adjustment_type == 'Lost') {
            $adjustment_type = 'Loss';
        } else {
            $adjustment_type = $request->adjustment_type;
        }

        DB::beginTransaction();
        try {
            $StockAjdustment = $this->StockAjdustment::findOrFail($id);

            // Old Data 
            $oldData = $StockAjdustment->toArray();

            // ==================== Main Record Update ====================
            $StockAjdustment->date            = $request->date;
            $StockAjdustment->branch_id       = $request->branch_id;
            $StockAjdustment->quantity        = array_sum($request->qty);
            $StockAjdustment->approval_qty    = array_sum($request->qty);
            $StockAjdustment->subtotal        = array_sum($request->unitprice);
            $StockAjdustment->grand_total     = array_sum($request->total);
            $StockAjdustment->status          = 'Active';
            $StockAjdustment->adjustment_type = $adjustment_type;
            $StockAjdustment->approve_by      = Auth::user()->id;
            $StockAjdustment->approval_date   = date('Y-m-d');
            $StockAjdustment->note            = $request->narration;
            $StockAjdustment->save();

            $StockAjdustment_id = $StockAjdustment->id;


            StockAjdustmentDetailst::where('purchases_id', $StockAjdustment_id)->delete();

            $stockDetailsId = $request->stockDetailsId;
            $category_id    = $request->catName;
            $proName        = $request->proName;
            $subtotal       = $request->unitprice;
            $grand_total    = $request->total;
            $qty            = $request->qty;

            for ($i = 0; $i < count($stockDetailsId); $i++) {

                // ==================== Stock Adjustment Details Update ====================

                $purchaseDetail = [
                    'product_id'     => $proName[$i],
                    'quantity'       => $qty[$i],
                    'category_id'    => $category_id[$i],
                    'branch_id'      => $request->branch_id,
                    'unit_price'     => $subtotal[$i],
                    'total_price'    => $grand_total[$i],
                    'purchases_id'   => $StockAjdustment_id,
                    'date'           => $request->date,
                    'status'         => 'Active',
                    'approval_date'  => date('Y-m-d'),
                ];


                StockAjdustmentDetailst::where('purchases_id', $stockDetailsId[$i])
                    ->update($purchaseDetail);

                // ==================== Stock Transaction ====================
                $stock = new Stock();
                $stock->general_id   = $StockAjdustment_id;
                $stock->date         = $StockAjdustment->date;
                $stock->branch_id    = $request->branch_id;
                $stock->invoice_no   = $StockAjdustment->invoice_no;
                $stock->product_id   = $proName[$i];
                $stock->unit_price   = $subtotal[$i];
                $stock->total_price  = $grand_total[$i];
                $stock->quantity     = $qty[$i];
                $stock->status       = $request->adjustment_type;
                $stock->created_by   = Auth::id();
                $stock->save();

                // ==================== StockSummary Update ====================
                $summary = StockSummary::firstOrNew([
                    'product_id' => $proName[$i],
                    'branch_id'  => $request->branch_id,
                    'type'       => 'Branch'
                ]);

                if ($adjustment_type == 'Gain') {
                    $summary->quantity = ($summary->quantity ?? 0) + $qty[$i];
                } else if (in_array($adjustment_type, ['Loss', 'Damage', 'Lost'])) {
                    $summary->quantity = ($summary->quantity ?? 0) - $qty[$i];
                }

                $summary->save();
            }

            //  Activity Log
            activity_log(
                'approve',
                'stock_adjustments',
                $StockAjdustment->toArray(),
                $oldData,
                "Stock Adjustment Approved by " . Auth::user()->name .
                    " (Invoice: {$StockAjdustment->invoice_no}) — Type: {$adjustment_type}"
            );

            DB::commit();
            return $StockAjdustment;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage(), $e->getLine());
            return redirect('inventory-purchase-create')
                ->with('error', 'Something Wrong Please try again: ' . $e->getMessage());
        }
    }

    public function statusUpdate($id, $status)
    {

        $purchase = $this->StockAjdustment::find($id);
        $purchase->status = $status;
        $purchase->save();
        return $purchase;
    }

    // public function destroy($id)
    // {
    //     $purchase = $this->StockAjdustment::find($id);
    //     $oldData = $purchase->toArray();

    //     $purchase->delete();

    //     activity_log(
    //         'delete',
    //         'stock_adjustments',
    //         [],
    //         $oldData,
    //         "Stock Adjustment deleted (Invoice: {$oldData['invoice_no']}) — Type: {$oldData['adjustment_type']}"
    //     );

    //     return true;
    // }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $StockAdustment = $this->StockAjdustment::find($id);

            if (!$StockAdustment) {
                session()->flash('error', 'Stock Adjustment not found!');
                DB::rollBack();
                return false;
            }

            $oldData       = $StockAdustment->toArray();
            $adjustmentType = $StockAdustment->adjustment_type;
            $isApproved    = $StockAdustment->status == 'Active';


            $stockDetails = DB::table('stock_ajdustment_detailsts')
                ->where('purchases_id', $StockAdustment->id)
                ->get();


            if ($isApproved) {

                if ($adjustmentType == 'Lost') {
                    $adjustmentType = 'Loss';
                }

                foreach ($stockDetails as $item) {
                    // Stock Summary Reverse
                    $summary = StockSummary::where([
                        'product_id' => $item->product_id,
                        'branch_id'  => $item->branch_id,
                        'type'       => 'Branch',
                    ])->first();

                    if ($summary) {
                        if ($adjustmentType == 'Gain') {
                            $newQty = $summary->quantity - $item->quantity;
                        } elseif (in_array($adjustmentType, ['Loss', 'Damage'])) {
                            $newQty = $summary->quantity + $item->quantity;
                        } else {
                            $newQty = $summary->quantity;
                        }

                        if ($newQty <= 0) {
                            $summary->quantity = $newQty;
                            $summary->save();
                        } else {
                            $summary->quantity = $newQty;
                            $summary->save();
                        }
                    }

                    // Stocks Ledger Delete
                    Stock::where('invoice_no', $StockAdustment->invoice_no)
                        ->where('product_id', $item->product_id)
                        ->where('general_id', $id)
                        ->delete();
                }
            }

            // Details Delete
            DB::table('stock_ajdustment_detailsts')->where('purchases_id', $id)->delete();


            $StockAdustment->forceDelete();

            // Activity Log
            activity_log(
                'delete',
                'stock_adjustments',
                [],
                $oldData,
                "Stock Adjustment deleted (Invoice: {$oldData['invoice_no']}) — Type: {$oldData['adjustment_type']}"
            );

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong: ' . $e->getMessage());
            return false;
        }
    }
}
