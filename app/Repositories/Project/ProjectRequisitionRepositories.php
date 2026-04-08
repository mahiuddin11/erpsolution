<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;
use App\Models\ExpenseCategory;
use App\Models\ProjectRequisition;
use App\Models\Transection;
use App\Models\Project;
use App\Models\ProjectRequisitionDetails;
use App\Models\Stock;
use App\Models\StockSummary;
use phpDocumentor\Reflection\PseudoTypes\False_;

class ProjectRequisitionRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Opening
     */
    private $ProjectRequisition;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(ProjectRequisition $ProjectRequisition)
    {
        $this->ProjectRequisition = $ProjectRequisition;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOpening()
    {
        return  $this->ProjectRequisition::get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {

        $columns = array(
            0 => 'id',
            1 => 'amount',
        );

        $user = Auth::user();

        $projectbranch = Project::where('branch_id', $user->branch_id)->get();
        $projectid = "";
        foreach ($projectbranch as $value) {
            $projectid .= $value->id . ',';
        }
        $project = Project::where('manager_id', $user->id)->pluck('condition')->first();
        $condition = $project ? $project : "Complete";


        $edit = Helper::roleAccess('project.Productrequisition.edit')  ? 1 : 0;
        $delete = Helper::roleAccess('project.Productrequisition.destroy')  ? 1 : 0;
        $view = Helper::roleAccess('project.Productrequisition.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $user = Auth::user();

        $totalData = $this->ProjectRequisition::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if ($user->branch_id == null) {

            if (empty($request->input('search.value'))) {
                $ProjectRequisition = $this->ProjectRequisition::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::count();
            } else {
                $search = $request->input('search.value');
                $ProjectRequisition = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")->count();
            }
        } else {

            if (empty($request->input('search.value'))) {
                $ProjectRequisition = $this->ProjectRequisition::offset($start)
                    ->whereIn('project_id', [$projectid])
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::count();
            } else {
                $search = $request->input('search.value');
                $ProjectRequisition = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")
                    ->whereIn('project_id', [$projectid])
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")->count();
            }
        }

        $data = array();
        if ($ProjectRequisition) {
            foreach ($ProjectRequisition as $key => $expens) {
                // dd($expens->projects);
                $nestedData['id'] = $key + 1;
                $nestedData['project_id'] = $expens->projects->name;
                $nestedData['date'] = $expens->date;
                // $nestedData['total_price'] = $expens->total_price;
                // $nestedData['branch_id'] = $expens->branch_id ? $expens->branch->name : "N/A";
                $nestedData['approve_by'] = $expens->approve_by ? $expens->user->name : 'N/A';
                $nestedData['user_id'] = $expens->user ? $expens->user->name : 'N/A';
                if ($expens->status == 'Accepted') {

                    $nestedData['status'] = '<button class="btn btn-success btn-sm">Accepted</button>';
                } elseif ($expens->status == 'Pending') {

                    $nestedData['status'] = '<button class="btn btn-warning btn-sm">Pending</button>';
                } else {
                    $nestedData['status'] = '<button class="btn btn-danger btn-sm">Cancel</button>';
                }

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('project.Productrequisition.edit', $expens->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view = !0)
                        $view_data = '<a href="' . route('project.Productrequisition.show', $expens->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('project.Productrequisition.destroy', $expens->id) . '" delete_id="' . $expens->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $expens->id . '"><i class="fa fa-times"></i></a>';
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
    public function getpandingList($request)
    {



        $columns = array(
            0 => 'id',
        );

        $user = Auth::user();

        $approve = Helper::roleAccess('project.RequisitionAction.approve') ? 1 : 0;
        $delete = Helper::roleAccess('project.RequisitionAction.destroy') ? 1 : 0;
        $ced = $approve + $delete;

        $user = Auth::user();
        // dd($user->branch_id);
        $totalData = $this->ProjectRequisition::count();
        // dd($totalData);
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if ($user->branch === null) {
            if (empty($request->input('search.value'))) {
                $ProjectRequisition = $this->ProjectRequisition::offset($start)
                    ->where('status', 'Pending')
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::count();
            } else {
                $search = $request->input('search.value');
                $ProjectRequisition = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")
                    ->where('status', 'Pending')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")->count();
            }
        } else {
            if (empty($request->input('search.value'))) {
                $ProjectRequisition = $this->ProjectRequisition::offset($start)
                    ->where('branch_id', $user->branch_id)
                    ->where('status', 'Pending')
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::count();
            } else {
                $search = $request->input('search.value');
                $ProjectRequisition = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")
                    ->where('branch_id', $user->branch_id)
                    ->where('status', 'Pending')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectRequisition::where('amount', 'like', "%{$search}%")->count();
            }
        }






        $data = array();
        if ($ProjectRequisition) {
            foreach ($ProjectRequisition as $key => $expens) {
                $nestedData['id'] = $key + 1;
                $nestedData['project_id'] = $expens->projects->name;
                $nestedData['date'] = $expens->date;
                $nestedData['total_price'] = $expens->total_price;
                $nestedData['approve_at'] = $expens->approve_at ? $expens->approve_at : 'N/A';
                $nestedData['user_id'] = $expens->user ? $expens->user->name : 'N/A';
                if ($expens->status == 'Accepted') {

                    $nestedData['status'] = '<button class="btn btn-success btn-sm">Accepted</button>';
                } elseif ($expens->status == 'Pending') {

                    $nestedData['status'] = '<button class="btn btn-warning btn-sm">Pending</button>';
                } else {
                    $nestedData['status'] = '<button class="btn btn-danger btn-sm">Cancel</button>';
                }

                if ($ced != 0) :
                    if ($approve != 0)
                        $approve_data = '<a href="' . route('project.RequisitionAction.approve', $expens->id) . '" class="btn btn-xs btn-default"><i class="fas fa-check"></i></a>';
                    else
                        $approve_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('project.RequisitionAction.destroy', $expens->id) . '" delete_id="' . $expens->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $expens->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';
                    $nestedData['action'] = $approve_data . ' ' . $delete_data;
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
        $result = $this->ProjectRequisition::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd($request->all(), array_sum($request->total));
        DB::beginTransaction();
        try {
            $projectrequisition = new $this->ProjectRequisition();
            $projectrequisition->invoice_no = $request->requisitionCode;
            $projectrequisition->project_id = $request->project_id;
            $projectrequisition->date = $request->date;
            // $projectrequisition->branch_id = $request->branch_id;
            $projectrequisition->total_qty = array_sum($request->qty ?? [0]);
            $projectrequisition->unitprice_price = array_sum($request->unitprice ?? [0]);
            $projectrequisition->total_price = array_sum($request->total ?? [0]);
            $projectrequisition->user_id = Auth::user()->id;
            $projectrequisition->note = $request->note;
            $projectrequisition->save();
            $project_requisiton_id = $projectrequisition->id;

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;

            for ($i = 0; $i < count($category); $i++) {
                $purchasedetails = new ProjectRequisitionDetails();
                $purchasedetails->project_requisition_id = $project_requisiton_id;
                $purchasedetails->category_id = $category[$i];
                $purchasedetails->product_id = $product[$i];
                $purchasedetails->unit_price = $unitprice[$i] ?? null;
                $purchasedetails->total_price = $total[$i] ?? null;
                $purchasedetails->qty = $qty[$i] ?? null;
                $purchasedetails->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup.purchaserequisition.create')->with('error', 'Something Wrong Please try again' . "Error" . $e->getMessage() . 'Line' . $e->getLine());
        }
        return true;
    }

    public function update($request, $id)
    {
        //  dd($request->all());
        DB::beginTransaction();
        try {
            $purchaserequisition = $this->ProjectRequisition::find($id);
            $purchaserequisition->project_id = $request->project_id;
            $purchaserequisition->date = $request->date;
            // $purchaserequisition->branch_id = $request->branch_id;
            $purchaserequisition->update_by = Auth::user()->id;
            $purchaserequisition->note = $request->note;
            $purchaserequisition->save();
            $project_requisiton_id = $purchaserequisition->id;

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;

            ProjectRequisitionDetails::where('project_requisition_id', $id)->forceDelete();
            for ($i = 0; $i < count($category); $i++) {
                $purchasedetails = new ProjectRequisitionDetails();
                $purchasedetails->project_requisition_id = $project_requisiton_id;
                $purchasedetails->category_id = $category[$i];
                $purchasedetails->product_id = $product[$i];
                $purchasedetails->unit_price = $unitprice[$i] ?? null;
                $purchasedetails->total_price = $total[$i] ?? null;
                $purchasedetails->qty = $qty[$i] ?? null;
                $purchasedetails->status = 'Pending';
                $purchasedetails->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup.purchaserequisition.create')->with('error', 'Something Wrong Please try again');
        }
        return $purchaserequisition;
    }

    public function approveupdate($request, $id)
    {
        // dd($request->all());
        for ($i = 0; $i < count($request->product_nm); $i++) {
            $product_array = array(
                'type' => 'Branch',
                'branch_id' =>  $request->branch_id,
                'product_id' => $request->product_nm[$i],
            );
            $stocksamary = StockSummary::where($product_array)->exists();
            $stocksamaryquntity = StockSummary::where($product_array)->pluck('quantity')->first();
            if (!$stocksamary || $stocksamaryquntity < $request->qty[$i]) {
                session()->flash('error', 'product not available in stock!!');
                return;
            }
        }

        DB::beginTransaction();
        try {
            $purchaserequisition = $this->ProjectRequisition::find($id);
            $purchaserequisition->approve_at = date('Y-m-d');
            $purchaserequisition->approve_qty = array_sum($request->qty);
            $purchaserequisition->unitprice_price = array_sum($request->unitprice ?? [0]);
            $purchaserequisition->total_price = array_sum($request->total ?? [0]);
            $purchaserequisition->approve_by = Auth::user()->id;
            $purchaserequisition->status = 'Accepted';
            $purchaserequisition->save();
            $project_requisiton_id = $purchaserequisition->id;

            $category = $request->category_nm;
            $product = $request->product_nm;
            $qty = $request->qty;
            $unitprice = $request->unitprice;
            $total = $request->total;

            ProjectRequisitionDetails::where('project_requisition_id', $id)->forceDelete();
            for ($i = 0; $i < count($category); $i++) {
                $purchasedetails = new ProjectRequisitionDetails();
                $purchasedetails->project_requisition_id = $project_requisiton_id;
                $purchasedetails->category_id = $category[$i];
                $purchasedetails->product_id = $product[$i];
                $purchasedetails->unit_price = $unitprice[$i];
                $purchasedetails->total_price = $total[$i] ?? null;
                $purchasedetails->qty = $qty[$i] ?? null;
                $purchasedetails->status = 'Accepted';
                $purchasedetails->save();

                $stock = new Stock();
                $stock->general_id = $project_requisiton_id;
                $stock->product_id = $product[$i];
                $stock->quantity = $qty[$i];
                $stock->branch_id = $request->branch_id;
                $stock->unit_price = $unitprice[$i];
                $stock->total_price = $total[$i] ?? 0;
                $stock->date = $request->date;
                $stock->status = 'Project Out';
                $stock->save();

                $stock = new Stock();
                $stock->general_id = $project_requisiton_id;
                $stock->product_id = $product[$i];
                $stock->quantity = $qty[$i];
                $stock->branch_id = $request->project; // project id insert on branch_id column
                $stock->unit_price = $unitprice[$i] ?? 0;
                $stock->total_price = $total[$i] ?? 0;
                $stock->date = $request->date;
                $stock->status = 'Project In';
                $stock->save();

                $products_array = array(
                    'type' => 'Branch',
                    'branch_id' =>  $request->branch_id,
                    'product_id' => $request->product_nm[$i],
                );
                $project_array = array(
                    'type' => 'Project',
                    'branch_id' =>  $request->project,
                    'product_id' => $request->product_nm[$i],
                );

                $stocksamaryquntitys = StockSummary::where($products_array)->pluck('quantity')->first();
                $stockcheck['quantity'] = $stocksamaryquntitys - $request->qty[$i];
                StockSummary::where($products_array)->update($stockcheck);

                $stocksamaryproject = StockSummary::where($project_array)->exists();

                if ($stocksamaryproject) {
                    $stocksamaryprojects = StockSummary::where($project_array)->pluck('quantity')->first();
                    $stockchecko['quantity'] = $stocksamaryprojects + $request->qty[$i];
                    StockSummary::where($project_array)->update($stockchecko);
                } else {
                    $updatestock = new StockSummary();
                    $updatestock->branch_id = $request->project;
                    $updatestock->product_id =  $product[$i];
                    $updatestock->quantity = $qty[$i];
                    $updatestock->type = 'Project';
                    $updatestock->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            redirect('inventorySetup.purchaserequisition.create')->with('error', 'Something Wrong Please try again');
        }
        return $purchaserequisition;
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->ProjectRequisition::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $opening = $this->ProjectRequisition::find($id);
        if ($opening->status == 'Accepted') {
            session()->flash('error', 'This item is Accepted, You can not delete it!!');
            return;
        } else {
            $opening->delete();
            ProjectRequisitionDetails::where('project_requisition_id', $id)->delete();
            return true;
        }
    }
}
