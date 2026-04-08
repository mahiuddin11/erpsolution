<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Models\PrDetails;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class PurchaseRequisitionRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $prchaserequisition;
    /**
     * CourseRepository constructor.
     */
    public function __construct(PurchaseRequisition $prchaserequisition)
    {
        $this->prchaserequisition = $prchaserequisition;
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
        $result = $this->prchaserequisition::latest()->get();
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
            1 => 'product_id',
            2 => 'branch_id',
        );

        $edit = Helper::roleAccess('inventorySetup.purchaserequisition.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.purchaserequisition.destroy') ? 1 : 0;
        $approve = Helper::roleAccess('inventorySetup.purchaserequisition.approve') ? 1 : 0;
        $invoice = Helper::roleAccess('inventorySetup.purchaserequisition.invoice') ? 1 : 0;
        $ced = $edit + $delete  + $invoice + $approve;


        $totalData = $this->prchaserequisition::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $auth = Auth::user();

        if (empty($request->input('search.value'))) {
            $prchaserequisitions = $this->prchaserequisition::offset($start);
            $prchaserequisitions = $prchaserequisitions->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->prchaserequisition::count();
        } else {
            $search = $request->input('search.value');
            $prchaserequisitions = $this->prchaserequisition::where('invoice_no', 'like', "%{$search}%");

            $prchaserequisitions = $prchaserequisitions->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->prchaserequisition::where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($prchaserequisitions) {
            foreach ($prchaserequisitions as $key => $value) {
                $nestedData['id'] = $key + 1;
                $nestedData['invoice_no'] = $value->invoice_no;
                $nestedData['project_id'] = $value->project->name ?? "";
                $nestedData['date'] = $value->date;
                $nestedData['user_id'] = $value->user->name ?? "N/A";
                $nestedData['approve_by'] = !empty($value->approved->name) ? $value->approved->name : 'N/A';
                if ($value->status == 'Accepted') {
                    $nestedData['status'] = '<a class="btn btn-success">' . $value->status . '</a>';
                } elseif ($value->status == 'Pending') {
                    $nestedData['status'] = '<a class="btn btn-warning">' . $value->status . '</a>';
                } else {
                    $nestedData['status'] = '<a class="btn btn-danger">' . $value->status . '</a>';
                }

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('inventorySetup.purchaserequisition.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }
                    if ($approve != 0 && $value->status != 'Accepted') {
                        $approve_data = '<a href="' . route('inventorySetup.purchaserequisition.approve', $value->id) . '" class="btn btn-xs btn-default"><i class="fas fa-check"></i></a>';
                    } else {
                        $approve_data = '';
                    }
                    if ($invoice != 0) {
                        $invoice_data = '<a href="' . route('inventorySetup.purchaserequisition.invoice', $value->id) . '" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>';
                    } else {
                        $invoice_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('inventorySetup.purchaserequisition.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

                    $nestedData['action'] = $edit_data . ' ' .$approve_data . '  ' . $invoice_data . ' ' . $delete_data;
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
        $result = $this->prchaserequisition::find($id);
        return $result;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $purchaserequisition = new PurchaseRequisition();
            $purchaserequisition->invoice_no = $request->requisitionCode;
            $purchaserequisition->project_id = $request->project_id;
            $purchaserequisition->date = $request->date;
            $purchaserequisition->total_qty = array_sum($request->qty);
            // $purchaserequisition->total_price = array_sum($request->total);
            $purchaserequisition->user_id = Auth::user()->id;
            $purchaserequisition->note = $request->note;
            $purchaserequisition->save();
            $pr_id = $purchaserequisition->id;

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;

            for ($i = 0; $i < count($category); $i++) {
                $purchasedetails = new PrDetails();
                $purchasedetails->pr_id = $pr_id;
                $purchasedetails->category_id = $category[$i];
                $purchasedetails->product_id = $product[$i];
                $purchasedetails->purchasetype = $request->purchasetype[$i];
                $purchasedetails->qty = $qty[$i];
                $purchasedetails->project_id = $request->project_id;
                $purchasedetails->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage(), $e->getLine());
            redirect('inventorySetup.purchaserequisition.create')->with('error', 'Something Wrong Please try again');
        }
        return $pr_id;
    }

    public function update($request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $purchaserequisition = PurchaseRequisition::find($id);
            $purchaserequisition->project_id = $request->project_id;
            $purchaserequisition->date = $request->date;
            $purchaserequisition->update_by = Auth::user()->id;
            $purchaserequisition->note = $request->note;
            $purchaserequisition->save();
            $pr_id = $purchaserequisition->id;

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;

            PrDetails::where('pr_id', $id)->delete();
            for ($i = 0; $i < count($category); $i++) {
                $purchasedetails = new PrDetails();
                $purchasedetails->pr_id = $pr_id;
                $purchasedetails->category_id = $category[$i];
                $purchasedetails->product_id = $product[$i];
                $purchasedetails->purchasetype = $request->purchasetype[$i];
                $purchasedetails->qty = $qty[$i];
                $purchasedetails->project_id = $request->project_id;
                $purchasedetails->status = 'Pending';
                $purchasedetails->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup.purchaserequisition.create')->with('error', 'Something Wrong Please try again');
        }
        return $purchaserequisition;
    }

    public function approvepr($request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $purchaserequisition = PurchaseRequisition::find($id);
            $purchaserequisition->approve_by = Auth::user()->id;
            $purchaserequisition->approve_at = date('Y-m-d');
            $purchaserequisition->status = 'Accepted';
            $purchaserequisition->save();
            $pr_id = $purchaserequisition->id;

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            PrDetails::where('pr_id', $id)->delete();
            for ($i = 0; $i < count($category); $i++) {
                $purchasedetails = new PrDetails();
                $purchasedetails->pr_id = $pr_id;
                $purchasedetails->category_id = $category[$i];
                $purchasedetails->product_id = $product[$i];
                $purchasedetails->qty = $qty[$i];
                $purchasedetails->project_id = $request->project_id;
                $purchasedetails->status = 'Accepted';
                $purchasedetails->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup.purchaserequisition.create')->with('error', 'Something Wrong Please try again');
        }
        return $purchaserequisition;
    }

    public function statusUpdate($id, $status)
    {
        $prchaserequisition = $this->prchaserequisition::find($id);
        $prchaserequisition->status = $status;
        $prchaserequisition->save();
        return $prchaserequisition;
    }

    public function destroy($id)
    {
        $purchaserequ = $this->prchaserequisition::find($id);
        if ($purchaserequ->status == "Accepted") {
            session()->flash('error', "Sorry, you couldn't delete!!");
            return false;
        } else {
            $purchaserequ->delete();
            PrDetails::where('pr_id', $id)->delete();
            return true;
        }
    }
}
