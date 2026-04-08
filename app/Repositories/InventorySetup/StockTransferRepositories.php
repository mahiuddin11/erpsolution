<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Models\Stock;
use App\Models\Transfer;
use App\Models\StockSummary;
use App\Models\TransferDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var Brand
     */
    private $transfer;

    /**
     * CourseRepository constructor.
     * @param brand $purchase
     */
    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
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
        $result = $this->transfer::latest()->get();

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
            1 => 'voucher_code',
        );

        $userData = Auth::user();

        $edit = Helper::roleAccess('inventorySetup.transfer.edit') ? 1 : 0;
        $approval = Helper::roleAccess('inventorySetup.transfer.approval') ? 1 : 0;
        $editapproval = Helper::roleAccess('inventorySetup.transfer.editapproval ') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.transfer.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.transfer.show') ? 1 : 0;
        $ced = $edit + $approval + $delete + $view + $editapproval;

        $totalData = $this->transfer::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');


        if (empty($request->input('search.value'))) {
            $transfers = $this->transfer::offset($start);
            if ($userData->branch_id !== null) {
                $transfers = $transfers->where('to_branch_id', $userData->branch_id);
                $transfers = $transfers->orWhere('from_branch_id', $userData->branch_id);
            }
            $transfers =   $transfers->limit($limit)
                ->orderBy($order, $dir)

                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->transfer::count();
        } else {
            $search = $request->input('search.value');
            $transfers = $this->transfer::where('voucher_code', 'like', "%{$search}%");
            if ($userData->branch_id !== null) {
                $transfers = $transfers->where('to_branch_id', $userData->branch_id);
                $transfers = $transfers->orWhere('from_branch_id', $userData->branch_id);
            }
            $transfers =   $transfers->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->transfer::where('voucher_code', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($transfers) {
            foreach ($transfers as $key => $etransf) {
                $nestedData['id'] = $key + 1;
                $nestedData['voucher_code'] = $etransf->voucher_code;
                $nestedData['date'] = $etransf->date;
                $nestedData['from_branch_id'] = $etransf->frombranch->branchCode . ' - ' . $etransf->frombranch->name ?? 'N/A';
                $nestedData['to_branch_id'] = $etransf->tobranch->branchCode . ' - ' . $etransf->tobranch->name ?? 'N/A';
                $nestedData['qty'] = $etransf->qty ?? 'N/A';
                $nestedData['approved_date'] = $etransf->approved_date;
                $nestedData['net_total'] = $etransf->net_total;
                $nestedData['shipping'] = $etransf->shipping;
                $nestedData['subtotal'] = $etransf->subtotal;
                if ($etransf->status == 'Pending') :
                    $nestedData['status'] = '<i style="color: red" class="fas fa-sync fa-spin" ></i>  &nbsp; <b style="color: red">Pending</b>';
                elseif ($etransf->status == 'Cancel') :
                    $nestedData['status'] = '<a class="btn btn-xs btn-danger" >Cancel</a>';
                else :
                    $nestedData['status'] = '<a class="btn btn-xs btn-success">Approved</a>';
                endif;

                if ($ced != 0) {
                    //dd($userData->branch_id);
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('inventorySetup.transfer.edit', $etransf->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }


                    if ($editapproval != 0) {
                        $edit_approval = '<a href="' . route('inventorySetup.transfer.editapproval', $etransf->id) . '" class="btn btn-xs btn-default"><i class="fas fa-clipboard-check"></i></a>';
                    } else {
                        $edit_approval = '';
                    }
                    if ($userData->branch_id == $etransf->to_branch_id) {
                        if ($approval != 0) {
                            $approval_data = '<a href="' . route('inventorySetup.transfer.approval', $etransf->id) . '" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></a>';
                        } else {
                            $approval_data = '';
                        }
                    } else {
                        $approval_data = '<a href="' . route('inventorySetup.transfer.approval', $etransf->id) . '" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></a>';
                    }
                    if ($view = !0) {
                        $view_data = '<a href="' . route('inventorySetup.transfer.show', $etransf->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('inventorySetup.transfer.destroy', $etransf->id) . '" delete_id="' . $etransf->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $etransf->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

                    if ($userData->branch_id == $etransf->to_branch_id) {
                        $nestedData['action'] =  $edit_approval . ' ' . $approval_data . ' ' . $view_data . ' ' . $delete_data;
                    }
                    if ($userData->branch_id == $etransf->from_branch_id) {
                        $nestedData['action'] = $edit_data . ' ' . $edit_approval . ' ' . $view_data . ' ' . $delete_data;
                    }
                    if ($userData->branch_id == ' ' || $userData->branch_id == null) {

                        $nestedData['action'] =  $edit_data . ' ' . $approval_data . ' ' . $edit_approval . ' ' . $view_data . ' ' . $delete_data;
                    }
                } else {
                    $nestedData['action'] = '';
                }
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        return $json_data;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {
        $result = $this->transfer::find($id);

        return $result;
    }


    public function store($request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $transfer = new $this->transfer();
            $transfer->voucher_code = $request->invoice_no;
            $transfer->date = $request->date;
            $transfer->from_branch_id = $request->from_branch_id;
            $transfer->to_branch_id = $request->to_branch_id;
            $transfer->qty = array_sum($request->qty);
            $transfer->net_total = array_sum($request->unitprice);
            $transfer->subtotal = array_sum($request->total);
            $transfer->shipping = $request->shipping;
            $transfer->note = $request->narration;
            $transfer->status = 'Pending';
            $transfer->created_by = Auth::user()->id;
            $transfer->save();
            $transfers_id = $transfer->id;

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;

            for ($i = 0; $i < count($category_id); $i++) {
                $transferDetails = new TransferDetails();
                $transferDetails->product_id = $proName[$i];
                $transferDetails->category_id = $category_id[$i];
                $transferDetails->qty = $qty[$i];
                $transferDetails->from_branch_id = $request->from_branch_id;
                $transferDetails->to_branch_id = $request->to_branch_id;
                $transferDetails->unit_price = $subtotal[$i];
                $transferDetails->total_price = $grand_total[$i];
                $transferDetails->transfer_id = $transfers_id;
                $transferDetails->date = $request->date;
                $transferDetails->status = 'Pending';
                $transferDetails->created_by = Auth::user()->id;
                $transferDetails->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup-transfer-create')->with('error', 'Something Wrong Please try again');
        }
        return $transfer;
    }

    public function approval($request)
    {

        DB::beginTransaction();
        try {
            $transferId = $request->transferId;
            $transfer = Transfer::find($transferId);
            $transfer->approved_date =  date('Y-m-d');
            $transfer->status = 'Approved';
            $transfer->approve_qty = array_sum($request->qty);
            $transfer->note = $request->narration;
            $transfer->updated_by = Auth::user()->id;
            $transfer->save();

            foreach ($transfer->details as $item) {
                $item->update([
                    "approve_qty" => $item->qty,
                    "updated_by" => auth()->id(),
                    "status" => "Approved"
                ]);

                $from_array = array(
                    'branch_id' => $item->from_branch_id,
                    'product_id' => $item->product_id,
                );

                $to_array = array(
                    'branch_id' => $item->to_branch_id,
                    'product_id' => $item->product_id,
                );

                $currentStock = StockSummary::where($from_array)->first();
                $currentStock->quantity = $currentStock->quantity - $item->qty;
                $currentStock->save();

                $updatestock = StockSummary::where($to_array)->first();
                if ($updatestock) :
                    $updatestock->quantity = $updatestock->quantity + $item->qty;
                    $updatestock->save();
                else :
                    $updatestock = new StockSummary();
                    $updatestock->branch_id =  $item->to_branch_id;
                    $updatestock->product_id =  $item->product_id;
                    $updatestock->quantity =  $item->qty;
                    $updatestock->save();
                endif;
            }

            dd($request->all());

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;
            for ($i = 0; $i < count($category_id); $i++) {
                $stock = new Stock();
                $stock->date = $request->date;
                $stock->general_id = $transferId;
                $stock->branch_id = $request->from_branch_id;
                $stock->product_id = $proName[$i];
                $stock->unit_price = $subtotal[$i];
                $stock->total_price = $grand_total[$i];
                $stock->quantity = $qty[$i];
                $stock->status = 'Transfer Out';
                $stock->created_by = Auth::user()->id;
                $stock->save();

                $stock = new Stock();
                $stock->date = $request->date;
                $stock->general_id = $transferId;
                $stock->branch_id = $request->to_branch_id;
                $stock->product_id = $proName[$i];
                $stock->unit_price = $subtotal[$i];
                $stock->total_price = $grand_total[$i];
                $stock->quantity = $qty[$i];
                $stock->status = 'Transfer In';
                $stock->created_by = Auth::user()->id;
                $stock->save();
            }



            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup-transfer-create')->with('error', 'Something Wrong Please try again');
        }
        return $transfer;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $transfer = $this->transfer::find($id);

            $transfer->date = $request->date;
            $transfer->from_branch_id = $request->from_branch_id;
            $transfer->to_branch_id = $request->to_branch_id;
            $transfer->qty = array_sum($request->qty);
            $transfer->net_total = array_sum($request->unitprice);
            $transfer->subtotal = array_sum($request->total);
            $transfer->shipping = $request->shipping;
            $transfer->note = $request->narration;
            $transfer->status = 'Pending';
            $transfer->created_by = Auth::user()->id;
            $transfer->save();
            $transfers_id = $transfer->id;

            TransferDetails::where('transfer_id', $id)->delete();

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;
            for ($i = 0; $i < count($category_id); $i++) {
                $transferDetails = new TransferDetails();
                $transferDetails->product_id = $proName[$i];
                $transferDetails->category_id = $category_id[$i];
                $transferDetails->qty = $qty[$i];
                $transferDetails->from_branch_id = $request->from_branch_id;
                $transferDetails->to_branch_id = $request->to_branch_id;
                $transferDetails->unit_price = $subtotal[$i];
                $transferDetails->total_price = $grand_total[$i];
                $transferDetails->transfer_id = $transfers_id;
                $transferDetails->date = $request->date;
                $transferDetails->status = 'Pending';
                $transferDetails->created_by = Auth::user()->id;
                $transferDetails->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup-transfer-create')->with('error', 'Something Wrong Please try again');
        }
        return $transfer;
    }
    public function stotransferStore($request)
    {
        DB::beginTransaction();

        try {
            $transfer = $this->transfer::find($request->id);
            $transfer->approve_qty = array_sum($request->qty);
            $transfer->status = 'Approved';
            $transfer->subtotal = array_sum($request->total);
            $transfer->note = $request->narration;
            $transfer->updated_by = Auth::user()->id;
            $transfer->approved_date = date('Y-m-d');
            $transfer->save();

            // for ($i = 0; $i < count($request->transDetail); $i++) {


            foreach ($transfer->details as $key => $item) {
                $details['approve_qty'] = $request->qty[$key];
                $details['total_price'] = $request->total[$key];
                $details['status'] = 'Approved';
                $details['updated_by'] = Auth::user()->id;
                TransferDetails::where('id', $request->transDetail[$key])->update($details);

                $from_array = array(
                    'branch_id' => $item->from_branch_id,
                    'product_id' => $item->product_id,
                );

                $to_array = array(
                    'branch_id' => $item->to_branch_id,
                    'product_id' => $item->product_id,
                );

                $currentStock = StockSummary::where($from_array)->first();
                $currentStock->quantity = $currentStock->quantity - $item->qty;
                $currentStock->save();

                $updatestock = StockSummary::where($to_array)->first();
                if ($updatestock) :
                    $updatestock->quantity = $updatestock->quantity + $item->qty;
                    $updatestock->save();
                else :
                    $updatestock = new StockSummary();
                    $updatestock->branch_id =  $item->to_branch_id;
                    $updatestock->product_id =  $item->product_id;
                    $updatestock->quantity =  $item->qty;
                    $updatestock->save();
                endif;
            }





            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup-transfer-create')->with('error', 'Something Wrong Please try again');
        }
        return $transfer;
    }

    public function statusUpdate($id, $status)
    {
        $purchase = $this->transfer::find($id);
        $purchase->status = $status;
        $purchase->save();
        return $purchase;
    }

    public function destroy($id)
    {
        $purchase = $this->transfer::find($id);
        $purchase->delete();
        return true;
    }
}
