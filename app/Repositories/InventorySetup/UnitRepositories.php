<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use phpDocumentor\Reflection\PseudoTypes\False_;

class UnitRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var ProductUnit
     */
    private $productUnit;
    /**
     * CourseRepository constructor.
     * @param productUnit $productUnit
     */
    public function __construct(ProductUnit $productUnit)
    {
        $this->productUnit = $productUnit;
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
        $result = $this->productUnit::latest()->get();
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

        $edit = Helper::roleAccess('inventorySetup.unit.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.unit.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.unit.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->productUnit::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $productUnits = $this->productUnit::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->productUnit::count();
        } else {
            $search = $request->input('search.value');
            $productUnits = $this->productUnit::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->productUnit::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($productUnits) {
            foreach ($productUnits as $key => $productUnit) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $productUnit->name;
                if ($productUnit->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.unit.status', [$productUnit->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.unit.status', [$productUnit->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.unit.edit', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.unit.show', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.unit.destroy', $productUnit->id) . '" delete_id="' . $productUnit->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $productUnit->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->productUnit::find($id);
        return $result;
    }

    public function store($request)
    {
        $productUnit = new $this->productUnit();
        $productUnit->name = $request->name;
        $productUnit->status = 'Active';
        $productUnit->created_by = Auth::user()->id;
        $productUnit->save();
        return $productUnit;
    }

    public function update($request, $id)
    {
        $productUnit = $this->productUnit::findOrFail($id);
        $productUnit->name = $request->name;
        $productUnit->status = 'Active';
        $productUnit->updated_by = Auth::user()->id;
        $productUnit->save();
        return $productUnit;
    }

    public function statusUpdate($id, $status)
    {
        $productUnit = $this->productUnit::find($id);
        $productUnit->status = $status;
        $productUnit->save();
        return $productUnit;
    }

    public function destroy($id)
    {
        $productUnit = $this->productUnit::find($id);
        $productUnit->delete();
        return true;
    }
}
