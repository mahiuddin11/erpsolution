<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\Adjust;
use App\Models\customerLedger;
use App\Models\ReturnDeposit;
use App\Models\Transection;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AdjustRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $adjust;
    /**
     * CourseRepository constructor.
     * @param adjust $adjust
     */
    public function __construct(Adjust $adjust)
    {
        $this->adjust = $adjust;
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
        $result = $this->adjust::latest()->get();
        return $result;
    }


    /**
     * @param $request
     * @return mixed
     */

    public function getList($request)
    {

        $type = session()->get('type');
        
        $columns = array(
            0 => 'id',
            1 => 'account_id',
            1 => 'customer_id',
            1 => 'payment_type',
        );


        if ($type == 1) {
            $edit = Helper::roleAccess('inventorySetup.adjustDeposit.edit') ? 1 : 0;
            $delete = Helper::roleAccess('inventorySetup.adjustDeposit.destroy') ? 1 : 0;
            $view = Helper::roleAccess('inventorySetup.adjustDeposit.show') ? 0 : 0;
            $ced = $edit + $delete + $view;
            $totalData = $this->adjust::count();
        }

        if ($type == 2) {
            $edit = Helper::roleAccess('inventorySetup.adjustCredit.edit') ? 1 : 0;
            $delete = Helper::roleAccess('inventorySetup.adjustCredit.destroy') ? 1 : 0;
            $view = Helper::roleAccess('inventorySetup.adjustCredit.show') ? 0 : 0;
            $ced = $edit + $delete + $view;
            $totalData = $this->adjust::count();
        }




        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if ($type == 1) {
            if (empty($request->input('search.value'))) {
                $adjusts = $this->adjust::offset($start)
                    ->where('payment_type', 'Deposit')
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    //->orderBy('status', 'desc')
                    ->get();
                $totalFiltered = $this->adjust::count();
            } else {
                $search = $request->input('search.value');
                $adjusts = $this->adjust::where('account_id', 'like', "%{$search}%")
                    ->where('payment_type', 'Deposit')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    // ->orderBy('status', 'desc')
                    ->get();
                $totalFiltered = $this->adjust::where('account_id', 'like', "%{$search}%")->count();
            }
        }
        if ($type == 2) {
            if (empty($request->input('search.value'))) {
                $adjusts = $this->adjust::offset($start)
                    ->where('payment_type', 'Credit')
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    //->orderBy('status', 'desc')
                    ->get();
                $totalFiltered = $this->adjust::count();
            } else {
                $search = $request->input('search.value');
                $adjusts = $this->adjust::where('account_id', 'like', "%{$search}%")
                    ->where('payment_type', 'Credit')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    // ->orderBy('status', 'desc')
                    ->get();
                $totalFiltered = $this->adjust::where('account_id', 'like', "%{$search}%")->count();
            }
        }





        $data = array();
        if ($adjusts) {
            foreach ($adjusts as $key => $adjust) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $adjust->date;
                if ($adjust->account_id > 0) {
                    $nestedData['account_id'] = $adjust->account->accountCode . ' - ' . $adjust->account->account_name;
                } else {
                    $nestedData['account_id'] = 'N/A';
                }
                $nestedData['customer_id'] = $adjust->customer->customerCode . ' - ' . $adjust->customer->name;
                $nestedData['branch_id'] = $adjust->branch->branchCode . ' - ' . $adjust->branch->name;
                $nestedData['expire_date'] = $adjust->expire_date;
                $nestedData['payment_type'] = $adjust->payment_type;
                $nestedData['debit'] = $adjust->debit;
                $nestedData['credit'] = $adjust->credit;
                $nestedData['note'] = $adjust->note;


                if ($type == 1) {
                    if ($ced != 0) :
                        if ($edit != 0)
                            $edit_data = '<a href="' . route('inventorySetup.adjustDeposit.edit', $adjust->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                        else
                            $edit_data = '';
                        if ($view != 0)
                            $view_data = '<a href="' . route('inventorySetup.adjustDeposit.show', $adjust->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                        else
                            $view_data = '';
                        if ($delete != 0)
                            $delete_data = '<a delete_route="' . route('inventorySetup.adjustDeposit.destroy', $adjust->id) . '" delete_id="' . $adjust->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $adjust->id . '"><i class="fa fa-times"></i></a>';
                        else
                            $delete_data = '';
                        $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;
                    else :
                        $nestedData['action'] = '';
                    endif;
                    $data[] = $nestedData;
                }
                if ($type == 2) {
                    if ($ced != 0) :
                        if ($edit != 0)
                            $edit_data = '<a href="' . route('inventorySetup.adjustCredit.edit', $adjust->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                        else
                            $edit_data = '';
                        if ($view != 0)
                            $view_data = '<a href="' . route('inventorySetup.adjustCredit.show', $adjust->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                        else
                            $view_data = '';
                        if ($delete != 0)
                            $delete_data = '<a delete_route="' . route('inventorySetup.adjustCredit.destroy', $adjust->id) . '" delete_id="' . $adjust->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $adjust->id . '"><i class="fa fa-times"></i></a>';
                        else
                            $delete_data = '';
                        $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;
                    else :
                        $nestedData['action'] = '';
                    endif;
                    $data[] = $nestedData;
                }
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

    public function getreturnList($request)
    {

        $type = session()->get('type');
        $columns = array(
            0 => 'id',
            1 => 'account_id',
            1 => 'customer_id',
            1 => 'payment_type',
        );


        $edit = Helper::roleAccess('inventorySetup.returnDeposit.returncreate') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.returnDeposit.returnshow') ? 0 : 0;
        $ced = $edit  + $view;
        $totalData = ReturnDeposit::count();


        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');


        if (empty($request->input('search.value'))) {
            $adjusts = ReturnDeposit::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = ReturnDeposit::count();
        } else {
            $search = $request->input('search.value');
            $adjusts = ReturnDeposit::where('account_id', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = ReturnDeposit::where('account_id', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($adjusts) {
            foreach ($adjusts as $key => $adjust) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $adjust->date;

                $nestedData['account_id'] = $adjust->account_id ? $adjust->account->accountCode . ' - ' . $adjust->account->account_name : "";

                $nestedData['customer_id'] = $adjust->customer->customerCode . ' - ' . $adjust->customer->name;
                $nestedData['branch_id'] = $adjust->branch->branchCode . ' - ' . $adjust->branch->name;
                $nestedData['payment_type'] = $adjust->payment_type;
                $nestedData['amount'] = $adjust->amount;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.returnDeposit.returnedit', $adjust->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.returnDeposit.returnshow', $adjust->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';

                    $nestedData['action'] = $edit_data . ' ' . $view_data;
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
        $result = $this->adjust::find($id);
        return $result;
    }

    public function returnstore($request)
    {
        $return = new ReturnDeposit();
        $return->date = $request->date;
        $return->customer_id = $request->customer_id;
        $return->account_id = $request->account_id;
        $return->branch_id = $request->branch_id;
        $return->amount = $request->credit;
        $return->payment_type = "Return";
        $return->created_by = Auth::user()->id;
        $return->save();

        $returndeposite = $return->id;

        $customerLedger = new customerLedger();
        $customerLedger->date = $request->date;
        $customerLedger->customer_id = $request->customer_id;
        $customerLedger->adjust_id = $returndeposite;
        $customerLedger->account_id = $request->account_id;
        $customerLedger->branch_id = $request->branch_id;
        $customerLedger->credit = $request->credit;
        $customerLedger->payment_type = "Return";
        $customerLedger->created_by = Auth::user()->id;
        $customerLedger->save();


        $transection = new transection();
        $transection->account_id = $request->account_id;
        $transection->branch_id = $request->branch_id;
        $transection->credit = $request->credit;
        $transection->amount = $request->credit;
        $transection->payment_id = $returndeposite;
        $transection->date = $request->date;
        $transection->type = 14;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        AccountTransaction::where('type', 10)->where('table_id', $returndeposite)->delete();
        $invoice = AccountTransaction::accountInvoice();
        $transactionPay['invoice'] = $invoice;
        $transactionPay['table_id'] = $returndeposite;
        $transactionPay['account_id'] = 18; // -> deposite
        $transactionPay['type'] = 10;
        $transactionPay['branch_id'] = $request->branch_id;
        $transactionPay['debit'] =  $request->credit;
        $transactionPay['created_by'] = Auth::id();
        $transactionPay['customer_id'] = $request->customer_id;
        AccountTransaction::create($transactionPay);

        $transaction['invoice'] = $invoice;
        $transaction['table_id'] = $returndeposite;
        $transaction['account_id'] = $request->account_id;
        $transaction['type'] = 10;
        $transaction['branch_id'] = $request->branch_id;
        $transaction['credit'] = $request->credit;
        $transaction['customer_id'] = $request->customer_id;
        $transaction['created_by'] = Auth::id();
        AccountTransaction::create($transaction);

        return $customerLedger;
    }

    public function returnupdate($request, $id)
    {
        $return = ReturnDeposit::find($id);
        $return->date = $request->date;
        $return->customer_id = $request->customer_id;
        $return->account_id = $request->account_id;
        $return->branch_id = $request->branch_id;
        $return->amount = $request->credit;
        $return->updated_by = Auth::user()->id;
        $return->save();
        $returndeposite = $return->id;

        $customerLedger = customerLedger::where('payment_type', 'Return')->where('adjust_id', $id)->delete();

        $customerLedger = new customerLedger();
        $customerLedger->date = $request->date;
        $customerLedger->customer_id = $request->customer_id;
        $customerLedger->account_id = $request->account_id;
        $customerLedger->adjust_id = $returndeposite;
        $customerLedger->branch_id = $request->branch_id;
        $customerLedger->credit = $request->credit;
        $customerLedger->payment_type = "Return";
        $customerLedger->updated_by = Auth::user()->id;
        $customerLedger->save();

        $transection = transection::where('payment_id', $id)->where('type', 14)->forceDelete();

        $transection = new transection();
        $transection->account_id = $request->account_id;
        $transection->branch_id = $request->branch_id;
        $transection->credit = $request->credit;
        $transection->amount = $request->credit;
        $transection->payment_id = $returndeposite;
        $transection->date = $request->date;
        $transection->type = 14;
        $transection->updated_by = Auth::user()->id;
        $transection->save();


        $invoice = AccountTransaction::accountInvoice();
        $transactionPay['invoice'] = $invoice;
        $transactionPay['table_id'] = $returndeposite;
        $transactionPay['account_id'] = 18; // -> deposite
        $transactionPay['type'] = 10;
        $transactionPay['branch_id'] = $request->branch_id;
        $transactionPay['debit'] =  $request->credit;
        $transactionPay['created_by'] = Auth::id();
        $transactionPay['customer_id'] = $request->customer_id;
        AccountTransaction::create($transactionPay);

        $transaction['invoice'] = $invoice;
        $transaction['table_id'] = $returndeposite;
        $transaction['account_id'] = $request->account_id;
        $transaction['type'] = 10;
        $transaction['branch_id'] = $request->branch_id;
        $transaction['credit'] = $request->credit;
        $transaction['customer_id'] = $request->customer_id;
        $transaction['created_by'] = Auth::id();
        AccountTransaction::create($transaction);

        return $customerLedger;
    }


    public function store($request)
    {
        // dd('sadf');
        try {
            $adjust = new adjust();
            $adjust->date = $request->date;
            $adjust->account_id = $request->account_id;
            $adjust->branch_id = $request->branch_id;
            $adjust->customer_id = $request->customer_id;
            if ($request->payment_type == "Credit") {
                $adjust->expire_date = $request->expire_date;
            }
            $adjust->bank_name = $request->bank_name;
            $adjust->check_date = $request->check_date;
            $adjust->check_no = $request->check_no;
            $adjust->payment_type = $request->payment_type;
            $adjust->debit = $request->amount;
            $adjust->note = $request->note;
            $adjust->created_by = Auth::user()->id;
            $adjust->save();
            $payment_id = $adjust->id;

            if ($request->payment_type == "Deposit") {
                $transection = new transection();

                $transection->account_id = $request->account_id;
                $transection->branch_id = $request->branch_id;
                $transection->debit = $request->amount;
                $transection->amount = $request->amount;
                $transection->note = $request->note;
                $transection->payment_id = $payment_id;
                $transection->date = $request->date;
                $transection->type = 5;
                $transection->created_by = Auth::user()->id;
                $transection->save();

                $invoice = AccountTransaction::accountInvoice();
                $transactionPay['invoice'] = $invoice;
                $transactionPay['table_id'] = $payment_id;
                $transactionPay['account_id'] = 18; // -> deposite
                $transactionPay['type'] = 9;
                $transactionPay['branch_id'] = $request->branch_id;
                $transactionPay['credit'] =  $request->amount;
                $transactionPay['remark'] = $request->note;
                $transactionPay['created_by'] = Auth::id();
                $transactionPay['customer_id'] = $request->customer_id;
                AccountTransaction::create($transactionPay);

                $transaction['invoice'] = $invoice;
                $transaction['table_id'] = $payment_id;
                $transaction['account_id'] = $request->account_id;
                $transaction['type'] = 9;
                $transaction['branch_id'] = $request->branch_id;
                $transaction['debit'] = $request->amount;
                $transaction['remark'] = $request->note;
                $transaction['customer_id'] = $request->customer_id;
                $transaction['created_by'] = Auth::id();
                AccountTransaction::create($transaction);
            }

            if ($request->payment_type == "Deposit") {
                $customerLedger = new customerLedger();
                $customerLedger->date = $request->date;
                $customerLedger->customer_id = $request->customer_id;
                $customerLedger->adjust_id = $payment_id;
                $customerLedger->account_id = $request->account_id;
                $customerLedger->branch_id = $request->branch_id;
                $customerLedger->debit = $request->amount;
                $customerLedger->payment_type = $request->payment_type;
                $customerLedger->created_by = Auth::user()->id;
                $customerLedger->save();
            }
            return $payment_id;
        } catch (\Throwable $th) {
            DB::rollback();
            session()->flash('error', 'somthing was wrong!!');
            return redirect()->route('inventorySetup.adjustDeposit.index');
        }



        return $adjust;
    }

    public function update($request, $id)
    {
        $adjust = Adjust::find($id);
        $adjust->date = $request->date;
        $adjust->account_id = $request->account_id;
        $adjust->branch_id = $request->branch_id;
        $adjust->customer_id = $request->customer_id;
        $adjust->bank_name = $request->bank_name;
        $adjust->check_date = $request->check_date;
        $adjust->check_no = $request->check_no;
        $adjust->expire_date = $request->expire_date;
        $adjust->payment_type = $request->payment_type;
        $adjust->debit = $request->amount;
        $adjust->note = $request->note;
        $adjust->created_by = Auth::user()->id;
        $adjust->save();
        $payment_id = $adjust->id;

        if ($request->payment_type == "Deposit") {

            $transection = Transection::where('payment_id', $payment_id)->first();
            $transection->account_id = $request->account_id;
            $transection->branch_id = $request->branch_id;
            $transection->debit = $request->amount;
            $transection->amount = $request->amount;
            $transection->note = $request->note;
            $transection->payment_id = $payment_id;
            $transection->date = $request->date;
            $transection->type = 5;
            $transection->updated_by = Auth::user()->id;
            $transection->save();

            AccountTransaction::where('type', 9)->where('table_id', $payment_id)->delete();
            $invoice = AccountTransaction::accountInvoice();
            $transactionPay['invoice'] = $invoice;
            $transactionPay['table_id'] = $payment_id;
            $transactionPay['account_id'] = 18; // -> deposite
            $transactionPay['type'] = 9;
            $transactionPay['branch_id'] = $request->branch_id;
            $transactionPay['credit'] =  $request->amount;
            $transactionPay['remark'] = $request->note;
            $transactionPay['created_by'] = Auth::id();
            $transactionPay['customer_id'] = $request->customer_id;
            AccountTransaction::create($transactionPay);

            $transaction['invoice'] = $invoice;
            $transaction['table_id'] = $payment_id;
            $transaction['account_id'] = $request->account_id;
            $transaction['type'] = 9;
            $transaction['branch_id'] = $request->branch_id;
            $transaction['debit'] = $request->amount;
            $transaction['remark'] = $request->note;
            $transaction['customer_id'] = $request->customer_id;
            $transaction['created_by'] = Auth::id();
            AccountTransaction::create($transaction);
        }
        if ($request->payment_type == "Deposit") {
            $customerLedger =  customerLedger::where('adjust_id', $payment_id)->first();
            $customerLedger->date = $request->date;
            $customerLedger->customer_id = $request->customer_id;
            $customerLedger->account_id = $request->account_id;
            $customerLedger->branch_id = $request->branch_id;
            $customerLedger->debit = $request->amount;
            $customerLedger->payment_type = $request->payment_type;
            $customerLedger->updated_by = Auth::user()->id;
            $customerLedger->save();
        }

        return $adjust;
    }

    public function statusUpdate($id, $status)
    {

        $adjust = $this->adjust::find($id);
        $adjust->status = $status;
        $adjust->save();
        return $adjust;
    }

    public function destroy($id)
    {
        $adjust = $this->adjust::find($id);
        $delete['deleted_by'] = Auth::user()->id;
        $this->adjust::updated($delete);
        AccountTransaction::where('type', 9)->where('table_id', $adjust->id)->delete();
        $adjust->delete();

        $getid = Transection::where('payment_id', $id)->first();
        $deletetr = Transection::find($getid->id);
        $deletetr->delete();

        return true;
    }
}
