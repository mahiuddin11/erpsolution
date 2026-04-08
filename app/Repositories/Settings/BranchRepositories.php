<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use phpDocumentor\Reflection\PseudoTypes\False_;

class BranchRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Branch
     */
    private $branch;
    /**
     * CourseRepository constructor.
     * @param branch $branch
     */
    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
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
        return  $this->branch::get();
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
            2 => 'phone',
            3 => 'email',
        );

        $edit = Helper::roleAccess('settings.branch.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.branch.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.branch.show') ? 0 : 0;
        $ced = $edit + $delete + $view;



        $totalData = $this->branch::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $branchs = $this->branch::offset($start)
                ->limit($limit)
                ->where("parent_id", 0)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->branch::count();
        } else {
            $search = $request->input('search.value');
            $branchs = $this->branch::where('name', 'like', "%{$search}%")->orWhere('branchCode', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->where("parent_id", 0)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->branch::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($branchs) {
            foreach ($branchs as $key => $branch) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $branch->name;
                $nestedData['branchCode'] = $branch->branchCode;
                $nestedData['email'] = $branch->email;
                $nestedData['phone'] = $branch->phone;
                $nestedData['address'] = $branch->address;
                if ($branch->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('settings.branch.status', [$branch->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('settings.branch.status', [$branch->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('settings.branch.edit', $branch->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('settings.branch.show', $branch->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.branch.destroy', $branch->id) . '" delete_id="' . $branch->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $branch->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->branch::find($id);
        return $result;
    }

    public function store($request)
    {
        // Save the Branch
        $branch = new $this->branch();
        $branch->name = $request->name;
        $branch->branchCode = $request->branchCode;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->address = $request->address;
        $branch->status = 'Active';
        $branch->parent_id = 0; // Set parent_id to 0 for the branch
        $branch->created_by = Auth::user()->id;
        $branch->save();

        return $branch;
    }

    public function update($request, $id)
    {
        $branch = $this->branch::findOrFail($id);
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->address = $request->address;
        $branch->status = 'Active';
        $branch->updated_by = Auth::user()->id;
        $branch->save();

        return $branch;
    }

    public function statusUpdate($id, $status)
    {
        $branch = $this->branch::find($id);
        $branch->status = $status;
        $branch->save();
        return $branch;
    }

    public function destroy($id)
    {
        $branch = $this->branch::find($id);
        $branch->warehouse()->delete();
        $branch->delete();
        return true;
    }
}
