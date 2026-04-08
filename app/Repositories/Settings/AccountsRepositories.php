<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Accounts;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AccountsRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Accounts
     */
    private $Accounts;
    /**
     * CourseRepository constructor.
     * @param Accounts $Accounts
     */
    public function __construct(ChartOfAccount $Accounts)
    {
        $this->Accounts = $Accounts;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllBranch()
    {
        return  $this->Accounts::get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'account_name',
            2 => 'account_code',
        );

        $edit = Helper::roleAccess('settings.account.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.account.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.account.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->Accounts::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $Accounts = $this->Accounts::where('branch_id', auth()->user()->branch_id ?? 1)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->Accounts::count();
        } else {
            $search = $request->input('search.value');
            $Accounts = $this->Accounts::where('account_name', 'like', "%{$search}%")->orWhere('accountCode', 'like', "%{$search}%")->orWhere('account_code', 'like', "%{$search}%")
                ->where('branch_id', auth()->user()->branch_id ?? 1)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->Accounts::where('account_name', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($Accounts) {
            foreach ($Accounts as $key => $Account) {
                // dd($Account->branch);
                $nestedData['id'] = $key + 1;
                $nestedData['account_name'] = $Account->account_name;
                $nestedData['account_code'] = $Account->account_code;
                $nestedData['accountCode'] = $Account->accountCode;


                if ($Account->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('settings.account.status', [$Account->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('settings.account.status', [$Account->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('settings.account.edit', $Account->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('settings.account.show', $Account->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.account.destroy', $Account->id) . '" delete_id="' . $Account->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $Account->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->Accounts::find($id);
        return $result;
    }

    public function store($request)
    {
        $Accounts = new $this->Accounts();
        $Accounts->account_name = $request->account_name;
        $Accounts->accountCode = $request->accountCode;
        $Accounts->bank_name = $request->bank_name;
        $Accounts->parent_id = $request->parent_id;
        $Accounts->bill_by_bill = $request->billbybillpayment ?? 0;
        $Accounts->depreciation = $request->depreciation ?? 0;
        $Accounts->account_code = $request->account_code;
        $Accounts->status = 'Active';
        $Accounts->created_by = Auth::user()->id;
        $Accounts->save();
        return $Accounts;
    }

    public function update($request, $id)
    {
        // dd($request->all());
        $Accounts = $this->Accounts::findOrFail($id);
        $Accounts->account_name = $request->account_name;
        $Accounts->accountCode = $request->account_code;
        $Accounts->parent_id = $request->parent_id;
        $Accounts->bank_name = $request->bank_name;
        $Accounts->bill_by_bill = $request->billbybillpayment ?? 0;
        $Accounts->depreciation = $request->depreciation ?? 0;
        $Accounts->status = 'Active';
        $Accounts->updated_by = Auth::user()->id;
        $Accounts->save();
        return $Accounts;
    }

    public function statusUpdate($id, $status)
    {
        $Accounts = $this->Accounts::find($id);
        $Accounts->status = $status;
        $Accounts->save();
        return $Accounts;
    }

    public function destroy($id)
    {
        $Accounts = $this->Accounts::find($id);
        $Accounts->delete();
        return true;
    }
}
