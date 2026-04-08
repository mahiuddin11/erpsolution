<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Opening;
use App\Models\OpeningBalance;
use App\Models\Transection;
use Illuminate\Support\Facades\Auth;

class OpeningRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Opening
     */
    private $openingbalance;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(OpeningBalance $OpeningBalance)
    {
        $this->openingbalance = $OpeningBalance;
        $this->user_id = 1;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOpening()
    {
        return $this->openingbalance::get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'amount',
        );

        $edit = Helper::roleAccess('settings.openingbalance.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.openingbalance.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.openingbalance.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->openingbalance::count();
        // dd($totalData);
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $Transection = $this->openingbalance::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->openingbalance::count();
        } else {
            $search = $request->input('search.value');
            $Transection = $this->openingbalance::where('account_id', 'like', "%{$search}%")->orWhere('amount', 'like', "%{$search}%")->orWhere('date', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->openingbalance::where('amount', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($Transection) {
            foreach ($Transection as $key => $opening) {
                $nestedData['id'] = $key + 1;
                $nestedData['account_id'] =  $opening->account->accountCode ? $opening->account->accountCode . ' - ' . $opening->account->account_name : "N/A";
                $nestedData['amount'] = $opening->amount;
                $nestedData['note'] = $opening->note;
                $nestedData['date'] = $opening->date_;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('settings.openingbalance.edit', $opening->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view != 0) {
                        $view_data = '<a href="' . route('settings.openingbalance.show', $opening->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('settings.openingbalance.destroy', $opening->id) . '" delete_id="' . $opening->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $opening->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->openingbalance::find($id);
        return $result;
    }

    public function store($request)
    {
        foreach ($request->accounts as $id => $data) {
            $account = ChartOfAccount::find($id);
            $account->opening_balance = empty($data['debit']) ? $data['credit']:$data['debit'];
            $account->balance_type =  empty($data['debit']) ? "credit":"debit";
            $account->save();
        }

        return $account;
    }

    public function update($request, $id)
    {
        $transection = OpeningBalance::find($id);
        $transection->account_id = $request->to_account_id;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date_ = $request->date;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        return $transection;
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->openingbalance::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $opening = $this->openingbalance::find($id);
        AccountTransaction::where('table_id', $id)->where('type', 12)->delete();
        $opening->delete();
        return true;
    }
}
