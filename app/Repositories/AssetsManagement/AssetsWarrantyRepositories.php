<?php

namespace App\Repositories\AssetsManagement;

use App\Helpers\Helper;
use App\Models\AssetsWarranty;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AssetsWarrantyRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var AssetsWarranty
     */
    private $assetsWarranty;
    /**
     * CourseRepository constructor.
     * @param assetsWarranty $productUnit
     */
    public function __construct(AssetsWarranty $assetsWarranty)
    {
        $this->assetsWarranty = $assetsWarranty;
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
        $result = $this->assetsWarranty::latest()->get();
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

        $edit = Helper::roleAccess('assets.warranty.edit') ? 1 : 0;
        $delete = Helper::roleAccess('assets.warranty.destroy') ? 1 : 0;
        $view = Helper::roleAccess('assets.warranty.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->assetsWarranty::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $assetsWarranty = $this->assetsWarranty::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->assetsWarranty::count();
        } else {
            $search = $request->input('search.value');
            $assetsWarranty = $this->assetsWarranty::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->assetsWarranty::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($assetsWarranty) {
            foreach ($assetsWarranty as $key => $warranty) {
                $nestedData['id'] = $key + 1;
                $nestedData['assetlist_id'] = $warranty->assetList->name;
                $nestedData['form_date'] = $warranty->form_date;
                $nestedData['to_date'] = $warranty->to_date;
                $nestedData['type'] = $warranty->type;
                $nestedData['desc'] = $warranty->desc;
                // if ($warranty->status == 'Active') :
                //     $status = '<input class="status_row" status_route="' . route('assets.warranty.status', [$warranty->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                // else :
                //     $status = '<input  class="status_row" status_route="' . route('assets.warranty.status', [$warranty->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                // endif;
                // $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('assets.warranty.edit', $warranty->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('assets.warranty.show', $warranty->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('assets.warranty.destroy', $warranty->id) . '" delete_id="' . $warranty->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $warranty->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->assetsWarranty::find($id);
        return $result;
    }

    public function store($request)
    {

        $warranty = new $this->assetsWarranty();
        $warranty->assetlist_id = $request->assetlist_id;
        $warranty->form_date = $request->form_date;
        $warranty->to_date = $request->to_date;
        $warranty->type = $request->type;
        $warranty->desc = $request->desc;
        $warranty->save();
        return $warranty;
    }

    public function update($request, $id)
    {
        $warranty = $this->assetsWarranty::findOrFail($id);
        $warranty->assetlist_id = $request->assetlist_id;
        $warranty->form_date = $request->form_date;
        $warranty->to_date = $request->to_date;
        $warranty->type = $request->type;
        $warranty->desc = $request->desc;
        $warranty->save();
        return $warranty;
    }

    public function statusUpdate($id, $status)
    {
        $warranty = $this->assetsWarranty::find($id);
        $warranty->status = $status;
        $warranty->save();
        return $warranty;
    }

    public function destroy($id)
    {
        $warranty = $this->assetsWarranty::find($id);
        $warranty->delete();
        return true;
    }
}
