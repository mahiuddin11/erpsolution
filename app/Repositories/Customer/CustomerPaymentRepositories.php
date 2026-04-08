<?php

namespace App\Repositories\Customer;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\customer;
use App\Models\customerLedger;
use App\Models\Transection;
use App\Models\Branch;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CustomerPaymentRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $customerLedger;
    private $Transection;
    /**
     * CourseRepository constructor.
     * @param customer $customer
     */
    public function __construct(customerLedger $customerLedger, transection $Transection)
    {
        $this->customerLedger = $customerLedger;
        $this->Transection = $Transection;
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
        $result = $this->customerLedger::latest()->get();
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
            1 => 'customer_id',
        );

        $edit = Helper::roleAccess('payment.customer.edit') ? 1 : 0;
        $delete = Helper::roleAccess('payment.customer.destroy') ? 1 : 0;
        $view = Helper::roleAccess('payment.customer.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->customerLedger::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $customerLedger = $this->customerLedger::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->customerLedger::count();
        } else {
            $search = $request->input('search.value');
            $customerLedger = $this->customerLedger::where('account_id', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->customerLedger::where('account_id', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($customerLedger) {
            foreach ($customerLedger as $key => $customer) {

                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $customer->date;
                $nestedData['branch_id'] = $customer->branch->name;
                // $nestedData['account_id'] = $customer->accounts->account_name;
                $nestedData['customer_id'] = $customer->customer->name;
                $nestedData['payment_type'] = $customer->payment_type;
                $nestedData['debit'] = $customer->debit;
                $nestedData['credit'] = $customer->credit;
                // $nestedData['total_due'] = $customer->total_due;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('payment.customer.edit', $customer->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('payment.customer.show', $customer->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('payment.customer.destroy', $customer->id) . '" delete_id="' . $customer->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $customer->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->customerLedger::find($id);
        return $result;
    }

    public function store($request)
    {

        //  dd($request->all());

        $customerLedger = new customerLedger();
        $customerLedger->date = $request->date;
        $customerLedger->branch_id = $request->branch_id;


        $customerLedger->bank_name = $request->bank_name;
        $customerLedger->check_date = $request->check_date;
        $customerLedger->check_no = $request->check_no;

        $customerLedger->customer_id = $request->customer_id;
        $customerLedger->account_id = $request->account_id;
        $customerLedger->debit = $request->amount;
        $customerLedger->sale_id = $request->invoice_id;
        $customerLedger->payment_type = 'Collect';
        $customerLedger->created_by = Auth::user()->id;
        $customerLedger->save();
        $payment_id = $customerLedger->id;

        $transection = new transection();
        $transection->account_id = $request->account_id;
        $transection->branch_id = $request->branch_id;
        $transection->debit = $request->amount;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->payment_id = $payment_id;
        $transection->type = 8;
        $transection->created_by = Auth::user()->id;
        $transection->save();
        return $transection;
    }

    public function update($request, $id)
    {
        customerLedger::where('id', $id)->delete();
        $customerLedger = new customerLedger();
        $customerLedger->date = $request->date;
        $customerLedger->branch_id = $request->branch_id;

        $customerLedger->bank_name = $request->bank_name;
        $customerLedger->check_date = $request->check_date;
        $customerLedger->check_no = $request->check_no;
        // $customerLedger->customer_branch_id = $request->customer_branch_id;
        $customerLedger->customer_id = $request->customer_id;
        $customerLedger->account_id = $request->account_id;
        $customerLedger->debit = $request->amount;
        $customerLedger->sale_id = $request->invoice_id;
        $customerLedger->payment_type = 'Collect';
        $customerLedger->created_by = Auth::user()->id;
        $customerLedger->save();
        $payment_id = $customerLedger->id;

        Transection::where('id', $id)->delete();
        $transection = new transection();
        $transection->from_account = $request->account_id;
        $transection->branch_id = $request->account_branch_id;
        $transection->credit = $request->amount;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->payment_id = $payment_id;
        $transection->type = 8;
        $transection->created_by = Auth::user()->id;
        $transection->save();
        return $transection;
    }

    public function statusUpdate($id, $status)
    {
        $customer = $this->customerLedger::find($id);
        $customer->status = $status;
        $customer->save();
        return $customer;
    }

    public function destroy($id)
    {
        $customer = $this->customerLedger::find($id);
        $customer->delete();
        return true;
    }
}
