<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversion;
use phpDocumentor\Reflection\PseudoTypes\False_;

class ConversionRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var conversion
     */
    private $conversion;
    /**
     * CourseRepository constructor.
     * @param conversion $conversion
     */
    public function __construct(conversion $conversion)
    {
        $this->conversion = $conversion;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }
    /**
    
    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        $result = $this->conversion::latest()->get();
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
            1 => 'title',
        );

        $edit = Helper::roleAccess('inventorySetup.conversion.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.conversion.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.conversion.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->conversion::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $conversions = $this->conversion::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->conversion::count();
        } else {
            $search = $request->input('search.value');
            $conversions = $this->conversion::where('title', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->conversion::where('title', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($conversions) {
            foreach ($conversions as $key => $conversion) {
                $nestedData['id'] = $key + 1;
                $nestedData['title'] = $conversion->title;
                $nestedData['rate'] = $conversion->rate;
                if ($conversion->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.conversion.status', [$conversion->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.conversion.status', [$conversion->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.conversion.edit', $conversion->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.conversion.show', $conversion->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.conversion.destroy', $conversion->id) . '" delete_id="' . $conversion->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $conversion->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->conversion::find($id);
        return $result;
    }

    public function store($request)
    {

        //    / dd($request->all());

        $conversion = new $this->conversion();
        $conversion->title = $request->title;
        $conversion->rate = $request->rate;
        $conversion->status = 'Active';

        $conversion->save();
        return $conversion;
    }

    public function update($request, $id)
    {
        $conversion = $this->conversion::findOrFail($id);
        $conversion->title = $request->title;
        $conversion->rate = $request->rate;
        $conversion->status = 'Active';
        $conversion->save();
        return $conversion;
    }

    public function statusUpdate($id, $status)
    {
        $conversion = $this->conversion::find($id);
        $conversion->status = $status;
        $conversion->save();
        return $conversion;
    }

    public function destroy($id)
    {
        $conversion = $this->conversion::find($id);
        $conversion->delete();
        return true;
    }
}
