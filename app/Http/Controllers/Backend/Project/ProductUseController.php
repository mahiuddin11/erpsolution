<?php

namespace App\Http\Controllers\Backend\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Adjust;
use App\Models\Product;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\ProductUse;
use App\Models\ProductUseDetails;
use App\Models\Project;
use App\Models\ProjectRequisition;
use DB;
use App\Services\Project\ProductUseService;
use App\Transformers\ProductUseTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function GuzzleHttp\Promise\all;

class ProductUseController extends Controller
{
    /**
     * @var ProductUseService
     */
    private $systemService;

    /**
     * @var ProductUseTransformer
     */
    private $systemTransformer;

    /**
     * ProjectController constructor.
     * @param ProductUseService $systemService
     * @param ProductUseService $systemTransformer
     */
    public function __construct(ProductUseService $ProductUseService, ProductUseTransformer $ProductUseTransformer)
    {
        $this->systemService = $ProductUseService;
        $this->systemTransformer = $ProductUseTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Project List';
        $user = Auth::user();
        $project = Project::where('manager_id', $user->id)->pluck('condition')->first();
        return view('backend.pages.use-product.index', get_defined_vars());
    }

    public function dataProcessingusepro(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Use Product';
        $user = Auth::user();
        $branch = Branch::where('status', 'Active')->get();
        $category_info = Category::where('status', 'Active')->get();
        $project = Project::where('status', 'Active')->where('manager_id', $user->id)->get();
        $projectuse = ProductUse::latest('id')->first();
        $projRequisition = ProjectRequisition::where('user_id', Auth::user()->id)->where('status', 'Accepted')->get();
        if ($projectuse) :
            $puCode = $projectuse->id + 1;
        else :
            $puCode = 1;
        endif;
        $puInv = 'PU' . str_pad($puCode, 5, "0", STR_PAD_LEFT);
        return view('backend.pages.use-product.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Project Invoice';
        $invoice = ProductUse::find($id);
        $productuseDetails = ProductUseDetails::where('product_use_id', $id)->get();
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.use-product.invoice', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('project.productuse.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        return redirect()->back();
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Edit Product use';
        $user = Auth::user();
        $branch = Branch::where('status', 'Active')->get();
        $category_info = Category::where('status', 'Active')->get();
        $project = Project::where('status', 'Active')->first();
        $projectuse = ProductUse::findOrFail($id);
        $projectuseDetails = ProductUseDetails::where('product_use_id', $id)->get();
        $stockSumery = StockSummary::where('branch_id', $project->id)->where('type', 'Project')->get();

        return view('backend.pages.use-product.edit', get_defined_vars());
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
        return redirect()->route('project.project.index');
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

    public function getProductListForproject(Request $request)
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

    public function unitPiceForproject(Request $request)
    {
        $proid = $request->productId;
        $productPrice = Product::get()->where('id', $proid)->first();

        echo json_encode(array('purchases_price' => $productPrice->purchases_price, 'project_price' => $productPrice->project_price));
    }

    function getProductStock(Request $request)
    {

        $product_id = $request->productId;
        $productStock = StockSummary::get()->where('product_id', $product_id)->first();
        if (!empty($productStock->quantity) && $productStock->quantity > 0) :
            echo $productStock->quantity;
        endif;
    }

    public function searchpu(Request $request)
    {
        $stock = StockSummary::where('branch_id', $request->project_id)->where('product_id',  $request->id)->where('type', 'Project')->pluck('quantity')->first();
        return response()->json([
            'stock' =>  $stock
        ]);
    }

    public function getstockdata(Request $request)
    {
        $data = "";
        $stockSumery = StockSummary::where('branch_id', $request->project_id)->where('type', 'Project')->get();
        if (!$stockSumery->isEmpty()) {
            $data .= "<option selected disabled>Selected Product</option>";
            foreach ($stockSumery as $value) {
                $data .= '<option value="' . $value->product_id . '" proName="' . $value->products->name . '">
                                                ' . $value->products->productCode . ' - ' . $value->products->name . '</option>';
            }
        } else {
            $data .= "<option selected disabled>No Product Found</option>";
        }
        // dd($data);
        echo $data;
    }
}
