<?php

namespace App\Repositories\Settings;
Use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Smtp;
use phpDocumentor\Reflection\PseudoTypes\False_;

class SmptRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var smpt
     */
    private $smpt;
    /**
     * CourseRepository constructor.
     * @param smpt $smpt
     */
    public function __construct(Smtp $smpt)
    {
        $this->smpt = $smpt;
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
            1 => 'protocol',
            2 => 'smtp_host',
            3 => 'smtp_port',
        );

        $edit = Helper::roleAccess('settings.smpt.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.smpt.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.smpt.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->smpt::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $smpts = $this->smpt::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->smpt::count();
        } else {
            $search = $request->input('search.value');
            $smpts = $this->smpt::where('protocol', 'like', "%{$search}%")
                ->orWhere('smtp_host', 'like', "%{$search}%")
                ->orWhere('sender_mail', 'like', "%{$search}%")
                ->orWhere('smtp_port', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->smpt::where('protocol', 'like', "%{$search}%")
                ->orWhere('smtp_host', 'like', "%{$search}%")
                ->orWhere('sender_mail', 'like', "%{$search}%")
                ->orWhere('smtp_port', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($smpts) {
            foreach ($smpts as $key => $smpt) {
                $nestedData['id'] = $key + 1;
                $nestedData['protocol'] = $smpt->protocol;
                $nestedData['smtp_host'] = $smpt->smtp_host;
                $nestedData['smtp_port'] = $smpt->smtp_port;
                $nestedData['sender_mail'] = $smpt->sender_mail;
                $nestedData['password'] = $smpt->password;
                if ($smpt->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('settings.smpt.status', [$smpt->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('settings.smpt.status', [$smpt->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
        if ($ced != 0) :
            if ($edit != 0)
                $edit_data = '<a href="' . route('settings.smpt.edit', $smpt->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
            else
                $edit_data = '';
            if ($view =! 0)
                $view_data = '<a href="' . route('settings.smpt.show', $smpt->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            else
                $view_data = '';
            if ($delete != 0)
                $delete_data = '<a delete_route="' . route('settings.smpt.destroy', $smpt->id) . '" delete_id="' . $smpt->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $smpt->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->smpt::find($id);
        return $result;
    }

    public function store($request)
    {
        $smpt = new $this->smpt();
        $smpt->protocol = $request->protocol;
        $smpt->smtp_host = $request->smtp_host;
        $smpt->smtp_port     = $request->smtp_port;
        $smpt->sender_mail = $request->sender_mail;
        $smpt->password = $request->password;
        $smpt->status = 'Active';
        $smpt->created_by = Auth::user()->id;
        $smpt->save();
        return $smpt;
    }

    public function update($request, $id)
    {
        $smpt = $this->smpt::findOrFail($id);
        $smpt->protocol = $request->protocol;
        $smpt->smtp_host = $request->smtp_host;
        $smpt->smtp_port     = $request->smtp_port;
        $smpt->sender_mail = $request->sender_mail;
        $smpt->password = $request->password;
        $smpt->status = 'Active';
        $smpt->updated_by = Auth::user()->id;
        $smpt->save();
        return $smpt;
    }

    public function statusUpdate($id, $status)
    {
        $smpt = $this->smpt::find($id);
        $smpt->status = $status;
        $smpt->save();
        return $smpt;
    }

    public function destroy($id)
    {
        $smpt = $this->smpt::find($id);
        $smpt->delete();
        return true;
    }
}