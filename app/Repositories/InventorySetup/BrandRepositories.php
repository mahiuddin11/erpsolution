<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use phpDocumentor\Reflection\PseudoTypes\False_;

class BrandRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Brand
     */
    private $brand;
    /**
     * CourseRepository constructor.
     * @param brand $brand
     */
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
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
        $result = $this->brand::latest()->get();
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

        $edit = Helper::roleAccess('inventorySetup.brand.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.brand.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.brand.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->brand::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $brands = $this->brand::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->brand::count();
        } else {
            $search = $request->input('search.value');
            $brands = $this->brand::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->brand::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($brands) {
            foreach ($brands as $key => $brand) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $brand->name;
                if ($brand->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.brand.status', [$brand->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.brand.status', [$brand->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.brand.edit', $brand->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.brand.show', $brand->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.brand.destroy', $brand->id) . '" delete_id="' . $brand->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $brand->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->brand::find($id);
        return $result;
    }

    public function store($request)
    {
        $brand = new $this->brand();
        $brand->name = $request->name;
        $brand->status = 'Active';
        $brand->created_by = Auth::user()->id;
        $brand->save();
        return $brand;
    }

    public function update($request, $id)
    {
        $brand = $this->brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->status = 'Active';
        $brand->updated_by = Auth::user()->id;
        $brand->save();
        return $brand;
    }

    public function statusUpdate($id, $status)
    {
        $brand = $this->brand::find($id);
        $brand->status = $status;
        $brand->save();
        return $brand;
    }

    public function destroy($id)
    {
        $brand = $this->brand::find($id);
        $brand->delete();
        return true;
    }
}
