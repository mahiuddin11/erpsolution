<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Grn;
use App\Models\Grn_detail;
use App\Models\Purchases;
use App\Models\PurchasesDetails;
use Illuminate\Support\Facades\DB;

class GrnRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $goodrcvnote;
    /**
     * CourseRepository constructor.
     * @param brand $purchase
     */
    public function __construct(Grn $goodrcvnote)
    {
        $this->goodrcvnote = $goodrcvnote;
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
        $result = $this->goodrcvnote::latest()->get();
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

        $edit = Helper::roleAccess('inventorySetup.goodrcvnote.edit') ? 0 : 0;
        $delete = Helper::roleAccess('inventorySetup.goodrcvnote.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.goodrcvnote.invoice') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->goodrcvnote::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $goodrcvnote = $this->goodrcvnote::offset($start);
            // if ($auth->branch_id !== null) {
            //     $goodrcvnote = $goodrcvnote->where('branch_id', $auth->branch_id);
            // }
            $goodrcvnote = $goodrcvnote->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->goodrcvnote::count();
        } else {
            $search = $request->input('search.value');
            $goodrcvnote = $this->goodrcvnote::where('invoice_no', 'like', "%{$search}%");
            if ($auth->branch_id !== null) {
                $goodrcvnote = $goodrcvnote->where('branch_id', $auth->branch_id);
            }
            $goodrcvnote = $goodrcvnote
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = $this->goodrcvnote::where('invoice_no', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($goodrcvnote) {
            foreach ($goodrcvnote as $key => $grn) {
                // dd($grn);
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $grn->date;
                $nestedData['invoice_no'] = $grn->invoice_no;
                $nestedData['supplier_id'] = "";
                $nestedData['project_id'] =  $grn->project->name ?? '';
                $nestedData['total_price'] = $grn->total_price;
                // $nestedData['payment'] = $grn->payment;
                // $nestedData['due'] = $grn->due;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.goodrcvnote.edit', $grn->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.goodrcvnote.invoice', $grn->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.goodrcvnote.destroy', $grn->id) . '" delete_id="' . $grn->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $grn->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';
                    $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;
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
        $result = $this->goodrcvnote::find($id);
        return $result;
    }

    // public function store($request)
    // {

    //     $invoice_no = $request->grnCode;
    //     $exists = Grn::where('invoice_no', $invoice_no)->select('invoice_no')->exists();

    //     if ($exists) {
    //         $lastGrn = Grn::latest('id')->first();
    //         if ($lastGrn) {
    //             $nextCode = $lastGrn->id + 1;
    //         } else {
    //             $nextCode = 1;
    //         }
    //         $invoice_no = 'GRN' . str_pad($nextCode, 5, "0", STR_PAD_LEFT);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $remainingtt = $request->remaining;
    //         $approve_qtyt = $request->approve_qty;
    //         $statuscheck = array_sum($remainingtt) + array_sum($approve_qtyt);
    //         $goodrcvnote = new Grn();
    //         $goodrcvnote->date = $request->date;
    //         $goodrcvnote->invoice_no = $invoice_no;
    //         $goodrcvnote->supplier_id = $request->subblier_id ?? 0;
    //         $goodrcvnote->purchase_voucher_id = $request->purchase_voucher;
    //         $goodrcvnote->project_id = $request->project_id;
    //         $goodrcvnote->total_price = array_sum($request->total);
    //         $goodrcvnote->total_qty = array_sum($request->qty);
    //         $goodrcvnote->note = $request->note;
    //         $goodrcvnote->create_by = Auth::user()->id;
    //         $goodrcvnote->save();
    //         $grnId = $goodrcvnote->id;

    //         activity_log(
    //             'create',
    //             'grns',
    //             $goodrcvnote->toArray(),
    //             [],
    //             "GRN created (Invoice: {$invoice_no})  Total Qty: {$goodrcvnote->total_qty}, Total Price: {$goodrcvnote->total_price}"
    //         );

    //         $purchase = Purchases::findOrFail($request->purchase_voucher);
    //         $purchase->status = 'Active';
    //         if (array_sum($request->qty) > abs($statuscheck)) {
    //             $purchase->status =  'Close';
    //         } else {
    //             $purchase->status =  'Active';
    //         }
    //         $purchase->save();

    //         $purchaseDetails['status'] = 'Active';

    //         PurchasesDetails::where('purchases_id', $request->purchase_voucher)->update($purchaseDetails);

    //         //grn supplyer_id update

    //         $category = $request->category_nm;
    //         $product = $request->product_nm;
    //         $qty = $request->qty;
    //         $approve_qty = $request->approve_qty;
    //         $remainingqty = $request->remaining;
    //         $unitprice = $request->unitprice;
    //         $total = $request->total;

    //         for ($i = 0; $i < count($category); $i++) {
    //             $grnDetails = new Grn_detail();
    //             $grnDetails->good_rcv_note_id = $grnId;
    //             $grnDetails->category_id = $category[$i];
    //             $grnDetails->product_id = $product[$i];
    //             $grnDetails->purchasetype = $request->purchasetype[$i];
    //             $grnDetails->qty = $qty[$i];
    //             $grnDetails->purchase_voucher =  $request->purchase_voucher;
    //             $grnDetails->approve_qty = $approve_qty[$i] + $remainingqty[$i];
    //             $grnDetails->unit_price = $unitprice[$i];
    //             $grnDetails->total_price = $total[$i];
    //             $grnDetails->save();

    //             $stock = new Stock();
    //             $stock->invoice_no = $invoice_no;
    //             $stock->project_id = $request->project_id ?? '';
    //             $stock->product_id = $product[$i];
    //             $stock->quantity = $qty[$i];
    //             $stock->branch_id = $request->branch_id ?? 0;
    //             $stock->unit_price = $unitprice[$i];
    //             $stock->total_price = $total[$i];
    //             $stock->general_id = $request->purchase_voucher;
    //             $stock->date = $request->date;
    //             $stock->status = 'Purchase';
    //             $stock->created_by = Auth::user()->id;
    //             $stock->save();

    //             $existingCheck = StockSummary::where('product_id', $product[$i])->where('type', 'Project')->where('branch_id', $request->project_id)->where('purchasetype', $request->purchasetype[$i])->first();
    //             // dd($existingCheck);
    //             if (!empty($existingCheck)) :
    //                 $newQty['quantity'] = $existingCheck->quantity + $remainingqty[$i];
    //                 StockSummary::where('product_id', $product[$i])->where('type', 'Project')->where('branch_id', $request->project_id)->where('purchasetype', $request->purchasetype[$i])->update($newQty);
    //             else :
    //                 $stockSummary = new StockSummary();
    //                 $stockSummary->branch_id = $request->project_id;
    //                 $stockSummary->purchasetype = $request->purchasetype[$i];
    //                 $stockSummary->product_id = $product[$i];
    //                 $stockSummary->quantity = $remainingqty[$i];
    //                 $stockSummary->type = "Project";
    //                 $stockSummary->save();
    //             endif;
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage(), $e->getLine());
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }
    //     return;
    // }

    public function store($request)
    {

        $invoice_no = $request->grnCode;
        $exists = Grn::where('invoice_no', $invoice_no)->select('invoice_no')->exists();

        if ($exists) {
            $lastGrn    = Grn::latest('id')->first();
            $nextCode   = $lastGrn ? $lastGrn->id + 1 : 1;
            $invoice_no = 'GRN' . str_pad($nextCode, 5, "0", STR_PAD_LEFT);
        }

        DB::beginTransaction();
        try {
            $remainingtt  = $request->remaining;
            $approve_qtyt = $request->approve_qty;
            $statuscheck  = array_sum($remainingtt) + array_sum($approve_qtyt);

            // ── GRN Header ───────────────────────────────────────────
            $goodrcvnote                      = new Grn();
            $goodrcvnote->date                = $request->date;
            $goodrcvnote->invoice_no          = $invoice_no;
            $goodrcvnote->supplier_id         = $request->subblier_id ?? 0;
            $goodrcvnote->purchase_voucher_id = $request->purchase_voucher;
            $goodrcvnote->project_id          = $request->project_id;
            $goodrcvnote->total_price         = array_sum($request->total);
            $goodrcvnote->total_qty           = array_sum($request->qty);
            $goodrcvnote->note                = $request->note;
            $goodrcvnote->create_by           = Auth::user()->id;
            $goodrcvnote->save();
            $grnId = $goodrcvnote->id;

            //  Supplier name
            $supplierName = \App\Models\Supplier::find($request->subblier_id)?->name ?? 'N/A';

            //  GRN CREATE log
            activity_log(
                'create',
                'grns',
                $goodrcvnote->toArray(),
                [],
                "GRN created (Invoice: {$invoice_no}) — Supplier: {$supplierName}, Total Qty: {$goodrcvnote->total_qty}, Total Price: {$goodrcvnote->total_price}"
            );

            // ── Purchase Status Update ────────────────────────────────
            $purchase        = Purchases::findOrFail($request->purchase_voucher);
            $oldPurchaseData = $purchase->toArray();

            $purchase->status = array_sum($request->qty) > abs($statuscheck) ? 'Close' : 'Active';
            $purchase->save();



            //  Purchase status change log
            activity_log(
                'update',
                'purchases',
                $purchase->toArray(),
                $oldPurchaseData,
                "Purchase status updated (Invoice: {$purchase->invoice_no}) — Status: {$oldPurchaseData['status']} → {$purchase->status} via GRN: {$invoice_no}"
            );

            PurchasesDetails::where('purchases_id', $request->purchase_voucher)
                ->update(['status' => 'Active']);

            // ── Details Loop ─────────────────────────────────────────
            $category     = $request->category_nm;
            $product      = $request->product_nm;
            $qty          = $request->qty;
            $approve_qty  = $request->approve_qty;
            $remainingqty = $request->remaining;
            $unitprice    = $request->unitprice;
            $total        = $request->total;

            for ($i = 0; $i < count($category); $i++) {

                // ── GRN Detail ───────────────────────────────────────
                $grnDetails                   = new Grn_detail();
                $grnDetails->good_rcv_note_id = $grnId;
                $grnDetails->category_id      = $category[$i];
                $grnDetails->product_id       = $product[$i];
                $grnDetails->purchasetype     = $request->purchasetype[$i];
                $grnDetails->qty              = $qty[$i];
                $grnDetails->purchase_voucher = $request->purchase_voucher;
                $grnDetails->approve_qty      = $approve_qty[$i] + $remainingqty[$i];
                $grnDetails->unit_price       = $unitprice[$i];
                $grnDetails->total_price      = $total[$i];
                $grnDetails->save();


                // ── Stock IN  ─────────────────────────────────
                $stockIn              = new Stock();
                $stockIn->invoice_no  = $invoice_no;
                $stockIn->product_id  = $product[$i];
                $stockIn->quantity    = $qty[$i];
                $stockIn->project_id  = $request->project_id;
                $stockIn->branch_id   =  $request->project_id ?? 0;
                $stockIn->unit_price  = $unitprice[$i];
                $stockIn->total_price = $total[$i];
                $stockIn->general_id  = $request->purchase_voucher;
                $stockIn->date        = $request->date;
                $stockIn->status      = 'Project In';
                $stockIn->created_by  = Auth::user()->id;
                $stockIn->save();

                if ($request->project_id) {

                    // ── Project GRN → Direct Consume ─────────────────
                    // Stock OUT record
                    $stockOut              = new Stock();
                    $stockOut->invoice_no  = $invoice_no;
                    $stockOut->product_id  = $product[$i];
                    $stockOut->quantity    = $qty[$i];
                    $stockOut->project_id  = $request->project_id;
                    $stockOut->branch_id   =  $request->project_id ?? 0;
                    $stockOut->unit_price  = $unitprice[$i];
                    $stockOut->total_price = $total[$i];
                    $stockOut->general_id  = $request->purchase_voucher;
                    $stockOut->date        = $request->date;
                    $stockOut->status      = 'Project Out';
                    $stockOut->created_by  = Auth::user()->id;
                    $stockOut->save();

                    // StockSummary → 0 (IN - OUT = 0)
                    $existingCheck = StockSummary::where('product_id', $product[$i])
                        ->where('type', 'Project')
                        ->where('branch_id', $request->project_id)
                        ->where('purchasetype', $request->purchasetype[$i])
                        ->first();

                    if (!empty($existingCheck)) {
                        StockSummary::where('product_id', $product[$i])
                            ->where('type', 'Project')
                            ->where('branch_id', $request->project_id)
                            ->where('purchasetype', $request->purchasetype[$i])
                            ->update(['quantity' => 0]);
                    } else {
                        $stockSummary               = new StockSummary();
                        $stockSummary->branch_id    = $request->project_id;
                        $stockSummary->purchasetype = $request->purchasetype[$i];
                        $stockSummary->product_id   = $product[$i];
                        $stockSummary->quantity     = 0;
                        $stockSummary->type         = 'Project';
                        $stockSummary->save();
                    }
                    activity_log(
                        'create',
                        'stocks',
                        array_merge($stockOut->toArray(), ['invoice_no' => $invoice_no]),
                        [],
                        "Project Direct Consume (GRN: {$invoice_no}) — Product ID: {$product[$i]}, Qty IN: {$qty[$i]}, Qty OUT: {$qty[$i]}, Stock Summary: 0"
                    );
                } else {

                    $existingCheck = StockSummary::where('product_id', $product[$i])
                        ->where('type', 'Project')
                        ->where('branch_id', $request->project_id)
                        ->where('purchasetype', $request->purchasetype[$i])
                        ->first();
                    if (!empty($existingCheck)) {
                        StockSummary::where('product_id', $product[$i])
                            ->where('type', 'Project')
                            ->where('branch_id', $request->project_id)
                            ->where('purchasetype', $request->purchasetype[$i])
                            ->update(['quantity' => $existingCheck->quantity]); // unchanged
                    } else {

                        $stockSummary               = new StockSummary();
                        $stockSummary->branch_id    = $request->project_id;
                        $stockSummary->purchasetype = $request->purchasetype[$i];
                        $stockSummary->product_id   = $product[$i];
                        $stockSummary->quantity     = 0;
                        $stockSummary->type         = 'Project';
                        $stockSummary->save();
                    }
                    // Branch Stock IN log
                    activity_log(
                        'create',
                        'stocks',
                        array_merge($stockIn->toArray(), ['invoice_no' => $invoice_no]),
                        [],
                        "Branch Stock IN (GRN: {$invoice_no}) — Product ID: {$product[$i]}, Qty: {$qty[$i]}"
                    );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            //  Failed log
            activity_log(
                'failed',
                'grns',
                ['invoice_no' => $invoice_no],
                [],
                "GRN CREATE failed (Invoice: {$invoice_no}) — Error: {$e->getMessage()}"
            );

            dd($e->getMessage(), $e->getLine());
        }

        return;
    }


    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return;
    }

    public function statusUpdate($id, $status)
    {
        $purchase = $this->goodrcvnote::find($id);
        $purchase->status = $status;
        $purchase->save();
        return $purchase;
    }

    public function destroy($id)
    {
        // $grn = $this->goodrcvnote::find($id);
        // $grn->delete();
        // Grn_detail::where('good_rcv_note_id',$id)->delete();
        // Stock::where('good_rcv_note_id',$id)->delete();
        return true;
    }
}
