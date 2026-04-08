<?php

namespace App\Repositories\Settings;
Use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\SmsSetting;
use phpDocumentor\Reflection\PseudoTypes\False_;

class SmsSettingRepository
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var SmsSetting
     */
    private $smsSetting;
    /**
     * CourseRepository constructor.
     * @param smsSetting $smsSetting
     */
    public function __construct(SmsSetting $smsSetting)
    {
        $this->smsSetting = $smsSetting;
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
            1 => 'api_key',
            2 => 'api_secret',
            3 => 'sender_mobile',
            4 => 'sales',
 );

        $edit = Helper::roleAccess('settings.sms_setting.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.sms_setting.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.sms_setting.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->smsSetting::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $smsSettings = $this->smsSetting::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->smsSetting::count();
        } else {
            $search = $request->input('search.value');
            $smsSettings = $this->smsSetting::where('api_key', 'like', "%{$search}%")->orWhere('api_secret', 'like', "%{$search}%")->orWhere('sender_mobile', 'like', "%{$search}%")->orWhere('sales', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->smsSetting::where('api_key', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($smsSettings) {
            foreach ($smsSettings as $key => $smsSetting) {
                $nestedData['id'] = $key + 1;
                $nestedData['api_key'] = $smsSetting->api_key;
                $nestedData['api_secret'] = $smsSetting->api_secret;
                $nestedData['sender_mobile'] = $smsSetting->sender_mobile;
                $nestedData['sales'] = $smsSetting->sales;
                $nestedData['purchases'] = $smsSetting->purchases;
                $nestedData['payment_voucher'] = $smsSetting->payment_voucher;
                $nestedData['receive_voucher'] = $smsSetting->receive_voucher;
                if ($smsSetting->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('settings.sms_setting.status', [$smsSetting->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('settings.sms_setting.status', [$smsSetting->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
        if ($ced != 0) :
            if ($edit != 0)
                $edit_data = '<a href="' . route('settings.sms_setting.edit', $smsSetting->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
            else
                $edit_data = '';
            if ($view =! 0)
                $view_data = '<a href="' . route('settings.sms_setting.show', $smsSetting->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            else
                $view_data = '';
            if ($delete != 0)
                $delete_data = '<a delete_route="' . route('settings.sms_setting.destroy', $smsSetting->id) . '" delete_id="' . $smsSetting->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $smsSetting->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->smsSetting::find($id);
        return $result;
    }

    public function store($request)
    {
        $smsSetting = new $this->smsSetting();
        $smsSetting->api_key = $request->api_key;
        $smsSetting->api_secret = $request->api_secret;
        $smsSetting->sender_mobile = $request->sender_mobile;
        $smsSetting->sales = $request->sales;
        $smsSetting->purchases = $request->purchases;
        $smsSetting->payment_voucher = $request->payment_voucher;
        $smsSetting->receive_voucher = $request->receive_voucher;
        $smsSetting->status = 'Active';
        $smsSetting->created_by = Auth::user()->id;
        $smsSetting->save();
        return $smsSetting;
    }

    public function update($request, $id)
    {
        $smsSetting = $this->smsSetting::findOrFail($id);
        $smsSetting->api_key = $request->api_key;
        $smsSetting->api_secret = $request->api_secret;
        $smsSetting->sender_mobile = $request->sender_mobile;
        $smsSetting->sales = $request->sales;
        $smsSetting->purchases = $request->purchases;
        $smsSetting->payment_voucher = $request->payment_voucher;
        $smsSetting->receive_voucher = $request->receive_voucher;
        $smsSetting->status = 'Active';
        $smsSetting->updated_by = Auth::user()->id;
        $smsSetting->save();
        return $smsSetting;
    }

    public function statusUpdate($id, $status)
    {
        $smsSetting = $this->smsSetting::find($id);
        $smsSetting->status = $status;
        $smsSetting->save();
        return $smsSetting;
    }

    public function destroy($id)
    {
        $smsSetting = $this->smsSetting::find($id);
        $smsSetting->delete();
        return true;
    }
}