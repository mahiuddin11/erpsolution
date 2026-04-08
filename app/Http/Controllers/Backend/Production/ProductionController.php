<?php

namespace App\Http\Controllers\Backend\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Branch;

use App\Models\User;
use App\Models\Product;
use App\Models\Navigation;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\Conversion;
use App\Models\Production;
use App\Models\ProductUnit;
use App\Models\Adjust;
use App\Models\Purchases;
use App\Models\PurchasesDetails;
use DB;
use App\Services\Production\ProductionService;
use App\Transformers\ProductionTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\ValifdationException;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class ProductionController extends Controller
{

    /**
     * @var ProductionService
     */
    private $systemService;

    /**
     * @var ProductionTransformer
     */
    private $systemTransformer;
    /**
     * ProductionController constructor.
     * @param ProductionService $systemService
     * @param ProductionService $systemTransformer
     */
    public function __construct(ProductionService $ProductionService, ProductionTransformer $ProductionTransformer)
    {
        $this->systemService = $ProductionService;
        $this->systemTransformer = $ProductionTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Production List';
        return view('backend.pages.production.index', get_defined_vars());
    }

    public function dataProcessingProduction(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Production';
        $ProductionLastData = Production::latest('id')->first();
        if ($ProductionLastData) :
            $ProductionData = $ProductionLastData->id + 1;
        else :
            $ProductionData = 1;
        endif;
        $ProductionCode = 'PRO' . str_pad($ProductionData, 5, "0", STR_PAD_LEFT);
        $user = auth()->user();
        $products = Product::get()->where('status', 'Active');

        if ($user->branch_id == null) {
            $branch = Branch::get()->where('status', 'Active');
        } else {
            $branch = Branch::get()->where('status', 'Active')->where('id', $user->branch_id);
        }

        $categorys = Category::get()->where('status', 'Active');
        $brands = Brand::get()->where('status', 'Active');
        $units = ProductUnit::get()->where('status', 'Active');
        $conversion = Conversion::get()->where('status', 'Active');
        return view('backend.pages.production.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Production Invoice';

        $invoice = Production::with(['details.product.category', 'branch', 'customer'])->findOrFail($id);

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.production.invoice', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('production.production.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Edit Production';
        $products = Product::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        $categorys = Category::get()->where('status', 'Active');
        $brands = Brand::get()->where('status', 'Active');
        $units = ProductUnit::get()->where('status', 'Active');
        $conversion = Conversion::get()->where('status', 'Active');
        $manager = User::get()->where('status', 'Active');
        return view('backend.pages.production.edit', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        try {
            $this->validate($request, $this->systemService->updateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('Production.production.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statusUpdate($id, $status)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo = $this->systemService->statusUpdate($id, $status);
        if ($statusInfo) {
            return response()->json($this->systemTransformer->statusUpdate($statusInfo), 200);
        }
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo = $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }

    public function getProductListForProduction(Request $request)
    {
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);
        //   dd($productList);
        $add = '';
        if (!empty($productList)) :
            $add .= "<option value=''>Select Product</option>";
            foreach ($productList as $key => $value) :
                $add .= "<option proName='" . $value->name . "'   value='" . $value->id . "'>$value->productCode - $value->name</option>";
            endforeach;
            echo $add;
            die;
        else :
            echo "<option value='' selected disabled>No Product Available</option>";
            die;
        endif;
    }

    public function getCustomerBalance(Request $request)
    {

        $finalValue = 0;
        $conditionalArray = array(
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
        );

        $debit = Adjust::where($conditionalArray)->sum('debit');
        $credit = Adjust::where($conditionalArray)->sum('credit');

        $adjustArray = array(
            'customer_id' => $request->customer_id,
            'payment_type' => 'Credit',
        );

        $expireData = Adjust::where($adjustArray)->orderBy('id', 'desc')->first();
        $finalValue = $debit - $credit;
        echo json_encode(array('finalBalance' => $finalValue, 'expireData' => $expireData['expire_date']));
    }

    public function unitPiceForProduction(Request $request)
    {
        $proid = $request->productId;
        $productPrice = Product::get()->where('id', $proid)->first();

        echo json_encode(array('purchases_price' => $productPrice->purchases_price, 'Production_price' => $productPrice->Production_price));
    }

    function getProductStock(Request $request)
    {

        $product_id = $request->productId;
        $productStock = StockSummary::get()->where('product_id', $product_id)->first();
        if (!empty($productStock->quantity) && $productStock->quantity > 0) :
            echo $productStock->quantity;
        endif;
    }


    function getProductListForThisBranchWise(Request $request)
    {
        $branch_id = $request->branch_id;
        $productList = StockSummary::where('branch_id', $branch_id)
            ->join('products', 'products.id', '=', 'stock_summaries.product_id')
            ->get();
        // pops($productList);

        $html = '';
        if ($productList->isNotEmpty()) {
            $html .= "<option value='' selected disabled>--Select Product--</option>";
            foreach ($productList as $key => $pro) {
                $html .= "<option value='" . $pro->id . "'>$pro->productCode - $pro->name</option>";
            }
        } else {
            $html .= "<option value='' selected disabled>--No Stock Products Available--</option>";
        }
        return $html;
    }

    function getCurrentStockAndRateofThisProduct(Request $request)
    {
        $whereCond = array(
            'product_id' => $request->product_id,
            'branch_id' => $request->branch_id,
        );
        return  $productDetails = StockSummary::where($whereCond)->first();
    }
    function getToProPrice(Request $request)
    {
        $whereCond = array(
            'id' => $request->product_id,
        );
        return  $productDetails = Product::where($whereCond)->first();
    }

    function purchaseDetailsByProduct(Request $request)
    {

        $pid = $request->pid;
        $priceDetails = PurchasesDetails::select(
            'products.sale_price',
            \DB::raw('avg(unit_price) as avg'),
            \DB::raw('sum(quantity) as ttlqty')
        )
            ->join('products', 'products.id', '=', 'purchases_details.product_id')
            ->groupBy('product_id')->orderBy('avg', 'DESC')
            ->where('product_id', $pid)
            ->first();
        // pops($priceDetails);
        return $priceDetails;
    }
}
