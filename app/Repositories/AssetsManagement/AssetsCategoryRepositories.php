<?php

namespace App\Repositories\AssetsManagement;

use App\Helpers\Helper;
use App\Models\AssetsCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AssetsCategoryRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var AssetsCategory
     */
    private $assetsCategory;
    /**
     * CourseRepository constructor.
     * @param productUnit $productUnit
     */
    public function __construct(AssetsCategory $assetsCategory)
    {
        $this->assetsCategory = $assetsCategory;
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
        $result = $this->assetsCategory::latest()->get();
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

        $edit = Helper::roleAccess('assets.category.edit') ? 1 : 0;
        $delete = Helper::roleAccess('assets.category.destroy') ? 1 : 0;
        $view = Helper::roleAccess('assets.category.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->assetsCategory::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $productUnits = $this->assetsCategory::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->assetsCategory::count();
        } else {
            $search = $request->input('search.value');
            $productUnits = $this->assetsCategory::where('category_name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->assetsCategory::where('category_name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($productUnits) {
            foreach ($productUnits as $key => $productUnit) {
                $nestedData['id'] = $key + 1;
                $nestedData['category_name'] = $productUnit->category_name;
                if ($productUnit->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('assets.category.status', [$productUnit->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('assets.category.status', [$productUnit->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('assets.category.edit', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('assets.category.show', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('assets.category.destroy', $productUnit->id) . '" delete_id="' . $productUnit->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $productUnit->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->assetsCategory::find($id);
        return $result;
    }

    public function store($request)
    {

        $productUnit = new $this->assetsCategory();
        $productUnit->category_name = $request->category_name;
        $productUnit->status = 'Active';
        $productUnit->created_by = Auth::user()->id;
        $productUnit->save();
        return $productUnit;
    }

    public function update($request, $id)
    {
        $productUnit = $this->assetsCategory::findOrFail($id);
        $productUnit->category_name = $request->category_name;
        $productUnit->status = $request->status;
        $productUnit->updated_by = Auth::user()->id;
        $productUnit->save();
        return $productUnit;
    }

    public function statusUpdate($id, $status)
    {
        $productUnit = $this->assetsCategory::find($id);
        $productUnit->status = $status;
        $productUnit->save();
        return $productUnit;
    }

    public function destroy($id)
    {
        $productUnit = $this->assetsCategory::find($id);
        $productUnit->delete();
        return true;
    }
}
