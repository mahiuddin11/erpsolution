<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\CommissionRule;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CommissionRulesRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var CommissionRule
     */
    private $commission;
    /**
     * CourseRepository constructor.
     * @param CommissionRule $commission
     */
    public function __construct(CommissionRule $commission)
    {
        $this->commission = $commission;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllBranch()
    {
        return  $this->commission::get();
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
            2 => 'phone',
            3 => 'email',
        );

        $edit = Helper::roleAccess('settings.branch.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.branch.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.branch.show') ? 0 : 0;
        $ced = $edit + $delete + $view;



        $totalData = $this->commission::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $commission = $this->commission::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->commission::count();
        } else {
            $search = $request->input('search.value');
            $commission = $this->commission::where('commission_type', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->commission::where('commission_type', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($commission) {
            foreach ($commission as $key => $value) {
                $nestedData['id'] = $key + 1;
                $nestedData['employee'] = $value->salesperson->name ?? "N/A";
                $nestedData['commission_type'] = ucfirst($value->commission_type) ?? "N/A";
                $nestedData['fixed_percentage'] = $value->fixed_percentage ?? "N/A";
                $nestedData['min_amount'] = $value->min_amount ?? "N/A";
                $nestedData['max_amount'] = $value->max_amount ?? "N/A";
                $nestedData['percentage'] = $value->percentage ?? "N/A";

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('settings.commissionRules.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('settings.commissionRules.show', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.commissionRules.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->commission::find($id);
        return $result;
    }

    public function store($request)
    {
        $commisstion = CommissionRule::create($request->all());

        return $commisstion;
    }

    public function update($request, $id)
    {
        $commissionRule = CommissionRule::findOrFail($id);
        $commissionRule->update($request->all());

        return $commissionRule;
    }



    public function destroy($id)
    {
        $branch = $this->commission::find($id);
        $branch->delete();
        return true;
    }
}
