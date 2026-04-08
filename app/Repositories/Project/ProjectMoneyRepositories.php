<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\ProjectMoney;
use App\Models\Transection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectMoneyRepositories
{
    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var projectMoney
     */
    private $projectMoney;

    /**
     * CourseRepository constructor.
     * @param projectMoney $eprojectMoney
     */
    public function __construct(projectMoney $projectMoneys)
    {
        $this->projectMoney = $projectMoneys;
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
        $result = $this->projectMoney::latest()->get();
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
            1 => 'projectMoneyCode',
        );

        $edit = Helper::roleAccess('project.balance.edit') ? 1 : 0;
        $delete = Helper::roleAccess('project.balance.destroy') ? 1 : 0;
        $view = Helper::roleAccess('project.balance.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->projectMoney::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $projectMoney = $this->projectMoney::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->projectMoney::count();
        } else {
            $search = $request->input('search.value');
            $projectMoney = $this->projectMoney::where('projectBananceCode', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->projectMoney::where('projectBananceCode', 'like', "%{$search}%")->count();
        }  

        $data = array();
        if ($projectMoney) {
            foreach ($projectMoney as $key => $eprojectMoney) {
                $nestedData['id'] = $key + 1;
                $nestedData['project_id'] = $eprojectMoney->project->name;
                $nestedData['projectBananceCode'] = $eprojectMoney->projectBananceCode;
                $nestedData['account_id'] = $eprojectMoney->account->account_name;
                $nestedData['date'] = $eprojectMoney->date;
                $nestedData['debit'] = $eprojectMoney->debit;
                $nestedData['credit'] = $eprojectMoney->credit;
                $nestedData['note'] = $eprojectMoney->note;

                if ($eprojectMoney->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('project.balance.status', [$eprojectMoney->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('project.balance.status', [$eprojectMoney->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('project.balance.edit', $eprojectMoney->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view != 0) {
                        $view_data = '<a href="' . route('project.balance.show', $eprojectMoney->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('project.balance.destroy', $eprojectMoney->id) . '" delete_id="' . $eprojectMoney->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $eprojectMoney->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->projectMoney::find($id);
        return $result;
    }

    public function store($request)
    {
        //dd($request->all());

        $eprojectMoney = new $this->projectMoney();
        $eprojectMoney->projectBananceCode = $request->projectBananceCode;
        $eprojectMoney->date = $request->date;
        $eprojectMoney->project_id = $request->project_id;
        $eprojectMoney->account_id = $request->account_id;
        $eprojectMoney->debit = $request->debit;
        $eprojectMoney->note = $request->note;
        $eprojectMoney->created_by = Auth::user()->id;
        $eprojectMoney->save();
        $payment_id = $eprojectMoney->id;


        $transection = new transection();
        $transection->account_id = $request->account_id;
        $transection->credit = $request->debit;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->payment_id = $payment_id;
        $transection->type = 12;
        $transection->created_by = Auth::user()->id;
        $transection->save();
        return $transection;
    }

    public function update($request, $id)
    {
        $eprojectMoney =  projectMoney::find($id);
        $eprojectMoney->date = $request->date;
        $eprojectMoney->project_id = $request->project_id;
        $eprojectMoney->account_id = $request->account_id;
        $eprojectMoney->debit = $request->debit;
        $eprojectMoney->note = $request->note;
        $eprojectMoney->updated_by = Auth::user()->id;
        $eprojectMoney->save();
        $payment_id = $eprojectMoney->id;

        $transection =  Transection::find($id);
        $transection->account_id = $request->account_id;
        $transection->credit = $request->debit;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->payment_id = $payment_id;
        $transection->type = 12;
        $transection->created_by = Auth::user()->id;
        $transection->save();
        return $transection;
    }

    public function statusUpdate($id, $status)
    {
        $eprojectMoney = $this->projectMoney::find($id);
        $eprojectMoney->status = $status;
        $eprojectMoney->save();
        return $eprojectMoney;
    }

    public function destroy($id)
    {
        $eprojectMoney = $this->projectMoney::find($id);
        $eprojectMoney->delete();
        return true;
    }
}
