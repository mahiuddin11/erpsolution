<?php

namespace App\Repositories\Sale;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Models\customerLedger;
use App\Models\Sale;
use App\Models\sales_Details;
use App\Models\deliveryChalan;
use App\Models\deliveryChalanDetails;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Transection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryChallanRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var Brand
     */
    private $DeliveryChallan;

    /**
     * CourseRepository constructor.
     * @param brand $esale
     */
    public function __construct(deliveryChalan $deliveryChalan)
    {
        $this->DeliveryChallan = $deliveryChalan;
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
        $result = $this->DeliveryChallan::latest()->get();
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
            1 => 'chalan_no',
        );


        $edit = Helper::roleAccess('sale.challan.edit') ? 1 : 0;
        $delete = Helper::roleAccess('sale.challan.destroy') ? 1 : 0;
        $view = Helper::roleAccess('sale.challan.show') ? 1 : 0;
        $ced = $edit + $delete + $view;
        $totalData = $this->DeliveryChallan::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $Sale = $this->DeliveryChallan::offset($start);
            if ($auth->branch_id !== null) {
                $Sale = $Sale->where('branch_id', $auth->branch_id);
            }
            $Sale = $Sale->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->DeliveryChallan::count();
        } else {
            $search = $request->input('search.value');
            $Sale = $this->DeliveryChallan::where('invoice_no', 'like', "%{$search}%");
            if ($auth->branch_id !== null) {
                $Sale = $Sale->where('branch_id', $auth->branch_id);
            }
            $Sale = $Sale->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->DeliveryChallan::where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($Sale) {
            foreach ($Sale as $key => $esale) {
                $nestedData['id'] = $key + 1;
                $nestedData['chalan_no'] = $esale->chalan_no;
                $nestedData['sale_id'] = $esale->sale->invoice_no;
                $nestedData['branch_id'] = $esale->branch->branchCode . ' - ' . $esale->branch->name;
                $nestedData['customer_id'] = $esale->customer->customerCode . ' - ' . $esale->customer->name;
                $nestedData['note'] = $esale->note;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('sale.challan.edit', $esale->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view = !0) {
                        $view_data = '<a href="' . route('sale.challan.show', $esale->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('sale.challan.destroy', $esale->id) . '" delete_id="' . $esale->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $esale->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

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
        $result = $this->DeliveryChallan::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $deliverych = new deliveryChalan();
            $deliverych->date = $request->date;
            $deliverych->chalan_no = $request->deliveryCode;
            $deliverych->sale_id = $request->sales_id;
            $deliverych->branch_id = $request->branch_id;
            $deliverych->customer_id = $request->coustomer_id;
            $deliverych->note = $request->note;
            $deliverych->created_by = Auth::user()->id;
            $deliverych->save();
            $deliverychalanID = $deliverych->id;

            for ($i = 0; $i < count($request->category_nm); $i++) {
                $deliverychalanDetails = new deliveryChalanDetails();
                $deliverychalanDetails->chalan_id = $deliverychalanID;
                $deliverychalanDetails->sale_id = $request->sales_id;
                $deliverychalanDetails->category_id = $request->category_nm[$i];
                $deliverychalanDetails->product_id = $request->product_nm[$i];
                $deliverychalanDetails->delivary_qty = $request->qty[$i];
                $deliverychalanDetails->created_by = Auth::user()->id;
                $deliverychalanDetails->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }

        return true;
    }

    public function update($request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $deliverych = deliveryChalan::find($id);
            $deliverych->date = $request->date;
            $deliverych->sale_id = $request->sales_id;
            $deliverych->branch_id = $request->branch_id;
            $deliverych->customer_id = $request->coustomer_id;
            $deliverych->note = $request->note;
            $deliverych->updated_by = Auth::user()->id;
            $deliverych->save();
            $deliverychalanID = $deliverych->id;

            deliveryChalanDetails::where('chalan_id', $id)->forceDelete();

            for ($i = 0; $i < count($request->category_nm); $i++) {
                $deliverychalanDetails = new deliveryChalanDetails();
                $deliverychalanDetails->chalan_id = $deliverychalanID;
                $deliverychalanDetails->sale_id = $request->sales_id;
                $deliverychalanDetails->category_id = $request->category_nm[$i];
                $deliverychalanDetails->product_id = $request->product_nm[$i];
                $deliverychalanDetails->delivary_qty = $request->qty[$i];
                $deliverychalanDetails->updated_by = Auth::user()->id;
                $deliverychalanDetails->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }

        return true;
    }

    public function statusUpdate($id, $status)
    {
        $esale = $this->DeliveryChallan::find($id);
        $esale->status = $status;
        $esale->save();
        return $esale;
    }

    public function destroy($id)
    {
        $esale = $this->DeliveryChallan::find($id);
        $esale->delete();
        return true;
    }
}
