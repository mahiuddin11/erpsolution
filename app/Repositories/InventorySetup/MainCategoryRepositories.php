<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\MainCategory;
use phpDocumentor\Reflection\PseudoTypes\False_;

class MainCategoryRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var mainCategory
     */
    private $mainCategory;
    /**
     * CourseRepository constructor.
     * @param mainCategory $MainCategory
     */
    public function __construct(MainCategory $mainCategory)
    {
        $this->mainCategory = $mainCategory;
        $this->user_id = 1; //auth()->user()->id;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        $result = $this->mainCategory::latest()->get();
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

        $edit = Helper::roleAccess('inventorySetup.maincategory.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.maincategory.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.maincategory.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->mainCategory::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $categorys = $this->mainCategory::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->mainCategory::count();
        } else {
            $search = $request->input('search.value');
            $categorys = $this->mainCategory::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->mainCategory::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($categorys) {
            foreach ($categorys as $key => $category) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $category->name;
                if ($category->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.maincategory.status', [$category->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.maincategory.status', [$category->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.maincategory.edit', $category->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.maincategory.show', $category->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.maincategory.destroy', $category->id) . '" delete_id="' . $category->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $category->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->mainCategory::find($id);
        return $result;
    }

    public function store($request)
    {
        $category = new $this->mainCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->status = 'Active';
        $category->created_by = Auth::user()->id;
        $category->save();
        return $category;
    }

    public function update($request, $id)
    {
        $category = $this->mainCategory::findOrFail($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->status = 'Active';
        $category->updated_by = Auth::user()->id;
        $category->save();
        return $category;
    }

    public function statusUpdate($id, $status)
    {
        $category = $this->mainCategory::find($id);
        $category->status = $status;
        $category->save();
        return $category;
    }

    public function destroy($id)
    {
        $category = $this->mainCategory::find($id);
        $category->delete();
        return true;
    }
}
