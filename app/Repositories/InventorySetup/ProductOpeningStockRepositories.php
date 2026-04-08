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
                // dd($purchase->branch);
                $nestedData['id'] = $key + 1;
                $nestedData['invoice_no'] = $purchase->invoice_no;
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

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $productOpeningStock = new $this->productOpeningStock();
            $productOpeningStock->invoice_no = $request->invoice_no;
            $productOpeningStock->created_by = auth()->id();
            $productOpeningStock->date = $request->date;
            $productOpeningStock->branch_id = $request->branch_id;
            $productOpeningStock->project_id = $request->project_id;
            $productOpeningStock->qty = array_sum($request->qty);
            $productOpeningStock->total_price = array_sum($request->total);
            $productOpeningStock->narration = $request->narration;
            $productOpeningStock->save();
            $productOpeningStocks_id = $productOpeningStock->id;

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;
            for ($i = 0; $i < count($category_id); $i++) {
                $existingCheck = StockSummary::where('product_id', $proName[$i]);

                if($request->branch_id){
                    $existingCheck =  $existingCheck->where('branch_id', $request->branch_id)->where('type', "Branch");
                }

                if($request->project_id){
                    $existingCheck =  $existingCheck->where('branch_id', $request->project_id)->where('type', "Project");
                }
    
                $existingCheck = $existingCheck->where('purchasetype', $request->purchasetype[$i])->first();

                if (!empty($existingCheck)) :
                    $newQty = $existingCheck->quantity + $qty[$i];
                    if($request->branch_id):
                        StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Branch")->update(array('quantity' => $newQty));
                    endif;

                    if($request->project_id):
                        StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->project_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Project")->update(array('quantity' => $newQty));
                    endif;
                else :
                    $stockSummary = new StockSummary();
                    $stockSummary->product_id = $proName[$i];
                    $stockSummary->purchasetype = $request->purchasetype[$i];
                    $stockSummary->quantity = $qty[$i];

                    if($request->branch_id){
                        $stockSummary->type =  "Branch";
                        $stockSummary->branch_id = $request->branch_id;
                    }
                    
                    if($request->project_id){
                        $stockSummary->type =  "Project";
                        $stockSummary->branch_id = $request->project_id;
                    }

                    $stockSummary->save();
                endif;

                $productOpeningStockDetails = new ProductOpeningStockDetails();
                $productOpeningStockDetails->product_opening_stock_id = $productOpeningStocks_id;
                $productOpeningStockDetails->branch_id = $request->branch_id;
                $productOpeningStockDetails->project_id = $request->project_id;
                $productOpeningStockDetails->category_id = $category_id[$i];
                $productOpeningStockDetails->product_id = $proName[$i];
                $productOpeningStockDetails->purchasetype =  $request->purchasetype[$i];
                $productOpeningStockDetails->date = $request->date;
                $productOpeningStockDetails->quantity = $qty[$i];
                $productOpeningStockDetails->unit_price = $subtotal[$i];
                $productOpeningStockDetails->total_price = $grand_total[$i];
                $productOpeningStockDetails->updated_by = Auth::user()->id;
                $productOpeningStockDetails->created_by = Auth::user()->id;
                $productOpeningStockDetails->deleted_by = Auth::user()->id;
                $productOpeningStockDetails->save();
            }

            
            // $transactionPay['payment_invoice'] = $request->invoice_no;
            // $transactionPay['invoice'] = $request->invoice_no;
            // $transactionPay['table_id'] = $productOpeningStocks_id;
            // $transactionPay['account_id'] = getAccountByUniqueID(3)->id; // ->purchase
            // $transactionPay['type'] = 'opening_stock';
            // $transactionPay['branch_id'] = $request->branch_id ?? 0;
            // $transactionPay['debit'] =  array_sum( $grand_total);
            // $transactionPay['remark'] = $request->narration;
            // $transactionPay['created_by'] = Auth::id();
            // $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
            // AccountTransaction::create($transactionPay);

            // $transaction['payment_invoice'] = $request->invoice_no;
            // $transaction['invoice'] = $request->invoice_no;
            // $transaction['table_id'] = $productOpeningStocks_id;
            // $transaction['account_id'] = getAccountByUniqueID(13)->id; // account payable
            // $transaction['type'] = 'opening_stock';
            // $transaction['branch_id'] = $request->branch_id ?? 0;
            // $transaction['credit'] = (array_sum( $grand_total));
            // $transaction['remark'] = $request->narration;
            // $transaction['created_by'] = Auth::id();
            // $transaction['supplier_id'] = $request->supplier_id ?? 0;
            // AccountTransaction::create($transaction);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage(),$e->getLine());
            redirect()->route('inventorySetup.productOS.index')->with('error', 'Something Wrong Please try again');
        }
        return true;
    }

    public function update($request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $productOpeningStock = $this->productOpeningStock::findOrFail($id);
            // $productOpeningStock->invoice_no = $request->invoice_no;
            $productOpeningStock->created_by = auth()->id();
            $productOpeningStock->date = $request->date;
            $productOpeningStock->branch_id = $request->branch_id;
            $productOpeningStock->project_id = $request->project_id;
            $productOpeningStock->qty = array_sum($request->qty);
            $productOpeningStock->total_price = array_sum($request->total);
            $productOpeningStock->narration = $request->narration;
            $productOpeningStock->save();
            $productOpeningStocks_id = $productOpeningStock->id;

            foreach ($productOpeningStock->details as $item) {

                $mywhereCondition = array(
                    'branch_id' => $item->branch_id == 0 ?  $item->project_id:$item->branch_id,
                    'product_id' => $item->product_id,
                    'type' => $item->branch_id == 0 ? 'Project':'Branch',
                );

                $oldstockupdate = StockSummary::where($mywhereCondition)->first();
                DB::table('stock_summaries')
                    ->where($mywhereCondition)
                    ->update(
                        ['quantity' => $oldstockupdate->quantity - $item->quantity],
                    );
            }

            ProductOpeningStockDetails::where('product_opening_stock_id', $productOpeningStocks_id)->delete();
            AccountTransaction::where('table_id', $productOpeningStocks_id)->where('type', "opening_stock")->delete();

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;
            for ($i = 0; $i < count($category_id); $i++) {
                $existingCheck = StockSummary::where('product_id', $proName[$i]);

                if($request->branch_id){
                    $existingCheck =  $existingCheck->where('branch_id', $request->branch_id)->where('type', "Branch");
                }

                if($request->project_id){
                    $existingCheck =  $existingCheck->where('branch_id', $request->project_id)->where('type', "Project");
                }
    
                $existingCheck = $existingCheck->where('purchasetype', $request->purchasetype[$i])->first();

                if (!empty($existingCheck)) :
                    $newQty = $existingCheck->quantity + $qty[$i];
                    if($request->branch_id):
                        StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Branch")->update(array('quantity' => $newQty));
                    endif;

                    if($request->project_id):
                        StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->project_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Project")->update(array('quantity' => $newQty));
                    endif;
                else :
                    $stockSummary = new StockSummary();
                    $stockSummary->product_id = $proName[$i];
                    $stockSummary->purchasetype = $request->purchasetype[$i];
                    $stockSummary->quantity = $qty[$i];

                    if($request->branch_id){
                        $stockSummary->type =  "Branch";
                        $stockSummary->branch_id = $request->branch_id;
                    }
                    
                    if($request->project_id){
                        $stockSummary->type =  "Project";
                        $stockSummary->branch_id = $request->project_id;
                    }

                    $stockSummary->save();
                endif;

                $productOpeningStockDetails = new ProductOpeningStockDetails();
                $productOpeningStockDetails->product_opening_stock_id = $productOpeningStocks_id;
                $productOpeningStockDetails->branch_id = $request->branch_id;
                $productOpeningStockDetails->project_id = $request->project_id;
                $productOpeningStockDetails->category_id = $category_id[$i];
                $productOpeningStockDetails->product_id = $proName[$i];
                $productOpeningStockDetails->purchasetype =  $request->purchasetype[$i];
                $productOpeningStockDetails->date = $request->date;
                $productOpeningStockDetails->quantity = $qty[$i];
                $productOpeningStockDetails->unit_price = $subtotal[$i];
                $productOpeningStockDetails->total_price = $grand_total[$i];
                $productOpeningStockDetails->updated_by = Auth::user()->id;
                $productOpeningStockDetails->created_by = Auth::user()->id;
                $productOpeningStockDetails->deleted_by = Auth::user()->id;
                $productOpeningStockDetails->save();
            }

            // $transactionPay['invoice'] = $productOpeningStock->invoice_no;
            // $transactionPay['table_id'] = $productOpeningStocks_id;
            // $transactionPay['account_id'] = getAccountByUniqueID(3)->id; // ->purchase
            // $transactionPay['type'] = 'opening_stock';
            // $transactionPay['branch_id'] = $request->branch_id ?? 0;
            // $transactionPay['debit'] =  array_sum( $grand_total);
            // $transactionPay['remark'] = $request->narration;
            // $transactionPay['created_by'] = Auth::id();
            // $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
            // AccountTransaction::create($transactionPay);

            // $transaction['invoice'] = $productOpeningStock->invoice_no;
            // $transaction['table_id'] = $productOpeningStocks_id;
            // $transaction['account_id'] = getAccountByUniqueID(13)->id; // account payable
            // $transaction['type'] = 'opening_stock';
            // $transaction['branch_id'] = $request->branch_id ?? 0;
            // $transaction['credit'] = (array_sum( $grand_total));
            // $transaction['remark'] = $request->narration;
            // $transaction['created_by'] = Auth::id();
            // $transaction['supplier_id'] = $request->supplier_id ?? 0;
            // AccountTransaction::create($transaction);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage(),$e->getLine());
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchase = $this->productOpeningStock::find($id);
            if ($purchase->status == "Accepted") {
                session()->flash('error', "Sorry, you couldn't delete!!");
                DB::commit();
                return false;
            } else {
                $purchase->forceDelete();
                AccountTransaction::where('table_id', $id)->where('type', "opening_stock")->delete();
                $purchasedetails =  ProductOpeningStockDetails::where('product_opening_stock_id', $id)->get();

                foreach ($purchasedetails as $item) {
                    $mywhereCondition = array(
                        'branch_id' => $item->branch_id == 0 ?  $item->project_id:$item->branch_id,
                        'product_id' => $item->product_id,
                        'type' => $item->branch_id == 0 ? 'Project':'Branch',
                    );
    
                    $oldstockupdate = StockSummary::where($mywhereCondition)->first();
                    DB::table('stock_summaries')
                        ->where($mywhereCondition)
                        ->update(
                            ['quantity' => $oldstockupdate->quantity - $item->quantity],
                        );

                    $item->forceDelete();
                }
                DB::commit();
                return true;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again' . $e->getMessage());
        }
        return true;
    }
}
