<?php

namespace App\Repositories\AssetsManagement;

use App\Helpers\Helper;
use App\Models\AssetsList;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use phpDocumentor\Reflection\PseudoTypes\False_;

class FinancialYearRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var FinancialYear
     */
    private $FinancialYear;
    /**
     * CourseRepository constructor.
     * @param assetsList $productUnit
     */
    public function __construct(FinancialYear $FinancialYear)
    {
        $this->FinancialYear = $FinancialYear;
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
        $result = $this->FinancialYear::latest()->get();
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

        $edit = Helper::roleAccess('assets.list.edit') ? 1 : 0;
        $delete = Helper::roleAccess('assets.list.destroy') ? 1 : 0;
        $view = Helper::roleAccess('assets.list.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->FinancialYear::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $productUnits = $this->FinancialYear::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->FinancialYear::count();
        } else {
            $search = $request->input('search.value');
            $productUnits = $this->FinancialYear::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->FinancialYear::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($productUnits) {
            foreach ($productUnits as $key => $productUnit) {
                $nestedData['id'] = $key + 1;
                $nestedData['f_year'] = $productUnit->f_year;
                $nestedData['f_year_start'] = $productUnit->f_year_start;
                $nestedData['f_year_end'] = $productUnit->f_year_end;
                if ($productUnit->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('financial.year.status', [$productUnit->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('financial.year.status', [$productUnit->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('financial.year.edit', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('financial.year.show', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('financial.year.destroy', $productUnit->id) . '" delete_id="' . $productUnit->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $productUnit->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->FinancialYear::find($id);
        return $result;
    }

    public function store($request)
    {

        $productUnit = new $this->FinancialYear();
        $productUnit->f_year = $request->f_year;
        $productUnit->f_year_start = $request->f_year_start;
        $productUnit->f_year_end = $request->f_year_end;
        $productUnit->status = $request->status;
        $productUnit->save();
        return $productUnit;
    }

    public function update($request, $id)
    {
        $productUnit = $this->FinancialYear::findOrFail($id);
        $productUnit->f_year = $request->f_year;
        $productUnit->f_year_start = $request->f_year_start;
        $productUnit->f_year_end = $request->f_year_end;
        $productUnit->save();
        return $productUnit;
    }

    public function statusUpdate($id, $status)
    {
        $productUnit = $this->FinancialYear::find($id);
        $productUnit->status = $status;
        $productUnit->save();
        return $productUnit;
    }

    public function destroy($id)
    {
        $productUnit = $this->FinancialYear::find($id);
        $productUnit->delete();
        return true;
    }
}
