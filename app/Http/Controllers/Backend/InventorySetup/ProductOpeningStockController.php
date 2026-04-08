<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Company;
use App\Models\ProductOpeningStock;
use App\Models\Project;
use App\Models\Transection;
use App\Models\StockAjdustment;
use App\Services\InventorySetup\ProductOpeningStockService;
use App\Transformers\StockAdjustmentTransformer;
use Illuminate\Validation\ValidationException;

class ProductOpeningStockController extends Controller
{

    /**
     * @var ProductOpeningStockService
     */
    private $systemService;

    /**
     * @var ProductOpeningStockTransformer
     */
    private $systemTransformer;

    /**
     * StockAjdustmentController constructor.
     * @param ProductOpeningStockService $systemService
     * @param ProductOpeningStockService $systemTransformer
     */

    public function __construct(ProductOpeningStockService $ProductOpeningStockService, StockAdjustmentTransformer $StockAdjustmentTransformer)
    {
        $this->systemService = $ProductOpeningStockService;
        $this->systemTransformer = $StockAdjustmentTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Product Opening Stock List';
        return view('backend.pages.inventories.product_opening_stock.index', get_defined_vars());
    }

    public function dataProcessing(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Product Opening Stock';
        $category_info = Category::get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');
        $projects = Project::get();
        $user = auth()->user();
        $branchs = Branch::where('status', 'Active');

        $branchs = $branchs->get();

        $purchaseLastData = ProductOpeningStock::latest('id')->first();
        if ($purchaseLastData) :
            $purchaseData = $purchaseLastData->id + 1;
        else :
            $purchaseData = 1;
        endif;
        $invoice_no = 'OS' . str_pad($purchaseData, 5, "0", STR_PAD_LEFT);

        return view('backend.pages.inventories.product_opening_stock.create', get_defined_vars());
    }
    public function show(Request $request, $id)
    {
        $title = 'Purchase Invoice';

        $invoice = ProductOpeningStock::with(['details.product.category'])->findOrFail($id);

        $companyInfo = Company::latest('id')->first();

        return view('backend.pages.inventories.product_opening_stock.invoice', get_defined_vars());
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
        return redirect()->route('inventorySetup.productOS.index');
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

        $editInfo = $this->systemService->details($id)->load('details');

        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $user = auth()->user();
        $purchase = $this->systemService->getAllList();
        $category_info = Category::get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');

        $branchs = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branchs = $branchs->where('id', $user->branch_id);
        }
        $branchs = $branchs->get();

        $projects = Project::get();

        $title = 'Edit Stock Ajdustment';
        $accounts = ChartOfAccount::get();

        $account_id = $editInfo->chart_of_account_id;
        $debit = Transection::where('account_id', '=', $account_id)->sum('debit');
        $credit = Transection::where('account_id', '=', $account_id)->sum('credit');

        $remainingBalance = $debit - $credit;

        return view('backend.pages.inventories.product_opening_stock.edit', get_defined_vars());
    }

    public function approval($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }

        $editInfo = $this->systemService->details($id)->load('details');

        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }

        $purchase = $this->systemService->getAllList();
        $category_info = Category::get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        $title = 'Edit Stock Ajdustment';
        $accounts = ChartOfAccount::get();

        $account_id = $editInfo->chart_of_account_id;
        $debit = Transection::where('account_id', '=', $account_id)->sum('debit');
        $credit = Transection::where('account_id', '=', $account_id)->sum('credit');

        $remainingBalance = $debit - $credit;

        return view('backend.pages.inventories.product_opening_stock.approve', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeapproval(Request $request, $id)
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
            // dd($e->getMessage(), $e->getFile(), $e->getLine());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->storeapproval($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.productOS.index');
    }

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
            // dd($e->getMessage(), $e->getFile(), $e->getLine());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.productOS.index');
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

    public function getProductListforadjust(Request $request)
    {
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);
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

    public function unitPriceforadjust(Request $request)
    {
        $proid = $request->productId;
        $productPrice = Product::get()->where('id', $proid)->first();
        echo $productPrice->purchases_price;
    }
    public function checkBalanceforadjust(Request $request)
    {
        $proid = $request->productId;
        $productPrice = Product::get()->where('id', $proid)->first();
        echo $productPrice->purchases_price;
    }

    public function getAccountsforadjust(Request $request)
    {
        $accounts = ChartOfAccount::get();
        $html = '';
        if ($accounts->isNotEmpty()) {
            $html .= "<option value='' selected disabled>--Select Account--</option>";
            foreach ($accounts as $key => $account) {
                $html .= "<option value='" . $account->id . "'>$account->accountCode - $account->account_name</option>";
            }
        } else {
            $html .= "<option value='' selected disabled>--No Account Available--</option>";
        }
        return $html;
    }
}
