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

class LeaveApplicationRepositories
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

        $edit = Helper::roleAccess('hrm.leave.edit') ? 1 : 0;
        $delete = Helper::roleAccess('hrm.leave.destroy') ? 1 : 0;
        $view = Helper::roleAccess('hrm.leave.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->model::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $LeaveApplication = $this->model::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            if (auth()->user()->type != "Admin") {
                $LeaveApplication = $LeaveApplication->where("employee_id", (auth()->user()->employee->id ?? 0));
            }

            $LeaveApplication = $LeaveApplication->get();
            $totalFiltered = $this->model::count();
        } else {
            $search = $request->input('search.value');
            $LeaveApplication = $this->model::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);
                
            if (auth()->user()->type != "Admin") {
                $LeaveApplication = $LeaveApplication->where("employee_id", (auth()->user()->employee->id ?? 0));
            }

            $LeaveApplication = $LeaveApplication->get();
            $totalFiltered = $this->model::where('name', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($LeaveApplication) {
            foreach ($LeaveApplication as $key => $value) {
                $to = \Carbon\Carbon::parse($value->end_date);
                $from = \Carbon\Carbon::parse($value->apply_date);
                $days = $to->diffInDays($from);
                $nestedData['id'] = $key + 1;
                $nestedData['employee_id'] = $value->employee->name ?? '';
                $nestedData['branch_id'] = $value->branch->name;
                $nestedData['days'] = $days;
                $nestedData['apply_date'] = $value->apply_date;
                $nestedData['end_date'] = $value->end_date;
                $nestedData['reason'] = $value->reason;
                $nestedData['payment_status'] = $value->payment_status;
                $nestedData['status'] = $value->status;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('hrm.leave.edit', $value->id) . '" class="btn btn-xs btn-success"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('hrm.leave.show', $value->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('hrm.leave.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-danger delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->model::find($id);
        return $result;
    }

    public function store($request)
    {
        $LeaveApplication = new $this->model;
        $LeaveApplication->employee_id = $request->employee_id;
        $LeaveApplication->branch_id = $request->branch_id;
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

        // $LeaveApplication->employee_id = $request->employee_id;
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
            Storage::disk('public')->delete('leave/' . $LeaveApplication->file);


            $file->storeAs('leave', $fileName, 'public');
        } else {
            $fileName = null;
        }
        $LeaveApplication->file = $fileName;
        $LeaveApplication->save();
        return $LeaveApplication;
    }

    public function statusUpdate($id, $status)
    {
        $customer = $this->model::find($id);
        $customer->status = $status;
        $customer->save();
        return $customer;
    }

    public function destroy($id)
    {
        $customer = $this->model::find($id);
        if ($customer) {
            $customer->delete();
            return true;
        } else {
            $customer->delete();
            return true;
        }
    }
}
