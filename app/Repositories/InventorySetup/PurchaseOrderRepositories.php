<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Models\PrDetails;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequisition;
use App\Models\SupplierSelectPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $purchaseorder;
    /**
     * CourseRepository constructor.
     */
    public function __construct(PurchaseOrder $purchaseorder)
    {
        $this->purchaseorder = $purchaseorder;
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
        $result = $this->purchaseorder::latest()->get();
        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */

    public function getList($request)
    {

    // dd($request->all());

        $columns = array(
            0 => 'id',
            1 => 'order_date',
            2 => 'invoice_no',
        );

        $edit = Helper::roleAccess('inventorySetup.purchaseorder.edit') && $this->purchaseorder->status != "Accepted" ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.purchaseorder.destroy') ? 1 : 0;
        $invoice = Helper::roleAccess('inventorySetup.purchaseorder.invoice') ? 1 : 0;
        $approve = Helper::roleAccess('inventorySetup.purchaseorder.approve') ? 1 : 0;
        $ced = $edit + $delete + $invoice + $approve;

        // dd($approve);

        $totalData = $this->purchaseorder::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $purchaseorders = $this->purchaseorder::offset($start);
            // if ($auth->branch_id !== null) {
            //     $purchaseorders = $purchaseorders->where('branch_id', $auth->branch_id);
            // }
            $purchaseorders = $purchaseorders->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->purchaseorder::count();
        } else {
            $search = $request->input('search.value');
            $purchaseorders = $this->purchaseorder::where('invoice_no', 'like', "%{$search}%");
            if ($auth->branch_id !== null) {
                $purchaseorders = $purchaseorders->where('branch_id', $auth->branch_id);
            }
            $purchaseorders = $purchaseorders->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->purchaseorder::where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($purchaseorders) {
            foreach ($purchaseorders as $key => $value) {
                $nestedData['id'] = $key + 1;
                $nestedData['order_date'] = $value->order_date;
                $nestedData['invoice_no'] = $value->invoice_no;
                $nestedData['supplier_id'] = $value->supplier->name ?? "";
                $nestedData['purchase_requisition_id'] = $value->purchaseRequisition->invoice_no ?? "";
                $nestedData['project_id'] =  $value->project->name ?? '';
                // $nestedData['total_bill'] = $value->total_bill;
                if ($value->status == 'Accepted') {
                    $nestedData['status'] = '<a class="btn btn-success">' . $value->status . '</a>';
                } elseif ($value->status == 'Pending') {
                    $nestedData['status'] = '<a class="btn btn-warning">' . $value->status . '</a>';
                } else {
                    $nestedData['status'] = '<a class="btn btn-danger">' . $value->status . '</a>';
                }

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('inventorySetup.purchaseorder.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }
                    if ($invoice != 0) {
                        $invoice_data = '<a href="' . route('inventorySetup.purchaseorder.invoice', $value->id) . '" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>';
                    } else {
                        $invoice_data = '';
                    }

                    if ($approve != 0) {
                        $approve_data = '<a href="' . route('inventorySetup.purchaseorder.approve', $value->id) . '" class="btn btn-xs btn-default"><i class="fas fa-check"></i></a>';
                    } else {
                        $approve_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('inventorySetup.purchaseorder.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

                    $nestedData['action'] = $edit_data . ' ' . $approve_data . ' ' . $invoice_data . ' ' . $delete_data;
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

    // public function store($request)
    // {
    //     dd('purchase order repository',$request->all());

    //     DB::beginTransaction();
    //     try {
    //         $purchaseorder = new $this->purchaseorder();
    //         $purchaseorder->order_date = $request->date;
    //         $purchaseorder->invoice_no = $request->orderCode;
    //         $purchaseorder->supplier_id = $request->subblier_id ?? 0;
    //         $purchaseorder->account_id = $request->account_id ?? null; // ledger/account id
    //         $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
    //         // $purchaseorder->advance_payment = $request->paid_amount;
    //         $purchaseorder->project_id = $request->project_id;
    //         // $purchaseorder->total_bill = array_sum($request->total);
    //         $purchaseorder->note = $request->note;
    //         $purchaseorder->save();

    //         $purchaseOr_id = $purchaseorder->id;

    //         $category = $request->category_nm;
    //         $product = $request->product_nm;
    //         $qty = $request->qty;
    //         $unitprice = $request->unitprice;
    //         $total = $request->total;
    //         for ($i = 0; $i < count($category); $i++) {
    //             $purchaseOrderDetails = new PurchaseOrderDetail();
    //             $purchaseOrderDetails->purchase_order_id = $purchaseOr_id;
    //             $purchaseOrderDetails->category_id = $category[$i];
    //             $purchaseOrderDetails->purchasetype = $request->purchasetype[$i];
    //             $purchaseOrderDetails->project_id = $request->project_id;
    //             $purchaseOrderDetails->product_id = $product[$i];
    //             $purchaseOrderDetails->qty = $qty[$i];

    //             $purchaseOrderDetails->save();

    //             $req_supplier = request('supplier_' . $product[$i]);
    //             $req_amount = request('amount_' . $product[$i]);

    //             if ($req_supplier) {
    //                 for ($j = 0; $j < count($req_supplier); $j++) {
    //                     $suppliersaleprice[] = [
    //                         'purchase_order_id' => $purchaseOrderDetails->id,
    //                         'supplier_id' => $req_supplier[$j] ?? "",
    //                         'purchases_price' => $req_amount[$j],
    //                     ];
    //                 }
    //             }
    //         }

    //         DB::table('supplier_select_prices')->insert($suppliersaleprice);
            
    //         $purchasereq['approve_by'] = Auth::user()->id;
    //         $purchasereq['approve_at'] = date('Y-m-d');
    //         $purchasereq['status'] = 'Complete';
    //         PurchaseRequisition::where('id', $request->purchase_requisition)->update($purchasereq);

    //         $prDetails['status'] = 'Accepted';
    //         PrDetails::where('pr_id', $request->purchase_requisition)->update($prDetails);

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage(), $e->getLine());
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }

    //     return  $purchaseorder;
    // }


    public function store($request)
    {
        
        DB::beginTransaction();
        try {
            // 1️⃣ Create Purchase Order
            $purchaseorder = new $this->purchaseorder();
            $purchaseorder->order_date = $request->date;
            $purchaseorder->invoice_no = $request->orderCode;
            $purchaseorder->supplier_id = $request->subblier_id ?? 0; // optional supplier
            $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
            $purchaseorder->project_id = $request->project_id;
            $purchaseorder->note = $request->note;
            $purchaseorder->save();
            $purchaseOrderId = $purchaseorder->id;

            
            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $purchasetype = $request->purchasetype;
            
            // 2️⃣ Loop through each product
            for ($i = 0; $i < count($category); $i++) {
                $purchaseOrderDetails = new PurchaseOrderDetail();
                $purchaseOrderDetails->purchase_order_id = $purchaseOrderId;
                $purchaseOrderDetails->category_id = $category[$i];
                $purchaseOrderDetails->product_id = $product[$i];
                $purchaseOrderDetails->qty = $qty[$i];
                $purchaseOrderDetails->purchasetype = $purchasetype[$i];
                $purchaseOrderDetails->project_id = $request->project_id;
                $purchaseOrderDetails->save();
                
                $detailId = $purchaseOrderDetails->id;
                
                // 3️⃣ Handle Supplier / Customer / Account quotations
                $accountKey = 'account_' . $product[$i];
                $amountKey = 'amount_' . $product[$i];
                
                
                if ($request->has($accountKey) && $request->has($amountKey)) {
                    $accounts = $request->$accountKey;
                    $amounts = $request->$amountKey;
                    $supplierPrices = [];
                    
                    
                    
                    for ($j = 0; $j < count($accounts); $j++) {
                        $supplierPrices[] = [
                            'purchase_order_id' => $detailId,
                            'supplier_id' => $request->subblier_id ?? null, // optional
                            'customer_id' => $request->customer_id ?? null, // optional customer
                            'account_id' => $accounts[$j] ?? null,
                            'purchases_price' => $amounts[$j] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                            ];
                            }

                    DB::table('supplier_select_prices')->insert($supplierPrices);
                }
            }

            // 4️⃣ Update PR status
            PurchaseRequisition::where('id', $request->purchase_requisition)->update([
                'approve_by' => Auth::user()->id,
                'approve_at' => now(),
                // 'total_price' => '',
                'status' => 'Complete',
            ]);

            PrDetails::where('pr_id', $request->purchase_requisition)->update([
                'status' => 'Accepted'
            ]);

            DB::commit();
            return $purchaseorder;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage(), $e->getLine());
        }
    }

    public function update($request, $id)
    {
        // dd('purchase order repo',$request->all(), $id);
        DB::beginTransaction();
        try {
            $purchaseorder = $this->purchaseorder::find($id);
            $purchaseorder->order_date = $request->date;
            $purchaseorder->supplier_id = $request->subblier_id ?? 0;
            // $purchaseorder->account_id = $request->account_id ?? 0;
            $purchaseorder->project_id = $request->project_id;
            $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
            // $purchaseorder->advance_payment = $request->paid_amount;
            // $purchaseorder->total_bill = array_sum($request->total);
            $purchaseorder->note = $request->note;
            $purchaseorder->save();

            $orderDetailId = PurchaseOrderDetail::where('purchase_order_id', $id)->pluck('id')->toArray();
            SupplierSelectPrice::whereIn('purchase_order_id', $orderDetailId)->delete();
            PurchaseOrderDetail::where('purchase_order_id', $id)->delete();

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;
            for ($i = 0; $i < count($category); $i++) {
                $purchaseOrderDetails = new PurchaseOrderDetail();
                $purchaseOrderDetails->purchase_order_id = $id;
                $purchaseOrderDetails->category_id = $category[$i];
                // $purchaseOrderDetails->branch_id = $request->branch_id;
                $purchaseOrderDetails->product_id = $product[$i];
                $purchaseOrderDetails->purchasetype = $request->purchasetype[$i];
                $purchaseOrderDetails->project_id = $request->project_id;
                $purchaseOrderDetails->qty = $qty[$i];
                // $purchaseOrderDetails->unit_price = $unitprice[$i];
                // $purchaseOrderDetails->total_price = $total[$i];
                $purchaseOrderDetails->save();

                $req_supplier = request('supplier_' . $product[$i]);
                $req_account = request('account_' . $product[$i]); // new add
                $req_customer = request('customer_' . $product[$i]); // new add
                $req_amount = request('amount_' . $product[$i]);

                $count = 0; // new add
                if ($req_supplier) {
                    $count = count($req_supplier);
                } elseif ($req_account) {
                    $count = count($req_account);
                } elseif ($req_customer) {
                    $count = count($req_customer);
                }

                for ($j = 0; $j < $count; $j++) {

                    $suppliersaleprice[] = [
                        'purchase_order_id' => $purchaseOrderDetails->id,
                        'supplier_id' => $req_supplier[$j] ?? null, // new add
                        'account_id' => $req_account[$j] ?? null, // new add
                        'customer_id' => $req_customer[$j] ?? null, // new add
                        'purchases_price' => $req_amount[$j] ?? 0,
                        'created_at' => now(), // new add
                        'updated_at' => now(), // new add
                    ];
                }

                // if ($req_supplier) {
                //     for ($j = 0; $j < count($req_supplier); $j++) {
                //         $suppliersaleprice[] = [
                //             'purchase_order_id' => $purchaseOrderDetails->id,
                //             'supplier_id' => $req_supplier[$j] ?? "",
                //             'purchases_price' => $req_amount[$j],
                //         ];
                //     }
                // }
            }

            if (!empty($suppliersaleprice)) { // new add
                DB::table('supplier_select_prices')->insert($suppliersaleprice);
            }

            // DB::table('supplier_select_prices')->insert($suppliersaleprice);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
    }

    public function destroy($id)
    {
        $purchaseorder = $this->purchaseorder::find($id);
        if ($purchaseorder->status == "Accepted") {
            session()->flash('error', "Sorry, you couldn't delete!!");
            return false;
        } else {
            $purchaseorder->delete();
            PurchaseOrderDetail::where('purchase_order_id', $id)->delete();
            $purchaserequpdate['status'] = "Pending";
            PurchaseRequisition::where('id', $purchaseorder->purchase_requisition_id)->update($purchaserequpdate);
            return true;
        }
    }
    public function details($id)
    {
        return  $this->purchaseorder::find($id);
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
        <td class="text-right">' . ' <input class="ttlqty qnty form-control" type="number"  name="qty[]" value="' . $value->qty . '"></td>
        </td>
        <td class="text-right"><button class="btn btn-info supplierButton" type="button" btn-id="'.$value->product_id.'"> <i class="fa fa-plus"></i> </button></td>
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
