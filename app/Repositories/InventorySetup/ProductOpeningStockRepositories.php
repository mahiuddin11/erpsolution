<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductOpeningStock;
use App\Models\ProductOpeningStockDetails;
use App\Models\StockAjdustment;
use App\Models\StockAjdustmentDetailst;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class ProductOpeningStockRepositories
{
    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var Brand
     */
    private $productOpeningStock;

    /**
     * CourseRepository constructor.
     * @param brand $purchase
     */
    public function __construct(ProductOpeningStock $ProductOpeningStock)
    {
        $this->productOpeningStock = $ProductOpeningStock;
        $this->user_id = 1; //auth()->user()->id;

    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        $result = $this->productOpeningStock::latest()->get();
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
        $ced = $edit + $delete + $view;

        $totalData = $this->productOpeningStock::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $purchases = $this->productOpeningStock::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->productOpeningStock::count();
        } else {
            $search = $request->input('search.value');
            $purchases = $this->productOpeningStock::where('invoice_no', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->productOpeningStock::where('invoice_no', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($purchases) {
            foreach ($purchases as $key => $purchase) {
                $nestedData['id'] = $key + 1;
                $nestedData['invoice_no'] = $purchase->invoice_no;
                $nestedData['branch'] = $purchase->branch->name  ??  $purchase->project->name ?? '-';
                $nestedData['warehosue'] = $purchase->warehouse->name ?? '-';
                $nestedData['created_by'] = $purchase->user->name ?? "";
                $nestedData['date'] = $purchase->date ?? 'N/A';
                $nestedData['qty'] = $purchase->qty;
                $nestedData['total_price'] = $purchase->total_price;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.productOS.edit', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';

                    if ($view = !0)
                        $view_data = '<a href="' . route('inventorySetup.productOS.show', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.productOS.destroy', $purchase->id) . '" delete_id="' . $purchase->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $purchase->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->productOpeningStock::find($id);
        return $result;
    }

    // public function store($request)
    // {
    //     DB::beginTransaction();
    //     try {

    //         $productOpeningStock = new $this->productOpeningStock();
    //         $productOpeningStock->invoice_no = $request->invoice_no;
    //         $productOpeningStock->created_by = auth()->id();
    //         $productOpeningStock->date = $request->date;
    //         $productOpeningStock->branch_id = $request->branch_id;
    //         $productOpeningStock->warehouse_id = $request->warehouse_id;
    //         $productOpeningStock->project_id = $request->project_id;
    //         $productOpeningStock->qty = array_sum($request->qty);
    //         $productOpeningStock->total_price = array_sum($request->total);
    //         $productOpeningStock->narration = $request->narration;
    //         $productOpeningStock->save();
    //         $productOpeningStocks_id = $productOpeningStock->id;

    //         $category_id = $request->catName;
    //         $proName = $request->proName;
    //         $subtotal = $request->unitprice;
    //         $grand_total = $request->total;
    //         $qty = $request->qty;

    //         for ($i = 0; $i < count($category_id); $i++) {
    //             $existingCheck = StockSummary::where('product_id', $proName[$i]);

    //             if ($request->branch_id) {
    //                 $existingCheck =  $existingCheck->where('branch_id', $request->branch_id)->where('warehouse_id',$request->warehouse_id )->where('type', "Branch");
    //             }

    //             if ($request->project_id) {
    //                 $existingCheck =  $existingCheck->where('project_id', $request->project_id)->where('type', "Project");
    //             }

    //             $existingCheck = $existingCheck->where('purchasetype', $request->purchasetype[$i])->first();

    //             if (!empty($existingCheck)) :
    //                 $newQty = $existingCheck->quantity + $qty[$i];
    //                 if ($request->branch_id):
    //                     StockSummary::where('product_id', $proName[$i])->where('warehouse_id', $request->warehouse_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Branch")->update(array('quantity' => $newQty));
    //                 endif;

    //                 if ($request->project_id):
    //                     StockSummary::where('product_id', $proName[$i])->where('project_id', $request->project_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Project")->update(array('quantity' => $newQty));
    //                 endif;
    //             else :
    //                 $stockSummary = new StockSummary();
    //                 $stockSummary->product_id = $proName[$i];
    //                 $stockSummary->purchasetype = $request->purchasetype[$i];
    //                 $stockSummary->quantity = $qty[$i];

    //                 if ($request->branch_id) {
    //                     $stockSummary->type =  "Branch";
    //                     $stockSummary->branch_id = $request->branch_id;
    //                     $stockSummary->warehouse_id = $request->warehouse_id ?? nullOrEmptyString();
    //                 }

    //                 if ($request->project_id) {
    //                     $stockSummary->type =  "Project";
    //                     $stockSummary->project_id = $request->project_id;
    //                 }

    //                 $stockSummary->save();
    //             endif;

    //             $productOpeningStockDetails = new ProductOpeningStockDetails();
    //             $productOpeningStockDetails->product_opening_stock_id = $productOpeningStocks_id;
    //             $productOpeningStockDetails->branch_id = $request->branch_id;
    //             $productOpeningStockDetails->warehouse_id = $request->warehouse_id;
    //             $productOpeningStockDetails->project_id = $request->project_id;
    //             $productOpeningStockDetails->category_id = $category_id[$i];
    //             $productOpeningStockDetails->product_id = $proName[$i];
    //             $productOpeningStockDetails->purchasetype =  $request->purchasetype[$i];
    //             $productOpeningStockDetails->date = $request->date;
    //             $productOpeningStockDetails->quantity = $qty[$i];
    //             $productOpeningStockDetails->unit_price = $subtotal[$i];
    //             $productOpeningStockDetails->total_price = $grand_total[$i];
    //             $productOpeningStockDetails->updated_by = Auth::user()->id;
    //             $productOpeningStockDetails->created_by = Auth::user()->id;
    //             $productOpeningStockDetails->deleted_by = Auth::user()->id;
    //             $productOpeningStockDetails->save();


    //             // ==================== STOCKS Opening Stock ====================
    //             $stock = new Stock();
    //             $stock->date          = $request->date;
    //             $stock->invoice_no     = $request->invoice_no;
    //             $stock->product_id    = $proName[$i];
    //             $stock->branch_id     = $request->branch_id;
    //             $stock->warehouse_id     = $request->warehouse_id;
    //             $stock->project_id    = $request->project_id ?? null;
    //             $stock->quantity      = $qty[$i];
    //             $stock->unit_price    = $subtotal[$i];
    //             $stock->total_price   = $grand_total[$i];
    //             $stock->status        = 'Opening';
    //             $stock->created_by    = Auth::id();
    //             $stock->save();
    //         }

    //         // CREATE log — header save 
    //         activity_log(
    //             'create',
    //             'product_opening_stocks',
    //             $productOpeningStock->toArray()
    //         );


    //         // $transactionPay['payment_invoice'] = $request->invoice_no;
    //         // $transactionPay['invoice'] = $request->invoice_no;
    //         // $transactionPay['table_id'] = $productOpeningStocks_id;
    //         // $transactionPay['account_id'] = getAccountByUniqueID(3)->id; // ->purchase
    //         // $transactionPay['type'] = 'opening_stock';
    //         // $transactionPay['branch_id'] = $request->branch_id ?? 0;
    //         // $transactionPay['debit'] =  array_sum( $grand_total);
    //         // $transactionPay['remark'] = $request->narration;
    //         // $transactionPay['created_by'] = Auth::id();
    //         // $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
    //         // AccountTransaction::create($transactionPay);

    //         // $transaction['payment_invoice'] = $request->invoice_no;
    //         // $transaction['invoice'] = $request->invoice_no;
    //         // $transaction['table_id'] = $productOpeningStocks_id;
    //         // $transaction['account_id'] = getAccountByUniqueID(13)->id; // account payable
    //         // $transaction['type'] = 'opening_stock';
    //         // $transaction['branch_id'] = $request->branch_id ?? 0;
    //         // $transaction['credit'] = (array_sum( $grand_total));
    //         // $transaction['remark'] = $request->narration;
    //         // $transaction['created_by'] = Auth::id();
    //         // $transaction['supplier_id'] = $request->supplier_id ?? 0;
    //         // AccountTransaction::create($transaction);


    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage(), $e->getLine());
    //         redirect()->route('inventorySetup.productOS.index')->with('error', 'Something Wrong Please try again');
    //     }
    //     return true;
    // }

    public function store($request)
{
    DB::beginTransaction();
    try {
        $isBranch  = !empty($request->branch_id);
        $isProject = !empty($request->project_id);

        // ---------- Guards ----------
        if ($isBranch === $isProject) {
            throw new \Exception('Select either a branch or a project, not both.');
        }
        if ($isBranch && !$request->warehouse_id) {
            throw new \Exception('Warehouse is required for branch opening stock.');
        }

        // ---------- Header ----------
        $productOpeningStock = new $this->productOpeningStock();
        $productOpeningStock->invoice_no   = $request->invoice_no;
        $productOpeningStock->created_by   = auth()->id();
        $productOpeningStock->date         = $request->date;
        $productOpeningStock->branch_id    = $request->branch_id;
        $productOpeningStock->warehouse_id = $request->warehouse_id;
        $productOpeningStock->project_id   = $request->project_id;
        $productOpeningStock->qty          = array_sum($request->qty);
        $productOpeningStock->total_price  = array_sum($request->total);
        $productOpeningStock->narration    = $request->narration;
        $productOpeningStock->save();
        $productOpeningStocks_id = $productOpeningStock->id;

        $category_id = $request->catName;
        $proName     = $request->proName;
        $subtotal    = $request->unitprice;
        $grand_total = $request->total;
        $qty         = $request->qty;

        for ($i = 0; $i < count($category_id); $i++) {
            $productId = $proName[$i];
            $ptype     = $request->purchasetype[$i];
            $addQty    = (float) $qty[$i];

            if ($addQty <= 0) {
                throw new \Exception('Quantity must be greater than zero.');
            }

            if ($isBranch) {
                $matchKey = [
                    'type'         => 'Branch',
                    'warehouse_id' => $request->warehouse_id,
                    'product_id'   => $productId,
                    'purchasetype' => $ptype,
                ];
                $extra = [
                    'branch_id'        => $request->branch_id,
                    'project_id'       => null,
                    'backup_branch_id' => $request->branch_id,
                ];
            } else {
                $matchKey = [
                    'type'         => 'Project',
                    'project_id'   => $request->project_id,
                    'branch_id'    => 0,
                    'product_id'   => $productId,
                    'purchasetype' => $ptype,
                ];
                $extra = [
                    'warehouse_id'     => null,
                    'backup_branch_id' => null,
                ];
            }

            $summary = StockSummary::where($matchKey)->lockForUpdate()->first();

            if ($summary) {
                // exist kore -> qty jog hobe
                $summary->quantity = (float) $summary->quantity + $addQty;
                $summary->save();
            } else {
                // exist kore na -> notun row
                $summary = new StockSummary();
                foreach (array_merge($matchKey, $extra) as $col => $val) {
                    $summary->{$col} = $val;
                }
                $summary->quantity = $addQty;
                $summary->save();
            }

            // ---------- Detail ----------
            $productOpeningStockDetails = new ProductOpeningStockDetails();
            $productOpeningStockDetails->product_opening_stock_id = $productOpeningStocks_id;
            $productOpeningStockDetails->branch_id     = $request->branch_id;
            $productOpeningStockDetails->warehouse_id  = $request->warehouse_id;
            $productOpeningStockDetails->project_id    = $request->project_id;
            $productOpeningStockDetails->category_id   = $category_id[$i];
            $productOpeningStockDetails->product_id    = $productId;
            $productOpeningStockDetails->purchasetype  = $ptype;
            $productOpeningStockDetails->date          = $request->date;
            $productOpeningStockDetails->quantity      = $addQty;
            $productOpeningStockDetails->unit_price    = $subtotal[$i];
            $productOpeningStockDetails->total_price   = $grand_total[$i];
            $productOpeningStockDetails->updated_by    = Auth::id();
            $productOpeningStockDetails->created_by    = Auth::id();
            $productOpeningStockDetails->save();

            // ---------- Stock ledger ----------
            $stock = new Stock();
            $stock->date         = $request->date;
            $stock->invoice_no   = $request->invoice_no;
            $stock->product_id   = $productId;
            $stock->branch_id    = $isBranch ? $request->branch_id : 0;   
            $stock->warehouse_id = $isBranch ? $request->warehouse_id : null;
            $stock->project_id   = $isProject ? $request->project_id : null;
            $stock->quantity     = $addQty;
            $stock->unit_price   = $subtotal[$i];
            $stock->total_price  = $grand_total[$i];
            $stock->status       = 'Opening';
            $stock->created_by   = Auth::id();
            $stock->save();
        }

        activity_log('create', 'product_opening_stocks', $productOpeningStock->toArray());

       

        DB::commit();
        return true;
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('ProductOpeningStock store failed: ' . $e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        session()->flash('error', 'Something went wrong: ' . $e->getMessage());
        return false;
    }
}

    // public function update($request, $id)
    // {
       
    //     DB::beginTransaction();
    //     try {
    //         $productOpeningStock = $this->productOpeningStock::findOrFail($id);
    //         // $productOpeningStock->invoice_no = $request->invoice_no;
    //         $productOpeningStock->created_by = auth()->id();
    //         $productOpeningStock->date = $request->date;
    //         $productOpeningStock->branch_id = $request->branch_id;
    //         $productOpeningStock->warehouse_id = $request->warehouse_id ?? null;
    //         $productOpeningStock->project_id = $request->project_id;
    //         $productOpeningStock->qty = array_sum($request->qty);
    //         $productOpeningStock->total_price = array_sum($request->total);
    //         $productOpeningStock->narration = $request->narration;
    //         $productOpeningStock->save();
    //         $productOpeningStocks_id = $productOpeningStock->id;

    //         foreach ($productOpeningStock->details as $item) {

    //             $mywhereCondition = array(
    //                 'branch_id' => $item->branch_id ?? 0,
    //                 'warehouse_id' => $item->warehouse_id ?? null,
    //                 'project_id' => $item->project_id,
    //                 'product_id' => $item->product_id,
    //                 'type' => $item->branch_id == 0 ? 'Project' : 'Branch',
    //             );

    //             $oldstockupdate = StockSummary::where($mywhereCondition)->first();
    //             DB::table('stock_summaries')
    //                 ->where($mywhereCondition)
    //                 ->update(
    //                     ['quantity' => $oldstockupdate->quantity - $item->quantity],
    //                 );
    //         }

    //         ProductOpeningStockDetails::where('product_opening_stock_id', $productOpeningStocks_id)->delete();
    //         AccountTransaction::where('table_id', $productOpeningStocks_id)->where('type', "opening_stock")->delete();

    //         $category_id = $request->catName;
    //         $proName = $request->proName;
    //         $purchaseType = $request->purchasetype;
    //         $subtotal = $request->unitprice;
    //         $grand_total = $request->total;
    //         $qty = $request->qty;
    //         for ($i = 0; $i < count($category_id); $i++) {
    //             $existingCheck = StockSummary::where('product_id', $proName[$i])->where('purchasetype' , $purchaseType[$i]);

    //             if ($request->branch_id) {
    //                 $existingCheck =  $existingCheck->where('branch_id', $request->branch_id)->where('warehouse_id', $request->warehouse_id)->where('type', "Branch");
    //             }

    //             if ($request->project_id) {
    //                 $existingCheck =  $existingCheck->where('project_id', $request->project_id)->where('type', "Project");
    //             }

    //             $existingCheck = $existingCheck->where('purchasetype', $request->purchasetype[$i])->first();

    //             if (!empty($existingCheck)) :
    //                 $newQty = $existingCheck->quantity + $qty[$i];
    //                 if ($request->branch_id):
    //                     StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Branch")->update(array('quantity' => $newQty));
    //                 endif;

    //                 if ($request->project_id):
    //                     StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->project_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Project")->update(array('quantity' => $newQty));
    //                 endif;
    //             else :
    //                 $stockSummary = new StockSummary();
    //                 $stockSummary->product_id = $proName[$i];
    //                 $stockSummary->purchasetype = $request->purchasetype[$i];
    //                 $stockSummary->quantity = $qty[$i];

    //                 if ($request->branch_id) {
    //                     $stockSummary->type =  "Branch";
    //                     $stockSummary->branch_id = $request->branch_id;
    //                 }

    //                 if ($request->project_id) {
    //                     $stockSummary->type =  "Project";
    //                     $stockSummary->branch_id = $request->project_id;
    //                 }

    //                 $stockSummary->save();
    //             endif;

    //             $productOpeningStockDetails = new ProductOpeningStockDetails();
    //             $productOpeningStockDetails->product_opening_stock_id = $productOpeningStocks_id;
    //             $productOpeningStockDetails->branch_id = $request->branch_id;
    //             $productOpeningStockDetails->project_id = $request->project_id;
    //             $productOpeningStockDetails->category_id = $category_id[$i];
    //             $productOpeningStockDetails->product_id = $proName[$i];
    //             $productOpeningStockDetails->purchasetype =  $request->purchasetype[$i];
    //             $productOpeningStockDetails->date = $request->date;
    //             $productOpeningStockDetails->quantity = $qty[$i];
    //             $productOpeningStockDetails->unit_price = $subtotal[$i];
    //             $productOpeningStockDetails->total_price = $grand_total[$i];
    //             $productOpeningStockDetails->updated_by = Auth::user()->id;
    //             $productOpeningStockDetails->created_by = Auth::user()->id;
    //             $productOpeningStockDetails->deleted_by = Auth::user()->id;
    //             $productOpeningStockDetails->save();
    //         }

    //         // $transactionPay['invoice'] = $productOpeningStock->invoice_no;
    //         // $transactionPay['table_id'] = $productOpeningStocks_id;
    //         // $transactionPay['account_id'] = getAccountByUniqueID(3)->id; // ->purchase
    //         // $transactionPay['type'] = 'opening_stock';
    //         // $transactionPay['branch_id'] = $request->branch_id ?? 0;
    //         // $transactionPay['debit'] =  array_sum( $grand_total);
    //         // $transactionPay['remark'] = $request->narration;
    //         // $transactionPay['created_by'] = Auth::id();
    //         // $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
    //         // AccountTransaction::create($transactionPay);

    //         // $transaction['invoice'] = $productOpeningStock->invoice_no;
    //         // $transaction['table_id'] = $productOpeningStocks_id;
    //         // $transaction['account_id'] = getAccountByUniqueID(13)->id; // account payable
    //         // $transaction['type'] = 'opening_stock';
    //         // $transaction['branch_id'] = $request->branch_id ?? 0;
    //         // $transaction['credit'] = (array_sum( $grand_total));
    //         // $transaction['remark'] = $request->narration;
    //         // $transaction['created_by'] = Auth::id();
    //         // $transaction['supplier_id'] = $request->supplier_id ?? 0;
    //         // AccountTransaction::create($transaction);
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage(), $e->getLine());
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }
    //     return true;
    // }

    //   public function update($request, $id)
    // {
 
  

    //     DB::beginTransaction();
    //     try {
           
    //         $projectId   = (int) $request->project_id;
    //         $branchId    = (int) $request->branch_id;
    //         $warehouseId = (int) $request->warehouse_id;
 
    //         if (($projectId > 0) === ($branchId > 0)) {
    //             throw new \Exception('Please select either a Project or a Branch.');
    //         }
 
    //         if ($branchId > 0) {
    //             // warehouse must belong to the selected (real) branch
    //             $validWarehouse = Warehouse::where('id', $warehouseId)->exists();
    //             if (!$validWarehouse) {
    //                 throw new \Exception('Please select a valid warehouse for the selected branch.');
    //             }
    //         } else {
              
    //             $branchId    = 0;
    //             $warehouseId = null;
    //         }
 
           
    //         if (empty($request->proName) || empty($request->catName)) {
    //             throw new \Exception('Please add at least one item.');
    //         }
 
        
    //         $findStock = function ($branchId, $warehouseId, $projectId, $productId, $purchaseType) {
    //             $query = StockSummary::where('product_id', $productId)->where('type'. 'Branch')->where('purchasetype', $purchaseType);
 
    //             if ($branchId > 0) {
    //                 return $query->where('type', 'Branch')
    //                     ->where('branch_id', $branchId)
    //                     ->where('warehouse_id', $warehouseId ?: null)
    //                     ->first();
    //             }
 
    //             $row = (clone $query)->where('type', 'Project')->where('project_id', $projectId)->first();
 
    //             return $row ?: (clone $query)->where('type', 'Project')->where('branch_id', $projectId)->first();
    //         };
 
    //         $productOpeningStock = $this->productOpeningStock::findOrFail($id);
    //         // $productOpeningStock->invoice_no = $request->invoice_no;
    //         $productOpeningStock->created_by = auth()->id(); 
    //         $productOpeningStock->date = $request->date;
    //         $productOpeningStock->branch_id = $branchId;         
    //         $productOpeningStock->warehouse_id = $warehouseId;   
    //         $productOpeningStock->project_id = $projectId;      
    //         $productOpeningStock->qty = array_sum($request->qty);
    //         $productOpeningStock->total_price = array_sum($request->total);
    //         $productOpeningStock->narration = $request->narration;
    //         $productOpeningStock->save();
    //         $productOpeningStocks_id = $productOpeningStock->id;
 
    //         foreach ($productOpeningStock->details as $item) {
 
    //             $oldStock = $findStock(
    //                 (int) ($item->branch_id ?? 0),
    //                 $item->warehouse_id ?? null,
    //                 (int) ($item->project_id ?? 0),
    //                 $item->product_id,
    //                 $item->purchasetype
    //             );
 
    //             if (!$oldStock) {
    //                 // change: the old code crashed here with "property of null"
    //                 throw new \Exception('Old stock summary row not found for product_id ' . $item->product_id
    //                     . ' (purchasetype ' . $item->purchasetype . ', branch ' . ($item->branch_id ?? 0)
    //                     . ', warehouse ' . ($item->warehouse_id ?? 'null') . ', project ' . ($item->project_id ?? 0) . ').');
    //             }
 
    //             StockSummary::where('id', $oldStock->id)
    //                 ->update(['quantity' => $oldStock->quantity - $item->quantity]);
    //         }
 
    //         ProductOpeningStockDetails::where('product_opening_stock_id', $productOpeningStocks_id)->delete();
    //         AccountTransaction::where('table_id', $productOpeningStocks_id)->where('type', "opening_stock")->delete();
 
    //         $category_id = $request->catName;
    //         $proName = $request->proName;
    //         $purchaseType = $request->purchasetype;
    //         $subtotal = $request->unitprice;
    //         $grand_total = $request->total;
    //         $qty = $request->qty;
    //         for ($i = 0; $i < count($category_id); $i++) {
 
    //             // change: one lookup, warehouse-aware; project rows use the project_id column
    //             $existingCheck = $findStock($branchId, $warehouseId, $projectId, $proName[$i], $purchaseType[$i]);
 
    //             if (!empty($existingCheck)) :
    //                 // change: update exactly this row (the old update had no warehouse_id in its where, so it
    //                 // overwrote all warehouses of the branch; for Project it filtered on branch_id and hit nothing)
    //                 StockSummary::where('id', $existingCheck->id)
    //                     ->update(['quantity' => $existingCheck->quantity + $qty[$i]]);
    //             else :
    //                 $stockSummary = new StockSummary();
    //                 $stockSummary->product_id = $proName[$i];
    //                 $stockSummary->purchasetype = $purchaseType[$i];
    //                 $stockSummary->quantity = $qty[$i];
 
    //                 if ($branchId > 0) {
    //                     $stockSummary->type = "Branch";
    //                     $stockSummary->branch_id = $branchId;
    //                     $stockSummary->warehouse_id = $warehouseId;   // add new: was never saved
    //                 } else {
    //                     $stockSummary->type = "Project";
    //                     $stockSummary->project_id = $projectId;       // change: was branch_id = project id
    //                 }
 
    //                 $stockSummary->save();
    //             endif;
 
    //             $productOpeningStockDetails = new ProductOpeningStockDetails();
    //             $productOpeningStockDetails->product_opening_stock_id = $productOpeningStocks_id;
    //             $productOpeningStockDetails->branch_id = $branchId;
    //             $productOpeningStockDetails->warehouse_id = $warehouseId; // add new: needed to reverse the stock next time
    //             $productOpeningStockDetails->project_id = $projectId;
    //             $productOpeningStockDetails->category_id = $category_id[$i];
    //             $productOpeningStockDetails->product_id = $proName[$i];
    //             $productOpeningStockDetails->purchasetype =  $purchaseType[$i];
    //             $productOpeningStockDetails->date = $request->date;
    //             $productOpeningStockDetails->quantity = $qty[$i];
    //             $productOpeningStockDetails->unit_price = $subtotal[$i];
    //             $productOpeningStockDetails->total_price = $grand_total[$i];
    //             $productOpeningStockDetails->updated_by = Auth::user()->id;
    //             $productOpeningStockDetails->created_by = Auth::user()->id;
    //             $productOpeningStockDetails->deleted_by = Auth::user()->id; // NOTE: unusual - deleted_by is set on every save
    //             $productOpeningStockDetails->save();
    //         }
 
    //         // $transactionPay['invoice'] = $productOpeningStock->invoice_no;
    //         // $transactionPay['table_id'] = $productOpeningStocks_id;
    //         // $transactionPay['account_id'] = getAccountByUniqueID(3)->id; // ->purchase
    //         // $transactionPay['type'] = 'opening_stock';
    //         // $transactionPay['branch_id'] = $request->branch_id ?? 0;
    //         // $transactionPay['debit'] =  array_sum( $grand_total);
    //         // $transactionPay['remark'] = $request->narration;
    //         // $transactionPay['created_by'] = Auth::id();
    //         // $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
    //         // AccountTransaction::create($transactionPay);
 
    //         // $transaction['invoice'] = $productOpeningStock->invoice_no;
    //         // $transaction['table_id'] = $productOpeningStocks_id;
    //         // $transaction['account_id'] = getAccountByUniqueID(13)->id; // account payable
    //         // $transaction['type'] = 'opening_stock';
    //         // $transaction['branch_id'] = $request->branch_id ?? 0;
    //         // $transaction['credit'] = (array_sum( $grand_total));
    //         // $transaction['remark'] = $request->narration;
    //         // $transaction['created_by'] = Auth::id();
    //         // $transaction['supplier_id'] = $request->supplier_id ?? 0;
    //         // AccountTransaction::create($transaction);
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage(), $e->getLine());
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }
    //     return true;
    // }

public function update($request, $id)
{
    DB::beginTransaction();
    try {
        $projectId   = (int) $request->project_id;
        $branchId    = (int) $request->branch_id;
        $warehouseId = (int) $request->warehouse_id;

        // ---------- Guards ----------
        if (($projectId > 0) === ($branchId > 0)) {
            throw new \Exception('Please select either a Project or a Branch.');
        }

        if ($branchId > 0) {
            // warehouse must belong to the selected branch
            $validWarehouse = Warehouse::where('id', $warehouseId)->where('status', 'Active')->exists();
            if (!$validWarehouse) {
                throw new \Exception('Please select a valid warehouse for the selected branch.');
            }
        } else {
            $branchId    = 0;
            $warehouseId = null;
        }

        if (empty($request->proName) || empty($request->catName)) {
            throw new \Exception('Please add at least one item.');
        }
        if (count($request->catName) !== count($request->proName)
            || count($request->proName) !== count($request->qty)
            || count($request->proName) !== count($request->purchasetype)) {
            throw new \Exception('Invalid product rows.');
        }

        // ---------- StockSummary key (store() er sathe hubohu ek) ----------
        // Branch  : type + warehouse_id + product_id + purchasetype   (branch_id-er kono effect nei)
        // Project : type + project_id   + product_id + purchasetype
        $summaryKey = function ($warehouseId, $projectId, $productId, $ptype) {
            if ((int) $projectId > 0) {
                return [
                    ['type' => 'Project', 'project_id' => $projectId, 'product_id' => $productId, 'purchasetype' => $ptype],
                    ['branch_id' => 0, 'warehouse_id' => null],   // shudhu notun row-er default
                ];
            }
            return [
                ['type' => 'Branch', 'warehouse_id' => $warehouseId, 'product_id' => $productId, 'purchasetype' => $ptype],
                [],
            ];
        };

        $productOpeningStock = $this->productOpeningStock::findOrFail($id);
        $invoiceNo  = $productOpeningStock->invoice_no;
        $oldDetails = $productOpeningStock->details;   // header update-er AGE load

        $oldHdrWarehouse = $productOpeningStock->warehouse_id;
        $oldHdrProject   = (int) ($productOpeningStock->project_id ?? 0);

        $touched = [];   // shesh e negative check-er jonno

        // ---------- STEP 1: purono stock reverse ----------
        foreach ($oldDetails as $item) {
            $oWarehouse = $item->warehouse_id ?: $oldHdrWarehouse;   // purono detail-e na thakle header theke
            $oProject   = (int) ($item->project_id ?: $oldHdrProject);

            [$oldKey] = $summaryKey($oWarehouse, $oProject, $item->product_id, $item->purchasetype);

            $oldStock = StockSummary::where($oldKey)->lockForUpdate()->first();
            if (!$oldStock) {
                throw new \Exception('Old stock summary row not found for product_id ' . $item->product_id
                    . ' (purchasetype ' . $item->purchasetype . ', warehouse ' . ($oWarehouse ?? 'null')
                    . ', project ' . $oProject . ').');
            }

            $oldStock->quantity = (float) $oldStock->quantity - (float) $item->quantity;
            $oldStock->save();
            $touched[$oldStock->id] = true;
        }

        ProductOpeningStockDetails::where('product_opening_stock_id', $id)->delete();
        Stock::where('invoice_no', $invoiceNo)->where('status', 'Opening')->delete();   // purono ledger
        AccountTransaction::where('table_id', $id)->where('type', 'opening_stock')->delete();

        // ---------- STEP 2: header update ----------
        $productOpeningStock->date         = $request->date;
        $productOpeningStock->branch_id    = $branchId;
        $productOpeningStock->warehouse_id = $warehouseId;
        $productOpeningStock->project_id   = $projectId;
        $productOpeningStock->qty          = array_sum($request->qty);
        $productOpeningStock->total_price  = array_sum($request->total);
        $productOpeningStock->narration    = $request->narration;
        $productOpeningStock->save();   // created_by touch kora hoyni; updated_by column thakle ekhane set koro

        // ---------- STEP 3: notun stock add ----------
        $category_id  = $request->catName;
        $proName      = $request->proName;
        $purchaseType = $request->purchasetype;
        $subtotal     = $request->unitprice;
        $grand_total  = $request->total;
        $qty          = $request->qty;

        for ($i = 0; $i < count($category_id); $i++) {
            $addQty = (float) $qty[$i];
            if ($addQty <= 0) {
                throw new \Exception('Quantity must be greater than zero.');
            }

            [$matchKey, $extra] = $summaryKey($warehouseId, $projectId, $proName[$i], $purchaseType[$i]);

            $summary = StockSummary::where($matchKey)->lockForUpdate()->first();
            if ($summary) {
                // exist kore -> qty jog
                $summary->quantity = (float) $summary->quantity + $addQty;
                $summary->save();
            } else {
                // exist kore na -> notun row
                $summary = new StockSummary();
                foreach (array_merge($matchKey, $extra) as $col => $val) {
                    $summary->{$col} = $val;
                }
                $summary->quantity = $addQty;
                $summary->save();
            }
            $touched[$summary->id] = true;

            // ---------- Detail ----------
            $d = new ProductOpeningStockDetails();
            $d->product_opening_stock_id = $id;
            $d->branch_id     = $branchId;
            $d->warehouse_id  = $warehouseId;
            $d->project_id    = $projectId;
            $d->category_id   = $category_id[$i];
            $d->product_id    = $proName[$i];
            $d->purchasetype  = $purchaseType[$i];
            $d->date          = $request->date;
            $d->quantity      = $addQty;
            $d->unit_price    = $subtotal[$i];
            $d->total_price   = $grand_total[$i];
            $d->updated_by    = Auth::id();
            $d->created_by    = Auth::id();
            $d->save();

            // ---------- Stock ledger (store() er moto) ----------
            $stock = new Stock();
            $stock->date         = $request->date;
            $stock->invoice_no   = $invoiceNo;
            $stock->product_id   = $proName[$i];
            $stock->branch_id    = $branchId;                          // project hole 0
            $stock->warehouse_id = $warehouseId;
            $stock->project_id   = $projectId > 0 ? $projectId : null;
            $stock->quantity     = $addQty;
            $stock->unit_price   = $subtotal[$i];
            $stock->total_price  = $grand_total[$i];
            $stock->status       = 'Opening';
            $stock->created_by   = Auth::id();
            $stock->save();
        }

        // ---------- STEP 4: kono row negative hole pura update rollback ----------
        $negative = StockSummary::whereIn('id', array_keys($touched))->where('quantity', '<', 0)->first();
        if ($negative) {
            throw new \Exception('Cannot update: stock of product ID ' . $negative->product_id
                . ' (' . $negative->purchasetype . ') has already been used, quantity would become negative ('
                . $negative->quantity . ').');
        }

        // ... tomar commented AccountTransaction block ekhane thakbe ...

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('ProductOpeningStock update failed: ' . $e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        session()->flash('error', 'Something went wrong: ' . $e->getMessage());
        return false;
    }

    return true;
}



    public function storeapproval($request, $id)
    {
        //  dd($request->all());
        DB::beginTransaction();
        try {
            $purchase = $this->productOpeningStock::findOrFail($id);
            // $purchase->invoice_no = $request->invoice_no;
            $purchase->date = $request->date;
            $purchase->branch_id = $request->branch_id;
            $purchase->quantity = array_sum($request->qty);
            $purchase->approval_qty = array_sum($request->qty);
            $purchase->subtotal = array_sum($request->unitprice);
            $purchase->grand_total = array_sum($request->total);
            $purchase->status = 'Active';
            $purchase->adjustment_type = $request->adjustment_type;
            $purchase->approve_by = Auth::user()->id;
            $purchase->approval_date = date('Y-m-d');
            $purchase->note = $request->narration;
            $purchase->save();
            $purchases_id = $purchase->id;

            StockAjdustmentDetailst::where('purchases_id', $purchase->id)->delete();

            $stockDetailsId = $request->stockDetailsId;
            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;

            for ($i = 0; $i < count($stockDetailsId); $i++) {
                $purchaseDetail['product_id'] = $proName[$i];
                $purchaseDetail['quantity'] = $qty[$i];
                $purchaseDetail['category_id'] = $category_id[$i];
                $purchaseDetail['branch_id'] = $request->branch_id;
                $purchaseDetail['unit_price'] = $subtotal[$i];
                $purchaseDetail['total_price'] = $grand_total[$i];
                $purchaseDetail['purchases_id'] = $purchases_id;
                $purchaseDetail['date'] = $request->date;
                $purchaseDetail['status'] = 'Active';
                $purchaseDetail['approval_date'] = date('Y-m-d');
                StockAjdustmentDetailst::where('purchases_id', $stockDetailsId[$i])->update($purchaseDetail);

                $stock = new Stock();
                $stock->general_id = $purchases_id;
                $stock->branch_id = $request->branch_id;
                $stock->product_id = $proName[$i];
                $stock->unit_price = $subtotal[$i];
                $stock->total_price = $grand_total[$i];
                $stock->quantity = $qty[$i];
                $stock->status = $request->adjustment_type;
                $stock->save();



                if ($request->adjustment_type == 'Lost'  || $request->adjustment_type == 'Damange') {
                    $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->first();
                    if (!empty($existingCheck)) :
                        $newQty = $existingCheck->quantity - $qty[$i];
                        StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQty));
                    endif;
                }
                if ($request->adjustment_type == 'Gain') {
                    $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->first();
                    if (!empty($existingCheck)) :
                        $newQty = $existingCheck->quantity + $qty[$i];
                        StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQty));
                    endif;
                }


                // $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->first();
                // if (!empty($existingCheck)) :
                //     $newQty = $existingCheck->quantity + $qty[$i];
                //     StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQty));
                // else :
                //     $stockSummary = new StockSummary();
                //     $stockSummary->branch_id = $request->branch_id;
                //     $stockSummary->product_id = $proName[$i];
                //     $stockSummary->quantity = $qty[$i];
                //     $stockSummary->type = "Branch";
                //     $stockSummary->save();
                // endif;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return $purchase;
    }

    public function statusUpdate($id, $status)
    {

        $purchase = $this->productOpeningStock::find($id);
        $purchase->status = $status;
        $purchase->save();
        return $purchase;
    }

    // public function destroy($id)
    // {

    //     DB::beginTransaction();
    //     try {
    //         $OpeningStock = $this->productOpeningStock::find($id);

    //         if ($OpeningStock->status == "Accepted") {
    //             session()->flash('error', "Sorry, you couldn't delete!!");
    //             DB::commit();
    //             return false;
    //         } else {

    //             $oldData = $OpeningStock->toArray();

    //             $OpeningStock->forceDelete();
    //             AccountTransaction::where('table_id', $id)->where('type', "opening_stock")->delete();
    //             $purchasedetails =  ProductOpeningStockDetails::where('product_opening_stock_id', $id)->get();

    //             // Activity Log (Delete)
    //             activity_log(
    //                 'delete',
    //                 'product_opening_stocks',
    //                 [],
    //                 $oldData,
    //                 "Opening Stock deleted successfully (Invoice: {$OpeningStock->invoice_no})"
    //             );

    //             foreach ($purchasedetails as $item) {
    //                 $mywhereCondition = array(
    //                     'branch_id' => $item->branch_id == 0 ?  $item->project_id : $item->branch_id,
    //                     'product_id' => $item->product_id,
    //                     'type' => $item->branch_id == 0 ? 'Project' : 'Branch',
    //                 );

    //                 $oldstockupdate = StockSummary::where($mywhereCondition)->first();
    //                 DB::table('stock_summaries')
    //                     ->where($mywhereCondition)
    //                     ->update(
    //                         ['quantity' => $oldstockupdate->quantity - $item->quantity],
    //                     );

    //                 $item->forceDelete();
    //             }
    //             DB::commit();
    //             return true;
    //         }
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again' . $e->getMessage());
    //     }
    //     return true;
    // }


    public function destroy($id)
{
    DB::beginTransaction();
    try {
        $OpeningStock = $this->productOpeningStock::find($id);

        if (!$OpeningStock) {
            DB::rollBack();
            session()->flash('error', 'Opening Stock not found!');
            return false;
        }

        $oldData  = $OpeningStock->toArray();
        $invoiceNo = $OpeningStock->invoice_no;
        $details  = ProductOpeningStockDetails::where('product_opening_stock_id', $id)->get();

       
        $hdrWarehouse = $OpeningStock->warehouse_id;
        $hdrProject   = (int) ($OpeningStock->project_id ?? 0);

        foreach ($details as $item) {
            $warehouseId = $item->warehouse_id ?: $hdrWarehouse;
            $projectId   = (int) ($item->project_id ?: $hdrProject);

            if ($projectId > 0) {
                $where = [
                    'type'         => 'Project',
                    'project_id'   => $projectId,
                    'product_id'   => $item->product_id,
                    'purchasetype' => $item->purchasetype,
                ];
            } else {
                $where = [
                    'type'         => 'Branch',
                    'warehouse_id' => $warehouseId,
                    'product_id'   => $item->product_id,
                    'purchasetype' => $item->purchasetype,
                ];
            }

            $stock = StockSummary::where($where)->lockForUpdate()->first();

            if (!$stock) {
                throw new \Exception('Stock summary row not found for product_id ' . $item->product_id
                    . ' (purchasetype ' . $item->purchasetype . ', '
                    . ($projectId > 0 ? 'project ' . $projectId : 'warehouse ' . ($warehouseId ?? 'null')) . ').');
            }

            $newQty = (float) $stock->quantity - (float) $item->quantity;

          
            if (round($newQty, 2) < 0) {
                throw new \Exception('Cannot delete: stock of product ID ' . $item->product_id
                    . ' (' . $item->purchasetype . ') has already been used. Available: '
                    . (float) $stock->quantity . ', needed to reverse: ' . (float) $item->quantity);
            }

            $stock->quantity = $newQty;   // 0 hole row thakbe (update()-er moto)
            $stock->save();
        }

        Stock::where('invoice_no', $invoiceNo)->where('status', 'Opening')->delete();
        ProductOpeningStockDetails::where('product_opening_stock_id', $id)->forceDelete();
        AccountTransaction::where('table_id', $id)->where('type', 'opening_stock')->delete();
        $OpeningStock->forceDelete();

        activity_log(
            'delete',
            'product_opening_stocks',
            [],
            $oldData,
            "Opening Stock deleted successfully (Invoice: {$oldData['invoice_no']})"
        );

        DB::commit();
        return true;
    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('ProductOpeningStock destroy failed: ' . $e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        session()->flash('error', 'Something went wrong: ' . $e->getMessage());
        return false;
    }
}

   
}
