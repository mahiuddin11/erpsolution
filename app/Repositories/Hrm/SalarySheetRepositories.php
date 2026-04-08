<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Models\Employee;
use App\Models\Position;
use App\Models\SalarySheet;

class SalarySheetRepositories
{
    /**
     * @var Brand
     */
    private $model;
    /**
     * PositionRepository Position.
     * @param position $position
     */
    public function __construct(SalarySheet $salarySheet)
    {
        $this->model = $salarySheet;
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

        $edit = Helper::roleAccess('hrm.salary.sheet.edit') ? 1 : 0;
        $delete = Helper::roleAccess('hrm.salary.sheet.destroy') ? 1 : 0;
        $view = Helper::roleAccess('hrm.salary.sheet.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->model::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $position = $this->model::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->model::count();
        } else {
            $search = $request->input('search.value');
            $position = $this->model::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->model::where('name', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($position) {
            foreach ($position as $key => $value) {
                $nestedData['id'] = $key + 1;
                $nestedData['employee_id'] = $value->employee->name;
                $nestedData['month'] = $value->month;
                $nestedData['type'] = $value->type;
                $nestedData['reason'] = $value->reason;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('hrm.salary.sheet.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('hrm.salary.sheet.show', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('hrm.salary.sheet.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
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
        $employee = Employee::find($request->employee_id);
        $salarysheet = new SalarySheet();
        $salarysheet->employee_id = $request->employee_id;
        $salarysheet->month = \Carbon\Carbon::parse($request->month)->format('Y-m-d');
        $salarysheet->paid_amount = $request->paid_amount;
        $salarysheet->overtime = $request->overtime;
        $salarysheet->incentive = $request->incentive;
        $salarysheet->bonus = $request->bonus;
        $salarysheet->paid_date = $request->paid_date;
        $salarysheet->reason = $request->reason;
        $salarysheet->type = "Paid";
        $salarysheet->salary = $employee->salary;
        $salarysheet->save();
        return $salarysheet;
    }

    public function update($request, $id)
    {
        $salarysheet = $this->model::find($id);
        $salarysheet->month = \Carbon\Carbon::parse($request->month)->format('Y-m-d');
        $salarysheet->paid_amount = $request->paid_amount;
        $salarysheet->overtime = $request->overtime;
        $salarysheet->incentive = $request->incentive;
        $salarysheet->bonus = $request->bonus;
        $salarysheet->paid_date = $request->paid_date;
        $salarysheet->reason = $request->reason;
        $salarysheet->type = "Paid";
        $salarysheet->save();
        return $salarysheet;
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
        $customer->delete();
        return true;
    }
}
