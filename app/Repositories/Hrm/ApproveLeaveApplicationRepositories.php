<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\Transection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ApproveLeaveApplicationRepositories
{
    /**
     * @var Brand
     */
    private $model;
    /**
     * PositionRepository Position.
     * @param LeaveApplication $Attendance
     */
    public function __construct(LeaveApplication $LeaveApplication)
    {
        $this->model = $LeaveApplication;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {

        $result = $this->model::latest()->get();
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
            1 => 'name',
        );

        $edit = Helper::roleAccess('hrm.leaveapprove.edit') ? 1 : 0;
        $cancel = Helper::roleAccess('hrm.leaveapprove.cancel') ? 1 : 0;
        $view = Helper::roleAccess('hrm.leaveapprove.show') ? 1 : 0;
        $ced = $edit + $cancel + $view;

        $totalData = $this->model::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $LeaveApplication = $this->model::offset($start)
                ->where('status', 'pending')
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->model::count();
        } else {
            $search = $request->input('search.value');
            $LeaveApplication = $this->model::where('name', 'like', "%{$search}%")
                ->where('status', 'pending')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->model::where('name', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($LeaveApplication) {
            foreach ($LeaveApplication as $key => $value) {
                $nestedData['id'] = $key + 1;
                $nestedData['employee_id'] = $value->employee->name;
                $nestedData['branch_id'] = $value->branch->name;
                $nestedData['apply_date'] = $value->apply_date;
                $nestedData['end_date'] = $value->end_date;
                $nestedData['reason'] = $value->reason;
                $nestedData['payment_status'] = $value->payment_status;
                $nestedData['status'] = $value->status;


                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('hrm.leaveapprove.approve', $value->id) . '" onclick="return confirm(`Are You Sure`)" class="btn btn-xs btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('hrm.leaveapprove.show', $value->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($cancel != 0)
                        $cancel_data = '<a href="' . route('hrm.leaveapprove.cancel', $value->id) . '" title="Cancel" class="btn btn-xs btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    else
                        $cancel_data = '';
                    $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $cancel_data;
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
        $result = $this->model::find($id);
        return $result;
    }

    public function store($request)
    {
        $LeaveApplication = new $this->model;
        $LeaveApplication->employee_id = $request->employee_id;
        $LeaveApplication->apply_date = $request->apply_date;
        $LeaveApplication->end_date = $request->end_date;
        $LeaveApplication->reason = $request->reason;
        $LeaveApplication->payment_status = $request->payment_status;
        $file = $request->file('file');
        if (isset($file)) {
            $currentDate = Carbon::now()->toDateString();
            $fileName  = $currentDate . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('leave')) {
                Storage::disk('public')->makeDirectory('leave');
            }


            $file->storeAs('leave', $fileName, 'public');
        } else {
            $fileName = null;
        }
        $LeaveApplication->file = $fileName;
        $LeaveApplication->save();
        return $LeaveApplication;
    }

    public function update($request, $id)
    {
        $LeaveApplication = $this->model::find($id);

        $LeaveApplication->payment_status = $request->payment_status;

        $LeaveApplication->save();
        
        return $LeaveApplication;
    }

    public function statusUpdate($id, $status)
    {
        $customer = $this->model::find($id);
        $customer->payment_status = $status;
        $customer->save();
        return $customer;
    }

    public function destroy($id)
    {
        $customer = $this->model::find($id);
        $customer->delete();
        return true;
    }
}
