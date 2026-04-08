<?php

namespace App\Repositories\Usermanage;

use App\Helpers\Helper;
use App\Models\RoleAccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var user
     */
    private $user;
    /**
     * CourseRepository constructor.
     * @param user $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }
    /**
     * @param $request
     * @return mixed
     */

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'email',
        );

        $edit = Helper::roleAccess('usermanage.user.edit') ? 1 : 0;
        $delete = Helper::roleAccess('usermanage.user.destroy') ? 1 : 0;
        $view = Helper::roleAccess('usermanage.user.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->user::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $users = $this->user::with('userRole')->offset($start)
                ->limit($limit)
                ->adminUser(1)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->user::count();
        } else {
            $search = $request->input('search.value');
            $users = $this->user::with('userRole')->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->user::where('name', 'like', "%{$search}%")
                ->count();
        }

        $data = array();
        if ($users) {
            foreach ($users as $key => $user) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $user->name;
                $nestedData['userRole'] = $user->userRole->role_name ?? "";
                if ($user->branch_id) {
                    $nestedData['branch_id'] = ($user->branch->branchCode ?? "") . '-' . ($user->branch->name ?? "");
                } else {
                    $nestedData['branch_id']  = '';
                }
                $nestedData['type'] = $user->type;
                $nestedData['email'] = $user->email;
                $nestedData['phone'] = $user->phone;
                if ($user->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('usermanage.user.status', [$user->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('usermanage.user.status', [$user->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('usermanage.user.edit', $user->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view != 0) {
                        $view_data = '<a href="' . route('usermanage.user.show', $user->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('usermanage.user.destroy', $user->id) . '" delete_id="' . $user->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $user->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

                    $nestedData['action'] = $edit_data . ' ' . $delete_data . ' ' . $view_data;
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
        $result = $this->user::find($id);
        return $result;
    }

    public function store($request)
    {

        // dd($request->all());

        $user = new $this->user();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->employee_id = $request->employee_id;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->branch_id = $request->branch_id;
        $user->role_id = $request->role_name;
        $user->type = $request->type;
        $user->status = 'Active';
        $user->created_by = Auth::user()->id;
        $user->save();
        $userID = $user->id;


        $accessRoll = new RoleAccess();
        $accessRoll->user_id = $userID;
        $accessRoll->role_id = $request->role_name;
        $accessRoll->save();
        return $user;
    }

    public function update($request, $id)
    {
     
        $user = $this->user::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->branch_id = $request->branch_id;
        $user->employee_id = $request->employee_id;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->role_id = $request->role_name;
        $user->type = $request->type;
        $user->status = 'Active';
        $user->updated_by = Auth::user()->id;
        $user->save();
        $userID = $user->id;

        $accessRoll =  RoleAccess::find($id);
        if (!$accessRoll) {
            $accessRoll = new RoleAccess();
        }
        $accessRoll->user_id = $userID;
        $accessRoll->role_id = $request->role_name;
        $accessRoll->save();
        return $user;
    }

    public function statusUpdate($id, $status)
    {
        $user = $this->user::find($id);
        $user->status = $status;
        $user->save();
        return $user;
    }

    public function destroy($id)
    {
        $user = $this->user::find($id);
        $user->delete();
        return true;
    }
}
