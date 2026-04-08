<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\Project;
use App\Models\ProjectReturn;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var project
     */
    private $project;

    /**
     * CourseRepository constructor.
     * @param project $eproject
     */
    public function __construct(project $projects)
    {
        $this->project = $projects;
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
        $result = $this->project::latest()->get();
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
            1 => 'projectCode',
        );

        $edit = Helper::roleAccess('project.project.edit') ? 1 : 0;
        $delete = Helper::roleAccess('project.project.destroy') ? 1 : 0;
        $view = Helper::roleAccess('project.project.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->project::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $project = $this->project::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->project::count();
        } else {
            $search = $request->input('search.value');
            $project = $this->project::where(function ($query) use ($search) {
                $query->where('projectCode', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = $this->project::where(function ($query) use ($search) {
                $query->where('projectCode', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })->count();

            // $totalFiltered = $this->project::where('projectCode', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($project) {

        
            foreach ($project as $key => $eproject) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $eproject->name;
                $nestedData['projectCode'] = $eproject->projectCode;
                $nestedData['manager_id'] =  $eproject->manager_id ? $eproject->manager->name : "N/A";
                $nestedData['budget'] = $eproject->budget;
                $nestedData['address'] = $eproject->address;
                $nestedData['start_date'] = $eproject->start_date;
                $nestedData['end_date'] = $eproject->end_date;
                $nestedData['estimate_profit'] = $eproject->estimate_profit;

                // $nestedData['customer_id'] = $eproject->customer->customerCode . ' _ ' . $eproject->customer->name;
                if ($eproject->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('project.project.status', [$eproject->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('project.project.status', [$eproject->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;

                if ($eproject->condition == 'Complete') :
                    $nestedData['condition'] = '<button class="btn btn-success btn-sm"> Complete </button>';
                else :
                    $nestedData['condition'] = '<button data-toggle="modal" data-target="#projectcompleate" dataId="' . $eproject->id . '" class="btn btn-warning btn-sm complateid"> One Going </button>';
                endif;


                $nestedData['status'] = $status;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('project.project.edit', $eproject->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view != 0) {
                        $view_data = '<a href="' . route('project.project.show', $eproject->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('project.project.destroy', $eproject->id) . '" delete_id="' . $eproject->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $eproject->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->project::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd('repository',$request->all());

        $eproject = new $this->project();
        $eproject->projectCode = $request->projectCode;
        $eproject->name = $request->name;
        // $eproject->ledger_id = $request->ledger_id ?? '';
        $eproject->customer_id = $request->customer_id ?? '';
        $eproject->manager_id = $request->manager_id ? $request->manager_id : '';
        $eproject->budget = $request->budget;
        $eproject->start_date = $request->start_date;
        $eproject->end_date = $request->end_date;
        $eproject->address = $request->address;
        $eproject->estimate_profit = $request->estimate_profit;
        $eproject->created_by = Auth::user()->id;
        $eproject->save();

        return $eproject;

    }

    public function completestore($request)
    {


        $eproject = $this->project::find($request->projectid);
        $allReturnData = ProjectReturn::where('project_id', $request->projectid)
            ->where('status', 'Pending')
            ->count();

        if ($allReturnData > 0) {
            $statusHub = 1;
            return redirect()->back()->with('error', ' You Have Pending return products request.');
        }

        $eproject->condition = 'Complete';
        $eproject->closing = $request->close_date;
        $eproject->save();
        return $eproject;
    }

    public function update($request, $id)
    {
        $eproject = project::find($id);
        $eproject->name = $request->name;
        $eproject->customer_id = $request->customer_id;
        $eproject->manager_id = $request->manager_id ? $request->manager_id : '';
        $eproject->budget = $request->budget;
        $eproject->start_date = $request->start_date;
        $eproject->end_date = $request->end_date;
        $eproject->address = $request->address;
        $eproject->estimate_profit = $request->estimate_profit;
        $eproject->updated_by = Auth::user()->id;
        $eproject->save();
        return $eproject;
    }

    public function statusUpdate($id, $status)
    {
        $eproject = $this->project::find($id);
        $eproject->status = $status;
        $eproject->save();
        return $eproject;
    }

    public function destroy($id)
    {
        $eproject = $this->project::find($id);
        if ($eproject->condition == "One Going") {
            $eproject->forceDelete();
            return true;
        } else {
            return false;
        }
    }
}
