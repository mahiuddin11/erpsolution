<?php

namespace App\Repositories\Settings;
Use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\GeneralSetup;
use phpDocumentor\Reflection\PseudoTypes\False_;

class GeneralSettingRepository
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var GeneralSetup
     */
    private $generalSetup;
    /**
     * CourseRepository constructor.
     * @param generalSetup $generalSetup
     */
    public function __construct(GeneralSetup $generalSetup)
    {
        $this->generalSetup = $generalSetup;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }
    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'currency',
            2 => 'currency_position',
            3 => 'language',
            4 => 'timezone',
            5 => 'dateformat',
            6 => 'decimal_separate',
            7 => 'thousand_separate',

        );
        $edit = Helper::roleAccess('settings.general_setup.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.general_setup.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.general_setup.show') ? 1 : 0;
        $ced = $edit + $delete + $view;
        $totalData = $this->generalSetup::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $generalSetups = $this->generalSetup::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->generalSetup::count();
        } else {
            $search = $request->input('search.value');
            $generalSetups = $this->generalSetup::where('currency', 'like', "%{$search}%")->orWhere('language', 'like', "%{$search}%")->orWhere('dateformat', 'like', "%{$search}%")->orWhere('timezone', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->generalSetup::where('currency', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($generalSetups) {
            foreach ($generalSetups as $key => $generalSetup) {
                $nestedData['id'] = $key + 1;
                $nestedData['currency'] = $generalSetup->currency;
                $nestedData['currency_position'] = $generalSetup->currency_position;
                $nestedData['language'] = $generalSetup->language;
                $nestedData['timezone'] = $generalSetup->timezone;
                $nestedData['dateformat'] = $generalSetup->dateformat;
                $nestedData['decimal_separate'] = $generalSetup->decimal_separate;
                $nestedData['thousand_separate'] = $generalSetup->thousand_separate;
                if ($generalSetup->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('settings.general_setup.status', [$generalSetup->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('settings.general_setup.status', [$generalSetup->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
        if ($ced != 0) :
            if ($edit != 0)
                $edit_data = '<a href="' . route('settings.general_setup.edit', $generalSetup->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
            else
                $edit_data = '';
            if ($view =! 0)
                $view_data = '<a href="' . route('settings.general_setup.show', $generalSetup->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            else
                $view_data = '';
            if ($delete != 0)
                $delete_data = '<a delete_route="' . route('settings.general_setup.destroy', $generalSetup->id) . '" delete_id="' . $generalSetup->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $generalSetup->id . '"><i class="fa fa-times"></i></a>';
            else
                $delete_data = '';

                $nestedData['action'] = $edit_data . ' ' . $delete_data . ' ' . $view_data;
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
        $result = $this->generalSetup::find($id);
        return $result;
    }

    public function store($request)
    {
        $generalSetup = new $this->generalSetup();
        $generalSetup->currency = $request->currency;
        $generalSetup->currency_position = $request->currency_position;
        $generalSetup->language = $request->language;
        $generalSetup->timezone = $request->timezone;
        $generalSetup->dateformat = $request->dateformat;
        $generalSetup->decimal_separate = $request->decimal_separate;
        $generalSetup->thousand_separate = $request->thousand_separate;
        $generalSetup->status = 'Active';
        $generalSetup->created_by = Auth::user()->id;
        $generalSetup->save();
        return $generalSetup;
    }

    public function update($request, $id)
    {
        $generalSetup = $this->generalSetup::findOrFail($id);
        $generalSetup->currency = $request->currency;
        $generalSetup->currency_position = $request->currency_position;
        $generalSetup->language = $request->language;
        $generalSetup->timezone = $request->timezone;
        $generalSetup->dateformat = $request->dateformat;
        $generalSetup->decimal_separate = $request->decimal_separate;
        $generalSetup->thousand_separate = $request->thousand_separate;
        $generalSetup->status = 'Active';
        $generalSetup->updated_by = Auth::user()->id;
        $generalSetup->save();
        return $generalSetup;
    }

    public function statusUpdate($id, $status)
    {
        $generalSetup = $this->generalSetup::find($id);
        $generalSetup->status = $status;
        $generalSetup->save();
        return $generalSetup;
    }

    public function destroy($id)
    {
        $generalSetup = $this->generalSetup::find($id);
        $generalSetup->delete();
        return true;
    }
}