<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Navigation;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\StockTransfer;
use App\Models\Transfer;
use App\Models\TransferDetails;
use DB;
use App\Services\InventorySetup\StockTransferService;
use App\Transformers\StockTransferTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\ValifdationException;
use Illuminate\Support\Facades\Validator;

class StockTransferController extends Controller
{

    /**
     * @var StockTransferService
     */
    private $systemService;

    /**
     * @var StockTransferTransformer
     */
    private $systemTransformer;

    /**
     * StockTransferController constructor.
     * @param StockTransferService $systemService
     * @param StockTransferService $systemTransformer
     */
    public function __construct(StockTransferService $saleService, StockTransferTransformer $saleTransformer)
    {
        $this->systemService = $saleService;
        $this->systemTransformer = $saleTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Transfer List';
        return view('backend.pages.inventories.transfer.index', get_defined_vars());
    }

    public function dataProcessingTransfer(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $title = 'Add New Transfer';
        $tobranch = Branch::get()->where('status', 'Active');
        $user = auth()->user();
        $branch = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branch = $branch->where('id', $user->branch_id);
        }
        $branch = $branch->get();
        $category_info = Category::get()->where('status', 'Active');

        $stockTransferData = Transfer::latest('id')->first();
        if ($stockTransferData) :
            $stockTransfer = $stockTransferData->id + 1;
        else :
            $stockTransfer = 1;
        endif;
        $invoice_no = 'TV' . str_pad($stockTransfer, 5, "0", STR_PAD_LEFT);
        return view('backend.pages.inventories.transfer.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Transfer Invoice';
        $invoice = Transfer::with(['details.product.category', 'branch'])->findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.transfer.invoice', get_defined_vars());
    }

    public function approval($id)
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
        $transfer = $this->systemService->getAllList();
        $category_info = Category::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        $title = 'Approved Edit';
        return view('backend.pages.inventories.transfer.approval', get_defined_vars());
    }

    public function getProductListTransfer(Request $request)
    {
        // dd($request->all());
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);

        $add = '';
        if (!empty($productList)) :
            $add .= "<option value=''>Select Product</option>";

            foreach ($productList as $key => $value) :
                $stocksummerylst = StockSummary::where('branch_id', $request->branch_id)->where('product_id', $value->id)->first();
                // dd($stocksummerylst);
                if (!empty($stocksummerylst) && $stocksummerylst->quantity > 0) {

                    $add .= "<option proName='" . $value->name . "'   value='" . $value->id . "'>$value->productCode - $value->name</option>";
                }
            endforeach;
            echo $add;
            die;
        else :
            echo "<option value='' selected disabled>No Product Available</option>";
            die;
        endif;
    }

    public function approval_store(Request $request)
    {
        if ($request->approvalstatus == 'Approved') {
            session()->flash('error', ' Already Approved!!');
            return redirect()->route('inventorySetup.transfer.index');
        }

        try {
            $this->validate($request, $this->systemService->storeValidation_approval($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->approval($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.transfer.index');
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
        return redirect()->route('inventorySetup.transfer.index');
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
        $transfer = $this->systemService->getAllList();

        $title = 'Approved Edit';
        $tobranch = Branch::get()->where('status', 'Active');
        $user = auth()->user();
        $branch = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branch = $branch->where('id', $user->branch_id);
        }
        $branch = $branch->get();
        $category_info = Category::get()->where('status', 'Active');
        $transfe = Transfer::find($id);
        $transfeDetails = TransferDetails::where('transfer_id', $id)->get();
        return view('backend.pages.inventories.transfer.edit', get_defined_vars());
    }

    public function editapproval($id)
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
        $transfer = $this->systemService->getAllList();

        $title = 'Approved Edit';
        $branch = Branch::get()->where('status', 'Active');
        $category_info = Category::get()->where('status', 'Active');
        $transfe = Transfer::find($id);
        $transfeDetails = TransferDetails::where('transfer_id', $id)->get();
        return view('backend.pages.inventories.transfer.editapprove', get_defined_vars());
    }
    public function updateapprove(Request $request)
    {
        if ($request->approvalstatus == 'Approved') {
            session()->flash('error', ' Already Approved!!');
            return redirect()->route('inventorySetup.transfer.index');
        }
        try {
            $this->validate($request, $this->systemService->transferApprove($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->stotransferStore($request);

        return redirect()->route('inventorySetup.transfer.index');
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
        return redirect()->route('inventorySetup.transfer.index');
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

    public function getProductListForSale(Request $request)
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

    public function unitPiceForSale(Request $request)
    {
        $proid = $request->productId;
        $productPrice = Product::get()->where('id', $proid)->first();

        echo json_encode(array('purchases_price' => $productPrice->purchases_price, 'sale_price' => $productPrice->sale_price));
    }

    function getProductStock(Request $request)
    {
        $product_id = $request->productId;
        $productStock = StockSummary::get()->where('product_id', $product_id)->first();
        if (!empty($productStock->quantity) && $productStock->quantity > 0) :
            echo $productStock->quantity;
        endif;
    }
}
