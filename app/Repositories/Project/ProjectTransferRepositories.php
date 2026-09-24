<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Models\PrDetails;
use App\Models\ProjectTransfer;
use App\Models\ProjectTransferDetails;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequisition;
use App\Models\PurchasesDetails;
use App\Models\Stock;
use App\Models\StockSummary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            2 => 'tr_invoice',
            3 => 'pr_invoice',
        );



        $edit = Helper::roleAccess('project.transferproject.edit')  ? 1 : 0;
        $delete = Helper::roleAccess('project.transferproject.destroy') ? 1 : 0;
        $invoice = Helper::roleAccess('project.transferproject.invoice') ? 1 : 0;
        $ced = $edit + $delete + $invoice;

        $auth = Auth::user();
        $baseQuery = $this->projectTransfer::query();

        if ($auth->type !== 'Admin') {
            $baseQuery->where('created_by', $auth->id);
        }

        $totalData = (clone $baseQuery)->count(); // filtered base  count

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {

            $purchaseorders = (clone $baseQuery)->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = $totalData;
        } else {

            $search = $request->input('search.value');

            $purchaseorders = (clone $baseQuery)->where('invoice_no', 'like', "%{$search}%")->offset($start)->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = (clone $baseQuery)->where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($purchaseorders) {
            foreach ($purchaseorders as $key => $value) {

                $nestedData['id'] = $key + 1;
                $nestedData['order_date'] = $value->order_date;
                $nestedData['tr_invoice'] = $value->invoice_no ?? '';
                $nestedData['pr_invoice'] = $value->purchaseRequisition->invoice_no ?? '';
                $nestedData['transfer_type'] = $value->transfer_type;
                $nestedData['transfer_status'] = $value->status;
                // Transfer Type - User Friendly Display
                switch ($value->transfer_type) {
                    case 'branch_to_project':
                        $nestedData['transfer_type'] = 'Branch/Warehouse → Project';
                        break;

                    case 'project_to_project':
                        $nestedData['transfer_type'] = 'Project → Project';
                        break;

                    case 'project_to_branch':
                        $nestedData['transfer_type'] = 'Project → Branch/warehouse';
                        break;

                    default:
                        $nestedData['transfer_type'] = $value->transfer_type ?? '';
                        break;
                }
                // $nestedData['purchase_requisition_id'] = $value->purchaseRequisition->invoice_no;

                $nestedData['project_id'] = $value->project->name ?? '';

                if ($ced != 0) :
                    $edit_data = $edit != 0
                        ? '<a href="' . route('project.transferproject.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit"></i></a>'
                        : '';

                    $invoice_data = $invoice != 0
                        ? '<a href="' . route('project.transferproject.invoice', $value->id) . '" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>'
                        : '';

                    $delete_data = $delete != 0
                        ? '<a delete_route="' . route('project.transferproject.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>'
                        : '';

                    $nestedData['action'] = $edit_data . ' ' . $invoice_data . ' ' . $delete_data;
                else :
                    $nestedData['action'] = '';
                endif;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        );

        return $json_data;
    }

    /**
     * @param $request
     * @return mixed
     */

    // public function store($request)
    // {


    //     DB::beginTransaction();

    //     $user = Auth::user();
    //     try {
    //         $purchaseorder = new $this->projectTransfer();
    //         $purchaseorder->order_date = $request->date;
    //         $purchaseorder->invoice_no = $request->orderCode;
    //         $purchaseorder->branch_id = $request->branch_id;
    //         $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
    //         // $purchaseorder->advance_payment = $request->paid_amount;
    //         $purchaseorder->project_id = $request->project_id;
    //         // $purchaseorder->total_bill = array_sum($request->total);
    //         $purchaseorder->note = $request->note;
    //         $purchaseorder->create_by = $user->id ?? auth()->user()->id;
    //         $purchaseorder->save();
    //         $purchaseOr_id = $purchaseorder->id;

    //         $category = $request->category_nm;
    //         $product = $request->product_nm;
    //         $qty = $request->qty;
    //         $unitprice = $request->unitprice;
    //         $total = $request->total;
    //         for ($i = 0; $i < count($category); $i++) {
    //             $purchaseOrderDetails = new ProjectTransferDetails();
    //             $purchaseOrderDetails->project_transfer_id = $purchaseOr_id;
    //             $purchaseOrderDetails->category_id = $category[$i];
    //             $purchaseOrderDetails->purchasetype = $request->purchasetype[$i];
    //             $purchaseOrderDetails->project_id = $request->project_id;
    //             $purchaseOrderDetails->product_id = $product[$i];
    //             $purchaseOrderDetails->qty = $qty[$i];
    //             // $purchaseOrderDetails->unit_price = $unitprice[$i];
    //             // $purchaseOrderDetails->total_price = $total[$i];
    //             $purchaseOrderDetails->save();


    //             $stock = new Stock();
    //             $stock->general_id = $purchaseOr_id;
    //             $stock->product_id = $product[$i];
    //             $stock->quantity = $qty[$i];
    //             $stock->branch_id = $request->branch_id;
    //             // $stock->unit_price = $unitprice[$i];
    //             // $stock->total_price = $total[$i] ?? 0;
    //             $stock->date = $request->date;
    //             $stock->status = 'Project Out';
    //             $stock->save();

    //             $stock = new Stock();
    //             $stock->general_id = $purchaseOr_id;
    //             $stock->product_id = $product[$i];
    //             $stock->quantity = $qty[$i];
    //             $stock->branch_id = $request->project_id; // project id insert on branch_id column
    //             // $stock->unit_price = $unitprice[$i] ?? 0;
    //             // $stock->total_price = $total[$i] ?? 0;
    //             $stock->date = $request->date;
    //             $stock->status = 'Project In';
    //             $stock->save();

    //             $products_array = array(
    //                 'type' => 'Branch',
    //                 'purchasetype' => $request->purchasetype[$i],
    //                 'branch_id' =>  $request->branch_id,
    //                 'product_id' => $request->product_nm[$i],
    //             );
    //             $project_array = array(
    //                 'type' => 'Project',
    //                 'purchasetype' => $request->purchasetype[$i],
    //                 'branch_id' =>  $request->project_id,
    //                 'product_id' => $request->product_nm[$i],
    //             );

    //             $stocksamaryquntitys = StockSummary::where($products_array)->pluck('quantity')->first();
    //             $stockcheck['quantity'] = $stocksamaryquntitys - $request->qty[$i];
    //             StockSummary::where($products_array)->update($stockcheck);

    //             $stocksamaryproject = StockSummary::where($project_array)->exists();

    //             if ($stocksamaryproject) {
    //                 $stocksamaryprojects = StockSummary::where($project_array)->pluck('quantity')->first();
    //                 $stockchecko['quantity'] = $stocksamaryprojects + $request->qty[$i];
    //                 StockSummary::where($project_array)->update($stockchecko);
    //             } else {
    //                 $updatestock = new StockSummary();
    //                 $updatestock->branch_id = $request->project_id;
    //                 $updatestock->purchasetype = $request->purchasetype[$i];
    //                 $updatestock->product_id =  $product[$i];
    //                 $updatestock->quantity = $qty[$i];
    //                 $updatestock->type = 'Project';
    //                 $updatestock->save();
    //             }
    //         }

    //         $purchasereq['approve_by'] = Auth::user()->id;
    //         $purchasereq['approve_at'] = date('Y-m-d');
    //         $purchasereq['status'] = 'Accepted';
    //         PurchaseRequisition::where('id', $request->purchase_requisition)->update($purchasereq);

    //         $prDetails['status'] = 'Transfer';
    //         PrDetails::where('pr_id', $request->purchase_requisition)->update($prDetails);
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage(), $e->getLine());
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }
    //     return  $purchaseorder;
    // }

    /**
     * @param $request
     * @return mixed
     */

    // public function store($request)
    // {
    //     DB::beginTransaction();
    //     $user = Auth::user();

    //     try {
    //         $type = $request->transfer_type;

    //         // ---------- 1. Header ----------
    //         $lastTransfer = $this->projectTransfer::latest('id')->first();
    //         $nextId       = $lastTransfer ? $lastTransfer->id + 1 : 1;
    //         $invoiceNo    = 'PT' . str_pad($nextId, 5, "0", STR_PAD_LEFT);

    //         $purchaseorder = new $this->projectTransfer();
    //         $purchaseorder->transfer_type = $type;
    //         $purchaseorder->order_date    = $request->date;
    //         $purchaseorder->invoice_no    = $invoiceNo;
    //         $purchaseorder->note          = $request->note;
    //         $purchaseorder->status        = 'Accepted';
    //         $purchaseorder->create_by     = $user->id ?? $this->user_id;

    //         if ($type === 'branch_to_project') {
    //             $purchaseorder->branch_id               = $request->from_branch_id;
    //             $purchaseorder->project_id               = $request->to_project_id_a;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
    //         } elseif ($type === 'project_to_project') {
    //             $purchaseorder->project_id               = $request->from_project_id;
    //             $purchaseorder->to_project_id            = $request->to_project_id_b;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition ?: null;
    //         } else { // project_to_branch
    //             $purchaseorder->project_id = $request->from_project_id;
    //             $purchaseorder->branch_id  = $request->to_branch_id;
    //         }

    //         $purchaseorder->save();
    //         $purchaseOr_id = $purchaseorder->id;

    //         // ---------- 2. Loop products ----------
    //         $category     = $request->category_nm;
    //         $product      = $request->product_nm;
    //         $qty          = $request->qty;
    //         $purchasetype = $request->purchasetype;
    //         $requestedQty = $request->requested_qty ?? []; // empty for manually-added rows

    //         for ($i = 0; $i < count($product); $i++) {
    //             $productId   = $product[$i];
    //             $transferQty = (float) $qty[$i];
    //             $ptype       = $purchasetype[$i] ?? 'local';
    //             $reqQtySnap  = isset($requestedQty[$i]) && $requestedQty[$i] !== '' ? (float) $requestedQty[$i] : null;

    //             // ---- 2a. Detail line ----
    //             $purchaseOrderDetails = new ProjectTransferDetails();
    //             $purchaseOrderDetails->project_transfer_id = $purchaseOr_id;
    //             $purchaseOrderDetails->category_id         = $category[$i];
    //             $purchaseOrderDetails->purchasetype        = $ptype;
    //             $purchaseOrderDetails->product_id          = $productId;
    //             $purchaseOrderDetails->qty                 = $transferQty;
    //             $purchaseOrderDetails->requested_qty       = $reqQtySnap;
    //             $purchaseOrderDetails->status               = 'Accepted';

    //             if ($type === 'branch_to_project') {
    //                 $purchaseOrderDetails->branch_id  = $request->from_branch_id;
    //                 $purchaseOrderDetails->project_id = $request->to_project_id_a;
    //             } elseif ($type === 'project_to_project') {
    //                 $purchaseOrderDetails->project_id = $request->to_project_id_b; // destination
    //             } else {
    //                 $purchaseOrderDetails->branch_id  = $request->to_branch_id;
    //                 $purchaseOrderDetails->project_id = $request->from_project_id;
    //             }
    //             $purchaseOrderDetails->save();

    //             // ---- 2b. Stock ledger rows (uses REAL project_id column, not branch_id) ----
    //             // NOTE: stocks.branch_id is NOT NULL at the DB level and cannot be altered.
    //             // We use 0 as a sentinel meaning "not applicable / project-only row" instead of NULL.
    //             $stockRows = [];

    //             if ($type === 'branch_to_project') {
    //                 $stockRows[] = ['branch_id' => $request->from_branch_id, 'project_id' => null, 'status' => 'Project Out'];
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->to_project_id_a, 'status' => 'Project In'];
    //             } elseif ($type === 'project_to_project') {
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->from_project_id, 'status' => 'Project Out'];
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->to_project_id_b, 'status' => 'Project In'];
    //             } else { // project_to_branch
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->from_project_id, 'status' => 'Project Out'];
    //                 $stockRows[] = ['branch_id' => $request->to_branch_id, 'project_id' => null, 'status' => 'Return'];
    //             }

    //             foreach ($stockRows as $row) {
    //                 $stock = new Stock();
    //                 $stock->general_id = $purchaseOr_id;
    //                 $stock->product_id = $productId;
    //                 $stock->quantity   = $transferQty;
    //                 $stock->branch_id  = $row['branch_id'];
    //                 $stock->project_id = $row['project_id'];
    //                 $stock->date       = $request->date;
    //                 $stock->status     = $row['status'];
    //                 $stock->created_by = $user->id ?? $this->user_id;
    //                 $stock->save();
    //             }

    //             // ---- 2c. StockSummary sync (purchasetype-aware) ----
    //             if ($type === 'branch_to_project') {
    //                 $fromKey = ['branch_id' => $request->from_branch_id, 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_project_id_a, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } elseif ($type === 'project_to_project') {
    //                 $fromKey = ['branch_id' => $request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_project_id_b, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } else {
    //                 $fromKey = ['branch_id' => $request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_branch_id, 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //             }

    //             // FROM: decrement (create if somehow missing, to avoid a hard failure)
    //             $fromRow = StockSummary::where($fromKey)->first();
    //             if ($fromRow) {
    //                 $fromRow->quantity = $fromRow->quantity - $transferQty;
    //                 $fromRow->save();
    //             } else {
    //                 $fromRow = new StockSummary();
    //                 $fromRow->branch_id    = $fromKey['branch_id'];
    //                 $fromRow->product_id   = $fromKey['product_id'];
    //                 $fromRow->type         = $fromKey['type'];
    //                 $fromRow->purchasetype = $fromKey['purchasetype'];
    //                 $fromRow->quantity     = -$transferQty;
    //                 $fromRow->save();
    //             }

    //             // TO: increment or create
    //             $toRow = StockSummary::where($toKey)->first();
    //             if ($toRow) {
    //                 $toRow->quantity = $toRow->quantity + $transferQty;
    //                 $toRow->save();
    //             } else {
    //                 $toRow = new StockSummary();
    //                 $toRow->branch_id    = $toKey['branch_id'];
    //                 $toRow->product_id   = $toKey['product_id'];
    //                 $toRow->type         = $toKey['type'];
    //                 $toRow->purchasetype = $toKey['purchasetype'];
    //                 $toRow->quantity     = $transferQty;
    //                 $toRow->save();
    //             }

    //             // ---- 2d. Decrement pr_details.remaining_qty (branch_to_project only, requisition-driven) ----
    //             if ($type === 'branch_to_project' && $request->purchase_requisition) {
    //                 $prDetail = PrDetails::where('pr_id', $request->purchase_requisition)
    //                     ->where('product_id', $productId)
    //                     ->first();

    //                 if ($prDetail) {
    //                     $currentRemaining = $prDetail->remaining_qty !== null
    //                         ? (float) $prDetail->remaining_qty
    //                         : (float) $prDetail->qty;

    //                     $newRemaining = $currentRemaining - $transferQty;

    //                     $prDetail->remaining_qty = max($newRemaining, 0);
    //                     $prDetail->status        = $newRemaining <= 0 ? 'Transfer' : 'Partial';
    //                     $prDetail->save();
    //                 }
    //             }
    //         }

    //         // ---------- 3. Requisition header status (branch_to_project only) ----------
    //         if ($type === 'branch_to_project' && $request->purchase_requisition) {
    //             PurchaseRequisition::where('id', $request->purchase_requisition)->update([
    //                 'approve_by' => $user->id ?? $this->user_id,
    //                 'approve_at' => date('Y-m-d'),
    //                 'status'     => 'Accepted',
    //             ]);
    //         }

    //         DB::commit();
    //         return $purchaseorder;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         \Log::error('ProjectTransfer store failed: ' . $e->getMessage(), [
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //         ]);
    //         session()->flash('error', 'Something went wrong while creating the transfer: ' . $e->getMessage());
    //         return null;
    //     }
    // }



    // public function store($request)
    // {
    //     DB::beginTransaction();
    //     $user = Auth::user();

    //     // dd($request->all());
    //     try {
    //         $type = $request->transfer_type;

    //         // ---------- 1. Header ----------
    //         $lastTransfer = $this->projectTransfer::latest('id')->first();
    //         $nextId       = $lastTransfer ? $lastTransfer->id + 1 : 1;
    //         $invoiceNo    = 'PT' . str_pad($nextId, 5, "0", STR_PAD_LEFT);

    //         $purchaseorder = new $this->projectTransfer();
    //         $purchaseorder->transfer_type = $type;
    //         $purchaseorder->order_date    = $request->date;
    //         $purchaseorder->invoice_no    = $invoiceNo;
    //         $purchaseorder->note          = $request->note;
    //         $purchaseorder->status        = 'Accepted';
    //         $purchaseorder->create_by     = $user->id ?? $this->user_id;

    //         // ---------- NEW: source branch/project resolve  ----------
    //         $fromBranchId  = null;
    //         $fromProjectId = null;

    //         if ($type === 'branch_to_project') {
    //             $purchaseorder->branch_id               = $request->from_branch_id;
    //             $purchaseorder->project_id               = $request->to_project_id_a;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
    //             $purchaseorder->warehouse_id     = $this->resolveWarehouseIdForBranch($request->from_branch_id);
    //             $fromBranchId = $request->from_branch_id;
    //         } elseif ($type === 'project_to_project') {
    //             $purchaseorder->project_id               = $request->from_project_id;
    //             $purchaseorder->to_project_id            = $request->to_project_id_b;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition ?: null;

    //             $fromProjectId = $request->from_project_id;
    //         } else {
    //             $purchaseorder->project_id = $request->from_project_id;
    //             $purchaseorder->branch_id  = $request->to_branch_id;
    //             $fromProjectId = $request->from_project_id;
    //             $purchaseorder->warehouse_id     = $this->resolveWarehouseIdForBranch($request->to_branch_id);
    //         }





    //         $purchaseorder->save();
    //         $purchaseOr_id = $purchaseorder->id;

    //         // ---------- 2. Loop products ----------
    //         $category     = $request->category_nm;
    //         $product      = $request->product_nm;
    //         $qty          = $request->qty;
    //         $purchasetype = $request->purchasetype;
    //         $requestedQty = $request->requested_qty ?? [];

    //         for ($i = 0; $i < count($product); $i++) {
    //             $productId   = $product[$i];
    //             $transferQty = (float) $qty[$i];
    //             $ptype       = $purchasetype[$i];
    //             $reqQtySnap  = isset($requestedQty[$i]) && $requestedQty[$i] !== '' ? (float) $requestedQty[$i] : null;

    //             // ---- NEW: server-side unit price resolve —
    //             $unitPrice  = $this->resolveUnitPrice($type, $productId, $fromBranchId, $fromProjectId);

    //             $totalPrice = round($unitPrice * $transferQty);



    //             // ---- 2a. Detail line ----
    //             $purchaseOrderDetails = new ProjectTransferDetails();
    //             $purchaseOrderDetails->project_transfer_id = $purchaseOr_id;
    //             $purchaseOrderDetails->category_id         = $category[$i];
    //             $purchaseOrderDetails->purchasetype        = $ptype;
    //             $purchaseOrderDetails->product_id          = $productId;
    //             $purchaseOrderDetails->qty                 = $transferQty;
    //             $purchaseOrderDetails->requested_qty       = $reqQtySnap;
    //             $purchaseOrderDetails->unit_price           = $unitPrice;   // NEW
    //             $purchaseOrderDetails->total_price          = $totalPrice;  // NEW
    //             $purchaseOrderDetails->status               = 'Accepted';



    //             if ($type === 'branch_to_project') {
    //                 $purchaseOrderDetails->branch_id  = $request->from_branch_id;
    //                 $purchaseOrderDetails->project_id = $request->to_project_id_a;
    //                 $purchaseOrderDetails->warehouse_id     = $this->resolveWarehouseIdForBranch($request->from_branch_id);
    //             } elseif ($type === 'project_to_project') {
    //                 $purchaseOrderDetails->project_id = $request->to_project_id_b; // destination
    //             } else {
    //                 $purchaseOrderDetails->branch_id  = $request->to_branch_id;
    //                 $purchaseOrderDetails->project_id = $request->from_project_id;
    //                 $purchaseOrderDetails->warehouse_id     = $this->resolveWarehouseIdForBranch($request->to_branch_id);
    //             }
    //             $purchaseOrderDetails->save();
    //             $stockRows = [];

    //             if ($type === 'branch_to_project') {
    //                 $stockRows[] = ['branch_id' => $request->from_branch_id, 'warehouse_id' => $this->resolveWarehouseIdForBranch($request->from_branch_id), 'project_id' => null, 'status' => 'Project Transfer Out', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->to_project_id_a, 'status' => 'Project Transfer In', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //             } elseif ($type === 'project_to_project') {
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->from_project_id, 'status' => 'Project To Project Out', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->to_project_id_b, 'status' => 'Project To Project In', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //             } else { // project_to_branch
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->from_project_id, 'status' => 'Project Transfer Out', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //                 $stockRows[] = ['branch_id' => $request->to_branch_id, 'warehouse_id' => $this->resolveWarehouseIdForBranch($request->to_branch_id), 'project_id' => null, 'status' => 'Project Transfer In', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //             }



    //             foreach ($stockRows as $row) {
    //                 $stock = new Stock();
    //                 $stock->general_id = $purchaseOr_id;
    //                 $stock->product_id = $productId;
    //                 $stock->quantity   = $transferQty;
    //                 $stock->unit_price = $row['unit_price'];
    //                 $stock->total_price = $row['totalPrice'];
    //                 $stock->branch_id  = $row['branch_id'];
    //                 $stock->project_id = $row['project_id'];
    //                 $stock->warehouse_id = $row['warehouse_id'];
    //                 $stock->date       = $request->date;
    //                 $stock->status     = $row['status'];
    //                 $stock->invoice_no = $row['invoice_no'] ?? '';
    //                 $stock->created_by = $user->id ?? $this->user_id;
    //                 $stock->save();
    //             }

    //             // ---- 2c. StockSummary sync (purchasetype-aware) ----
    //             if ($type === 'branch_to_project') {
    //                 $fromKey = ['branch_id' => $request->from_branch_id, 'project_id' => null, 'warehouse_id' => $this->resolveWarehouseIdForBranch($request->from_branch_id), 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_project_id_a, 'project_id' => $request->to_project_id_a, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } elseif ($type === 'project_to_project') {
    //                 $fromKey = ['branch_id' => $request->from_project_id, 'project_id' => $request->$request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_project_id_b, 'project_id' => $request->$request->to_project_id_b, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } else {
    //                 $fromKey = ['branch_id' => $request->from_project_id,  'project_id' => $request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_branch_id, 'project_id' => null, 'warehouse_id' => $this->resolveWarehouseIdForBranch($request->to_branch_id), 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //             }

    //             // FROM: decrement (create if somehow missing, to avoid a hard failure)
    //             $fromRow = StockSummary::where($fromKey)->first();
    //             if ($fromRow) {
    //                 $fromRow->quantity = $fromRow->quantity - $transferQty;
    //                 $fromRow->save();
    //             } else {
    //                 $fromRow = new StockSummary();
    //                 $fromRow->branch_id    = $fromKey['branch_id'];
    //                 $fromRow->product_id   = $fromKey['product_id'];
    //                 $fromRow->warehouse_id    = $fromKey['warehouse_id'];
    //                 $fromRow->project_id   = $fromKey['project_id'];
    //                 $fromRow->type         = $fromKey['type'];
    //                 $fromRow->purchasetype = $fromKey['purchasetype'];
    //                 $fromRow->quantity     = -$transferQty;
    //                 $fromRow->save();
    //             }

    //             // TO: increment or create
    //             $toRow = StockSummary::where($toKey)->first();
    //             if ($toRow) {
    //                 $toRow->quantity = $toRow->quantity + $transferQty;
    //                 $toRow->save();
    //             } else {
    //                 $fromRow = new StockSummary();
    //                 $fromRow->branch_id    = $fromKey['branch_id'];
    //                 $fromRow->product_id   = $fromKey['product_id'];
    //                 $fromRow->warehouse_id    = $fromKey['warehouse_id'];
    //                 $fromRow->project_id   = $fromKey['project_id'];
    //                 $fromRow->type         = $fromKey['type'];
    //                 $fromRow->purchasetype = $fromKey['purchasetype'];
    //                 $fromRow->quantity     = -$transferQty;
    //                 $fromRow->save();
    //             }

    //             // ---- 2d. Decrement pr_details.remaining_qty (branch_to_project only, requisition-driven) ----
    //             if ($type === 'branch_to_project' && $request->purchase_requisition) {
    //                 $prDetail = PrDetails::where('pr_id', $request->purchase_requisition)
    //                     ->where('product_id', $productId)
    //                     ->first();

    //                 if ($prDetail) {
    //                     $currentRemaining = $prDetail->remaining_qty !== null
    //                         ? (float) $prDetail->remaining_qty
    //                         : (float) $prDetail->qty;

    //                     $newRemaining = $currentRemaining - $transferQty;

    //                     $prDetail->remaining_qty = max($newRemaining, 0);
    //                     $prDetail->status        = $newRemaining <= 0 ? 'Transfer' : 'Partial';
    //                     $prDetail->save();
    //                 }
    //             }
    //         }

    //         // ---------- 3. Requisition header status (branch_to_project only) ----------
    //         if ($type === 'branch_to_project' && $request->purchase_requisition) {
    //             PurchaseRequisition::where('id', $request->purchase_requisition)->update([
    //                 'approve_by' => $user->id ?? $this->user_id,
    //                 'approve_at' => date('Y-m-d'),
    //                 'status'     => 'Accepted',
    //             ]);
    //         }

    //         DB::commit();
    //         return $purchaseorder;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         \Log::error('ProjectTransfer store failed: ' . $e->getMessage(), [
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //         ]);
    //         session()->flash('error', 'Something went wrong while creating the transfer: ' . $e->getMessage());
    //         return null;
    //     }
    // }

    // public function store($request)
    // {
        

    //     DB::beginTransaction();
    //     $user = Auth::user();

    //     try {
    //         $type = $request->transfer_type;

    //         // ---------- 1. Header ----------
    //         $lastTransfer = $this->projectTransfer::latest('id')->first();
    //         $nextId       = $lastTransfer ? $lastTransfer->id + 1 : 1;
    //         $invoiceNo    = 'PT' . str_pad($nextId, 5, "0", STR_PAD_LEFT);

    //         $purchaseorder = new $this->projectTransfer();
    //         $purchaseorder->transfer_type = $type;
    //         $purchaseorder->order_date    = $request->date;
    //         $purchaseorder->invoice_no    = $invoiceNo;
    //         $purchaseorder->note          = $request->note;
    //         $purchaseorder->status        = 'Accepted';
    //         $purchaseorder->create_by     = $user->id ?? $this->user_id;

    //         // ---------- NEW: source branch/project resolve  ----------
    //         $fromBranchId  = null;
    //         $fromProjectId = null;

    //         if ($type === 'branch_to_project') {
    //             $purchaseorder->branch_id               = $request->from_branch_id;
    //             $purchaseorder->project_id               = $request->to_project_id_a;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
    //             $purchaseorder->warehouse_id     = $request->from_warehouse_id;
    //             $purchaseorder->backup_branch_id = $request->from_branch_id;

    //             $fromBranchId = $request->from_branch_id;
    //         } elseif ($type === 'project_to_project') {
    //             $purchaseorder->project_id               = $request->from_project_id;
    //             $purchaseorder->to_project_id            = $request->to_project_id_b;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition ?: null;

    //             $fromProjectId = $request->from_project_id;
    //             // no branch involved in this type -> warehouse_id / backup_branch_id stay NULL (default)
    //         } else {
    //             $purchaseorder->project_id = $request->from_project_id;
    //             $purchaseorder->branch_id  = $request->to_branch_id;
    //             $fromProjectId = $request->from_project_id;
    //             $purchaseorder->warehouse_id     = $request->to_warehouse_id;
    //             $purchaseorder->backup_branch_id = $request->to_branch_id; // FIXED: was missing

    //         }

    //         $purchaseorder->save();
    //         $purchaseOr_id = $purchaseorder->id;

    //         // ---------- 2. Loop products ----------
    //         $category     = $request->category_nm;
    //         $product      = $request->product_nm;
    //         $qty          = $request->qty;
    //         $purchasetype = $request->purchasetype;
    //         $requestedQty = $request->requested_qty ?? [];

    //         for ($i = 0; $i < count($product); $i++) {
    //             $productId   = $product[$i];
    //             $transferQty = (float) $qty[$i];
    //             $ptype       = $purchasetype[$i];
    //             $reqQtySnap  = isset($requestedQty[$i]) && $requestedQty[$i] !== '' ? (float) $requestedQty[$i] : null;

    //             // ---- NEW: server-side unit price resolve —
    //             $unitPrice  = $this->resolveUnitPrice($type, $productId, $fromBranchId, $fromProjectId);

    //             $totalPrice = round($unitPrice * $transferQty);

    //             // ---- 2a. Detail line ----
    //             $purchaseOrderDetails = new ProjectTransferDetails();
    //             $purchaseOrderDetails->project_transfer_id = $purchaseOr_id;
    //             $purchaseOrderDetails->category_id         = $category[$i];
    //             $purchaseOrderDetails->purchasetype        = $ptype;
    //             $purchaseOrderDetails->product_id          = $productId;
    //             $purchaseOrderDetails->qty                 = $transferQty;
    //             $purchaseOrderDetails->requested_qty       = $reqQtySnap;
    //             $purchaseOrderDetails->unit_price           = $unitPrice;   // NEW
    //             $purchaseOrderDetails->total_price          = $totalPrice;  // NEW
    //             $purchaseOrderDetails->status               = 'Accepted';

    //             if ($type === 'branch_to_project') {
    //                 $purchaseOrderDetails->branch_id  = $request->from_branch_id;
    //                 $purchaseOrderDetails->project_id = $request->to_project_id_a;
    //                 $purchaseOrderDetails->warehouse_id     = $request->from_warehouse_id;
    //                 $purchaseOrderDetails->backup_branch_id = $request->from_branch_id;
    //             } elseif ($type === 'project_to_project') {
    //                 $purchaseOrderDetails->project_id = $request->to_project_id_b; // destination
    //                 // no branch involved -> warehouse_id / backup_branch_id stay NULL
    //             } else {
    //                 $purchaseOrderDetails->branch_id  = $request->to_branch_id;
    //                 $purchaseOrderDetails->project_id = $request->from_project_id;
    //                 $purchaseOrderDetails->warehouse_id     = $request->to_warehouse_id;
    //                 $purchaseOrderDetails->backup_branch_id = $request->to_branch_id; // FIXED: was missing

    //             }
    //             $purchaseOrderDetails->save();

    //             $stockRows = [];

    //             if ($type === 'branch_to_project') {
    //                 $stockRows[] = ['branch_id' => $request->from_branch_id, 'warehouse_id' => $request->from_warehouse_id, 'backup_branch_id' => $request->from_branch_id, 'project_id' => null, 'status' => 'Branch to Project', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //                 $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->to_project_id_a, 'status' => 'Project Transfer In', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //             } elseif ($type === 'project_to_project') {
    //                 $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->from_project_id, 'status' => 'Project To Project Out', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //                 $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->to_project_id_b, 'status' => 'Project To Project In', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //             } else { // project_to_branch
    //                 $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->from_project_id, 'status' => 'Project Transfer Out', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //                 $stockRows[] = ['branch_id' => $request->to_branch_id, 'warehouse_id' => $request->to_warehouse_id, 'backup_branch_id' => $request->to_branch_id, 'project_id' => null, 'status' => 'Project to Branch', 'invoice_no' => $invoiceNo, 'unit_price' => $unitPrice, 'totalPrice' => $totalPrice];
    //             }

    //             foreach ($stockRows as $row) {
    //                 $stock = new Stock();
    //                 $stock->general_id = $purchaseOr_id;
    //                 $stock->product_id = $productId;
    //                 $stock->quantity   = $transferQty;
    //                 $stock->unit_price = $row['unit_price'];
    //                 $stock->total_price = $row['totalPrice'];
    //                 $stock->branch_id  = $row['branch_id'];
    //                 $stock->project_id = $row['project_id'];
    //                 $stock->date       = $request->date;
    //                 $stock->status     = $row['status'];
    //                 $stock->invoice_no = $row['invoice_no'] ?? '';
    //                 $stock->created_by = $user->id ?? $this->user_id;
    //                 $stock->warehouse_id     = $row['warehouse_id'];
    //                 $stock->backup_branch_id = $row['backup_branch_id']; // FIXED: was never set before
    //                 // <<< END NEW
    //                 $stock->save();
    //             }

         
    //             if ($type === 'branch_to_project') {
    //                 $fromMatchKey = ['branch_id' => $request->from_branch_id, 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //                 $fromExtra    = ['project_id' => null, 'warehouse_id' => $request->from_warehouse_id, 'backup_branch_id' => $request->from_branch_id];

    //                 $toMatchKey = ['branch_id' => $request->to_project_id_a, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toExtra    = ['project_id' => $request->to_project_id_a, 'warehouse_id' => null, 'backup_branch_id' => null];
    //             } elseif ($type === 'project_to_project') {
    //                 $fromMatchKey = ['branch_id' => $request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 // FIXED: was the invalid `$request->$request->from_project_id` (fatal error)
    //                 $fromExtra    = ['project_id' => $request->from_project_id, 'warehouse_id' => null, 'backup_branch_id' => null];

    //                 $toMatchKey = ['branch_id' => $request->to_project_id_b, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 // FIXED: was the invalid `$request->$request->to_project_id_b` (fatal error)
    //                 $toExtra    = ['project_id' => $request->to_project_id_b, 'warehouse_id' => null, 'backup_branch_id' => null];
    //             } else {
    //                 $fromMatchKey = ['branch_id' => $request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $fromExtra    = ['project_id' => $request->from_project_id, 'warehouse_id' => null, 'backup_branch_id' => null];

    //                 $toMatchKey = ['branch_id' => $request->to_branch_id, 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //                 $toExtra    = ['project_id' => null, 'warehouse_id' => $request->to_warehouse_id, 'backup_branch_id' => $request->to_branch_id];
    //             }

    //             // FROM: decrement (create if somehow missing, to avoid a hard failure)
    //             $fromRow = StockSummary::where($fromMatchKey)->first();
    //             if ($fromRow) {
    //                 $fromRow->quantity = $fromRow->quantity - $transferQty;
    //                 $fromRow->save();
    //             } else {
    //                 $fromRow = new StockSummary();
    //                 $fromRow->branch_id    = $fromMatchKey['branch_id'];
    //                 $fromRow->product_id   = $fromMatchKey['product_id'];
    //                 $fromRow->type         = $fromMatchKey['type'];
    //                 $fromRow->purchasetype = $fromMatchKey['purchasetype'];
    //                 $fromRow->quantity     = -$transferQty;
    //                 $fromRow->project_id       = $fromExtra['project_id'];
    //                 $fromRow->warehouse_id     = $fromExtra['warehouse_id'];
    //                 $fromRow->backup_branch_id = $fromExtra['backup_branch_id'];
    //                 $fromRow->save();
    //             }

    //             // TO: increment or create
    //             // FIXED (critical): previously this block wrongly reused
    //             // $fromRow / $fromKey and set quantity = -$transferQty here,
    //             // meaning the TO summary row was never created correctly.
    //             $toRow = StockSummary::where($toMatchKey)->first();
    //             if ($toRow) {
    //                 $toRow->quantity = $toRow->quantity + $transferQty;
    //                 $toRow->save();
    //             } else {
    //                 $toRow = new StockSummary();
    //                 $toRow->branch_id    = $toMatchKey['branch_id'];
    //                 $toRow->product_id   = $toMatchKey['product_id'];
    //                 $toRow->type         = $toMatchKey['type'];
    //                 $toRow->purchasetype = $toMatchKey['purchasetype'];
    //                 $toRow->quantity     = $transferQty;
    //                 $toRow->project_id       = $toExtra['project_id'];
    //                 $toRow->warehouse_id     = $toExtra['warehouse_id'];
    //                 $toRow->backup_branch_id = $toExtra['backup_branch_id'];
    //                 $toRow->save();
    //             }

    //             // ---- 2d. Decrement pr_details.remaining_qty (branch_to_project only, requisition-driven) ----
    //             if ($type === 'branch_to_project' && $request->purchase_requisition) {
    //                 $prDetail = PrDetails::where('pr_id', $request->purchase_requisition)
    //                     ->where('product_id', $productId)
    //                     ->first();

    //                 if ($prDetail) {
    //                     $currentRemaining = $prDetail->remaining_qty !== null
    //                         ? (float) $prDetail->remaining_qty
    //                         : (float) $prDetail->qty;

    //                     $newRemaining = $currentRemaining - $transferQty;

    //                     $prDetail->remaining_qty = max($newRemaining, 0);
    //                     $prDetail->status        = $newRemaining <= 0 ? 'Transfer' : 'Partial';
    //                     $prDetail->save();
    //                 }
    //             }
    //         }

    //         // ---------- 3. Requisition header status (branch_to_project only) ----------
    //         if ($type === 'branch_to_project' && $request->purchase_requisition) {
    //             PurchaseRequisition::where('id', $request->purchase_requisition)->update([
    //                 'approve_by' => $user->id ?? $this->user_id,
    //                 'approve_at' => date('Y-m-d'),
    //                 'status'     => 'Accepted',
    //             ]);
    //         }

    //         DB::commit();
    //         return $purchaseorder;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         \Log::error('ProjectTransfer store failed: ' . $e->getMessage(), [
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //         ]);
    //         session()->flash('error', 'Something went wrong while creating the transfer: ' . $e->getMessage());
    //         return null;
    //     }
    // }

 
    public function store($request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        try {
            $type = $request->transfer_type;
    
            // ---------- 0. Server-side guards ----------
            if ($type === 'project_to_project' && (int) $request->from_project_id === (int) $request->to_project_id_b) {
                throw new \Exception('Source and destination project cannot be the same.');
            }
            if ($type === 'branch_to_project' && (!$request->from_branch_id || !$request->from_warehouse_id)) {
                throw new \Exception('Source branch and warehouse are required.');
            }
            if ($type === 'project_to_branch' && (!$request->to_branch_id || !$request->to_warehouse_id)) {
                throw new \Exception('Destination branch and warehouse are required.');
            }
    
            // ---------- 1. Header ----------
            $lastTransfer = $this->projectTransfer::latest('id')->first();
            $nextId       = $lastTransfer ? $lastTransfer->id + 1 : 1;
            $invoiceNo    = 'PT' . str_pad($nextId, 5, "0", STR_PAD_LEFT);
    
            $purchaseorder = new $this->projectTransfer();
            $purchaseorder->transfer_type = $type;
            $purchaseorder->order_date    = $request->date;
            $purchaseorder->invoice_no    = $invoiceNo;
            $purchaseorder->note          = $request->note;
            $purchaseorder->status        = 'Accepted';
            $purchaseorder->create_by     = $user->id ?? $this->user_id;
    
            // ---------- source / destination resolve ----------
            $fromBranchId  = null;
            $fromProjectId = null;
            $toProjectId   = null;
    
            if ($type === 'branch_to_project') {
                $purchaseorder->branch_id               = $request->from_branch_id;
                $purchaseorder->project_id              = $request->to_project_id_a;
                $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
                $purchaseorder->warehouse_id            = $request->from_warehouse_id;
                $purchaseorder->backup_branch_id        = $request->from_branch_id;
    
                $fromBranchId = $request->from_branch_id;
                $toProjectId  = $request->to_project_id_a;
            } elseif ($type === 'project_to_project') {
                $purchaseorder->project_id              = $request->from_project_id;
                $purchaseorder->to_project_id           = $request->to_project_id_b;
                $purchaseorder->purchase_requisition_id = $request->purchase_requisition ?: null;
    
                $fromProjectId = $request->from_project_id;
                $toProjectId   = $request->to_project_id_b;
            } else { // project_to_branch
                $purchaseorder->project_id       = $request->from_project_id;
                $purchaseorder->branch_id        = $request->to_branch_id;
                $purchaseorder->warehouse_id     = $request->to_warehouse_id;
                $purchaseorder->backup_branch_id = $request->to_branch_id;
    
                $fromProjectId = $request->from_project_id;
            }
    
            $purchaseorder->save();
            $purchaseOr_id = $purchaseorder->id;
    
            // ---------- 2. Loop products ----------
            $category     = $request->category_nm;
            $product      = $request->product_nm;
            $qty          = $request->qty;
            $purchasetype = $request->purchasetype;
            $requestedQty = $request->requested_qty ?? [];
    
            for ($i = 0; $i < count($product); $i++) {
                $productId   = $product[$i];
                $transferQty = (float) $qty[$i];
                $ptype       = $purchasetype[$i];
                $reqQtySnap  = isset($requestedQty[$i]) && $requestedQty[$i] !== '' ? (float) $requestedQty[$i] : null;
    
                if ($transferQty <= 0) {
                    throw new \Exception('Quantity must be greater than zero.');
                }
    
                // ---- unit price: destination project GRN -> Purchase -> fallback ----
                $unitPrice  = $this->resolveUnitPrice($type, $productId, $fromBranchId, $fromProjectId, $toProjectId, $ptype);
                $totalPrice = round($unitPrice * $transferQty, 2);
    
                // ---- 2a. Detail line ----
                $purchaseOrderDetails = new ProjectTransferDetails();
                $purchaseOrderDetails->project_transfer_id = $purchaseOr_id;
                $purchaseOrderDetails->category_id         = $category[$i];
                $purchaseOrderDetails->purchasetype        = $ptype;
                $purchaseOrderDetails->product_id          = $productId;
                $purchaseOrderDetails->qty                 = $transferQty;
                $purchaseOrderDetails->requested_qty       = $reqQtySnap;
                $purchaseOrderDetails->unit_price          = $unitPrice;
                $purchaseOrderDetails->total_price         = $totalPrice;
                $purchaseOrderDetails->status              = 'Accepted';
    
                if ($type === 'branch_to_project') {
                    $purchaseOrderDetails->branch_id        = $request->from_branch_id;
                    $purchaseOrderDetails->project_id       = $request->to_project_id_a;
                    $purchaseOrderDetails->warehouse_id     = $request->from_warehouse_id;
                    $purchaseOrderDetails->backup_branch_id = $request->from_branch_id;
                } elseif ($type === 'project_to_project') {
                    $purchaseOrderDetails->project_id = $request->to_project_id_b; // destination
                } else {
                    $purchaseOrderDetails->branch_id        = $request->to_branch_id;
                    $purchaseOrderDetails->project_id       = $request->from_project_id;
                    $purchaseOrderDetails->warehouse_id     = $request->to_warehouse_id;
                    $purchaseOrderDetails->backup_branch_id = $request->to_branch_id;
                }
                $purchaseOrderDetails->save();
    
                // ---- 2b. Stock ledger rows ----
                $stockRows = [];
    
                if ($type === 'branch_to_project') {
                    $stockRows[] = ['branch_id' => $request->from_branch_id, 'warehouse_id' => $request->from_warehouse_id, 'backup_branch_id' => $request->from_branch_id, 'project_id' => null, 'status' => 'Branch to Project'];
                    $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->to_project_id_a, 'status' => 'Project Transfer In'];
                } elseif ($type === 'project_to_project') {
                    $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->from_project_id, 'status' => 'Project To Project Out'];
                    $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->to_project_id_b, 'status' => 'Project To Project In'];
                } else { // project_to_branch
                    $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->from_project_id, 'status' => 'Project Transfer Out'];
                    $stockRows[] = ['branch_id' => $request->to_branch_id, 'warehouse_id' => $request->to_warehouse_id, 'backup_branch_id' => $request->to_branch_id, 'project_id' => null, 'status' => 'Project to Branch'];
                }
    
                foreach ($stockRows as $row) {
                    $stock = new Stock();
                    $stock->general_id       = $purchaseOr_id;
                    $stock->product_id       = $productId;
                    $stock->quantity         = $transferQty;
                    $stock->unit_price       = $unitPrice;
                    $stock->total_price      = $totalPrice;
                    $stock->branch_id        = $row['branch_id'];
                    $stock->warehouse_id     = $row['warehouse_id'];
                    $stock->project_id       = $row['project_id'];
                    $stock->date             = $request->date;
                    $stock->status           = $row['status'];
                    $stock->invoice_no       = $invoiceNo;
                    $stock->created_by       = $user->id ?? $this->user_id;
                    $stock->backup_branch_id = $row['backup_branch_id'];
                    $stock->save();
                }
    
                // ---- 2c. Stock summary FROM / TO ----
                // Project row => type + project_id + branch_id(0) + product_id + purchasetype
                // Branch row  => type + branch_id + warehouse_id + product_id + purchasetype
                if ($type === 'branch_to_project') {
                    $fromMatchKey = ['type' => 'Branch', 'branch_id' => $request->from_branch_id, 'warehouse_id' => $request->from_warehouse_id, 'product_id' => $productId, 'purchasetype' => $ptype];
                    $toMatchKey   = ['type' => 'Project', 'project_id' => $request->to_project_id_a, 'branch_id' => 0, 'product_id' => $productId, 'purchasetype' => $ptype];
                    $toExtra      = ['warehouse_id' => null, 'backup_branch_id' => null];
                } elseif ($type === 'project_to_project') {
                    $fromMatchKey = ['type' => 'Project', 'project_id' => $request->from_project_id, 'branch_id' => 0, 'product_id' => $productId, 'purchasetype' => $ptype];
                    $toMatchKey   = ['type' => 'Project', 'project_id' => $request->to_project_id_b, 'branch_id' => 0, 'product_id' => $productId, 'purchasetype' => $ptype];
                    $toExtra      = ['warehouse_id' => null, 'backup_branch_id' => null];
                } else { // project_to_branch
                    $fromMatchKey = ['type' => 'Project', 'project_id' => $request->from_project_id, 'branch_id' => 0, 'product_id' => $productId, 'purchasetype' => $ptype];
                    $toMatchKey   = ['type' => 'Branch', 'branch_id' => $request->to_branch_id, 'warehouse_id' => $request->to_warehouse_id, 'product_id' => $productId, 'purchasetype' => $ptype];
                    $toExtra      = ['project_id' => null, 'backup_branch_id' => $request->to_branch_id];
                }
    
                // FROM: decrement (row + enough stock mandatory)
                $fromRow   = StockSummary::where($fromMatchKey)->lockForUpdate()->first();
                $available = $fromRow ? (float) $fromRow->quantity : 0;
    
                if (!$fromRow || $available < $transferQty) {
                    throw new \Exception('Insufficient stock for product ID ' . $productId . ' (' . $ptype . '). Available: ' . $available . ', requested: ' . $transferQty);
                }
    
                $fromRow->quantity = $available - $transferQty;
                $fromRow->save();
    
                // TO: increment or create
                $toRow = StockSummary::where($toMatchKey)->lockForUpdate()->first();
                if ($toRow) {
                    $toRow->quantity = (float) $toRow->quantity + $transferQty;
                    $toRow->save();
                } else {
                    $toRow = new StockSummary();
                    foreach (array_merge($toMatchKey, $toExtra) as $col => $val) {
                        $toRow->{$col} = $val;
                    }
                    $toRow->quantity = $transferQty;
                    $toRow->save();
                }
    
                // ---- 2d. pr_details.remaining_qty (branch_to_project only) ----
                if ($type === 'branch_to_project' && $request->purchase_requisition) {
                    $prDetail = PrDetails::where('pr_id', $request->purchase_requisition)
                        ->where('product_id', $productId)
                        ->first();
    
                    if ($prDetail) {
                        $currentRemaining = $prDetail->remaining_qty !== null
                            ? (float) $prDetail->remaining_qty
                            : (float) $prDetail->qty;
    
                        $newRemaining = $currentRemaining - $transferQty;
    
                        $prDetail->remaining_qty = max($newRemaining, 0);
                        $prDetail->status        = $newRemaining <= 0 ? 'Transfer' : 'Partial';
                        $prDetail->save();
                    }
                }
            }
    
            // ---------- 3. Requisition header status ----------
            if ($type === 'branch_to_project' && $request->purchase_requisition) {
                PurchaseRequisition::where('id', $request->purchase_requisition)->update([
                    'approve_by' => $user->id ?? $this->user_id,
                    'approve_at' => date('Y-m-d'),
                    'status'     => 'Accepted',
                ]);
            }
    
            DB::commit();
            return $purchaseorder;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('ProjectTransfer store failed: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            session()->flash('error', 'Something went wrong while creating the transfer: ' . $e->getMessage());
            return null;
        }
    }
    
