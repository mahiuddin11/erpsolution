<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectReturn;
use App\Models\ProjectReturnDetails;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectReturnRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var ProjectReturn
     */
    private $ProjectReturn;

    /**
     * CourseRepository constructor.
     * @param project $eproject
     */
    public function __construct(ProjectReturn $ProjectReturn)
    {
        $this->ProjectReturn = $ProjectReturn;
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
        $result = $this->ProjectReturn::latest()->get();
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
            1 => 'invoice_no',
        );



        $user = Auth::user();
        $project = Project::where('manager_id', $user->id)->pluck('condition')->first();
        $condition = $project ? $project : "Complete";

        $edit = Helper::roleAccess('project.projectreturn.edit')  ? 1 : 0;
        $delete = Helper::roleAccess('project.projectreturn.destroy') ? 1 : 0;
        $view = Helper::roleAccess('project.projectreturn.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->ProjectReturn::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');


        $user = Auth::user();

        $projectbranch = Project::where('branch_id', $user->branch_id)->get();
        $projectid = "";
        foreach ($projectbranch as $value) {
            $projectid .= $value->id . ',';
        }

        if ($user->btnach_id == null) {
            if (empty($request->input('search.value'))) {
                $ProjectReturn = $this->ProjectReturn::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectReturn::count();
            } else {
                $search = $request->input('search.value');
                $ProjectReturn = $this->ProjectReturn::where('invoice_no', 'like', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectReturn::where('invoice_no', 'like', "%{$search}%")->count();
            }
        } else {
            if (empty($request->input('search.value'))) {
                $ProjectReturn = $this->ProjectReturn::offset($start)
                    ->whereIn('project_id', [$projectid])
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectReturn::count();
            } else {
                $search = $request->input('search.value');
                $ProjectReturn = $this->ProjectReturn::where('invoice_no', 'like', "%{$search}%")
                    ->whereIn('project_id', [$projectid])
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectReturn::where('invoice_no', 'like', "%{$search}%")->count();
            }
        }

        $data = array();
        if ($ProjectReturn) {
            foreach ($ProjectReturn as $key => $eproject) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $eproject->date;
                $nestedData['invoice_no'] = $eproject->invoice_no;
                $nestedData['project_id'] = $eproject->project->projectCode . ' - ' . $eproject->project->name;
                $nestedData['branch_id'] = $eproject->branch->branchCode . ' - ' . $eproject->branch->name;
                $nestedData['stock_total'] = $eproject->stock_total;
                $nestedData['return_total'] = $eproject->return_total;
                $nestedData['create_by'] = $eproject->user->name;

                if ($eproject->status == 'Pending') {
                    if (Auth::user()->branch_id == null) {
                        $nestedData['status'] = '<button data-toggle="modal" data-target="#projectreturnapprove" dataId="' . $eproject->id . '" class="btn btn-warning returnid">Pending</button>';
                    } else {
                        $nestedData['status'] = '<button  class="btn btn-warning">Pending</button>';
                    }
                } elseif ($eproject->status == 'Approve') {
                    $nestedData['status'] = '<button href="" class="btn btn-success">Approve</button>';
                } else {
                    $nestedData['status'] = '<button href="" class="btn btn-danger">Cancel</button>';
                }

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('project.projectreturn.edit', $eproject->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view = !0) {
                        $view_data = '<a href="' . route('project.projectreturn.show', $eproject->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }
                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('project.projectreturn.destroy', $eproject->id) . '" delete_id="' . $eproject->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $eproject->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->ProjectReturn::find($id);
        return $result;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $ProjectReturn = new $this->ProjectReturn();
            $ProjectReturn->date = $request->date;
            $ProjectReturn->invoice_no = $request->grnCode;
            $ProjectReturn->project_id = $request->project_id;
            $ProjectReturn->branch_id = $request->branch_id;
            $ProjectReturn->stock_total = array_sum($request->stock);
            $ProjectReturn->return_total = array_sum($request->return_Qty);
            $ProjectReturn->note = $request->note;
            $ProjectReturn->create_by = Auth::user()->id;
            $ProjectReturn->save();
            $projeeReturnID = $ProjectReturn->id;

            $product_id = $request->product_nm;
            $stock = $request->stock;
            $return_Qty = $request->return_Qty;

            for ($i = 0; $i < count($product_id); $i++) {
                $projectReturnDetail = new ProjectReturnDetails();
                $projectReturnDetail->project_return_id = $projeeReturnID;
                $projectReturnDetail->product_id = $product_id[$i];
                $projectReturnDetail->stock_qty = $stock[$i];
                $projectReturnDetail->return_qty = $return_Qty[$i];
                $projectReturnDetail->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-projectreturn-create')->with('error', 'Something Wrong Please try again');
        }

        return $ProjectReturn;
    }

    public function update($request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $ProjectReturn = $this->ProjectReturn::find($id);
            $ProjectReturn->date = $request->date;
            $ProjectReturn->project_id = $request->project_id;
            $ProjectReturn->branch_id = $request->branch_id;
            $ProjectReturn->stock_total = array_sum($request->stock);
            $ProjectReturn->return_total = array_sum($request->return_Qty);
            $ProjectReturn->note = $request->note;
            $ProjectReturn->update_by = Auth::user()->id;
            $ProjectReturn->save();
            $projeeReturnID = $ProjectReturn->id;

            ProjectReturnDetails::where('project_return_id', $id)->forceDelete();

            $product_id = $request->product_nm;
            $stock = $request->stock;
            $return_Qty = $request->return_Qty;

            for ($i = 0; $i < count($product_id); $i++) {
                $projectReturnDetail = new ProjectReturnDetails();
                $projectReturnDetail->project_return_id = $projeeReturnID;
                $projectReturnDetail->product_id = $product_id[$i];
                $projectReturnDetail->stock_qty = $stock[$i];
                $projectReturnDetail->return_qty = $return_Qty[$i];
                $projectReturnDetail->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventory-projectreturn-create')->with('error', 'Something Wrong Please try again');
        }

        return true;
    }

    public function storeapprove($request)
    {
        // dd($request->all());
        $projectreturn = ProjectReturn::find($request->projectReturnId);
        $projectreturnDetails =  ProjectReturnDetails::where('project_return_id', $request->projectReturnId)->get()->toArray();

        for ($i = 0; $i < count($projectreturnDetails); $i++) {
            $checkwhats = array(
                'product_id' => $projectreturnDetails[$i]['product_id'],
                'branch_id' => $projectreturn->project_id,
                'type' => 'Project'
            );

            $getstockdata = StockSummary::where($checkwhats)->pluck('quantity')->first();
            if ($getstockdata < $projectreturnDetails[$i]['return_qty']) {
                $projectstatus['status'] = "Cancel";
                ProjectReturn::where('id', $request->projectReturnId)->update($projectstatus);
                session()->flash('error', 'Return Quantity not available!!');
                return back();
            }
        }

        if ($request->status == 'Cancel') {
            $projectstatus['status'] = $request->status;
            ProjectReturn::where('id', $request->projectReturnId)->update($projectstatus);
        } elseif ($request->status == 'Approve') {
            $projectstatus['status'] = $request->status;
            ProjectReturn::where('id', $request->projectReturnId)->update($projectstatus);

            for ($i = 0; $i < count($projectreturnDetails); $i++) {
                $product = Product::find($projectreturnDetails[$i]['product_id']);
                $calculate = $product->purchases_price * $projectreturnDetails[$i]['return_qty'];
                $stock = new Stock();
                $stock->date = date('Y-m-d');
                $stock->general_id = $request->projectReturnId;
                $stock->branch_id = $projectreturn->branch_id;
                $stock->product_id = $projectreturnDetails[$i]['product_id'];
                $stock->unit_price = $product->purchases_price;
                $stock->total_price = $calculate;
                $stock->quantity = $projectreturnDetails[$i]['return_qty'];
                $stock->status = 'Return';
                $stock->save();

                $stSummerminus = array(
                    'product_id' => $projectreturnDetails[$i]['product_id'],
                    'branch_id' => $projectreturn->project_id,
                    'type' => 'Project'
                );

                $stocksumMin = StockSummary::where($stSummerminus)->pluck('quantity')->first();
                $stocupsates['quantity'] = $stocksumMin - $projectreturnDetails[$i]['return_qty'];
                StockSummary::where($stSummerminus)->update($stocupsates);
            }
        } else {
            session()->flash('error', 'Approve info is invalid!!');
            return false;
        }
        return true;
    }

    public function statusUpdate($id, $status)
    {
        $eproject = $this->ProjectReturn::find($id);
        $eproject->status = $status;
        $eproject->save();
        return $eproject;
    }

    public function destroy($id)
    {
        $eproject = $this->ProjectReturn::find($id);
        if ($eproject->status == "Approve") {
            session()->flash('error', "Sorry, you couldn't delete!!");
            return false;
        } else {
            $eproject->forceDelete();
            ProjectReturnDetails::where('project_return_id', $id)->forceDelete();
            return true;
        }
    }
}
