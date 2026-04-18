<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\Attendance;
use App\Models\Lone;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\CashReq;
use App\Models\Transection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CashApplicationRepositories
{
   
    private $cashReq;
   
    public function __construct(CashReq $cashReq)
    {
        $this->cashReq = $cashReq;
    }

   
    public function getAllList()
    {
        $result = $this->cashReq::latest()->get();
        return $result;
    }


    /**
     * @param $request
     * @return mixed
     */

  

    public function getList($request)
    {
        $columns = [
            0 => 'id',
            1 => 'id'
        ];

        $edit = Helper::roleAccess('hrm.cashapplicaon.edit') ? 1 : 0;
        $delete = Helper::roleAccess('hrm.cashapplicaon.destroy') ? 1 : 0;
        $view = Helper::roleAccess('hrm.cashapplicaon.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';
        $search = $request->input('search.value');

        // base query
        $query = $this->cashReq::with(['employee']);

        // role filter
        if (auth()->user()->type != "Admin") {
            $query->where("employee_id", auth()->user()->employee->id ?? 0);
        }

        // total records
        $totalData = $query->count();

        // search
        if (!empty($search)) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // filtered count
        $totalFiltered = $query->count();

        // data fetch
        $Lone = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];

        foreach ($Lone as $key => $value) {

            $nestedData['id'] = $start + $key + 1;
            $nestedData['employee_id'] = $value->employee->name ?? '';
            $nestedData['amount'] = $value->amount;
            $nestedData['reason'] = $value->reason;
            $nestedData['status'] = $value->status;

            $edit_data = $view_data = $delete_data = '';

            if ($edit != 0) {
                $edit_data = '<a href="' . route('hrm.cashapplicaon.edit', $value->id) . '" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>';
            }

            if ($view != 0) {
                $view_data = '<a href="' . route('hrm.cashapplicaon.show', $value->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>';
            }

            if ($delete != 0) {
                $delete_data = '<a delete_route="' . route('hrm.cashapplicaon.destroy', $value->id) . '" class="btn btn-xs btn-danger delete_row"><i class="fa fa-times"></i></a>';
            }

            $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;

            $data[] = $nestedData;
        }

        return [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
    }
    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {
        $result = $this->cashReq::find($id);
        return $result;
    }

    public function store($request)
    {
        $cash_req = new $this->cashReq;

        $cash_req->employee_id = $request->employee_id;
        $cash_req->amount = $request->amount;
        $cash_req->reason = $request->reason;
        $cash_req->save();
        return $cash_req;
    }

    public function update($request, $id)
    {
        $cash_req = $this->cashReq::find($id);

        $cash_req->employee_id = $request->employee_id;
        $cash_req->amount = $request->amount;
        $cash_req->reason = $request->reason;
        $cash_req->save();
        return $cash_req;
    }

    public function statusUpdate($id, $status)
    {
        $customer = $this->cashReq::find($id);
        $customer->status = $status;
        $customer->save();
        return $customer;
    }

    public function destroy($id)
    {
        $customer = $this->cashReq::find($id);
        if ($customer->status == 'approved' || $customer->status == 'completed') {
            return false;
        } else {
            $customer->delete();
            return true;
        }
    }
}
