<?php

namespace App\Repositories\Sale;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\Brand;
use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\customerLedger;
use App\Models\Sale;
use App\Models\sales_Details;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Transection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleRepositories
{

    /**
     * @var user_id
     */

     private $user_id;

    /**
     * @var Brand
     */
    private $Sale;

    /**
     * CourseRepository constructor.
     * @param brand $esale
     */
    public function __construct(Sale $sales)
    {
        $this->Sale = $sales;
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
        $result = $this->Sale::latest()->get();
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

        $edit = Helper::roleAccess('sale.sale.edit') ? 1 : 0;
        $delete = Helper::roleAccess('sale.sale.destroy') ? 1 : 0;
        $view = Helper::roleAccess('sale.sale.show') ? 1 : 0;
        $chalan = Helper::roleAccess('sale.sale.challan') ? 1 : 0;
        $ced = $edit + $delete + $view + $chalan;

        $totalData = $this->Sale::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $Sale = $this->Sale::offset($start);

            $Sale = $Sale->limit($limit)
                ->orderBy($order, $dir);

            if ($request->date) {
                $Sale = $Sale->whereDate('date', $request->date);
            }
            $Sale = $Sale->get();
            $totalFiltered = $this->Sale::count();
        } else {
            $search = $request->input('search.value');
            $Sale = $this->Sale::where('invoice_no', 'like', "%{$search}%")
            ->orWhereHas('branch', function ($query) use ($search) {
                 $query->where('name', 'like', "%{$search}%");
            })
            
            ->orWhere('date', 'like', "%$search%");

            $Sale = $Sale->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->Sale::where('invoice_no', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($Sale) {
            foreach ($Sale as $key => $esale) {
                $nestedData['id'] = $key + 1;
                $nestedData['invoice_no'] = $esale->invoice_no;
                $nestedData['po_invoice'] = $esale->po_invoice;
                $nestedData['date'] = $esale->date;
                $nestedData['branch_id'] = $esale->branch->branchCode . ' - ' . $esale->branch->name;
                $nestedData['customer_id'] = $esale->customer->account_name ?? "";



                $nestedData['qty'] = $esale->qty;
                $nestedData['sub_total'] = $esale->sub_total;
                $nestedData['discount'] = $esale->discount;
                $nestedData['net_total'] = $esale->net_total;
                $nestedData['partialPayment'] = $esale->partialPayment;
                $nestedData['grand_total'] = $esale->grand_total;
                // if ($esale->sale_type == 'Regular') {
                //     $nestedData['sale_type'] = '<span class="btn btn-info">' . $esale->sale_type . '</span>';
                // } else {
                //     $nestedData['sale_type'] = '<span class="btn btn-success">' . $esale->sale_type . '</span>';
                // }

                //  $nestedData['sale_type'] = $esale->sale_type;

                if ($ced != 0 && $esale->sale_type == 'Regular') :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('sale.sale.edit', $esale->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view != 0) {
                        $view_data = '<a href="' . route('sale.sale.show', $esale->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }
                    if ($chalan != 0) {
                        $deliChalan = '<a href="' . route('sale.sale.challan', $esale->id) . '" class="btn btn-xs btn-default"><i class="fas fa-truck" aria-hidden="true"></i></a>';
                    } else {
                        $deliChalan = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('sale.sale.destroy', $esale->id) . '" delete_id="' . $esale->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $esale->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }
                    $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $deliChalan . ' ' . $delete_data;
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
        $result = $this->Sale::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $finalprice = (array_sum($request->total) + $request->carrying_cost + $request->labor_bill) - $request->discount;
            $accountbranch = $request->branch_id ?? 0;
            $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;

            $esale = new $this->Sale();
            $esale->invoice_no = $request->invoice_no;
            $esale->date = $request->date;
            $esale->po_invoice = $request->po_invoice;
            $esale->po_date = $request->po_date;
            // $esale->account_id = $request->account_id ? $request->account_id : '';
            $esale->branch_id =  $request->branch_id;
            $esale->ledger_id = $request->ledger_id;
            $esale->customer_id = $request->customer_id ?? 0;
            $esale->payment_type = $request->payment_type;
            $esale->qty = array_sum($request->qty);
            $esale->sub_total = array_sum($request->total);
            $esale->discount = $request->discount;
            $esale->carrying_cost = $request->carrying_cost;
            $esale->labor_bill = $request->labor_bill;
            $esale->net_total = $finalprice;
            $esale->partialPayment = $request->partialPayment;
            $esale->grand_total = $finalprice;
            $esale->narration = $request->narration;
            $esale->created_by = Auth::user()->id;
            $esale->save();
            $Sale_id = $esale->id;

            $category_id = $request->catName;
            $proName = $request->proName;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;
            $vat = $request->vat;
            $gas_qty = $request->gas_qty;

            for ($i = 0; $i < count($category_id); $i++) {
                $esaleDetail = new sales_Details();
                $esaleDetail->product_id = $proName[$i];
                $esaleDetail->qty = $qty[$i];
                $esaleDetail->purchasetype = $request->purchasetype[$i];
                // $esaleDetail->cty_size = $cty_size[$i] ?? 0;
                // $esaleDetail->gas_qty = $gas_qty[$i] ?? 0;
                $esaleDetail->category_id = $category_id[$i];
                $esaleDetail->branch_id = $request->branch_id;
                $esaleDetail->rate = $subtotal[$i];
                $esaleDetail->vat = $vat[$i];
                $esaleDetail->price = $grand_total[$i];
                $esaleDetail->Sale_id = $Sale_id;
                $esaleDetail->date = $request->date;
                $esaleDetail->save();

                $stock = new Stock();
                $stock->product_id = $proName[$i];
                $stock->quantity = $qty[$i];
                $stock->branch_id = $request->branch_id;
                $stock->unit_price = $subtotal[$i];
                $stock->total_price = $grand_total[$i];
                $stock->general_id = $Sale_id;
                $stock->date = $request->date;
                $stock->status = 'Sale';
                $stock->save();

                $existingCheck = StockSummary::where('product_id', $proName[$i])->where('type', "Branch")->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->first();
                if (!empty($existingCheck->quantity) && $existingCheck->quantity > 0) :
                    $newQty = $existingCheck->quantity - $qty[$i];
                    StockSummary::where('product_id', $proName[$i])->where('type', "Branch")->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->update(array('quantity' => $newQty));
                endif;
            }


            $transaction['payment_invoice'] = $request->invoice_no;
            $transaction['invoice'] = $request->invoice_no;
            $transaction['table_id'] = $Sale_id;
            $transaction['account_id'] = getAccountByUniqueID(18)->id; // sale
            $transaction['type'] = 2;
            $transaction['branch_id'] = $accountbranch;
            $transaction['credit'] = $finalprice;
            $transaction['remark'] = $request->narration;
            $transaction['created_by'] = Auth::id();
            $transaction['created_at'] = $request->date;
            AccountTransaction::create($transaction);

            $transactionPay['payment_invoice'] = $request->invoice_no;
            $transactionPay['invoice'] = $request->invoice_no;
            $transactionPay['table_id'] = $Sale_id;
            $transactionPay['account_id'] = $request->ledger_id; // Account Receivable;
            $transactionPay['type'] = 2;
            $transactionPay['branch_id'] = $accountbranch;
            $transactionPay['debit'] =  $finalprice;
            $transactionPay['remark'] = $request->narration;
            $transactionPay['created_by'] = Auth::id();
            $transactionPay['created_at'] = $request->date;
            AccountTransaction::create($transactionPay);


            if ($request->payment_type == 'Cash') {
                $transection = new Transection();
                $transection->date = $request->date;
                $transection->account_id = $request->account_id;
                $transection->payment_id = $Sale_id;
                $transection->branch_id = $request->branch_id;
                $transection->type = 10;
                // $transection->to_account =  $request->account_id;
                $transection->note = $request->narration;
                $transection->amount = array_sum($request->total) - $request->discount;
                $transection->debit = array_sum($request->total) - $request->discount;
                $transection->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return $esale;
    }

    public function update($request, $id)
    {
        $finalprice = (array_sum($request->price) + $request->carrying_cost + $request->labor_bill) - $request->discount;
        $mailbranhc = $request->branch_id;
        $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;
        // DB::beginTransaction();
        // try {
        $esale = $this->Sale::find($id);
        $esale->invoice_no = $request->invoice_no;
        $esale->date = $request->date;
        $esale->po_date = $request->po_date;
        $esale->po_invoice = $request->po_invoice;
        $esale->branch_id = $request->branch_id;
        $esale->carrying_cost = $request->carrying_cost;
        $esale->labor_bill = $request->labor_bill;
        $esale->ledger_id = $request->ledger_id;
        $esale->payment_type = $request->payment_type;
        $esale->qty = array_sum($request->qty);
        $esale->sub_total = $finalprice;
        $esale->discount = $request->discount;
        $esale->net_total = $finalprice;
        $esale->partialPayment = $request->partialPayment;
        $esale->grand_total = $finalprice;
        $esale->narration = $request->narration;
        $esale->updated_by = Auth::user()->id;
        $esale->save();
        $Sale_id = $esale->id;

        $category_id = $request->catName;
        $proName = $request->proName;
        $subtotal = $request->unitprice;
        $grand_total = $request->price;
        $qty = $request->qty;
        $slDetails = sales_Details::where('sale_id', $id)->get();
        foreach ($slDetails as $slDetail) {
            $quantitys =  StockSummary::where('product_id', $slDetail->product_id)->where('type', "Branch")->where('branch_id', $slDetail->branch_id)->where('purchasetype', $slDetail->purchasetype)->pluck('quantity')->first();
            $stocksum['quantity'] = abs($quantitys + $slDetail->qty);
            StockSummary::where('product_id', $slDetail->product_id)->where('type', "Branch")->where('branch_id', $slDetail->branch_id)->where('purchasetype', $slDetail->purchasetype)->update($stocksum);
        }
        Stock::where('general_id', $id)->Where('status', 'Sale')->forceDelete();
        sales_Details::where('sale_id', $id)->delete();

        $vat = $request->vat;
        $cty_size = $request->cty_size;
        $gas_qty = $request->gas_qty;

        for ($i = 0; $i < count($category_id); $i++) {
            $esaleDetail = new sales_Details();
            $esaleDetail->product_id = $proName[$i];
            $esaleDetail->qty = $qty[$i];
            $esaleDetail->purchasetype = $request->purchasetype[$i];
            $esaleDetail->vat = $vat[$i];
            $esaleDetail->gas_qty = $gas_qty[$i] ?? 0;
            $esaleDetail->category_id = $category_id[$i];
            $esaleDetail->branch_id = $request->branch_id;
            $esaleDetail->rate = $subtotal[$i];
            $esaleDetail->price = $grand_total[$i];
            $esaleDetail->Sale_id = $Sale_id;
            $esaleDetail->date = $request->date;
            $esaleDetail->save();

            $stock = new Stock();
            $stock->product_id = $proName[$i];
            $stock->quantity = $qty[$i];
            $stock->branch_id = $request->branch_id;
            $stock->unit_price = $subtotal[$i];
            $stock->total_price = $grand_total[$i];
            $stock->general_id = $Sale_id;
            $stock->date = $request->date;
            $stock->status = 'Sale';
            $stock->save();

            $existingCheck = StockSummary::where('product_id', $proName[$i])->where('type', "Branch")->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->first();
            if (!empty($existingCheck->quantity) && $existingCheck->quantity > 0) :
                $newQty = $existingCheck->quantity - $qty[$i];
                StockSummary::where('product_id', $proName[$i])->where('type', "Branch")->where('branch_id', $request->branch_id)->where('purchasetype', $request->purchasetype[$i])->update(array('quantity' => $newQty));
            endif;
        }

        customerLedger::where('sale_id', $id)->delete();
        $invoice =  AccountTransaction::where('table_id', $id)->where('type', 2)->first()->invoice;
        AccountTransaction::where('table_id', $id)->where('type', 2)->delete();


        $transaction['invoice'] = $request->invoice_no;
        $transaction['table_id'] = $Sale_id;
        $transaction['account_id'] = getAccountByUniqueID(18)->id; // sale
        $transaction['type'] = 2;
        $transaction['branch_id'] = $mailbranhc;
        $transaction['credit'] = $finalprice;
        $transaction['remark'] = $request->narration;
        $transaction['created_by'] = Auth::id();
        $transaction['created_at'] = $request->date;
        AccountTransaction::create($transaction);

        $transactionPay['invoice'] = $request->invoice_no;
        $transactionPay['table_id'] = $Sale_id;
        $transactionPay['account_id'] = $request->ledger_id; // Account Receivable;
        $transactionPay['type'] = 2;
        $transactionPay['branch_id'] = $mailbranhc;
        $transactionPay['debit'] =  $finalprice;
        $transactionPay['remark'] = $request->narration;
        $transactionPay['created_by'] = Auth::id();
        $transactionPay['created_at'] = $request->date;
        AccountTransaction::create($transactionPay);

        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        // }
        return $esale;
    }

    public function statusUpdate($id, $status)
    {
        $esale = $this->Sale::find($id);
        $esale->status = $status;
        $esale->save();
        return $esale;
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $esale = $this->Sale::find($id);
            $slDetails = sales_Details::where('sale_id', $id)->get();
            foreach ($slDetails as $slDetail) {
                $quantitys =  StockSummary::where('product_id', $slDetail->product_id)->where('type', "Branch")->where('branch_id', $slDetail->branch_id)->where('purchasetype', $slDetail->purchasetype)->pluck('quantity')->first();
                $stocksum['quantity'] = abs($quantitys + $slDetail->qty);
                StockSummary::where('product_id', $slDetail->product_id)->where('type', "Branch")->where('branch_id', $slDetail->branch_id)->where('purchasetype', $slDetail->purchasetype)->update($stocksum);
            }
            Stock::where('general_id', $id)->Where('status', 'Sale')->forceDelete();
            sales_Details::where('sale_id', $id)->delete();

            customerLedger::where('sale_id', $id)->delete();
            AccountTransaction::where('table_id', $id)->where('type', 2)->delete();

            $esale->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return true;
    }
    public function calculateCommission($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $commissionRule = CommissionRule::where('employee_id', ($sale->user->employee_id ?? 0))->first();

        if (!$commissionRule) {
            return false;
        }

        // Default commission amount calculation based on the rule's type
        switch ($commissionRule->commission_type) {
            case 'fixed':
                // Fixed percentage commission
                $commissionAmount = ($sale->total_amount * $commissionRule->fixed_percentage) / 100;
                break;

            case 'tiered':
                // Tiered commission
                $commissionAmount = $this->calculateTieredCommission($sale->total_amount, $commissionRule);
                break;

            case 'product_based':
                // Product-based commission
                $commissionAmount = $this->calculateProductBasedCommission($sale->products, $commissionRule);
                break;

            default:
                return false; // If no valid commission type, return false
        }

        // Create commission entry
        $commission = Commission::create([
            'employee_id' => $sale->user->employee_id,
            'sale_id' => $sale->id,
            'commission_amount' => $commissionAmount,
            'status' => 'pending'
        ]);
    }

    public function calculateTieredCommission($saleAmount, $commissionRule)
    {
        // Example: You can define the tiers in the commission rule or directly here
        $tiers = [
            100 => 5,    // 5% for the first $100
            500 => 10,   // 10% for $101 to $500
            1000 => 15   // 15% for amounts over $500
        ];

        $commissionAmount = 0;
        $remainingAmount = $saleAmount;

        foreach ($tiers as $limit => $percentage) {
            if ($remainingAmount > $limit) {
                $amountInTier = $remainingAmount - $limit;
                $commissionAmount += ($amountInTier * $percentage) / 100;
                $remainingAmount = $limit;
            }
        }

        // Add commission for the remaining amount
        if ($remainingAmount > 0) {
            $commissionAmount += ($remainingAmount * $commissionRule->fixed_percentage) / 100;
        }

        return $commissionAmount;
    }

    public function calculateProductBasedCommission($products, $commissionRule)
    {
        $commissionAmount = 0;

        foreach ($products as $product) {
            // Assuming each product has a commission_percentage field for the salesperson
            $commissionAmount += ($product->price * $product->commission_percentage) / 100;
        }

        return $commissionAmount;
    }
}
