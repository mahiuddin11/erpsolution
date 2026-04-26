<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Http\Controllers\Backend\Chart\ChartController;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\ChartOfAccount;
use App\Models\PurchaseOrder;
use App\Models\Purchases;
use App\Models\PurchasesDetails;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Supplier;
use App\Models\supplierLedger;
use App\Models\SupplierSelectPrice;
use App\Models\Transection;
use Illuminate\Support\Facades\DB;

class PurchaseRepositories
{
    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var Brand
     */
    private $purchases;

    /**
     * CourseRepository constructor.
     * @param brand $purchase
     */
    public function __construct(purchases $purchases)
    {
        $this->purchases = $purchases;
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
        $result = $this->purchases::latest()->get();
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

        $edit = Helper::roleAccess('inventorySetup.purchase.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.purchase.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.purchase.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->purchases::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $auth = Auth::user();

        if (empty($request->input('search.value'))) {
            $purchases = $this->purchases::where('purchase_type', 'Direct')->offset($start);

            $purchases =  $purchases->limit($limit)
                ->orderBy($order, $dir);
            if ($request->date) {
                $purchases = $purchases->whereDate('date', $request->date);
            }
            $purchases = $purchases->get();
            $totalFiltered = $this->purchases::count();
        } else {
            $search = $request->input('search.value');
            $purchases = $this->purchases::where(function ($query) use ($search) {
                    $query->where('invoice_no', 'like', "%{$search}%")
                          ->orWhereHas("supplier", function ($q) use ($search) {
                              $q->where("account_name", 'like', "%{$search}%");
                          })
                            ->orWhereHas("branch", function ($q) use ($search) {
                                $q->where("name", 'like', "%{$search}%");
                            })
                          ->orWhere('date', 'like' , "%{$search}%")
                          ->orWhere('payment_type', 'like' , "%{$search}%");
                });

            $purchases = $purchases->where('purchase_type', 'Direct')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = $this->purchases::where('invoice_no', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($purchases) {
            foreach ($purchases as $key => $purchase) {
                // dd($purchase->branch);
                $nestedData['id'] = $key + 1;
                $nestedData['invoice_no'] = $purchase->invoice_no;
                $nestedData['date'] = $purchase->date;
                $nestedData['branch'] = $purchase->branch->name ?? 'N/A';
                $nestedData['supplier'] = $purchase->supplier->account_name ?? 'N/A';
                $nestedData['payment_type'] = $purchase->payment_type;
                $nestedData['quantity'] = $purchase->quantity;
                $nestedData['subtotal'] = $purchase->subtotal;
                $nestedData['discount'] = $purchase->discount;
                $nestedData['grand_total'] = $purchase->grand_total;
                if ($purchase->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.purchase.status', [$purchase->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.purchase.status', [$purchase->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.purchase.edit', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('inventorySetup.purchase.show', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.purchase.destroy', $purchase->id) . '" delete_id="' . $purchase->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $purchase->id . '"><i class="fa fa-times"></i></a>';
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
    public function getpvList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'invoice_no',
        );


        $edit = Helper::roleAccess('inventorySetup.purchase.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.purchase.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.purchase.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->purchases::count();

        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $purchases = $this->purchases::where('purchase_type', 'Manual')
            ->with(['branch', 'supplier'])
            ->offset($start);
            $purchases = $purchases->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->purchases::count();

            
        } else {
            $search = $request->input('search.value');
            $purchases = $this->purchases::where('invoice_no', 'like', "%{$search}%");

            $purchases = $purchases->where('purchase_type', 'Manual')
            ->with(['branch', 'supplier'])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();

                
            $totalFiltered = $this->purchases::where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($purchases) {
           
            foreach ($purchases as $key => $purchase) {
                
                $nestedData['id'] = $key + 1;
                $nestedData['invoice_no'] = $purchase->invoice_no;
                $nestedData['date'] = $purchase->date;
                $nestedData['branch'] = $purchase->branch->name ?? 'N/A';
                $nestedData['supplier'] = $purchase->supplier->name ?? 'N/A';
                // $nestedData['supplier'] = $purchase->supplier->account_name ?? 'N/A';
                $nestedData['payment_type'] = $purchase->payment_type;
                $nestedData['subtotal'] = $purchase->subtotal;
                $nestedData['discount'] = $purchase->discount;
                $nestedData['grand_total'] = $purchase->grand_total;

                if ($purchase->status == 'Active') :
                    $nestedData['status'] = 'Accepted';
                else :
                    $nestedData['status'] = 'Pending';
                endif;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.purchase.pvedit', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('inventorySetup.purchase.pvinvoice', $purchase->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.purchase.pvdestroy', $purchase->id) . '" delete_id="' . $purchase->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $purchase->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->purchases::find($id);
        
        return $result;
    }

    public function store($request)
    {
        
   
        DB::beginTransaction();
        try {

            $branch_id = $request->branch_id;
            $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;
            $purchase = new $this->purchases();
            $purchase->invoice_no = $request->invoice_no;
            $purchase->custom_invoice = $request->custom_invoice;
            $purchase->date = $request->date;
            $purchase->ledger_id = $request->ledger_id ?? 0;
            $purchase->branch_id = $request->branch_id ?? 0;
            $purchase->supplier_id = $request->supplier_id ?? 0;
            $purchase->quantity = array_sum($request->qty);
            $purchase->purchase_type = 'Direct';
            $purchase->subtotal = array_sum($request->unitprice);   
            $purchase->grand_total = array_sum($request->total);
            $purchase->status = 'Active';
            $purchase->payment_type = $request->payment_type;
            $purchase->discount = $request->discount;
            $purchase->paid_amount = $request->paid_amount;
            $purchase->due_amount = $request->cart_due;
            $purchase->created_by = Auth::user()->id;
            $purchase->narration = $request->narration;

            
            
            if ($request->has('chart_of_account_id')) {
                $purchase->chart_of_account_id = $request->chart_of_account_id;
            }
            if ($request->has('account_number')) {
                $purchase->account_number = $request->account_number;
            }
            if ($request->has('check_number')) {
                $purchase->check_number = $request->check_number;
            }
            if ($request->has('bank')) {
                $purchase->bank = $request->bank;
            }
            if ($request->has('bank_branch')) {
                $purchase->bank_branch = $request->bank_branch;
            }
            if ($request->has('input_net_total')) {
                $purchase->net_total = $request->input_net_total;
            }
            $purchase->save();

            $purchases_id = $purchase->id;

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;


            for ($i = 0; $i < count($category_id); $i++) {
                $purchaseDetail = new PurchasesDetails();
                $purchaseDetail->product_id = $proName[$i];
                $purchaseDetail->category_id = $category_id[$i];
                $purchaseDetail->quantity = $qty[$i];
                $purchaseDetail->purchasetype = $request->purchasetype[$i];
                $purchaseDetail->branch_id = $request->branch_id ?? 0;
                $purchaseDetail->unit_price = $subtotal[$i];
                $purchaseDetail->total_price = $grand_total[$i];
                $purchaseDetail->purchases_id = $purchases_id;
                $purchaseDetail->date = $request->date;
                $purchaseDetail->created_by = Auth::user()->id;
                $purchaseDetail->save();

                $stock = new Stock();
                $stock->product_id = $proName[$i];
                $stock->quantity = $qty[$i];
                $stock->branch_id = $request->branch_id;
                $stock->unit_price = $subtotal[$i];
                $stock->total_price = $grand_total[$i];
                $stock->general_id = $purchases_id;
                $stock->date = $request->date;
                $stock->status = 'Purchase';
                $stock->created_by = Auth::user()->id;
                $stock->save();

                $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Branch")->first();
                if (!empty($existingCheck)) :
                    $newQty = $existingCheck->quantity + $qty[$i];
                    StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->where('type', "Branch")->update(array('quantity' => $newQty));

                else :
                    $stockSummary = new StockSummary();
                    $stockSummary->branch_id = $request->branch_id;
                    $stockSummary->product_id = $proName[$i];
                    $stockSummary->purchasetype = $request->purchasetype[$i];
                    $stockSummary->quantity = $qty[$i];
                    $stockSummary->type = "Branch";
                    $stockSummary->save();
                endif;
            }

            // $invoice = AccountTransaction::accountInvoice();
            $invoice = (new AccountTransaction())->accountInvoice();
            $transactionPay['payment_invoice'] = $request->invoice_no;
            $transactionPay['invoice'] = $request->invoice_no;
            $transactionPay['table_id'] = $purchases_id;
            $transactionPay['account_id'] = getAccountByUniqueID(22)->id; // ->purchase
            $transactionPay['type'] = 1;
            $transactionPay['branch_id'] = $branch_id ?? 0;
            $transactionPay['debit'] =  array_sum($request->total);
            $transactionPay['remark'] = $request->narration;
            $transactionPay['created_by'] = Auth::id();
            $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
            $transactionPay['created_at'] = $request->date;
            AccountTransaction::create($transactionPay);

            $transaction['payment_invoice'] = $request->invoice_no;
            $transaction['invoice'] = $request->invoice_no;
            $transaction['table_id'] = $purchases_id;
            $transaction['account_id'] = $request->ledger_id; // account payable
            $transaction['type'] = 1;
            $transaction['branch_id'] = $branch_id ?? 0;
            $transaction['credit'] = (array_sum($request->total));
            $transaction['remark'] = $request->narration;
            $transaction['created_by'] = Auth::id();
            $transaction['supplier_id'] = $request->supplier_id ?? 0;
            $transaction['created_at'] = $request->date;
            AccountTransaction::create($transaction);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return $purchase;
    }

    // public function prstore($request)
    // {

    //     DB::beginTransaction();

    //     try {
    //         $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;
    //         $purchase = new $this->purchases();
    //         $purchase->invoice_no = $request->invoice_no;
    //         $purchase->date = $request->date;
    //         $purchase->purchase_order_id = $request->purchase_order_id;
    //         $purchase->type =  'Project';
    //         $purchase->branch_id =  0;
    //         $purchase->project_id = $request->project_id;
    //         $purchase->supplier_id = $request->supplier_id ?? 0;
    //         $purchase->quantity = array_sum($request->qty);
    //         $purchase->purchase_type = 'Manual';
    //         $purchase->subtotal = array_sum($request->unitprice);
    //         $purchase->grand_total = array_sum($request->total);
    //         $purchase->status = 'Pending';
    //         $purchase->payment_type = $request->payment_type;
    //         $purchase->discount = $request->discount;
    //         $purchase->paid_amount = $request->paid_amount + $request->advance_payment; // payment and advance pay addition
    //         $purchase->due_amount = $request->cart_due;
    //         $purchase->created_by = Auth::user()->id;
    //         $purchase->narration = $request->narration;


    //         if ($request->has('chart_of_account_id')) {
    //             $purchase->chart_of_account_id = $request->chart_of_account_id;
    //         }
    //         if ($request->has('account_number')) {
    //             $purchase->account_number = $request->account_number;
    //         }
    //         if ($request->has('check_number')) {
    //             $purchase->check_number = $request->check_number;
    //         }
    //         if ($request->has('bank')) {
    //             $purchase->bank = $request->bank;
    //         }
    //         if ($request->has('bank_branch')) {
    //             $purchase->bank_branch = $request->bank_branch;
    //         }
    //         if ($request->has('input_net_total')) {
    //             $purchase->net_total = $request->input_net_total;
    //         }
    //         $purchase->save();
    //         $purchases_id = $purchase->id;

    //         $category_id = $request->category_nm;
    //         $supplier_id = $request->supplier_nm;
    //         $proName = $request->product_nm;
    //         $subtotal = $request->unitprice;
    //         $grand_total = $request->total;
    //         $qty = $request->qty;



    //         for ($i = 0; $i < count($supplier_id); $i++) {
    //             $purchaseDetail = new PurchasesDetails();
    //             $purchaseDetail->product_id = $proName[$i];
    //             $purchaseDetail->supplier_id = $supplier_id[$i];
    //             $purchaseDetail->category_id = $category_id[$i];
    //             $purchaseDetail->purchasetype = $request->purchasetype[$i];
    //             $purchaseDetail->quantity = $qty[$i];
    //             $purchaseDetail->project_id = $request->project_id;
    //             $purchaseDetail->unit_price = $subtotal[$i];
    //             $purchaseDetail->total_price = $grand_total[$i];
    //             $purchaseDetail->purchases_id = $purchases_id;
    //             $purchaseDetail->date = $request->date;
    //             $purchaseDetail->created_by = Auth::user()->id;
    //             $purchaseDetail->save();
    //         }

    //         $purchaseorder['approved_by'] = Auth::user()->id;
    //         $purchaseorder['approved_at'] = date('Y-m-d');
    //         $purchaseorder['status'] = 'Complete';
    //         PurchaseOrder::where('id', $request->purchase_order_id)->update($purchaseorder);

    //         // $invoice = AccountTransaction::accountInvoice();

    //         $invoice = (new AccountTransaction())->accountInvoice();

    //         // old function
    //         foreach ($supplier_id as $key => $id) {

    //             $supplier = Supplier::find($id);
    //             $debit = AccountTransaction::where([
    //                 'payment_invoice' => $request->invoice_no,
    //                 'invoice'       => $invoice,
    //                 'table_id'      => $purchases_id,
    //                 'account_id'    => getAccountByUniqueID(22)->id, //purchase
    //                 'project_id'    => $request->project_id,
    //                 'supplier_id'   => $id
    //             ])->first();

    //             $debitAmnt = isset($debit->debit) ? $debit->debit : 0;
    //             $totalDebit = $debitAmnt + $request->total[$key];


    //             AccountTransaction::updateOrCreate([
    //                 'payment_invoice' => $request->invoice_no,
    //                 'invoice'       => $invoice,
    //                 'table_id'      => $purchases_id,
    //                 'account_id'    => getAccountByUniqueID(22)->id,
    //                 'project_id'    => $request->project_id,
    //                 'supplier_id'   => $id
    //             ], [
    //                 'type' => 1,
    //                 'branch_id' => $request->branch_id ?? 0,
    //                 'debit' =>  $totalDebit,
    //                 'remark' => $request->narration,
    //                 'created_at' => $request->date,
    //                 'created_by' => Auth::id(),
    //             ]);
    //             $credit = AccountTransaction::where([
    //                 'payment_invoice' => $request->invoice_no,
    //                 'invoice'       => $invoice,
    //                 'table_id'      => $purchases_id,
    //                 'account_id'    => $supplier->account->id,
    //                 'project_id'    => $request->project_id,
    //                 'supplier_id'   => $id
    //             ])->first();

    //             $credit = isset($credit->credit) ? $credit->credit : 0;
    //             $totalCredit = $credit + $request->total[$key];


    //             AccountTransaction::updateOrCreate(
    //                 [
    //                     'payment_invoice' => $request->invoice_no,
    //                     'invoice'       => $invoice,
    //                     'table_id'      => $purchases_id,
    //                     'account_id'    => $supplier->account->id,
    //                     'project_id'    => $request->project_id,
    //                     'supplier_id'   => $id
    //                 ],
    //                 [
    //                     'type' => 1,
    //                     'branch_id' => $request->branch_id ?? 0,
    //                     'credit' =>  $totalCredit,
    //                     'created_at' => $request->date,
    //                     'remark' => $request->narration,
    //                     'created_by' => Auth::id(),
    //                 ]
    //             );
    //         }





    //         if ($request->payment_type == 'cash') {
    //             $transection = new Transection();
    //             $transection->date = $request->date;
    //             $transection->account_id = $request->chart_of_account_id;
    //             $transection->payment_id = $purchases_id;
    //             $transection->branch_id = $request->supplier_id;
    //             $transection->type =  11;
    //             $transection->note = $request->note;
    //             $transection->amount =  array_sum($request->total) - $request->discount;
    //             $transection->credit = array_sum($request->total) - $request->discount;
    //             $transection->save();
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage());
    //         redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
    //     }
    //     return $purchase;
    // }


    // new v0.1

    // public function prstore($request)
    // {
    //     DB::beginTransaction();

    //     try {

    //         $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;

    //         /* ================= PURCHASE MASTER ================= */
    //         $purchase = new $this->purchases();
    //         $purchase->invoice_no = $request->invoice_no;
    //         $purchase->date = $request->date;
    //         $purchase->purchase_order_id = $request->purchase_order_id;
    //         $purchase->type = 'Project';
    //         $purchase->branch_id = 0;
    //         $purchase->project_id = $request->project_id;

    //         $purchase->supplier_id = $request->supplier_id ?? 0;

    //         $purchase->quantity = array_sum($request->qty);
    //         $purchase->purchase_type = 'Manual';
    //         $purchase->subtotal = array_sum($request->unitprice);
    //         $purchase->grand_total = array_sum($request->total);

    //         $purchase->status = 'Pending';
    //         $purchase->payment_type = $request->payment_type;
    //         $purchase->discount = $request->discount;

    //         $purchase->paid_amount = ($request->paid_amount ?? 0) + ($request->advance_payment ?? 0);
    //         $purchase->due_amount = $request->cart_due;

    //         $purchase->created_by = Auth::user()->id;
    //         $purchase->narration = $request->narration;

    //         if ($request->has('input_net_total')) {
    //             $purchase->net_total = $request->input_net_total;
    //         }

    //         $purchase->save();
    //         $purchases_id = $purchase->id;

    //         /* ================= ARRAY DATA ================= */
    //         $category_id = $request->category_nm;
    //         $supplier_nm = $request->supplier_nm ?? [];
    //         $ledger_nm = $request->ledger_nm ?? [];
    //         $proName = $request->product_nm;
    //         $subtotal = $request->unitprice;
    //         $grand_total = $request->total;
    //         $qty = $request->qty;

    //         /* ================= DETAILS ================= */
    //         for ($i = 0; $i < count($proName); $i++) {

    //             $supplierId = $supplier_nm[$i] ?? 0;
    //             $ledgerId = $ledger_nm[$i] ?? 0;

    //             $purchaseDetail = new PurchasesDetails();
    //             $purchaseDetail->product_id = $proName[$i];
    //             $purchaseDetail->category_id = $category_id[$i];
    //             $purchaseDetail->quantity = $qty[$i];
    //             $purchaseDetail->purchasetype = $request->purchasetype[$i];
    //             $purchaseDetail->branch_id = $request->branch_id ?? 0;
    //             $purchaseDetail->unit_price = $subtotal[$i];
    //             $purchaseDetail->total_price = $grand_total[$i];
    //             $purchaseDetail->purchases_id = $purchases_id;
    //             $purchaseDetail->date = $request->date;
    //             $purchaseDetail->created_by = Auth::user()->id;

    //             /* ================= IMPORTANT LOGIC ================= */
    //             $purchaseDetail->supplier_id = ($supplierId != 0) ? $supplierId : 0;
    //             $purchaseDetail->ledger_id = ($ledgerId != 0) ? $ledgerId : 0;

    //             $purchaseDetail->save();
    //         }

    //         /* ================= PURCHASE ORDER UPDATE ================= */
    //         PurchaseOrder::where('id', $request->purchase_order_id)->update([
    //             'approved_by' => Auth::user()->id,
    //             'approved_at' => date('Y-m-d'),
    //             'status' => 'Complete'
    //         ]);

    //         /* ================= ACCOUNT TRANSACTION ================= */
    //         $invoice = (new AccountTransaction())->accountInvoice();

    //         $transactionPay = [];
    //         $transactionPay['payment_invoice'] = $request->invoice_no;
    //         $transactionPay['invoice'] = $request->invoice_no;
    //         $transactionPay['table_id'] = $purchases_id;
    //         $transactionPay['account_id'] = getAccountByUniqueID(22)->id;
    //         $transactionPay['type'] = 1;
    //         $transactionPay['branch_id'] = $request->branch_id ?? 0;
    //         $transactionPay['debit'] = array_sum($request->total);
    //         $transactionPay['remark'] = $request->narration;
    //         $transactionPay['created_by'] = Auth::id();
    //         $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
    //         $transactionPay['created_at'] = $request->date;

    //         AccountTransaction::create($transactionPay);

    //         $transaction = [];
    //         $transaction['payment_invoice'] = $request->invoice_no;
    //         $transaction['invoice'] = $request->invoice_no;
    //         $transaction['table_id'] = $purchases_id;
    //         $transaction['account_id'] = $request->ledger_id ?? 0;
    //         $transaction['type'] = 1;
    //         $transaction['branch_id'] = $request->branch_id ?? 0;
    //         $transaction['credit'] = array_sum($request->total);
    //         $transaction['remark'] = $request->narration;
    //         $transaction['created_by'] = Auth::id();
    //         $transaction['supplier_id'] = $request->supplier_id ?? 0;
    //         $transaction['created_at'] = $request->date;

    //         AccountTransaction::create($transaction);

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e->getMessage());
    //     }

    //     return $purchase;
    // }

    public function prstore($request)
    {
        DB::beginTransaction();

        try {

            /* =========================
         BRANCH SETUP
        ========================= */
            $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;

            /* =========================
         2. PURCHASE MASTER CREATE
         (Main purchase table data save)
               ========================= */


            $purchase = new $this->purchases();
            $purchase->invoice_no = $request->invoice_no;
            $purchase->date = $request->date;
            $purchase->purchase_order_id = $request->purchase_order_id;
            $purchase->type = 'Project';
            $purchase->branch_id = 0;
            $purchase->project_id = $request->project_id; // MAIN PROJECT ID
            $purchase->supplier_id = $request->supplier_id ?? 0;

            $purchase->quantity = array_sum($request->qty);
            $purchase->purchase_type = 'Manual';
            $purchase->subtotal = array_sum($request->unitprice);
            $purchase->grand_total = array_sum($request->total);

            $purchase->status = 'Pending';
            $purchase->payment_type = $request->payment_type;
            $purchase->discount = $request->discount;

            $purchase->paid_amount = ($request->paid_amount ?? 0) + ($request->advance_payment ?? 0);
            $purchase->due_amount = $request->cart_due;
            $purchase->created_by = Auth::user()->id;
            $purchase->narration = $request->narration;

            $purchase->save();

            $purchases_id = $purchase->id;

            /* =========================
          3. DETAILS TABLE DATA PREP
        ========================= */
            $category_id = $request->category_nm;
            $supplier_nm = $request->supplier_nm ?? [];
            $ledger_nm = $request->ledger_nm ?? [];
            $proName = $request->product_nm;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;

            /* =========================
          4. PURCHASE DETAILS INSERT
         (Each product row save here)
        ========================= */
            for ($i = 0; $i < count($proName); $i++) {

                $supplierId = $supplier_nm[$i] ?? 0;
                $ledgerId = $ledger_nm[$i] ?? 0;

                $purchaseDetail = new PurchasesDetails();

                $purchaseDetail->product_id = $proName[$i];
                $purchaseDetail->category_id = $category_id[$i];

                //FIXED: project_id এখন properly save হবে
                $purchaseDetail->project_id = $request->project_id;

                $purchaseDetail->quantity = $qty[$i];
                $purchaseDetail->purchasetype = $request->purchasetype[$i];
                $purchaseDetail->branch_id = $request->branch_id ?? 0;

                $purchaseDetail->unit_price = $subtotal[$i];
                $purchaseDetail->total_price = $grand_total[$i];

                $purchaseDetail->purchases_id = $purchases_id;
                $purchaseDetail->date = $request->date;

                $purchaseDetail->created_by = Auth::user()->id;

                /* =========================
              PARTY LOGIC (Supplier / Ledger)
             যদি supplier 0 হয় → ledger use হবে
            ========================= */
                $purchaseDetail->supplier_id = ($supplierId != 0) ? $supplierId : 0;
                $purchaseDetail->ledger_id = ($ledgerId != 0) ? $ledgerId : 0;

                $purchaseDetail->save();
            }

          
            PurchaseOrder::where('id', $request->purchase_order_id)->update([
                'approved_by' => Auth::user()->id,
                'approved_at' => date('Y-m-d'),
                'status' => 'Complete'
            ]);


            /* =========================
   5. ACCOUNTING ENTRY
   (Double Entry System)
========================= */

            $invoice = (new AccountTransaction())->accountInvoice();

            $totalAmount = array_sum($request->total);

            /* =========================
   5.1 DEBIT ENTRY
   (Purchase / Expense Account)
========================= */

            AccountTransaction::create([
                'payment_invoice' => $request->invoice_no,
                'invoice' => $request->invoice_no,
                'table_id' => $purchases_id,
                'account_id' => getAccountByUniqueID(22)->id, // Purchase Account
                'type' => 1,
                'branch_id' => $request->branch_id ?? 0,
                'debit' => $totalAmount,
                'credit' => 0,
                'project_id' => $request->project_id ?? 0,
                'remark' => $request->narration,
                'created_by' => Auth::id(),
                'created_at' => $request->date,
            ]);

            /* =========================
   5.2 CREDIT ENTRY
   (Supplier OR Ledger Payable)
========================= */

            $supplierId = $request->supplier_id ?? 0;
            $ledgerId   = $request->ledger_id ?? 0;

            /*
|--------------------------------------------------------------------------
| ACCOUNT SELECTION LOGIC
|--------------------------------------------------------------------------
| যদি supplier থাকে → supplier account
| না থাকলে → ledger account
*/

            $creditAccountId = 0;

            if (!empty($supplierId)) {
                $supplier = Supplier::find($supplierId);
                $creditAccountId = $supplier->account_id ?? 0;
            } elseif (!empty($ledgerId)) {
                $creditAccountId = $ledgerId;
            }

            AccountTransaction::create([
                'payment_invoice' => $request->invoice_no,
                'invoice' => $request->invoice_no,
                'table_id' => $purchases_id,
                'account_id' => $creditAccountId,
                'type' => 1,
                'branch_id' => $request->branch_id ?? 0,
                'debit' => 0,
                'credit' => $totalAmount,
                'project_id' => $request->project_id ?? 0,
                'remark' => $request->narration,
                'created_by' => Auth::id(),
                'created_at' => $request->date,
            ]);

           
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }

        return $purchase;
    }
    

    public function pvupdate($request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $purchase =  $this->purchases::find($id);
            $purchase->date = $request->date;
            $purchase->purchase_order_id = $request->purchase_order_id;
            $purchase->project_id = $request->project_id;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->quantity = array_sum($request->qty);
            $purchase->purchase_type = 'Manual';
            $purchase->subtotal = array_sum($request->unitprice);
            $purchase->grand_total = array_sum($request->total);
            $purchase->status = 'Close';
            $purchase->payment_type = $request->payment_type;
            $purchase->discount = $request->discount;
            $purchase->paid_amount = $request->paid_amount + $request->advance_payment; // payment and advance pay addition
            $purchase->due_amount = $request->cart_due;
            $purchase->updated_by = Auth::user()->id;
            $purchase->narration = $request->narration;

            if ($request->has('chart_of_account_id')) {
                $purchase->chart_of_account_id = $request->chart_of_account_id;
            }
            if ($request->has('account_number')) {
                $purchase->account_number = $request->account_number;
            }
            if ($request->has('check_number')) {
                $purchase->check_number = $request->check_number;
            }
            if ($request->has('bank')) {
                $purchase->bank = $request->bank;
            }
            if ($request->has('bank_branch')) {
                $purchase->bank_branch = $request->bank_branch;
            }
            if ($request->has('input_net_total')) {
                $purchase->net_total = $request->input_net_total;
            }
            $purchase->save();
            $purchases_id = $purchase->id;

            $category_id = $request->category_nm;
            $proName = $request->product_nm;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;

            PurchasesDetails::where('purchases_id', $id)->forceDelete();

            for ($i = 0; $i < count($category_id); $i++) {
                $purchaseDetail = new PurchasesDetails();
                $purchaseDetail->product_id = $proName[$i];
                $purchaseDetail->category_id = $category_id[$i];
                $purchaseDetail->quantity = $qty[$i];
                $purchaseDetail->purchasetype = $request->purchasetype[$i];
                $purchaseDetail->project_id = $request->project_id;
                $purchaseDetail->unit_price = $subtotal[$i];
                $purchaseDetail->total_price = $grand_total[$i];
                $purchaseDetail->purchases_id = $purchases_id;
                $purchaseDetail->date = $request->date;
                $purchaseDetail->updated_by = Auth::user()->id;
                $purchaseDetail->save();
            }

            supplierLedger::where('purchase_id', $purchases_id)->delete();
            AccountTransaction::where('type', 1)->where('table_id', $purchases_id)->delete();
            if ($request->payment_type == 'cash' || $request->payment_type == 'check') {
                $supplierLedger = new SupplierLedger();
                $supplierLedger->date = $request->date;
                $supplierLedger->purchase_id = $purchases_id;
                $supplierLedger->supplier_id = $request->supplier_id;
                $supplierLedger->branch_id =  $request->branch_id ?? 0;
                $supplierLedger->account_id =  $request->chart_of_account_id;
                $supplierLedger->payment_type = $request->payment_type;
                $supplierLedger->debit = array_sum($request->total);
                $supplierLedger->created_by = Auth::user()->id;
                $supplierLedger->save();

                $supplierLedger = new SupplierLedger();
                $supplierLedger->date = $request->date;
                $supplierLedger->purchase_id = $purchases_id;
                $supplierLedger->supplier_id = $request->supplier_id;
                $supplierLedger->branch_id =  $request->branch_id ?? 0;
                $supplierLedger->account_id =  $request->chart_of_account_id;
                $supplierLedger->payment_type = $request->payment_type;
                $supplierLedger->credit = $request->paid_amount;
                $supplierLedger->created_by = Auth::user()->id;
                $supplierLedger->save();

                // $invoice = AccountTransaction::accountInvoice();
                $invoice = (new AccountTransaction())->accountInvoice();
                $transactionPay['invoice'] = $invoice;
                $transactionPay['table_id'] = $purchases_id;
                $transactionPay['account_id'] = 7; // ->purchase
                $transactionPay['type'] = 1;
                $transactionPay['branch_id'] = $request->branch_id ?? 0;
                $transactionPay['debit'] =  array_sum($request->total);
                $transactionPay['remark'] = $request->narration;
                $transactionPay['created_by'] = Auth::id();
                $transactionPay['supplier_id'] = $request->supplier_id;
                AccountTransaction::create($transactionPay);

                $transaction['invoice'] = $invoice;
                $transaction['table_id'] = $purchases_id;
                $transaction['account_id'] = $request->chart_of_account_id;
                $transaction['type'] = 1;
                $transaction['branch_id'] = $request->branch_id ?? 0;
                $transaction['credit'] = $request->paid_amount;
                $transaction['remark'] = $request->narration;
                $transaction['supplier_id'] = $request->supplier_id;
                $transaction['created_by'] = Auth::id();
                AccountTransaction::create($transaction);
            } else {

                $supplierLedger = new SupplierLedger();
                $supplierLedger->date = $request->date;
                $supplierLedger->purchase_id = $purchases_id;
                $supplierLedger->supplier_id = $request->supplier_id;
                $supplierLedger->branch_id =  $request->branch_id ?? 0;
                $supplierLedger->account_id =  $request->chart_of_account_id;
                $supplierLedger->payment_type = $request->payment_type;
                $supplierLedger->debit = array_sum($request->total);
                $supplierLedger->updated_by = Auth::user()->id;
                $supplierLedger->save();

                // $invoice = AccountTransaction::accountInvoice();
                $invoice = (new AccountTransaction())->accountInvoice();

                $transactionPay['invoice'] = $invoice;
                $transactionPay['table_id'] = $purchases_id;
                $transactionPay['account_id'] = 7; // ->purchase
                $transactionPay['type'] = 1;
                $transactionPay['branch_id'] = $request->branch_id ?? 0;
                $transactionPay['debit'] =  array_sum($request->total);
                $transactionPay['remark'] = $request->narration;
                $transactionPay['created_by'] = Auth::id();
                $transactionPay['supplier_id'] = $request->supplier_id;
                $transactionPay['created_at'] = $request->date;
                AccountTransaction::create($transactionPay);

                $transaction['invoice'] = $invoice;
                $transaction['table_id'] = $purchases_id;
                $transaction['account_id'] = 14; // account payable
                $transaction['type'] = 1;
                $transaction['branch_id'] = $request->branch_id ?? 0;
                $transaction['credit'] = array_sum($request->total);
                $transaction['remark'] = $request->narration;
                $transaction['created_by'] = Auth::id();
                $transaction['supplier_id'] = $request->supplier_id;
                $transaction['created_at'] = $request->date;
                AccountTransaction::create($transaction);
            }


            if ($request->payment_type == 'cash') {
                $transection['date'] = $request->date;
                $transection['account_id'] = $request->chart_of_account_id;
                $transection['payment_id'] = $purchases_id;
                $transection['branch_id'] = $request->branch_id;
                $transection['type'] =  11;
                $transection['note'] = $request->note;
                $transection['amount'] =  array_sum($request->total) - $request->discount;
                $transection['credit'] = array_sum($request->total) - $request->discount;
                Transection::where('payment_id', $purchases_id)->where('type', 11)->update($transection);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return $purchase;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $purchase = $this->purchases::findOrFail($id);
            $purchase->date = $request->date;
            $purchase->branch_id = $request->branch_id;
            $purchase->ledger_id = $request->ledger_id;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->quantity = array_sum($request->qty);
            $purchase->subtotal = array_sum($request->unitprice);
            $purchase->grand_total = array_sum($request->total);
            $purchase->payment_type = $request->payment_type;
            $purchase->discount = $request->discount;
            $purchase->paid_amount = $request->paid_amount;
            $purchase->due_amount = $request->cart_due;
            $purchase->created_by = Auth::user()->id;
            $purchase->narration = $request->narration;

            if ($request->has('chart_of_account_id')) {
                $purchase->chart_of_account_id = $request->chart_of_account_id;
            }
            if ($request->has('account_number')) {
                $purchase->account_number = $request->account_number;
            }
            if ($request->has('check_number')) {
                $purchase->check_number = $request->check_number;
            }
            if ($request->has('bank')) {
                $purchase->bank = $request->bank;
            }
            if ($request->has('bank_branch')) {
                $purchase->bank_branch = $request->bank_branch;
            }
            if ($request->has('input_net_total')) {
                $purchase->net_total = $request->input_net_total;
            }
            $purchase->save();
            $purchases_id = $purchase->id;


            $category_id = $request->catName;
            $oldproName =  $request->oldproName;
            $oldqty =  $request->oldqty;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;

              for ($w = 0; $w < count($oldproName); $w++) {
                // echo $oldproName[$i];
                $mywhereCondition = array(
                    'branch_id' => $request->old_branch_id,
                    'product_id' => $oldproName[$w],
                    'type' => 'Branch',
                );

                $oldstockupdate = StockSummary::where($mywhereCondition)->first();
                dd($oldstockupdate);

                DB::table('stock_summaries')
                    ->where($mywhereCondition)
                    ->update(
                        ['quantity' => ($oldstockupdate->quantity ?? 0) - $oldqty[$w]],
                    );
            }

            PurchasesDetails::where('purchases_id', $purchase->id)->forceDelete();
            Stock::where('general_id', $purchase->id)->where('status', 'Purchase')->forceDelete();

            for ($i = 0; $i < count($category_id); $i++) {
                $purchaseDetail = new PurchasesDetails();
                $purchaseDetail->product_id = $proName[$i];
                $purchaseDetail->quantity = $qty[$i];
                $purchaseDetail->purchasetype = $request->purchasetype[$i];
                $purchaseDetail->branch_id = $request->branch_id;
                $purchaseDetail->unit_price = $subtotal[$i];
                $purchaseDetail->total_price = $grand_total[$i];
                $purchaseDetail->purchases_id = $purchases_id;
                $purchaseDetail->date = $request->date;
                $purchaseDetail->created_by = Auth::user()->id;
                $purchaseDetail->save();

                $stock = new Stock();
                $stock->product_id = $proName[$i];
                $stock->quantity = $qty[$i];
                $stock->branch_id = $request->branch_id;
                $stock->unit_price = $subtotal[$i];
                $stock->total_price = $grand_total[$i];
                $stock->general_id = $purchases_id;
                $stock->date = $request->date;
                $stock->status = 'Purchase';
                $stock->created_by = Auth::user()->id;
                $stock->save();

                $existingCheck = StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->where('type', 'Branch')->first();

                if (!empty($existingCheck) && $existingCheck->quantity >= 0) :
                    $newQty = $existingCheck->quantity + $qty[$i];
                    StockSummary::where('product_id', $proName[$i])->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->where('type', 'Branch')->update(array('quantity' => $newQty));
                else :
                    $stockSummary = new StockSummary();
                    $stockSummary->branch_id = $request->branch_id;
                    $stockSummary->product_id = $proName[$i];
                    $stockSummary->purchasetype = $request->purchasetype[$i];
                    $stockSummary->quantity = $qty[$i];
                    $stockSummary->type = 'Branch';
                    $stockSummary->save();
                endif;
            }

            supplierLedger::where('purchase_id', $purchases_id)->delete();
            AccountTransaction::where('table_id', $purchases_id)->where('type', 1)->delete();

            // $invoice = AccountTransaction::accountInvoice();
            $invoice = (new AccountTransaction())->accountInvoice();

            $transactionPay['payment_invoice'] = $request->invoice_no;
            $transactionPay['invoice'] = $purchase->invoice_no;
            $transactionPay['table_id'] = $purchases_id;
            $transactionPay['account_id'] = getAccountByUniqueID(22)->id; // ->purchase
            $transactionPay['type'] = 1;
            $transactionPay['branch_id'] = $request->branch_id ?? 0;
            $transactionPay['debit'] =  array_sum($request->total);
            $transactionPay['remark'] = $request->narration;
            $transactionPay['created_by'] = Auth::id();
            $transactionPay['supplier_id'] = $request->supplier_id ?? 0;
            $transactionPay['created_at'] = $request->date;
            AccountTransaction::create($transactionPay);

            $transaction['payment_invoice'] = $request->invoice_no;
            $transaction['invoice'] = $purchase->invoice_no;
            $transaction['table_id'] = $purchases_id;
            $transaction['account_id'] = $request->ledger_id; // account payable
            $transaction['type'] = 1;
            $transaction['branch_id'] = $request->branch_id ?? 0;
            $transaction['credit'] = (array_sum($request->total));
            $transaction['remark'] = $request->narration;
            $transaction['created_by'] = Auth::id();
            $transaction['supplier_id'] = $request->supplier_id ?? 0;
            $transaction['created_at'] = $request->date;
            AccountTransaction::create($transaction);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage(),$e->getLine());
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again' . $e->getMessage());
        }
        return $purchase;
    }

    public function statusUpdate($id, $status)
    {
        $purchase = $this->purchases::find($id);
        $purchase->status = $status;
        $purchase->save();
        return $purchase;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchase = $this->purchases::find($id);
            if ($purchase->status == "Accepted") {
                session()->flash('error', "Sorry, you couldn't delete!!");
                DB::commit();
                return false;
            } else {

                $purchase->forceDelete();
                AccountTransaction::where('table_id', $id)->where('type', 1)->delete();
                $purchasedetails =  PurchasesDetails::where('purchases_id', $id)->get();

                foreach ($purchasedetails as  $val) {
                    $mywhereCondition = array(
                        'branch_id' => $val->branch_id,
                        'product_id' => $val->product_id,
                        'type' => 'Branch',
                    );

                    // $oldstockupdate = StockSummary::where($mywhereCondition)->first();

                    // DB::table('stock_summaries')
                    //     ->where($mywhereCondition)
                    //     ->update(
                    //         ['quantity' => $oldstockupdate->quantity ? - $val->quantity],
                    //     );

                    $val->forceDelete();
                }

                SupplierLedger::where('purchase_id', $id)->delete();
                Transection::where('payment_id', $id)->where('type', 11)->forceDelete();
                Stock::where('general_id', $purchase->id)->where('status', 'Purchase')->forceDelete();
                $purchaseorder['status'] = "Pending";
                PurchaseOrder::where('id', $purchase->purchase_order_id)->update($purchaseorder);
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
