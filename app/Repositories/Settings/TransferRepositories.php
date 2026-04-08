<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use App\Models\BalanceTransferLog;
use App\Models\Customer;
use App\Models\CustomerOpening;
use App\Models\Transection;
use Illuminate\Support\Facades\Auth;

class TransferRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Opening
     */
    private $Transection;
    private $balanceTransferLog;
    /**
     * CourseRepository constructor.
     * @param opening $customerOpening
     */
    public function __construct(Transection $Transection, BalanceTransferLog $balanceTransferLog)
    {
        $this->Transection = $Transection;
        $this->balanceTransferLog = $balanceTransferLog;
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
        return $this->balanceTransferLog::get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'date',
            2 => 'account_id',
        );

        $edit = Helper::roleAccess('settings.transfer.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.transfer.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.transfer.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->balanceTransferLog::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $balanceTransferLog = $this->balanceTransferLog::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->balanceTransferLog::count();
        } else {
            $search = $request->input('search.value');
            $balanceTransferLog = $this->balanceTransferLog::where('date', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->balanceTransferLog::where('date', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($balanceTransferLog) {
            foreach ($balanceTransferLog as $key => $transfer) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $transfer->date;
                $nestedData['from_account_id'] = $transfer->from_account->accountCode . ' - ' . $transfer->from_account->account_name;
                $nestedData['to_account_id'] = $transfer->to_account->accountCode . ' - ' . $transfer->to_account->account_name;
                $nestedData['branch_id'] = $transfer->branch->branchCode . ' - ' . $transfer->branch->name;
                $nestedData['amount'] = $transfer->amount;
                $nestedData['note'] = $transfer->note;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('settings.transfer.edit', $transfer->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view != 0) {
                        $view_data = '<a href="' . route('settings.transfer.show', $transfer->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('settings.transfer.destroy', $transfer->id) . '" delete_id="' . $transfer->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $transfer->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

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
        $result = $this->balanceTransferLog::find($id);
        return $result;
    }

    public function store($request)
    {
        //  dd($request->all());
        $balanceTransferc = new BalanceTransferLog();
        $balanceTransferc->from_account_id = $request->from_account_id;
        $balanceTransferc->to_account_id = $request->to_account_id;
        $balanceTransferc->branch_id = $request->branch_id;
        $balanceTransferc->amount = $request->amount;
        $balanceTransferc->date = $request->date;
        $balanceTransferc->note = $request->note;
        $balanceTransferc->save();
        $paymentid =  $balanceTransferc->id;



        $transection = new transection();
        $transection->account_id = $request->from_account_id;
        $transection->branch_id = $request->branch_id;
        $transection->credit = $request->amount;
        $transection->payment_id = $paymentid;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->type = 2;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        $transection = new transection();
        $transection->account_id = $request->to_account_id;
        $transection->branch_id = $request->branch_id;
        $transection->debit = $request->amount;
        $transection->payment_id = $paymentid;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->type = 2;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        return $transection;
    }

    public function update($request, $id)
    {
        // dd(Transection::where('type','==',2)->orWhere('payment_id','=',$id)->get());
        $balanceTransferc = BalanceTransferLog::find($id);
        $balanceTransferc->from_account_id = $request->from_account_id;
        $balanceTransferc->to_account_id = $request->to_account_id;
        $balanceTransferc->branch_id = $request->branch_id;
        $balanceTransferc->amount = $request->amount;
        $balanceTransferc->date = $request->date;
        $balanceTransferc->note = $request->note;
        $balanceTransferc->save();

        Transection::orWhere('payment_id', '=', $id)->where('type', '=', 2)->forceDelete();

        $transection = new transection();
        $transection->account_id = $request->from_account_id;
        $transection->branch_id = $request->branch_id;
        $transection->credit = $request->amount;
        $transection->payment_id = $id;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->type = 2;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        $transection = new transection();
        $transection->account_id = $request->to_account_id;
        $transection->branch_id = $request->branch_id;
        $transection->debit = $request->amount;
        $transection->payment_id = $id;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->type = 2;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        return $transection;
    }

    public function statusUpdate($id, $status)
    {
        $customerOpening = $this->opening::find($id);
        $customerOpening->status = $status;
        $customerOpening->save();
        return $customerOpening;
    }

    public function destroy($id)
    {
        $customerOpening = $this->balanceTransferLog::find($id);
        $customerOpening->delete();
        Transection::orWhere('payment_id', '=', $id)->where('type', '=', 2)->delete();
        return true;
    }
}
