<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;

class ProductRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Product
     */
    private $product;
    /**
     * CourseRepository constructor.
     * @param product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
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
        // dd('product repo', $request->all());

        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'productCode',
        );

        $edit = Helper::roleAccess('inventorySetup.product.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.product.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.product.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->product::where('parent_id', 0)->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $products = $this->product::with('brand', 'productUnit')->offset($start)
                ->limit($limit)
                ->where('parent_id', 0)
                ->orderBy($order, $dir);
            //->orderBy('status', 'desc')
            if ($request->has('category_id') && !empty($request->category_id)) {
                $products->where('category_id', $request->category_id);
            }
            $products = $products->get();
            $totalFiltered = $this->product::where('parent_id', 0)->count();
        } else {
            $search = $request->input('search.value');
            $products = $this->product::with('brand', 'productUnit', 'category')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('productCode', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('brand', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                })
                ->offset($start)
                ->limit($limit)
                ->where('parent_id', 0)
                ->orderBy($order, $dir);
            //->orderBy('status', 'desc')
            if ($request->has('category_id') && !empty($request->category_id)) {
                $products->where('category_id', $request->category_id);
            }
            $products = $products->get();

            $totalFilteredQuery = $this->product::where('parent_id', 0);

            if ($request->has('category_id') && !empty($request->category_id)) {
                $totalFilteredQuery->where('category_id', $request->category_id);
            }
            $totalFilteredQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('productCode', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
            $totalFiltered = $totalFilteredQuery->count();
        }


        $data = array();
        if ($products) {
            foreach ($products as $key => $product) {
                $product->attributeSkip = true;
                $nestedData['id'] = $key + 1;
                $nestedData['name'] =  $product->name;
                $nestedData['productCode'] = $product->productCode;
                $nestedData['category_id'] = $product->category->name ?? "N/A";
                $nestedData['brand'] = $product->brand ? $product->brand->name : "N/A";
                $nestedData['productUnit'] = $product->productUnit ? $product->productUnit->name : "N/A";
                $nestedData['purchases_price'] = $product->purchases_price ? $product->purchases_price : "N/A";
                $nestedData['sale_price'] = $product->sale_price;
                $nestedData['low_stock'] = $product->low_stock;
                if ($product->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.product.status', [$product->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.product.status', [$product->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.product.edit', $product->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.product.show', $product->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.product.destroy', $product->id) . '" delete_id="' . $product->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $product->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->product::with('subproduct')->find($id);
        return $result;
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $product = new $this->product();
            $product->name = $request->name;
            $product->productCode = $request->productCode;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->unit_id = $request->unit_id;
            $product->purchases_price = $request->purchases_price_single;
            $product->sale_price = $request->sale_price_single;
            $product->box = $request->box;
            $product->status = 'Active';
            $product->created_by = Auth::user()->id;
            $product->save();

            if (($request->product_name[0])) {
                for ($i = 0; $i < count($request->product_name); $i++) {
                    $suppliersaleprice[] = [
                        'parent_id' => $product->id,
                        'name' => $request->product_name[$i] ?? "",
                        'sale_price' => $request->sale_price[$i],
                        'purchases_price' => $request->purchases_price[$i],
                    ];
                }
                DB::table('products')->insert($suppliersaleprice);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }

        return $product;
    }

    public function update($request, $id)
    {
        $product = $this->product::findOrFail($id);
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id;
        $product->purchases_price = $request->purchases_price_single;
        $product->sale_price = $request->sale_price_single;
        $product->status = 'Active';
        $product->box = $request->box;
        $product->updated_by = Auth::user()->id;
        $product->save();

        DB::table('supplier_sale_prices')->where('product_id', $id)->delete();

        if ($request->product_name_old) {
            for ($i = 0; $i < count($request->product_name_old); $i++) {
                $suppliersalepriceold = [
                    'parent_id' => $product->id,
                    'name' => $request->product_name_old[$i] ?? "",
                    'sale_price' => $request->sale_price_old[$i],
                    'purchases_price' => $request->purchases_price_old[$i],
                ];
                DB::table('products')->where('id', $request->product_id_old[$i])->update($suppliersalepriceold);
            }
        }

        if ($request->product_name) {
            for ($i = 0; $i < count($request->product_name); $i++) {
                $suppliersaleprice[] = [
                    'parent_id' => $product->id,
                    'name' => $request->product_name[$i] ?? "",
                    'sale_price' => $request->sale_price[$i],
                    'purchases_price' => $request->purchases_price[$i],
                ];
            }
            DB::table('products')->insert($suppliersaleprice);
        }
        
        return $product;
    }

    public function statusUpdate($id, $status)
    {
        $product = $this->product::find($id);
        $product->status = $status;
        $product->save();
        return $product;
    }

    public function destroy($id)
    {
        $product = $this->product::find($id);
        $product->delete();
        return true;
    }
}
