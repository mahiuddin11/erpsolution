<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use App\Models\ExpenseCategory;
use App\Models\Opening;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryRepositorie
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Opening
     */
    private $expensecategory;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(ExpenseCategory $expensecategory)
    {
        $this->expensecategory = $expensecategory;
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
        return $this->expensecategory::get();
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

        $edit = Helper::roleAccess('settings.category.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.category.destroy') ? 1 : 0;
        // $view = Helper::roleAccess('settings.category.show') ? 1 : 0;
        $ced = $edit + $delete;

        $totalData = $this->expensecategory::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $categorys = $this->expensecategory::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
            //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->expensecategory::count();
        } else {
            $search = $request->input('search.value');
            $categorys = $this->expensecategory::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
            // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->expensecategory::where('name', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($categorys) {
            foreach ($categorys as $key => $category) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $category->name;
                if ($category->status == 'Active'):
                    $status = '<input class="status_row" status_route="' . route('settings.category.status', [$category->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else:
                    $status = '<input  class="status_row" status_route="' . route('settings.category.status', [$category->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0):
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('settings.category.edit', $category->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    // if ($view = !0) {
                    //     $view_data = '<a href="' . route('settings.category.show', $category->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    // } else {
                    //     $view_data = '';
                    // }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('settings.category.destroy', $category->id) . '" delete_id="' . $category->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $category->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }
                    $nestedData['action'] = $edit_data . ' ' . $delete_data;
                else:
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
        $result = $this->expensecategory::find($id);
        return $result;
    }

    public function store($request)
    {
        $expensecategory = new $this->expensecategory();
        $expensecategory->name = $request->name;
        $expensecategory->parent_id = $request->parent_id;
        $expensecategory->status = 'Active';
        $expensecategory->created_by = Auth::user()->id;
        $expensecategory->save();
        return $expensecategory;
    }

    public function update($request, $id)
    {
        $expensecategory = $this->expensecategory::findOrFail($id);
        $expensecategory->name = $request->name;
        $expensecategory->parent_id = $request->parent_id;
        $expensecategory->updated_by = Auth::user()->id;
        $expensecategory->save();
        return $expensecategory;
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->expensecategory::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $excategory = $this->expensecategory::find($id);
        $excategory->delete();
        return true;
    }
}
