<?php

namespace App\Repositories\Production;

use App\Helpers\Helper;
use App\Models\Conversion;
use App\Models\Product;
use App\Models\Production;
use App\Models\Transection;
use App\Models\customerLedger;
use App\Models\Stock;
use App\Models\StockSummary;
use App\Models\Sale;
use App\Models\sales_Details;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var Production
     */
    private $Production;

    /**
     * CourseRepository constructor.
     * @param Production $eProduction
     */
    public function __construct(Production $Productions)
    {
        $this->Production = $Productions;
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
        $result = $this->Production::latest()->get();
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
            1 => 'ProductionCode',
        );

        $edit = Helper::roleAccess('production.production.edit') ? 1 : 0;
        $delete = Helper::roleAccess('production.production.destroy') ? 1 : 0;
        // $view = Helper::roleAccess('production.production.show') ? 1 : 0;
        $ced = $edit + $delete;

        $totalData = $this->Production::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $Production = $this->Production::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->Production::count();
        } else {
            $search = $request->input('search.value');
            $Production = $this->Production::where('ProductionCode', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->Production::where('ProductionCode', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($Production) {
            foreach ($Production as $key => $eProduction) {

                $producttionqty = Stock::where('general_id', $eProduction->id)->where('product_id', $eProduction->product_id)->where('status', 'Production')->first();
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $eProduction->date;
                $nestedData['product_id'] = $eProduction->product->name;
                $nestedData['productionCode'] = $eProduction->productionCode;
                $nestedData['branch_id'] = $eProduction->branch->name;
                $nestedData['conversion_id'] = $eProduction->conversion->title;
                $nestedData['product_qty'] = $producttionqty->quantity;
                $nestedData['category_id'] = $eProduction->category->name;

                if ($eProduction->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('production.production.status', [$eProduction->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('production.production.status', [$eProduction->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('production.production.edit', $eProduction->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }
                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('production.production.destroy', $eProduction->id) . '" delete_id="' . $eProduction->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $eProduction->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }
                    $nestedData['action'] = $edit_data . '' . '' . $delete_data;
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
        $result = $this->Production::find($id);
        return $result;
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $conversinDetails = Conversion::find($request->conversion_id);
            $productCategory = Product::find($request->to_product_id);
            // dd($productCategory);
            $newStock = $conversinDetails->rate * $request->deduct_quantiry;

            $production = new Production();
            $production->date = $request->date;
            $production->productionCode = $request->productionCode;
            $production->branch_id = $request->branch_id;
            $production->conversion_id = $request->conversion_id;
            $production->product_id = $request->to_product_id;
            $production->category_id = $productCategory->category_id;
            $production->purchases_price = $request->purchases_price;
            $production->sale_price = $request->sale_price;
            $production->created_by = Auth::user()->id;
            $production->save();
            $productionId = $production->id;

            $stock = new Stock();
            $stock->date = $request->date;
            $stock->product_id  = $request->product_id;
            $stock->quantity = $request->deduct_quantiry;
            $stock->branch_id = $request->branch_id;
            $stock->unit_price = $request->purchases_price;
            $stock->total_price = $request->purchases_price * $request->deduct_quantiry;
            $stock->general_id = $productionId;
            $stock->status = 'Production Out';
            $stock->save();

            $stock = new Stock();
            $stock->date = $request->date;
            $stock->product_id  = $request->to_product_id;
            $stock->quantity = (int)$newStock;
            $stock->branch_id = $request->branch_id;
            $stock->unit_price = $request->purchases_price;
            $stock->total_price = $request->purchases_price * $request->deduct_quantiry;
            $stock->general_id = $productionId;
            $stock->status = 'Production';
            $stock->save();
            $existingCheck = StockSummary::where('product_id', $request->product_id)->where('type', 'Branch')->first();
            // dd($request->all(), empty($existingCheck->quantity));
            if (!empty($existingCheck) && $existingCheck->quantity >= 0) :
                $newQty = $existingCheck->quantity - $request->deduct_quantiry;
                StockSummary::where('product_id', $request->product_id)->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQty));
            endif;

            $existingCheckIn = StockSummary::where('product_id', $request->to_product_id)->where('type', 'Branch')->first();
            if (!empty($existingCheckIn) && $existingCheckIn->quantity >= 0) :
                $newQtyIn = $existingCheckIn->quantity + (int)$newStock;
                StockSummary::where('product_id', $request->to_product_id)->where('branch_id', $request->branch_id)->where('type', 'Branch')->update(array('quantity' => $newQtyIn));
            else :
                $stockSummary = new StockSummary();
                $stockSummary->branch_id = $request->branch_id;
                $stockSummary->product_id = $request->to_product_id;
                $stockSummary->quantity = (int)$newStock;
                $stockSummary->type = "Branch";
                $stockSummary->save();
            endif;
            DB::commit();
            return  $production;
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function update($request, $id)
    {

        $eProduction = Production::find($id);
        $eProduction->name = $request->name;
        $eProduction->manager_id = $request->manager_id ? $request->manager_id : '';
        $eProduction->budget = $request->budget;
        // $eProduction->received_amount = $request->received_amount;
        $eProduction->start_date = $request->start_date;
        $eProduction->end_date = $request->end_date;
        $eProduction->address = $request->address;
        $eProduction->updated_by = Auth::user()->id;
        $eProduction->save();
        return $eProduction;
    }

    public function statusUpdate($id, $status)
    {
        $eProduction = $this->Production::find($id);
        $eProduction->status = $status;
        $eProduction->save();
        return $eProduction;
    }

    public function destroy($id)
    {
        $eProduction = $this->Production::find($id);
        $eProduction->delete();
        return true;
    }
}
