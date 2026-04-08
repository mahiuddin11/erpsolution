<?php

namespace App\Repositories\Service;

use App\Helpers\Helper;
use App\Models\Service;
use App\Models\Transection;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var Service
     */
    private $Service;

    /**
     * CourseRepository constructor.
     * @param Service $eService
     */
    public function __construct(Service $Service)
    {
        $this->Service = $Service;
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
        $result = $this->Service::latest()->get();
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
            1 => 'serciveCode',
        );


        $auth = Auth::user();

        $edit = Helper::roleAccess('service.service.edit')  ? 1 : 0;
        $delete = Helper::roleAccess('service.service.destroy')  ? 1 : 0;
        $view = Helper::roleAccess('service.service.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $Servicebranch = Service::where('branch_id', $auth->branch_id)->get();
        $Serviceid = "";
        foreach ($Servicebranch as $value) {
            $Serviceid .= $value->id . ',';
        }
        $totalData = $this->Service::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $Service = $this->Service::offset($start);
            if ($auth->branch_id !== null) {
                $Service = $Service->where('branch_id', $auth->branch_id);
            }
            $Service = $Service->limit($limit);
            $Service = $Service->orderBy($order, $dir);
            $Service = $Service->get();
            $totalFiltered = $this->Service::count();
        } else {
            $search = $request->input('search.value');
            $Service = $this->Service::where('serciveCode', 'like', "%{$search}%");
            if ($auth->branch_id !== null) {
                $Service = $Service->where('branch_id', $auth->branch_id);
            }
            $Service = $Service->offset($start);
            $Service = $Service->limit($limit);
            $Service = $Service->get();
            $totalFiltered = $this->Service::where('serciveCode', 'like', "%{$search}%")->count();
        }


        $data = array();
        if ($Service) {
            foreach ($Service as $key => $eService) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $eService->date;
                $nestedData['serciveCode'] = $eService->serciveCode;
                $nestedData['branch_id'] = $eService->branch->branchCode . '-' . $eService->branch->name;
                $nestedData['customer_id'] = $eService->customer->customerCode . '-' . $eService->customer->name;
                $nestedData['details'] = $eService->details;
                $nestedData['amount'] = $eService->amount;
                $nestedData['create_by'] = $eService->user->name;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('service.service.edit', $eService->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view = !0) {
                        $view_data = '<a href="' . route('service.service.show', $eService->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('service.service.destroy', $eService->id) . '" delete_id="' . $eService->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $eService->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

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
        $result = $this->Service::find($id);
        return $result;
    }

    public function store($request)
    {
        $Service = new $this->Service();
        $Service->date = $request->date;
        $Service->serciveCode = $request->serciveCode;
        $Service->branch_id = $request->branch_id;
        $Service->customer_id = $request->customer_id;
        $Service->account_id = $request->account_id;
        $Service->amount = $request->amount;
        $Service->details = $request->details;
        $Service->created_by = Auth::user()->id;
        $Service->save();
        $insertedid = $Service->id;

        $transection = new Transection();
        $transection->date = $request->date;
        $transection->account_id = $request->account_id;
        $transection->payment_id = $insertedid;
        $transection->branch_id = $request->branch_id;
        $transection->type = 13;
        $transection->note = $request->details;
        $transection->amount = $request->amount;
        $transection->debit = $request->amount;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        return $Service;
    }

    public function update($request, $id)
    {
        $Service = $this->Service::find($id);
        $Service->date = $request->date;
        $Service->serciveCode = $request->serciveCode;
        $Service->branch_id = $request->branch_id;
        $Service->customer_id = $request->customer_id;
        $Service->account_id = $request->account_id;
        $Service->amount = $request->amount;
        $Service->details = $request->details;
        $Service->updated_by = Auth::user()->id;
        $Service->save();
        $insertedid = $Service->id;

        Transection::where('type', 13)->where('payment_id', $id)->forceDelete();

        $transection = new Transection();
        $transection->date = $request->date;
        $transection->account_id = $request->account_id;
        $transection->payment_id = $insertedid;
        $transection->branch_id = $request->branch_id;
        $transection->type = 13;
        $transection->note = $request->details;
        $transection->amount = $request->amount;
        $transection->debit = $request->amount;
        $transection->updated_by = Auth::user()->id;
        $transection->save();

        return $Service;
    }

    public function statusUpdate($id, $status)
    {
        $eService = $this->Service::find($id);
        $eService->status = $status;
        $eService->save();
        return $eService;
    }

    public function destroy($id)
    {
        $eService = $this->Service::find($id);
        $eService->delete();
        return true;
    }
}
