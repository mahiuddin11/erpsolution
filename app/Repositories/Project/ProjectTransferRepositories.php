<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Models\PrDetails;
use App\Models\ProjectTransfer;
use App\Models\ProjectTransferDetails;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequisition;
use App\Models\Stock;
use App\Models\StockSummary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectTransferRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $projectTransfer;
    /**
     * CourseRepository constructor.
     */
    public function __construct(ProjectTransfer $ProjectTransfer)
    {
        $this->projectTransfer = $ProjectTransfer;
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
        $result = $this->projectTransfer::latest()->get();
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
            1 => 'order_date',
            2 => 'invoice_no',
        );

        $edit = Helper::roleAccess('project.transferproject.edit')  ? 1 : 0;
        $delete = Helper::roleAccess('project.transferproject.destroy') ? 1 : 0;
        $invoice = Helper::roleAccess('project.transferproject.invoice') ? 1 : 0;
        $ced = $edit + $delete + $invoice;
        // dd($approve);

        $totalData = $this->projectTransfer::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $purchaseorders = $this->projectTransfer::offset($start);
            if ($auth->branch_id !== null) {
                $purchaseorders = $purchaseorders->where('branch_id', $auth->branch_id);
            }
            $purchaseorders = $purchaseorders->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->projectTransfer::count();
        } else {
            $search = $request->input('search.value');
            $purchaseorders = $this->projectTransfer::where('invoice_no', 'like', "%{$search}%");
            if ($auth->branch_id !== null) {
                $purchaseorders = $purchaseorders->where('branch_id', $auth->branch_id);
            }
            $purchaseorders = $purchaseorders->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->projectTransfer::where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($purchaseorders) {
            foreach ($purchaseorders as $key => $value) {
                $nestedData['id'] = $key + 1;
                $nestedData['order_date'] = $value->order_date;
                $nestedData['invoice_no'] = $value->invoice_no;
                // $nestedData['supplier_id'] = $value->supplier->supplierCode . ' - ' . $value->supplier->name;
                $nestedData['purchase_requisition_id'] = $value->purchaseRequisition->invoice_no;
                $nestedData['project_id'] =  $value->project->name ?? '';
                // $nestedData['total_bill'] = $value->total_bill;
                // if ($value->status == 'Accepted') {
                //     $nestedData['status'] = '<a class="btn btn-success">' . $value->status . '</a>';
                // } elseif ($value->status == 'Pending') {
                //     $nestedData['status'] = '<a class="btn btn-warning">' . $value->status . '</a>';
                // } else {
                //     $nestedData['status'] = '<a class="btn btn-danger">' . $value->status . '</a>';
                // }

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('project.transferproject.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }
                    if ($invoice != 0) {
                        $invoice_data = '<a href="' . route('project.transferproject.invoice', $value->id) . '" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>';
                    } else {
                        $invoice_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('project.transferproject.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

                    $nestedData['action'] = $edit_data . ' ' . $invoice_data . ' ' . $delete_data;
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

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $purchaseorder = new $this->projectTransfer();
            $purchaseorder->order_date = $request->date;
            $purchaseorder->invoice_no = $request->orderCode;
            $purchaseorder->branch_id = $request->branch_id;
            $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
            // $purchaseorder->advance_payment = $request->paid_amount;
            $purchaseorder->project_id = $request->project_id;
            // $purchaseorder->total_bill = array_sum($request->total);
            $purchaseorder->note = $request->note;
            $purchaseorder->save();
            $purchaseOr_id = $purchaseorder->id;

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;
            for ($i = 0; $i < count($category); $i++) {
                $purchaseOrderDetails = new ProjectTransferDetails();
                $purchaseOrderDetails->project_transfer_id = $purchaseOr_id;
                $purchaseOrderDetails->category_id = $category[$i];
                $purchaseOrderDetails->purchasetype = $request->purchasetype[$i];
                $purchaseOrderDetails->project_id = $request->project_id;
                $purchaseOrderDetails->product_id = $product[$i];
                $purchaseOrderDetails->qty = $qty[$i];
                // $purchaseOrderDetails->unit_price = $unitprice[$i];
                // $purchaseOrderDetails->total_price = $total[$i];
                $purchaseOrderDetails->save();


                $stock = new Stock();
                $stock->general_id = $purchaseOr_id;
                $stock->product_id = $product[$i];
                $stock->quantity = $qty[$i];
                $stock->branch_id = $request->branch_id;
                // $stock->unit_price = $unitprice[$i];
                // $stock->total_price = $total[$i] ?? 0;
                $stock->date = $request->date;
                $stock->status = 'Project Out';
                $stock->save();

                $stock = new Stock();
                $stock->general_id = $purchaseOr_id;
                $stock->product_id = $product[$i];
                $stock->quantity = $qty[$i];
                $stock->branch_id = $request->project_id; // project id insert on branch_id column
                // $stock->unit_price = $unitprice[$i] ?? 0;
                // $stock->total_price = $total[$i] ?? 0;
                $stock->date = $request->date;
                $stock->status = 'Project In';
                $stock->save();

                $products_array = array(
                    'type' => 'Branch',
                    'purchasetype' => $request->purchasetype[$i],
                    'branch_id' =>  $request->branch_id,
                    'product_id' => $request->product_nm[$i],
                );
                $project_array = array(
                    'type' => 'Project',
                    'purchasetype' => $request->purchasetype[$i],
                    'branch_id' =>  $request->project_id,
                    'product_id' => $request->product_nm[$i],
                );

                $stocksamaryquntitys = StockSummary::where($products_array)->pluck('quantity')->first();
                $stockcheck['quantity'] = $stocksamaryquntitys - $request->qty[$i];
                StockSummary::where($products_array)->update($stockcheck);

                $stocksamaryproject = StockSummary::where($project_array)->exists();

                if ($stocksamaryproject) {
                    $stocksamaryprojects = StockSummary::where($project_array)->pluck('quantity')->first();
                    $stockchecko['quantity'] = $stocksamaryprojects + $request->qty[$i];
                    StockSummary::where($project_array)->update($stockchecko);
                } else {
                    $updatestock = new StockSummary();
                    $updatestock->branch_id = $request->project_id;
                    $updatestock->purchasetype = $request->purchasetype[$i];
                    $updatestock->product_id =  $product[$i];
                    $updatestock->quantity = $qty[$i];
                    $updatestock->type = 'Project';
                    $updatestock->save();
                }
            }

            $purchasereq['approve_by'] = Auth::user()->id;
            $purchasereq['approve_at'] = date('Y-m-d');
            $purchasereq['status'] = 'Accepted';
            PurchaseRequisition::where('id', $request->purchase_requisition)->update($purchasereq);

            $prDetails['status'] = 'Transfer';
            PrDetails::where('pr_id', $request->purchase_requisition)->update($prDetails);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage(), $e->getLine());
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return  $purchaseorder;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $purchaseorder = $this->projectTransfer::find($id);
            $purchaseorder->order_date = $request->date;
            $purchaseorder->branch_id = $request->branch_id;
            $purchaseorder->project_id = $request->project_id;
            $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
            $purchaseorder->note = $request->note;
            $purchaseorder->save();

            ProjectTransferDetails::where('project_transfer_id', $id)->delete();

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;
            for ($i = 0; $i < count($category); $i++) {
                $purchaseOrderDetails = new ProjectTransferDetails();
                $purchaseOrderDetails->project_transfer_id = $id;
                $purchaseOrderDetails->category_id = $category[$i];
                $purchaseOrderDetails->branch_id = $request->branch_id;
                $purchaseOrderDetails->product_id = $product[$i];
                $purchaseOrderDetails->project_id = $request->project_id;
                $purchaseOrderDetails->qty = $qty[$i];
                // $purchaseOrderDetails->unit_price = $unitprice[$i];
                // $purchaseOrderDetails->total_price = $total[$i];
                $purchaseOrderDetails->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
    }

    public function destroy($id)
    {
        $purchaseorder = $this->projectTransfer::find($id);
        if ($purchaseorder->status == "Accepted") {
            session()->flash('error', "Sorry, you couldn't delete!!");
            return false;
        } else {

            $purchaseorder->delete();
            ProjectTransferDetails::where('project_transfer_id', $id)->delete();
            return true;
        }
    }
    public function details($id)
    {
        return  $this->projectTransfer::find($id);
    }

    public function getprList($request)
    {
        $data = '';

        $prDetails = PrDetails::where('pr_id', $request->id);

        $purchaserequi = PurchaseRequisition::find($request->id);
        $project = '<option selected value="' . $purchaserequi->project_id  . '"> ' . $purchaserequi->project->projectCode . ' - ' .  $purchaserequi->project->name . '</option>';

        foreach ($prDetails->get() as $value) {
            $data .= '<tr class="delrow new_item' . $value->product_id . '">
        <td >
           ' . $value->category->name . '
            <input type="hidden" name="category_nm[]" value="' . $value->category_id . '">
        </td>
        <td class="text-right">' . $value->product->name . '<input type="hidden" name="product_nm[]" value="' . $value->product_id . '"></td>
        <td class="text-right">' . $value->purchasetype . '<input type="hidden" name="purchasetype[]" value="' . $value->purchasetype . '"></td>
        <td class="text-right">' . ' <input class="qnty form-control" type="number"  name="qty[]" value="' . $value->qty . '">
                                    <div class="productStockCheck' . $value->product_id . ' "></div>

        </td>
        <td>
                <a del_id="' . $value->product_id . '" class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                    <i class="fa fa-times"></i>
                </a>
        </td>
    </tr>';
        }

        return ['prdetails' => $data, 'project' => $project];
    }
}
