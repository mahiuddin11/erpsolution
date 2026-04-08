<?php

namespace App\Repositories\Settings;
Use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use phpDocumentor\Reflection\PseudoTypes\False_;

class LanguageRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Language
     */
    private $language;
    /**
     * CourseRepository constructor.
     * @param language $currency
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
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
            1 => 'name',
        );

        $edit = Helper::roleAccess('settings.language.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.language.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.language.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->language::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $languages = $this->language::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->language::count();
        } else {
            $search = $request->input('search.value');
            $languages = $this->language::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->language::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($languages) {
            foreach ($languages as $key => $language) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $language->name;
                $nestedData['flug'] = $language->flug;
                if ($language->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('settings.language.status', [$language->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('settings.language.status', [$language->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
        if ($ced != 0) :
            if ($edit != 0)
                $edit_data = '<a href="' . route('settings.language.edit', $language->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
            else
                $edit_data = '';
            if ($view =! 0)
                $view_data = '<a href="' . route('settings.language.show', $language->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            else
                $view_data = '';
            if ($delete != 0)
                $delete_data = '<a delete_route="' . route('settings.language.destroy', $language->id) . '" delete_id="' . $language->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $language->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->language::find($id);
        return $result;
    }

    public function store($request)
    {
        $language = new $this->language();
        $language->name = $request->name;
        $language->flug = $request->flug;
        $language->status = 'Active';
        $language->created_by = Auth::user()->id;
        $language->save();
        return $language;
    }

    public function update($request, $id)
    {
        $language = $this->language::findOrFail($id);
        $language->name = $request->name;
        $language->flug = $request->flug;
        $language->status = 'Active';
        $language->updated_by = Auth::user()->id;
        $language->save();
        return $language;
    }

    public function statusUpdate($id, $status)
    {
        $language = $this->language::find($id);
        $language->status = $status;
        $language->save();
        return $language;
    }

    public function destroy($id)
    {
        $language = $this->language::find($id);
        $language->delete();
        return true;
    }
}