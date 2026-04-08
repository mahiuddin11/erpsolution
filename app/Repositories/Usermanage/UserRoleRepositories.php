<?php

namespace App\Repositories\Usermanage;

use Illuminate\Support\Facades\Auth;
use App\Models\UserRole;
use App\Models\Navigation;
//use helper;
use App\Helpers\Helper;
use phpDocumentor\Reflection\PseudoTypes\False_;

class UserRoleRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var UserRole
     */
    private $userRole;
    /**
     * CourseRepository constructor.
     * @param branch $userRole
     */
    public function __construct(UserRole $userRole)
    {
        $this->userRole = $userRole;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }
    /**
     * @param $request
     * @return mixed
     */


    public function getAllRole()
    {
        return  $this->userRole::get();
    }

    public function getList($request)
    {

        $columns = array(
            0 => 'id',
            1 => 'role_name',
        );

        $totalData = $this->userRole::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $userRoles = $this->userRole::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->userRole::count();
        } else {
            $search = $request->input('search.value');
            $userRoles = $this->userRole::where('role_name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->userRole::where('role_name', 'like', "%{$search}%")->count();
        }


        $data = array();
        $edit_data = "";
        //dd($userRoles);
        if ($userRoles) {
            foreach ($userRoles as $key => $userRole) {
                $nestedData['id'] = $key + 1;
                $nestedData['role_name'] = $userRole->role_name;

                if ($userRole->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('usermanage.userRole.status', [$userRole->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('usermanage.userRole.status', [$userRole->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if (helper::roleAccess('usermanage.userRole.edit')) :
                    $edit_data = '<a href="' . route('usermanage.userRole.edit', $userRole->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                endif;

                // $view_data = '<a href="' . route('usermanage.userRole.show', $userRole->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                $delete_data = '<a delete_route="' . route('usermanage.userRole.destroy', $userRole->id) . '" delete_id="' . $userRole->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $userRole->id . '"><i class="fa fa-times"></i></a>';
                $nestedData['action'] = $edit_data . ' ' . $delete_data;
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
        $result = $this->userRole::find($id);
        return $result;
    }
    /**
     * @param $request
     * @return mixed
     */
    public function getNavigation()
    {
        $result = config('navigation');
        $allMenuList = array();
        foreach ($result as $key => $each_parent) :
            $submenuInfo = $each_parent->submenu;
            foreach ($submenuInfo as $key => $eachInfo) :
                $menuList['label'] = $each_parent->label;
                $menuList['sub_menu'] = $eachInfo->label;
                $menuList['uniqueName'] = $eachInfo->uniqueName;
                $menuList['child_menu'] = $eachInfo->childMenu;
                array_push($allMenuList, $menuList);
            endforeach;
        endforeach;
        return $allMenuList;
    }

    public function store($request)
    {
        $parents = array();
        foreach ($request->parent_id as $key => $value) :
            $parent_id = $value;
            array_push($parents, $parent_id);
        endforeach;
        $userRole = new $this->userRole();
        $userRole->role_name = $request->role_name;
        $userRole->parent_id = implode(",", array_unique($parents));
        $userRole->dashboard_id = implode(",", $request->dashboardCHeck ?? []);
        $userRole->navigation_id = implode(",", $request->permission);
        $userRole->status = 'Active';
        $userRole->created_by = Auth::user()->id;
        $userRole->save();
        return $userRole;
    }

    public function getParentId($childId)
    {
        $navigationInfo =  Navigation::findOrFail($childId);
        return $navigationInfo->parent_id;
    }

    public function update($request, $id)
    {
        $parents = array();
        foreach ($request->parent_id as $key => $value) :
            $parent_id = $value;
            array_push($parents, $parent_id);
        endforeach;
        $userRole = $this->userRole::findOrFail($id);
        $userRole->role_name = $request->role_name;
        $userRole->dashboard_id = implode(",", $request->dashboardCHeck ?? []);
        $userRole->parent_id = implode(",", array_unique($parents));
        $userRole->navigation_id = implode(",", $request->permission);
        // $userRole->branch_id = implode(",", $request->branch);
        $userRole->status = 'Active';
        $userRole->created_by = Auth::user()->id;
        $userRole->save();
        return $userRole;
    }

    public function statusUpdate($id, $status)
    {
        $userRole = $this->userRole::find($id);
        $userRole->status = $status;
        $userRole->save();
        return $userRole;
    }

    public function destroy($id)
    {
        $userRole = $this->userRole::find($id);
        $userRole->delete();
        return true;
    }
}
