<?php

namespace App\Repositories\AssetsManagement;

use App\Helpers\Helper;
use App\Models\Destroyitems;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AssetsDestroyRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var DestroyItems
     */
    private $destroyItems;
    /**
     * CourseRepository constructor.
     * @param DestroyItems $productUnit
     */
    public function __construct(Destroyitems $destroyItems)
    {
        $this->destroyItems = $destroyItems;
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
        $result = $this->destroyItems::latest()->get();
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

        $edit = Helper::roleAccess('assets.destroy.edit') ? 1 : 0;
        $delete = Helper::roleAccess('assets.destroy.destroy') ? 1 : 0;
        $view = Helper::roleAccess('assets.destroy.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->destroyItems::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $productUnits = $this->destroyItems::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->destroyItems::count();
        } else {
            $search = $request->input('search.value');
            $productUnits = $this->destroyItems::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->destroyItems::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($productUnits) {
            foreach ($productUnits as $key => $productUnit) {
                $nestedData['id'] = $key + 1;
                $nestedData['assetlist_id'] = $productUnit->assetname->name;
                $nestedData['reason'] = $productUnit->reason;
                $nestedData['qty'] = $productUnit->qty;
                $nestedData['destroy_date'] = $productUnit->destroy_date;
                $nestedData['destroy_by'] = $productUnit->destroy_by;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('assets.destroy.edit', $productUnit->id) . '" class="btn btn-xs btn-default" style="display:none"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('assets.destroy.show', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('assets.destroy.destroy', $productUnit->id) . '" delete_id="' . $productUnit->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $productUnit->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->destroyItems::find($id);
        return $result;
    }

    public function store($request)
    {

        $productUnit = new $this->destroyItems();
        $productUnit->assetlist_id = $request->assetlist_id;
        $productUnit->reason = $request->reason;
        $productUnit->qty = $request->qty;
        $productUnit->destroy_date = $request->destroy_date;
        $productUnit->destroy_by = Auth::user()->id;
        $productUnit->save();
        return $productUnit;
    }

    public function update($request, $id)
    {
        $productUnit = $this->destroyItems::findOrFail($id);
        $productUnit->name = $request->name;
        $productUnit->account_id = $request->name;
        $productUnit->category_asset_id = $request->category_asset_id;
        $productUnit->_date = $request->_date;
        $productUnit->qty = $request->qty;
        $productUnit->amount = $request->amount;
        $productUnit->save();
        return $productUnit;
    }


    public function destroy($id)
    {
        $productUnit = $this->destroyItems::find($id);
        $productUnit->delete();
        return true;
    }
}
