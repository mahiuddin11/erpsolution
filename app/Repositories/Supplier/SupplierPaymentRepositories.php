<?php

namespace App\Repositories\Supplier;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\supplierLedger;
use App\Models\Transection;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;

class SupplierPaymentRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $supplierLedger;
    /**
     * CourseRepository constructor.
     * @param supplier $supplier
     */
    public function __construct(supplierLedger $supplierLedger)
    {
        $this->supplierLedger = $supplierLedger;
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
        $result = $this->supplierLedger::latest()->get();
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
            1 => 'account_id',
            1 => 'supplier_id',
        );

        $edit = Helper::roleAccess('payment.supplier.edit') ? 0 : 0;
        $delete = Helper::roleAccess('payment.supplier.destroy') ? 1 : 0;
        $view = Helper::roleAccess('payment.supplier.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->supplierLedger::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $supplierLedger = $this->supplierLedger::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->supplierLedger::count();
        } else {
            $search = $request->input('search.value');
            $supplierLedger = $this->supplierLedger::where('account_id', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->supplierLedger::where('account_id', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($supplierLedger) {
            foreach ($supplierLedger as $key => $supplier) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $supplier->date;
                $nestedData['branch_id'] = $supplier->branch->name;
                // if ($supplier->account_id) {
                //     $nestedData['account_id'] = $supplier->accounts->account_name;
                // }
                $nestedData['supplier_id'] = $supplier->supplier->name;
                $nestedData['payment_type'] = $supplier->payment_type;
                $nestedData['debit'] = $supplier->debit;
                $nestedData['credit'] = $supplier->credit;
                // $nestedData['total_due'] = $supplier->total_due;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('payment.supplier.edit', $supplier->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('payment.supplier.show', $supplier->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('payment.supplier.destroy', $supplier->id) . '" delete_id="' . $supplier->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $supplier->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->supplierLedger::find($id);
        return $result;
    }

    public function store($request)
    {
         try {
            $supplierLedger = new supplierLedger();
            $supplierLedger->date = $request->date;
            $supplierLedger->branch_id = $request->branch_id;
    
            $supplierLedger->supplier_id = $request->supplier_id;
    
            $supplierLedger->credit = $request->amount;
            $supplierLedger->purchase_id = $request->invoice_id;
            $supplierLedger->account_id = $request->account_id;
            $supplierLedger->payment_type = 'Collect';
            $supplierLedger->created_by = Auth::user()->id;
            $supplierLedger->save();
            $payment_id = $supplierLedger->id;
    
            $invoice = AccountTransaction::accountInvoice();
            $transactionPay['invoice'] = $invoice;
            $transactionPay['table_id'] = $payment_id;
            $transactionPay['account_id'] = 14; // -> Account Payable
            $transactionPay['type'] = 4;
            $transactionPay['branch_id'] = $request->branch_id;
            $transactionPay['debit'] =  $request->amount;
            $transactionPay['remark'] = $request->note;
            $transactionPay['created_by'] = Auth::id();
            $transactionPay['supplier_id'] = $request->supplier_id;
            AccountTransaction::create($transactionPay);
    
            $transaction['invoice'] = $invoice;
            $transaction['table_id'] = $payment_id;
            $transaction['account_id'] = $request->account_id;
            $transaction['type'] = 4;
            $transaction['branch_id'] = $request->branch_id;
            $transaction['credit'] = $request->amount;
            $transaction['remark'] = $request->note;
            $transaction['created_by'] = Auth::id();
            $transaction['supplier_id'] = $request->supplier_id;
            AccountTransaction::create($transaction);
    
    
            $transection = new transection();
            $transection->account_id = $request->account_id;
            $transection->branch_id = $request->branch_id;
            $transection->credit = $request->amount;
            $transection->amount = $request->amount;
            $transection->note = $request->note;
            $transection->date = $request->date;
            $transection->payment_id = $payment_id;
            $transection->type = 6;
            $transection->created_by = Auth::user()->id;
            $transection->save();
          return $transection;

         } catch (\Throwable $th) {
            DB::rollback();
            redirect()->back()->with('error', 'Something Wrong Please try again'. $th->getMessage());
         }
        // dd($request->all());

    }

    public function update($request, $id)
    {
        supplierLedger::where('id', $id)->delete();
        $supplierLedger = new supplierLedger();
        $supplierLedger->date = $request->date;
        $supplierLedger->supplier_branch_id = $request->supplier_branch_id;
        $supplierLedger->account_branch_id = $request->account_branch_id;
        $supplierLedger->supplier_id = $request->supplier_id;
        $supplierLedger->supplier_branch_id = $request->supplier_branch_id;
        $supplierLedger->debit = $request->amount;
        $supplierLedger->purchase_id = $request->invoice_id;
        $supplierLedger->payment_type = 'Collect';
        $supplierLedger->updated_by = Auth::user()->id;
        $supplierLedger->save();
        $payment_id = $supplierLedger->id;

        AccountTransaction::where('type',4)->where('table_id',$payment_id)->delete();
        
        $invoice = AccountTransaction::accountInvoice();
        $transactionPay['invoice'] = $invoice;
        $transactionPay['table_id'] = $payment_id;
        $transactionPay['account_id'] = 14; // -> Account Payable
        $transactionPay['type'] = 4;
        $transactionPay['branch_id'] = $request->branch_id;
        $transactionPay['debit'] =  $request->amount;
        $transactionPay['remark'] = $request->note;
        $transactionPay['created_by'] = Auth::id();
        $transactionPay['supplier_id'] = $request->supplier_id;
        AccountTransaction::create($transactionPay);

        $transaction['invoice'] = $invoice;
        $transaction['table_id'] = $payment_id;
        $transaction['account_id'] = $request->account_id;
        $transaction['type'] = 4;
        $transaction['branch_id'] = $request->branch_id;
        $transaction['credit'] = $request->amount;
        $transaction['remark'] = $request->note;
        $transaction['created_by'] = Auth::id();
        $transaction['supplier_id'] = $request->supplier_id;
        AccountTransaction::create($transaction);

        $transection = new transection();
        $transection->from_account = $request->account_id;
        $transection->branch_id = $request->account_branch_id;
        $transection->credit = $request->amount;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->payment_id = $payment_id;
        $transection->type = 6;
        $transection->updated_by = Auth::user()->id;
        $transection->save();

        return $transection;
    }

    public function statusUpdate($id, $status)
    {
        $supplier = $this->supplierLedger::find($id);
        $supplier->status = $status;
        $supplier->save();
        return $supplier;
    }

    public function destroy($id)
    {
        $supplier = $this->supplierLedger::find($id);
        AccountTransaction::where('type',4)->where('table_id',$id)->delete();
        $supplier->delete();
        return true;
    }
}
