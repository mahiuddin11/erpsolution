<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerGroup;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CustomerGroupRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var customerGroup
     */
    private $customerGroup;
    /**
     * CourseRepository constructor.
     * @param customer $customer
     */
    public function __construct(CustomerGroup $customerGroup)
    {
        $this->customerGroup = $customerGroup;
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
        $result = $this->customerGroup::latest()->get();
        // dd($result);
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

        $edit = Helper::roleAccess('inventorySetup.customer.group.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.customer.group.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.customer.group.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->customerGroup::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $customergroups = $this->customerGroup::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->customerGroup::count();
        } else {
            $search = $request->input('search.value');
            $customergroups = $this->customerGroup::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->customerGroup::where('name', 'like', "%{$search}%")->count();
        }

        $data = array();
        //dd($customers);
        if ($customergroups) {
            foreach ($customergroups as $key => $customergroup) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $customergroup->name;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.customer.group.edit', $customergroup->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.customer.group.show', $customergroup->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.customer.group.destroy', $customergroup->id) . '" delete_id="' . $customergroup->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $customergroup->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->customerGroup::find($id);
        return $result;
    }

    public function store($request)
    {
        $customergroup = new $this->customerGroup();
        $customergroup->name = $request->name;
        $customergroup->save();
        return $customergroup;
    }

    public function update($request, $id)
    {
        //    dd($request->all());
        $customer = $this->customerGroup::findOrFail($id);
        $customer->name = $request->name;
        $customer->save();
        return $customer;
    }


    public function destroy($id)
    {
        $customer = $this->customerGroup::find($id);
        $customer->delete();
        return true;
    }
}
