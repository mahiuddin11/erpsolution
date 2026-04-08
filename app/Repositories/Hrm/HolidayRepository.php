<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\Holiday;

class HolidayRepository
{

    protected $model;

    public function __construct(Holiday $model)
    {
        $this->model = $model;
    }

    // public function getList($request)
    // {
    //     $columns = array(
    //         0 => 'id',
    //         1 => 'name',
    //     );

    //     $edit = Helper::roleAccess('hrm.holiday.edit') ? 1 : 0;
    //     $delete = Helper::roleAccess('hrm.holiday.destroy') ? 1 : 0;
    //     $view = Helper::roleAccess('hrm.holiday.show') ? 1 : 0;
    //     $ced = $edit + $delete + $view;

    //     $totalData = $this->model::count();

    //     $limit = $request->input('length');
    //     $start = $request->input('start');
    //     $order = $columns[$request->input('order.0.column')];
    //     $dir = $request->input('order.0.dir');

    //     if (empty($request->input('search.value'))) {
    //         $holiday = $this->model::offset($start)
    //             ->limit($limit)
    //             ->orderBy('date', 'desc');

    //         if (auth()->user()->type != "Admin") {
    //             $holiday = $holiday->where("emplyee_id", (auth()->user()->employee->id ?? 0));
    //         }

    //         $holiday = $holiday->get();

    //         $totalFiltered = $this->model::count();
    //     } else {
    //         $search = $request->input('search.value');

    //         $holiday = $this->model::with('employe')
    //             ->where(function ($query) use ($search) {
    //                 $query->where('date', 'like', "%{$search}%")
    //                     ->orWhereHas('employe', function ($q) use ($search) {
    //                         $q->where('name', 'like', "%{$search}%");
    //                     });
    //             })->offset($start)->limit($limit)->orderBy('date', 'desc');

    //         if (auth()->user()->type != "Admin") {
    //             $holiday = $holiday->where("emplyee_id", (auth()->user()->employee->id ?? 0));
    //         }

    //         $holiday = $holiday->get();

    //         $totalFiltered = $this->model::where('date', 'like', "%{$search}%")->count();
    //     }


    //     $data = array();
    //     if ($holiday) {
    //         foreach ($holiday as $key => $value) {
    //             $nestedData['id'] = $key + 1;
    //             $nestedData['emplyee_id'] = ucfirst($value->employe->name ?? "");
    //             $nestedData['date'] = $value->date;
    //             $nestedData['sign_in'] = date('h:i A', strtotime($value->sign_in));
    //             $nestedData['location_in'] = checkLocation($value->latitude, $value->longitude, "check_in");
    //             $nestedData['sign_out'] = !empty($value->sign_out) ? date('g:i A', strtotime($value->sign_out)) : '6:00 PM';

    //             if (($value->sign_out == "00:00:00" || empty($value->sign_out))
    //                 && $value->date == date('Y-m-d')
    //             ) {
    //                 // Only today's holiday show Running
    //                 $nestedData['location_out'] = '<span class="badge badge-warning"><i class="fa fa-clock-o"></i>Continue</span>';
    //             } else {
    //                 $nestedData['location_out'] = checkLocation($value->latitude_out, $value->longitude_out, 'check_out');
    //             }

    //             // $nestedData['status'] = $value->status;

    //             if ($ced != 0) :
    //                 if ($edit != 0)
    //                     $edit_data = '<a href="' . route('hrm.holiday.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
    //                 else
    //                     $edit_data = '';
    //                 if ($view != 0)
    //                     $view_data = '<a href="' . route('hrm.holiday.show', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
    //                 else
    //                     $view_data = '';
    //                 if ($delete != 0)
    //                     $delete_data = '<a delete_route="' . route('hrm.holiday.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
    //                 else
    //                     $delete_data = '';
    //                 $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;
    //             else :
    //                 $nestedData['action'] = '';
    //             endif;
    //             $data[] = $nestedData;
    //         }
    //     }
    //     $json_data = array(
    //         "draw" => intval($request->input('draw')),
    //         "recordsTotal" => intval($totalData),
    //         "recordsFiltered" => intval($totalFiltered),
    //         "data" => $data
    //     );

    //     return $json_data;
    // }

}
