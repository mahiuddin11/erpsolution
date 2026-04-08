<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\Attendance;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\Transection;
use App\Models\User;

class AttendanceRepositories
{
    /**
     * @var Brand
     */
    private $model;
    /**
     * PositionRepository Position.
     * @param Attendance $Attendance
     */
    public function __construct(Attendance $Attendance)
    {
        $this->model = $Attendance;
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

        $edit = Helper::roleAccess('hrm.attendance.edit') ? 1 : 0;
        $delete = Helper::roleAccess('hrm.attendance.destroy') ? 1 : 0;
        $view = Helper::roleAccess('hrm.attendance.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->model::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $Attendance = $this->model::offset($start)
                ->limit($limit)
                ->orderBy('date', 'desc');

            if (auth()->user()->type != "Admin") {
                $Attendance = $Attendance->where("emplyee_id", (auth()->user()->employee->id ?? 0));
            }

            $Attendance = $Attendance->get();

            $totalFiltered = $this->model::count();
        } else {
            $search = $request->input('search.value');

            $Attendance = $this->model::with('employe')
                ->where(function ($query) use ($search) {
                    $query->where('date', 'like', "%{$search}%")
                        ->orWhereHas('employe', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                })->offset($start)->limit($limit)->orderBy('date', 'desc');

            if (auth()->user()->type != "Admin") {
                $Attendance = $Attendance->where("emplyee_id", (auth()->user()->employee->id ?? 0));
            }

            $Attendance = $Attendance->get();

            $totalFiltered = $this->model::where('date', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($Attendance) {
            foreach ($Attendance as $key => $value) {
                $nestedData['id'] = $key + 1;
                $nestedData['emplyee_id'] = ucfirst($value->employe->name ?? "");
                $nestedData['date'] = $value->date;
                $nestedData['sign_in'] = date('h:i A', strtotime($value->sign_in));
                $nestedData['location_in'] = checkLocation($value->latitude, $value->longitude, "check_in");
                $nestedData['sign_out'] = !empty($value->sign_out) ? date('g:i A', strtotime($value->sign_out)) : '6:00 PM';

                if (($value->sign_out == "00:00:00" || empty($value->sign_out))
                    && $value->date == date('Y-m-d')
                ) {
                    // Only today's attendance show Running
                    $nestedData['location_out'] = '<span class="badge badge-warning"><i class="fa fa-clock-o"></i>Continue</span>';
                } else {
                    $nestedData['location_out'] = checkLocation($value->latitude_out, $value->longitude_out, 'check_out');
                }

                // $nestedData['status'] = $value->status;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('hrm.attendance.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('hrm.attendance.show', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('hrm.attendance.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
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

    public function signin($request)
    {

        // if (empty($request->latitude) || empty($request->longitude)) {
        //     session()->flash('error', 'Please Turn On Your Location');
        //     return redirect()->route('hrm.attendance.create');
        // }

        $branch = Branch::first();
        $Attendance = new $this->model;
        $Attendance->emplyee_id = $request->emplyee_id;
        $Attendance->branch_id = Auth()->id() ?? $branch->id;
        $Attendance->date = $request->date;
        $Attendance->sign_in = $request->sign_in;
        $Attendance->latitude = $request->latitude;
        $Attendance->longitude = $request->longitude;
        $Attendance->save();
        return $Attendance;
    }

    public function signout($request)
    {
        $Date = getEffectiveDate($request->date . ' ' . $request->sign_out);
        $branch = Branch::first();
        // $user = User::find(Auth()->id());
        // $Attendance['branch_id'] = $user->branch_id;
        $Attendance['emplyee_id'] = $request->emplyee_id;
        $Attendance['branch_id'] = Auth()->id() ?? $branch->id;
        $Attendance['sign_out'] = $request->sign_out;
        $Attendance['latitude_out'] = $request->latitude;
        $Attendance['longitude_out'] = $request->longitude;
        $Attendance = Attendance::where('emplyee_id', $request->emplyee_id)->whereDate('date', $Date)->update($Attendance);
        return $Attendance;
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
        $attendance = $this->model::find($id);
        $attendance->delete();
        return true;
    }

    public function edit($id)
    {
        $model = $this->model::find($id);

        return $model;
    }

    public function update($request, $id)
    {
        $attendance = $this->model::find($id);
        if ($attendance) {
            $attendance->update([
                'date'      => $request->date,
                'sign_in'   => $request->sign_in,
                'sign_out'   => $request->sign_out,
            ]);
        }
        return $attendance;
    }
}
