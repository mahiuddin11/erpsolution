<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\AssetsList;
use App\Models\Award;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AwardRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Award
     */
    private $Award;
    /**
     * CourseRepository constructor.
     * @param assetsList $productUnit
     */
    public function __construct(Award $Award)
    {
        $this->Award = $Award;
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

        $result = $this->Award::latest()->get();
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

        $edit = Helper::roleAccess('hrm.award.edit') ? 1 : 0;
        $delete = Helper::roleAccess('hrm.award.destroy') ? 1 : 0;
        $view = Helper::roleAccess('hrm.award.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->Award::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $productUnits = $this->Award::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->Award::count();
        } else {
            $search = $request->input('search.value');
            $productUnits = $this->Award::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->Award::where('name', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($productUnits) {
            foreach ($productUnits as $key => $productUnit) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $productUnit->name;
                $nestedData['desc'] = $productUnit->desc;
                $nestedData['gift_item'] = $productUnit->gift_item;
                $nestedData['date'] = $productUnit->date;
                $nestedData['employee_id'] = $productUnit->employee->name ?? "";
                $nestedData['award_by'] = $productUnit->award_by;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('hrm.award.edit', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('hrm.award.show', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('hrm.award.destroy', $productUnit->id) . '" delete_id="' . $productUnit->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $productUnit->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->Award::find($id);
        return $result;
    }

    public function store($request)
    {

        $productUnit = new $this->Award();
        $productUnit->name = $request->name;
        $productUnit->desc = $request->desc;
        $productUnit->gift_item = $request->gift_item;
        $productUnit->date = $request->date;
        $productUnit->employee_id = $request->employee_id;
        $productUnit->award_by = $request->award_by;
        $productUnit->save();
        return $productUnit;
    }

    public function update($request, $id)
    {
        $productUnit = $this->Award::findOrFail($id);
        $productUnit->name = $request->name;
        $productUnit->desc = $request->desc;
        $productUnit->gift_item = $request->gift_item;
        $productUnit->date = $request->date;
        $productUnit->employee_id = $request->employee_id;
        $productUnit->award_by = $request->award_by;
        $productUnit->save();
        return $productUnit;
    }

    public function statusUpdate($id, $status)
    {
        $productUnit = $this->Award::find($id);
        $productUnit->status = $status;
        $productUnit->save();
        return $productUnit;
    }

    public function destroy($id)
    {
        $productUnit = $this->Award::find($id);
        $productUnit->delete();
        return true;
    }
}