public function update($request, $id)
{
    $user            = Auth::user();
    $projectTransfer = $this->projectTransfer::findOrFail($id);
    $type            = $projectTransfer->transfer_type; // locked
 
    /* ---------- validation ---------- */
    $rules = [
        'date'           => 'required|date',
        'product_nm'     => 'required|array|min:1',
        'product_nm.*'   => 'required|integer',
        'category_nm.*'  => 'required|integer',
        'purchasetype.*' => 'required|in:local,imported',
        'qty.*'          => 'required|numeric|min:0.01',
    ];
 
    if ($type === 'branch_to_project') {
        $rules['to_project_id_a'] = 'required|exists:projects,id';
        if (!$projectTransfer->warehouse_id) {
            $rules['from_warehouse_id'] = ['required', Rule::exists('branches', 'id')->where('parent_id', $projectTransfer->branch_id)];
        }
    } elseif ($type === 'project_to_project') {
        $rules['from_project_id'] = 'required|exists:projects,id';
        $rules['to_project_id_b'] = 'required|exists:projects,id|different:from_project_id';
    } else { // project_to_branch
        $rules['from_project_id'] = 'required|exists:projects,id';
        $rules['to_branch_id']    = ['required', Rule::exists('branches', 'id')->where('parent_id', 0)];
        $rules['to_warehouse_id'] = ['required', Rule::exists('branches', 'id')->where('parent_id', $request->to_branch_id)];
    }
    $request->validate($rules);
 
    DB::beginTransaction();
 
    try {
        $prId = $projectTransfer->purchase_requisition_id;
 
        $fromBranchId    = null;
        $fromWarehouseId = null;
        $fromProjectId   = null;
        $toBranchId      = null;
        $toWarehouseId   = null;
        $toProjectId     = null; // price lookup (destination project)
 
        if ($type === 'branch_to_project') {
            $fromBranchId    = $projectTransfer->branch_id ?: $request->from_branch_id;
            $fromWarehouseId = $projectTransfer->warehouse_id ?: $request->from_warehouse_id;
            $toProjectId     = $request->to_project_id_a;
        } elseif ($type === 'project_to_project') {
            $fromProjectId = $request->from_project_id;
            $toProjectId   = $request->to_project_id_b;
        } else { // project_to_branch
            $fromProjectId = $request->from_project_id;
            $toBranchId    = $request->to_branch_id;
            $toWarehouseId = $request->to_warehouse_id;
        }
 
        // ---- stock_summaries key builders ----
        $branchKey  = fn($b, $w, $pid, $pt) => ['type' => 'Branch', 'branch_id' => $b, 'warehouse_id' => $w, 'product_id' => $pid, 'purchasetype' => $pt];
        $projectKey = fn($p, $pid, $pt) => ['type' => 'Project', 'project_id' => $p, 'branch_id' => 0, 'product_id' => $pid, 'purchasetype' => $pt];
 
        $findPr = function ($productId, $ptype) use ($prId) {
            $base = PrDetails::where('pr_id', $prId)->where('product_id', $productId);
 
            return (clone $base)->where('purchasetype', $ptype)->lockForUpdate()->first()
                ?? $base->lockForUpdate()->first();
        };
 
        /* ---------- STEP 1: give the OLD quantities back (keys from the SAVED transfer) ---------- */
        $PrTrDetails = ProjectTransferDetails::where('project_transfer_id', $id)->get();
 
        foreach ($PrTrDetails as $detail) {
            $Qty      = (float) $detail->qty;
            $oldPtype = $detail->purchasetype ?: 'local';
            $pid      = $detail->product_id;
 
            if ($type === 'branch_to_project') {
                $fromKey = $branchKey($projectTransfer->branch_id, $projectTransfer->warehouse_id, $pid, $oldPtype);
                $toKey   = $projectKey($projectTransfer->project_id, $pid, $oldPtype);
            } elseif ($type === 'project_to_project') {
                $fromKey = $projectKey($projectTransfer->project_id, $pid, $oldPtype);
                $toKey   = $projectKey($projectTransfer->to_project_id, $pid, $oldPtype);
            } else { // project_to_branch
                $fromKey = $projectKey($projectTransfer->project_id, $pid, $oldPtype);
                $toKey   = $branchKey($projectTransfer->branch_id, $projectTransfer->warehouse_id, $pid, $oldPtype);
            }
 
            // give back to source
            $fromRow = StockSummary::where($fromKey)->lockForUpdate()->first();
            if ($fromRow) {
                $fromRow->quantity = (float) $fromRow->quantity + $Qty;
                $fromRow->save();
            }
 
            // remove from destination
            $toRow = StockSummary::where($toKey)->lockForUpdate()->first();
            if ($toRow) {
                $toRow->quantity = (float) $toRow->quantity - $Qty;
                $toRow->save();
 
                if ((float) $toRow->quantity < -0.00001) {
                    throw new \RuntimeException(
                        'This transfer cannot be edited: the received quantity of product #' . $pid .
                            ' has already been used or moved out of the destination.'
                    );
                }
            }
 
            // restore requisition remaining balance (branch_to_project only)
            if ($type === 'branch_to_project' && $prId) {
                $prDetail = $findPr($pid, $oldPtype);
 
                if ($prDetail) {
                    $current  = $prDetail->remaining_qty !== null ? (float) $prDetail->remaining_qty : (float) $prDetail->qty;
                    $restored = min($current + $Qty, (float) $prDetail->qty);
 
                    $prDetail->remaining_qty = $restored;
                    $prDetail->status        = $restored >= (float) $prDetail->qty ? 'Accepted' : 'Partial';
                    $prDetail->save();
                }
            }
        }
 
        /* ---------- STEP 1b: check the NEW lines before re-applying ---------- */
        $category     = $request->category_nm;
        $product      = $request->product_nm;
        $qty          = $request->qty;
        $purchasetype = $request->purchasetype;
        $requestedQty = $request->requested_qty ?? [];
 
        $pools = [];
        for ($i = 0; $i < count($product); $i++) {
            $poolKey         = $product[$i] . '|' . ($purchasetype[$i] ?? 'local');
            $pools[$poolKey] = ($pools[$poolKey] ?? 0) + (float) $qty[$i];
        }
 
        $prBefore = [];
        foreach ($pools as $poolKey => $sum) {
            [$pid, $pt] = explode('|', $poolKey, 2);
 
            $srcKey = $type === 'branch_to_project'
                ? $branchKey($fromBranchId, $fromWarehouseId, $pid, $pt)
                : $projectKey($fromProjectId, $pid, $pt);
 
            $srcRow    = StockSummary::where($srcKey)->lockForUpdate()->first();
            $available = $srcRow ? (float) $srcRow->quantity : 0.0;
 
            if ($sum > $available + 0.00001) {
                throw new \RuntimeException(
                    'Insufficient stock for product #' . $pid . ' (' . $pt . '). Available: ' .
                        round($available, 2) . ', requested: ' . round($sum, 2) . '.'
                );
            }
 
            if ($type === 'branch_to_project' && $prId) {
                $prDetail = $findPr($pid, $pt);
                if ($prDetail) {
                    $remaining = $prDetail->remaining_qty !== null ? (float) $prDetail->remaining_qty : (float) $prDetail->qty;
                    if ($sum > $remaining + 0.00001) {
                        throw new \RuntimeException(
                            'Quantity of product #' . $pid . ' exceeds the remaining requisition quantity (' . round($remaining, 2) . ').'
                        );
                    }
                    $prBefore[$poolKey] = $remaining;
                }
            }
        }
 
        // remove old ledger + detail rows of THIS transfer
        Stock::where('general_id', $id)
            ->where(function ($q) use ($projectTransfer) {
                $q->where('invoice_no', $projectTransfer->invoice_no)
                    ->orWhereIn('status', [
                        'Branch to Project',
                        'Project Transfer In',
                        'Project To Project Out',
                        'Project To Project In',
                        'Project Transfer Out',
                        'Project to Branch',
                    ]);
            })
            ->delete();
 
        ProjectTransferDetails::where('project_transfer_id', $id)->delete();
 
        /* ---------- STEP 2: update header ---------- */
        $projectTransfer->order_date = $request->date;
        $projectTransfer->note       = $request->note;
        $projectTransfer->update_by  = $user->id ?? $this->user_id;
 
        if ($type === 'branch_to_project') {
            $projectTransfer->branch_id        = $fromBranchId;
            $projectTransfer->warehouse_id     = $fromWarehouseId;
            $projectTransfer->project_id       = $request->to_project_id_a;
            $projectTransfer->backup_branch_id = $fromBranchId;
        } elseif ($type === 'project_to_project') {
            $projectTransfer->project_id       = $request->from_project_id;
            $projectTransfer->to_project_id    = $request->to_project_id_b;
            $projectTransfer->warehouse_id     = null;
            $projectTransfer->backup_branch_id = null;
        } else { // project_to_branch
            $projectTransfer->project_id       = $request->from_project_id;
            $projectTransfer->branch_id        = $toBranchId;
            $projectTransfer->warehouse_id     = $toWarehouseId;
            $projectTransfer->backup_branch_id = $toBranchId;
        }
 
        $projectTransfer->save();
        $purchaseOr_id = $projectTransfer->id;
        $invoiceNo     = $projectTransfer->invoice_no;
 
        /* ---------- STEP 3: re-apply the new lines ---------- */
        for ($i = 0; $i < count($product); $i++) {
            $productId   = $product[$i];
            $transferQty = (float) $qty[$i];
            $ptype       = $purchasetype[$i] ?? 'local';
            $poolKey     = $productId . '|' . $ptype;
 
            $clientSnap = isset($requestedQty[$i]) && $requestedQty[$i] !== '' ? (float) $requestedQty[$i] : null;
            $reqQtySnap = $prBefore[$poolKey] ?? $clientSnap;
 
            // FIXED: same signature as store(): (..., $toProjectId, $ptype). Age ekhane warehouse id jachchilo.
            $unitPrice  = $this->resolveUnitPrice($type, $productId, $fromBranchId, $fromProjectId, $toProjectId, $ptype);
            $totalPrice = round($unitPrice * $transferQty, 2);
 
            // ---- 3a. Detail line ----
            $detail = new ProjectTransferDetails();
            $detail->project_transfer_id = $purchaseOr_id;
            $detail->category_id         = $category[$i];
            $detail->purchasetype        = $ptype;
            $detail->product_id          = $productId;
            $detail->qty                 = $transferQty;
            $detail->requested_qty       = $reqQtySnap;
            $detail->unit_price          = $unitPrice;
            $detail->total_price         = $totalPrice;
            $detail->status              = 'Accepted';
 
            if ($type === 'branch_to_project') {
                $detail->branch_id        = $fromBranchId;
                $detail->warehouse_id     = $fromWarehouseId;
                $detail->backup_branch_id = $fromBranchId;
                $detail->project_id       = $request->to_project_id_a;
            } elseif ($type === 'project_to_project') {
                $detail->project_id = $request->to_project_id_b;
            } else { // project_to_branch
                $detail->branch_id        = $toBranchId;
                $detail->warehouse_id     = $toWarehouseId;
                $detail->backup_branch_id = $toBranchId;
                $detail->project_id       = $request->from_project_id;
            }
            $detail->save();
 
            // ---- 3b. Stock ledger rows ----
            $stockRows = [];
 
            if ($type === 'branch_to_project') {
                $stockRows[] = ['branch_id' => $fromBranchId, 'warehouse_id' => $fromWarehouseId, 'backup_branch_id' => $fromBranchId, 'project_id' => null, 'status' => 'Branch to Project'];
                $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->to_project_id_a, 'status' => 'Project Transfer In'];
            } elseif ($type === 'project_to_project') {
                $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->from_project_id, 'status' => 'Project To Project Out'];
                $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->to_project_id_b, 'status' => 'Project To Project In'];
            } else { // project_to_branch
                $stockRows[] = ['branch_id' => 0, 'warehouse_id' => null, 'backup_branch_id' => null, 'project_id' => $request->from_project_id, 'status' => 'Project Transfer Out'];
                $stockRows[] = ['branch_id' => $toBranchId, 'warehouse_id' => $toWarehouseId, 'backup_branch_id' => $toBranchId, 'project_id' => null, 'status' => 'Project to Branch'];
            }
 
            foreach ($stockRows as $row) {
                $stock = new Stock();
                $stock->general_id       = $purchaseOr_id;
                $stock->product_id       = $productId;
                $stock->quantity         = $transferQty;
                $stock->unit_price       = $unitPrice;
                $stock->total_price      = $totalPrice;
                $stock->branch_id        = $row['branch_id'];
                $stock->warehouse_id     = $row['warehouse_id'];
                $stock->project_id       = $row['project_id'];
                $stock->date             = $request->date;
                $stock->status           = $row['status'];
                $stock->invoice_no       = $invoiceNo ?? '';
                $stock->created_by       = $user->id ?? $this->user_id;
                $stock->backup_branch_id = $row['backup_branch_id'];
                $stock->save();
            }
 
            // ---- 3c. StockSummary FROM / TO ----
            if ($type === 'branch_to_project') {
                $fromMatchKey = $branchKey($fromBranchId, $fromWarehouseId, $productId, $ptype);
                $toMatchKey   = $projectKey($request->to_project_id_a, $productId, $ptype);
                $toExtra      = ['warehouse_id' => null, 'backup_branch_id' => null];
            } elseif ($type === 'project_to_project') {
                $fromMatchKey = $projectKey($request->from_project_id, $productId, $ptype);
                $toMatchKey   = $projectKey($request->to_project_id_b, $productId, $ptype);
                $toExtra      = ['warehouse_id' => null, 'backup_branch_id' => null];
            } else { // project_to_branch
                $fromMatchKey = $projectKey($request->from_project_id, $productId, $ptype);
                $toMatchKey   = $branchKey($toBranchId, $toWarehouseId, $productId, $ptype);
                $toExtra      = ['project_id' => null, 'backup_branch_id' => $toBranchId];
            }
 
            // FROM: decrement (row + enough stock mandatory)
            $fromRow   = StockSummary::where($fromMatchKey)->lockForUpdate()->first();
            $available = $fromRow ? (float) $fromRow->quantity : 0;
 
            if (!$fromRow || $available < $transferQty - 0.00001) {
                throw new \RuntimeException('Insufficient stock for product ID ' . $productId . ' (' . $ptype . '). Available: ' . $available . ', requested: ' . $transferQty);
            }
 
            $fromRow->quantity = $available - $transferQty;
            $fromRow->save();
 
            // TO: increment or create
            $toRow = StockSummary::where($toMatchKey)->lockForUpdate()->first();
            if ($toRow) {
                $toRow->quantity = (float) $toRow->quantity + $transferQty;
                $toRow->save();
            } else {
                $toRow = new StockSummary();
                foreach (array_merge($toMatchKey, $toExtra) as $col => $val) {
                    $toRow->{$col} = $val;
                }
                $toRow->quantity = $transferQty;
                $toRow->save();
            }
 
            // ---- 3d. pr_details.remaining_qty (branch_to_project only) ----
            if ($type === 'branch_to_project' && $prId) {
                $prDetail = $findPr($productId, $ptype);
 
                if ($prDetail) {
                    $current      = $prDetail->remaining_qty !== null ? (float) $prDetail->remaining_qty : (float) $prDetail->qty;
                    $newRemaining = $current - $transferQty;
 
                    $prDetail->remaining_qty = max($newRemaining, 0);
                    $prDetail->status        = $newRemaining <= 0 ? 'Transfer' : 'Partial';
                    $prDetail->save();
                }
            }
        }
 
        /* ---------- STEP 4: requisition header status ---------- */
        if ($type === 'branch_to_project' && $prId) {
            PurchaseRequisition::where('id', $prId)->update([
                'approve_by' => $user->id ?? $this->user_id,
                'approve_at' => date('Y-m-d'),
                'status'     => 'Accepted',
            ]);
        }
 
        DB::commit();
        return $projectTransfer;
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('ProjectTransfer update failed: ' . $e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        session()->flash('error', 'Something went wrong while updating the transfer: ' . $e->getMessage());
        return null;
    }
}

  


    // public function update($request, $id)
    // {
    //     DB::beginTransaction();
    //     $user = Auth::user();

    //     try {
    //         $purchaseorder = $this->projectTransfer::findOrFail($id);
    //         $type = $purchaseorder->transfer_type; // locked, comes from the existing record — not from $request

    //         $oldDetails = ProjectTransferDetails::where('project_transfer_id', $id)->get();

    //         // ---------- STEP 1: reverse old effects (StockSummary + pr_details.remaining_qty) ----------
    //         foreach ($oldDetails as $old) {
    //             $oldQty = (float) $old->qty;
    //             $ptype  = $old->purchasetype ?: 'local';

    //             if ($type === 'branch_to_project') {
    //                 $fromKey = ['branch_id' => $purchaseorder->branch_id, 'product_id' => $old->product_id, 'type' => 'Branch', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $purchaseorder->project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } elseif ($type === 'project_to_project') {
    //                 $fromKey = ['branch_id' => $purchaseorder->project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $purchaseorder->to_project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } else { // project_to_branch
    //                 $fromKey = ['branch_id' => $purchaseorder->project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $purchaseorder->branch_id, 'product_id' => $old->product_id, 'type' => 'Branch', 'purchasetype' => $ptype];
    //             }

    //             // give back to source
    //             $fromRow = StockSummary::where($fromKey)->first();
    //             if ($fromRow) {
    //                 $fromRow->quantity = $fromRow->quantity + $oldQty;
    //                 $fromRow->save();
    //             }

    //             // remove from destination
    //             $toRow = StockSummary::where($toKey)->first();
    //             if ($toRow) {
    //                 $toRow->quantity = $toRow->quantity - $oldQty;
    //                 $toRow->save();
    //             }

    //             // restore requisition remaining balance (branch_to_project only, requisition-linked lines only)
    //             if ($type === 'branch_to_project' && $purchaseorder->purchase_requisition_id && $old->requested_qty !== null) {
    //                 $prDetail = PrDetails::where('pr_id', $purchaseorder->purchase_requisition_id)
    //                     ->where('product_id', $old->product_id)
    //                     ->first();

    //                 if ($prDetail) {
    //                     $current  = $prDetail->remaining_qty !== null ? (float) $prDetail->remaining_qty : (float) $prDetail->qty;
    //                     $restored = min($current + $oldQty, (float) $prDetail->qty);

    //                     $prDetail->remaining_qty = $restored;
    //                     $prDetail->status        = $restored >= (float) $prDetail->qty ? 'Accepted' : 'Partial';
    //                     $prDetail->save();
    //                 }
    //             }
    //         }

    //         // remove old ledger + detail rows tied to this transfer
    //         Stock::where('general_id', $id)->delete();
    //         ProjectTransferDetails::where('project_transfer_id', $id)->delete();

    //         // ---------- STEP 2: update header ----------
    //         $purchaseorder->order_date = $request->date;
    //         $purchaseorder->note       = $request->note;

    //         $fromBranchId  = null;
    //         $fromProjectId = null;


    //         if ($type === 'branch_to_project') {
    //             $purchaseorder->branch_id               = $request->from_branch_id;
    //             $purchaseorder->project_id               = $request->to_project_id_a;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
    //             $fromBranchId = $request->from_branch_id;
    //         } elseif ($type === 'project_to_project') {
    //             $purchaseorder->project_id               = $request->from_project_id;
    //             $purchaseorder->to_project_id            = $request->to_project_id_b;
    //             $purchaseorder->purchase_requisition_id = $request->purchase_requisition ?: null;
    //             $fromProjectId = $request->from_project_id;
    //         } else { // project_to_branch
    //             $purchaseorder->project_id = $request->from_project_id;
    //             $purchaseorder->branch_id  = $request->to_branch_id;
    //             $fromProjectId = $request->from_project_id;
    //         }

    //         $purchaseorder->save();
    //         $purchaseOr_id = $purchaseorder->id;

    //         // ---------- STEP 3: re-apply exactly like store ----------
    //         $category     = $request->category_nm;
    //         $product      = $request->product_nm;
    //         $qty          = $request->qty;
    //         $purchasetype = $request->purchasetype;
    //         $requestedQty = $request->requested_qty ?? [];

    //         for ($i = 0; $i < count($product); $i++) {
    //             $productId   = $product[$i];
    //             $transferQty = (float) $qty[$i];
    //             $ptype       = $purchasetype[$i] ?? 'local';
    //             $reqQtySnap  = isset($requestedQty[$i]) && $requestedQty[$i] !== '' ? (float) $requestedQty[$i] : null;

    //             $unitPrice  = $this->resolveUnitPrice($type, $productId, $fromBranchId, $fromProjectId);
    //             $totalPrice = round($unitPrice * $transferQty, 2);

    //             // ---- 3a. Detail line ----
    //             $detail = new ProjectTransferDetails();
    //             $detail->project_transfer_id = $purchaseOr_id;
    //             $detail->category_id         = $category[$i];
    //             $detail->purchasetype        = $ptype;
    //             $detail->product_id          = $productId;
    //             $detail->qty                 = $transferQty;
    //             $detail->requested_qty       = $reqQtySnap;
    //             $detail->unit_price          = $unitPrice;
    //             $detail->total_price         = $totalPrice;
    //             $detail->status              = 'Accepted';

    //             if ($type === 'branch_to_project') {
    //                 $detail->branch_id  = $request->from_branch_id;
    //                 $detail->project_id = $request->to_project_id_a;
    //             } elseif ($type === 'project_to_project') {
    //                 $detail->project_id = $request->to_project_id_b;
    //             } else {
    //                 $detail->branch_id  = $request->to_branch_id;
    //                 $detail->project_id = $request->from_project_id;
    //             }
    //             $detail->save();

    //             // ---- 3b. Stock ledger rows ----
    //             $stockRows = [];
    //             if ($type === 'branch_to_project') {
    //                 $stockRows[] = ['branch_id' => $request->from_branch_id, 'project_id' => null, 'status' => 'Project Out'];
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->to_project_id_a, 'status' => 'Project In'];
    //             } elseif ($type === 'project_to_project') {
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->from_project_id, 'status' => 'Project Out'];
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->to_project_id_b, 'status' => 'Project In'];
    //             } else {
    //                 $stockRows[] = ['branch_id' => 0, 'project_id' => $request->from_project_id, 'status' => 'Project Out'];
    //                 $stockRows[] = ['branch_id' => $request->to_branch_id, 'project_id' => null, 'status' => 'Return'];
    //             }

    //             foreach ($stockRows as $row) {
    //                 $stock = new Stock();
    //                 $stock->general_id = $purchaseOr_id;
    //                 $stock->product_id = $productId;
    //                 $stock->quantity   = $transferQty;
    //                 $stock->branch_id  = $row['branch_id'];
    //                 $stock->project_id = $row['project_id'];
    //                 $stock->date       = $request->date;
    //                 $stock->status     = $row['status'];
    //                 $stock->created_by = $user->id ?? $this->user_id;
    //                 $stock->save();
    //             }

    //             // ---- 3c. StockSummary sync ----
    //             if ($type === 'branch_to_project') {
    //                 $fromKey = ['branch_id' => $request->from_branch_id, 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_project_id_a, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } elseif ($type === 'project_to_project') {
    //                 $fromKey = ['branch_id' => $request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_project_id_b, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //             } else {
    //                 $fromKey = ['branch_id' => $request->from_project_id, 'product_id' => $productId, 'type' => 'Project', 'purchasetype' => $ptype];
    //                 $toKey   = ['branch_id' => $request->to_branch_id, 'product_id' => $productId, 'type' => 'Branch', 'purchasetype' => $ptype];
    //             }

    //             $fromRow = StockSummary::where($fromKey)->first();
    //             if ($fromRow) {
    //                 $fromRow->quantity = $fromRow->quantity - $transferQty;
    //                 $fromRow->save();
    //             } else {
    //                 $fromRow = new StockSummary();
    //                 $fromRow->branch_id    = $fromKey['branch_id'];
    //                 $fromRow->product_id   = $fromKey['product_id'];
    //                 $fromRow->type         = $fromKey['type'];
    //                 $fromRow->purchasetype = $fromKey['purchasetype'];
    //                 $fromRow->quantity     = -$transferQty;
    //                 $fromRow->save();
    //             }

    //             $toRow = StockSummary::where($toKey)->first();
    //             if ($toRow) {
    //                 $toRow->quantity = $toRow->quantity + $transferQty;
    //                 $toRow->save();
    //             } else {
    //                 $toRow = new StockSummary();
    //                 $toRow->branch_id    = $toKey['branch_id'];
    //                 $toRow->product_id   = $toKey['product_id'];
    //                 $toRow->type         = $toKey['type'];
    //                 $toRow->purchasetype = $toKey['purchasetype'];
    //                 $toRow->quantity     = $transferQty;
    //                 $toRow->save();
    //             }

    //             // ---- 3d. Decrement pr_details.remaining_qty (branch_to_project only) ----
    //             if ($type === 'branch_to_project' && $request->purchase_requisition) {
    //                 $prDetail = PrDetails::where('pr_id', $request->purchase_requisition)
    //                     ->where('product_id', $productId)
    //                     ->first();

    //                 if ($prDetail) {
    //                     $current      = $prDetail->remaining_qty !== null ? (float) $prDetail->remaining_qty : (float) $prDetail->qty;
    //                     $newRemaining = $current - $transferQty;

    //                     $prDetail->remaining_qty = max($newRemaining, 0);
    //                     $prDetail->status        = $newRemaining <= 0 ? 'Transfer' : 'Partial';
    //                     $prDetail->save();
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         return $purchaseorder;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         \Log::error('ProjectTransfer update failed: ' . $e->getMessage(), [
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //         ]);
    //         session()->flash('error', 'Something went wrong while updating the transfer: ' . $e->getMessage());
    //         return null;
    //     }
    // }

    // public function update($request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $purchaseorder = $this->projectTransfer::find($id);
    //         $purchaseorder->order_date = $request->date;
    //         $purchaseorder->branch_id = $request->branch_id;
    //         $purchaseorder->project_id = $request->project_id;
    //         $purchaseorder->purchase_requisition_id = $request->purchase_requisition;
    //         $purchaseorder->note = $request->note;
    //         $purchaseorder->save();

    //         ProjectTransferDetails::where('project_transfer_id', $id)->delete();

    //         $category = $request->category_nm;
    //         $product = $request->product_nm;
    //         $qty = $request->qty;
    //         $unitprice = $request->unitprice;
    //         $total = $request->total;
    //         for ($i = 0; $i < count($category); $i++) {
    //             $purchaseOrderDetails = new ProjectTransferDetails();
    //             $purchaseOrderDetails->project_transfer_id = $id;
    //             $purchaseOrderDetails->category_id = $category[$i];
    //             $purchaseOrderDetails->branch_id = $request->branch_id;
    //             $purchaseOrderDetails->product_id = $product[$i];
    //             $purchaseOrderDetails->project_id = $request->project_id;
    //             $purchaseOrderDetails->qty = $qty[$i];
    //             // $purchaseOrderDetails->unit_price = $unitprice[$i];
    //             // $purchaseOrderDetails->total_price = $total[$i];
    //             $purchaseOrderDetails->save();
    //         }
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage());
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }
    // }

    /**
     * @param $id
     * @return bool
     */

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $purchaseorder = $this->projectTransfer::find($id);

            if (!$purchaseorder) {
                DB::rollback();
                session()->flash('error', 'Transfer not found!!');
                return false;
            }


            if ($purchaseorder->status == "Accepted" && Auth::user()->type !== 'Admin') {
                DB::rollback();
                session()->flash('error', "Sorry, you couldn't delete!!");
                return false;
            }

            $type = $purchaseorder->transfer_type;
            $details = ProjectTransferDetails::where('project_transfer_id', $id)->get();

            // ---------- STEP 1: reverse StockSummary + pr_details.remaining_qty ----------
            foreach ($details as $old) {
                $oldQty = (float) $old->qty;
                $ptype  = $old->purchasetype ?: 'local';

                if ($type === 'branch_to_project') {
                    $fromKey = ['branch_id' => $purchaseorder->branch_id, 'product_id' => $old->product_id, 'type' => 'Branch', 'purchasetype' => $ptype];
                    $toKey   = ['branch_id' => $purchaseorder->project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
                } elseif ($type === 'project_to_project') {
                    $fromKey = ['branch_id' => $purchaseorder->project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
                    $toKey   = ['branch_id' => $purchaseorder->to_project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
                } else { // project_to_branch
                    $fromKey = ['branch_id' => $purchaseorder->project_id, 'product_id' => $old->product_id, 'type' => 'Project', 'purchasetype' => $ptype];
                    $toKey   = ['branch_id' => $purchaseorder->branch_id, 'product_id' => $old->product_id, 'type' => 'Branch', 'purchasetype' => $ptype];
                }

                // give back to source (undo the decrement done at store/update time)
                $fromRow = StockSummary::where($fromKey)->first();
                if ($fromRow) {
                    $fromRow->quantity = $fromRow->quantity + $oldQty;
                    $fromRow->save();
                } else {
                    $fromRow = new StockSummary();
                    $fromRow->branch_id    = $fromKey['branch_id'];
                    $fromRow->product_id   = $fromKey['product_id'];
                    $fromRow->type         = $fromKey['type'];
                    $fromRow->purchasetype = $fromKey['purchasetype'];
                    $fromRow->quantity     = $oldQty;
                    $fromRow->save();
                }

                // remove from destination (undo the increment done at store/update time)
                $toRow = StockSummary::where($toKey)->first();
                if ($toRow) {
                    $toRow->quantity = $toRow->quantity - $oldQty;
                    $toRow->save();
                }

                // restore requisition remaining balance (branch_to_project only, requisition-linked lines only)
                if ($type === 'branch_to_project' && $purchaseorder->purchase_requisition_id && $old->requested_qty !== null) {
                    $prDetail = PrDetails::where('pr_id', $purchaseorder->purchase_requisition_id)
                        ->where('product_id', $old->product_id)
                        ->first();

                    if ($prDetail) {
                        $current  = $prDetail->remaining_qty !== null ? (float) $prDetail->remaining_qty : (float) $prDetail->qty;
                        $restored = min($current + $oldQty, (float) $prDetail->qty);

                        $prDetail->remaining_qty = $restored;
                        $prDetail->status        = $restored >= (float) $prDetail->qty ? 'Accepted' : 'Partial';
                        $prDetail->save();
                    }
                }
            }

            // ---------- STEP 2: delete ledger + detail + header rows ----------
            Stock::where('general_id', $id)->delete();
            ProjectTransferDetails::where('project_transfer_id', $id)->delete();
            $purchaseorder->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('ProjectTransfer destroy failed: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            session()->flash('error', 'Something went wrong while deleting the transfer: ' . $e->getMessage());
            return false;
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


    public function getprListByTransfer($request)
    {
        $data = '';
        $prDetails = PrDetails::where('pr_id', $request->id)->get();

        $purchaserequi = PurchaseRequisition::find($request->id);
        $project = '<option selected value="' . $purchaserequi->project_id  . '"> ' . $purchaserequi->project->projectCode . ' - ' .  $purchaserequi->project->name . '</option>';


        foreach ($prDetails as $value) {
            $remaining = $value->remaining_qty !== null ? (float) $value->remaining_qty : (float) $value->qty;

            if ($remaining <= 0) {
                continue;
            }

            $data .= '<tr class="delrow new_item' . $value->product_id . '">
        <td>' . $value->category->name . '<input type="hidden" name="category_nm[]" value="' . $value->category_id . '"></td>
        <td class="text-right">' . $value->product->name . '<input type="hidden" name="product_nm[]" value="' . $value->product_id . '"></td>
        <td class="text-right">' . $value->purchasetype . '<input type="hidden" name="purchasetype[]" value="' . $value->purchasetype . '"></td>
        <td class="text-right"> <input class="qnty form-control" type="number" step="0.01" max="' . $remaining . '" name="qty[]" value="' . $remaining . '">
                                    <div class="productStockCheck' . $value->product_id . ' "></div>
        </td>
        <td>
                <a del_id="' . $value->product_id . '" class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                    <i class="fa fa-times"></i>
                </a>
        </td>
        <input type="hidden" name="requested_qty[]" value="' . $remaining . '">
    </tr>';
        }

        return ['prdetails' => $data, 'project' => $project];
    }


 private function resolveUnitPrice($type, $productId, $fromBranchId = null, $fromProjectId = null, $toProjectId = null ,  $ptype)
{
 

    // Destination project thakle: 1) oi project er GRN, 2) Purchase last price
    if (in_array($type, ['branch_to_project', 'project_to_project'])) {
        if ($toProjectId) {
            $grnPrice = $this->lastProjectGrnPrice($productId, $toProjectId , $ptype);
            if ($grnPrice !== null) {
                return $grnPrice;
            }
        }
 
        return $this->lastPurchasePrice($productId, null , $ptype);
    }
 
    // project_to_branch: ager logic
    $lastTransferPrice = ProjectTransferDetails::where('product_id', $productId)
        ->whereNotNull('unit_price')
        ->where('unit_price', '>', 0)
        ->orderByDesc('id')
        ->value('unit_price');
 
    if ($lastTransferPrice !== null) {
        return (float) $lastTransferPrice;
    }
 
    return $this->lastPurchasePrice($productId, null , $ptype);
}
 
private function lastProjectGrnPrice($productId, $projectId , $ptype)
{
  
    $price = DB::table('grn_details')
        ->join('grns', 'grns.id', '=', 'grn_details.good_rcv_note_id')
        ->where('grns.project_id', $projectId)
        ->where('grn_details.product_id', $productId)
        ->where('grn_details.purchasetype', $ptype)
        ->whereNotNull('grn_details.unit_price')
        ->where('grn_details.unit_price', '>', 0)
        ->orderByDesc('grn_details.id')
        ->value('grn_details.unit_price');

    return $price !== null ? (float) $price : null;
}
 
private function lastPurchasePrice($productId, $branchId = null , $ptype)
{
    $query = PurchasesDetails::where('product_id', $productId)->where('purchasetype' ,  $ptype)
        ->whereNotNull('unit_price');
 
    if ($branchId) {
        $branchPrice = (clone $query)->where('branch_id', $branchId)
            ->orderByDesc('id')
            ->value('unit_price');
 
        if ($branchPrice !== null) {
            return (float) $branchPrice;
        }
    }
 
    return (float) ($query->orderByDesc('id')->value('unit_price') ?? 0);
}


    private function resolveWarehouseIdForBranch($branchId): ?int
    {
        if (empty($branchId)) {
            return null;
        }

        $branch = DB::table('branches')->where('id', $branchId)->first();
        // no matching branches row, or it's a real branch (parent_id = 0) -> no warehouse mapping
        if (!$branch || (int) $branch->parent_id === 0) {
            return null;
        }

        $warehouse = DB::table('warehouses')->where('id', $branch->warehouse_id)->first();

        return $warehouse->id ?? null;
    }

 
}
