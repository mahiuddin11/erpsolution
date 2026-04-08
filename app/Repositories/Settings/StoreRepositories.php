<?php

namespace App\Repositories\Settings;
Use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use phpDocumentor\Reflection\PseudoTypes\False_;

class StoreRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Store
     */
    private $store;
    /**
     * CourseRepository constructor.
     * @param store $store
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }

/**
     * @param $request
     * @return mixed
     */
    // public function getAllBranch(){
    //   return  $this->branch::get();
    // }

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

        $edit = Helper::roleAccess('settings.store.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.store.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.store.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->store::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $stores = $this->store::with('branch')->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->store::count();
        } else {
            $search = $request->input('search.value');
            $stores = $this->store::with('branch')->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->store::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($stores) {
            foreach ($stores as $key => $store) {
                $nestedData['id'] = $key + 1;
                $nestedData['branch'] = $store->branch->name;
                $nestedData['name'] = $store->name;
                $nestedData['email'] = $store->email;
                $nestedData['phone'] = $store->phone;
                $nestedData['address'] = $store->address;
                if ($store->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('settings.store.status', [$store->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('settings.store.status', [$store->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
        if ($ced != 0) :
            if ($edit != 0)
                $edit_data = '<a href="' . route('settings.store.edit', $store->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
            else
                $edit_data = '';
            if ($view =! 0)
                $view_data = '<a href="' . route('settings.store.show', $store->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            else
                $view_data = '';
            if ($delete != 0)
                $delete_data = '<a delete_route="' . route('settings.store.destroy', $store->id) . '" delete_id="' . $store->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $store->id . '"><i class="fa fa-times"></i></a>';
            else
                $delete_data = '';
                
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
        $result = $this->store::find($id);
        return $result;
    }

    public function store($request)
    {
        $store = new $this->store();
        $store->branch_id = $request->branch_id;
        $store->name = $request->name;
        $store->email = $request->email;
        $store->phone = $request->phone;
        $store->address = $request->address;
        $store->status = 'Active';
        $store->created_by = Auth::user()->id;
        $store->save();
        return $store;
    }

    public function update($request, $id)
    {
        $store = $this->store::findOrFail($id);
        $store->name = $request->name;
        $store->branch_id = $request->branch_id;
        $store->email = $request->email;
        $store->phone = $request->phone;
        $store->address = $request->address;
        $store->status = 'Active';
        $store->updated_by = Auth::user()->id;
        $store->save();
        return $store;
    }

    public function statusUpdate($id, $status)
    {
        $store = $this->store::find($id);
        $store->status = $status;
        $store->save();
        return $store;
    }

    public function destroy($id)
    {
        $store = $this->store::find($id);
        $store->delete();
        return true;
    }
}