<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\ProductUse;
use App\Models\ProductUseDetails;
use App\Models\Project;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductUseRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var ProductUse
     */
    private $ProductUse;

    /**
     * CourseRepository constructor.
     * @param project $eproject
     */
    public function __construct(ProductUse $ProductUse)
    {
        $this->ProductUse = $ProductUse;
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
        $result = $this->ProductUse::latest()->get();
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
            1 => 'projectCode',
        );

        $user = Auth::user();
        $project = Project::where('manager_id', $user->id)->pluck('condition')->first();
        $condition = $project ? $project : "Complete";

        $edit = Helper::roleAccess('project.productuse.edit') && $condition !== "Complete" ? 0 : 0;
        $delete = Helper::roleAccess('project.productuse.destroy') && $condition !== "Complete" ? 1 : 0;
        $view = Helper::roleAccess('project.productuse.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $user = Auth::user();

        $projectbranch = Project::where('branch_id', $user->branch_id)->get();
        $projectid = "";
        foreach ($projectbranch as $value) {
            $projectid .= $value->id . ',';
        }

        $totalData = $this->ProductUse::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $productuse = $this->ProductUse::offset($start)
                ->whereIn('project_id', [$projectid])
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->ProductUse::count();
        } else {
            $search = $request->input('search.value');
            $productuse = $this->ProductUse::where('projectCode', 'like', "%{$search}%")
                ->whereIn('project_id', [$projectid])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->ProductUse::where('projectCode', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($productuse) {
            foreach ($productuse as $key => $eproject) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $eproject->date;
                $nestedData['invoice_no'] = $eproject->invoice_no;
                $nestedData['project_id'] = $eproject->project->name;
                $nestedData['stock_total'] = $eproject->stock_total;
                $nestedData['use_total'] = $eproject->use_total;
                $nestedData['create_by'] = $eproject->user->name;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('project.productuse.edit', $eproject->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view = !0) {
                        $view_data = '<a href="' . route('project.productuse.show', $eproject->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('project.productuse.destroy', $eproject->id) . '" delete_id="' . $eproject->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $eproject->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->ProductUse::find($id);
        return $result;
    }

    public function store($request)
    {
        $productUse = new $this->ProductUse();
        $productUse->date = $request->date;
        $productUse->invoice_no = $request->grnCode;
        $productUse->project_id = $request->project_id;
        $productUse->stock_total = array_sum($request->stock);
        $productUse->use_total = array_sum($request->useQty);
        $productUse->create_by = Auth::user()->id;
        $productUse->save();
        $projeeID = $productUse->id;

        $product_id = $request->product_nm;
        $stock_id = $request->stock;
        $useQty = $request->useQty;

        for ($i = 0; $i < count($product_id); $i++) {
            $useDetails = new ProductUseDetails();
            $useDetails->product_use_id = $projeeID;
            $useDetails->project_id = $request->project_id;
            $useDetails->product_id = $product_id[$i];
            $useDetails->stock_qty = $stock_id[$i];
            $useDetails->use_qty = $useQty[$i];
            $useDetails->save();

            $stock = new Stock();
            $stock->general_id = $projeeID;
            $stock->product_id = $product_id[$i];
            $stock->quantity = $useQty[$i];
            $stock->branch_id = $request->project_id; // project id insert on branch_id column
            $stock->date = $request->date;
            $stock->status = 'Project Use';
            $stock->save();

            $wherecheck = array(
                'status' => 'Project',
                'product_id' => $product_id[$i],
                'branch_id' => $request->project_id
            );
            $calculate = $stock_id[$i] - $useQty[$i];
            $stocks['quantity'] = $calculate;
            Stock::where($wherecheck)->update($stocks);
            $stocksumwereCHeck = array(
                'type' => 'Project',
                'branch_id' => $request->project_id,
                'product_id' => $product_id[$i],
            );
            $stocksummary['quantity'] = $calculate;
            StockSummary::where($stocksumwereCHeck)->update($stocksummary);
        }

        return $productUse;
    }

    public function update($request, $id)
    {
        // dd($request->all());
        $product_id = $request->product_nm;
        $stock_id = $request->stock;
        $useQty = $request->useQty;

        for ($i = 0; $i < count($product_id); $i++) {
            $wherecheck = array(
                'status' => 'Project',
                'product_id' => $product_id[$i],
                'branch_id' => $request->project_id
            );

            $stocksumwereCHeck = array(
                'type' => 'Project',
                'branch_id' => $request->project_id,
                'product_id' => $product_id[$i],
            );

            //  insert stock first 
            $stockmain = Stock::where($wherecheck)->first();
            $ProductUseDetails = ProductUseDetails::where('product_use_id', $id)->get();
            $calculates = $stockmain->quantity + $ProductUseDetails[$i];
            $stockupdate['quantity'] = $calculates;
            Stock::where($wherecheck)->update($stockupdate);

            $stocksummary['quantity'] = $calculates;
            StockSummary::where($stocksumwereCHeck)->update($stocksummary);

            $calculate = $stock_id[$i] - $useQty[$i];
            $stock['quantity'] = $calculate;
            Stock::where($wherecheck)->update($stock);

            $stocksummary['quantity'] = $calculate;
            StockSummary::where($stocksumwereCHeck)->update($stocksummary);
        }

        $productUse = $this->ProductUse::find($id);
        $productUse->date = $request->date;
        $productUse->project_id = $request->project_id;
        $productUse->stock_total = array_sum($request->stock);
        $productUse->use_total = array_sum($request->useQty);
        $productUse->update_by = Auth::user()->id;
        $productUse->save();
        $projeeID = $productUse->id;


        for ($i = 0; $i < count($product_id); $i++) {
            $useDetails['product_use_id'] = $projeeID;
            $useDetails['project_id'] = $request->project_id;
            $useDetails['product_id'] = $product_id[$i];
            $useDetails['stock_qty'] = $stock_id[$i];
            $useDetails['use_qty'] = $useQty[$i];
            ProductUseDetails::where('product_use_id', $id)->update($useDetails);
        }

        return true;
    }

    public function statusUpdate($id, $status)
    {
        $eproject = $this->ProductUse::find($id);
        $eproject->status = $status;
        $eproject->save();
        return $eproject;
    }

    public function destroy($id)
    {
        $eproject = $this->ProductUse::find($id);
        $eproject->delete();
        return true;
    }
}
