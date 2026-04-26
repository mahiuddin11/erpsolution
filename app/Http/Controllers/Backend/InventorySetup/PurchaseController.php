<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Purchases;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectRequisition;
use App\Models\ProjectRequisitionDetails;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchasesDetails;
use App\Models\supplierSalePrice;
use App\Models\SupplierSelectPrice;
use App\Models\Transection;
use App\Services\InventorySetup\PurchaseService;
use App\Transformers\PurchaseTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    /**
     * @var PurchaseService
     */
    private $systemService;

    /**
     * @var PurchaseTransformer
     */
    private $systemTransformer;

    /**
     * PurchaseController constructor.
     * @param PurchaseService $systemService
     * @param PurchaseService $systemTransformer
     */
    public function __construct(PurchaseService $purchaseService, PurchaseTransformer $purchaseTransformer)
    {
        $this->systemService = $purchaseService;
        $this->systemTransformer = $purchaseTransformer;
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Purchase (PV) List';
        return view('backend.pages.inventories.purchase.index', get_defined_vars());
    }

    public function pvindex(Request $request)
    {

        $title = 'Purchase List';
        return view('backend.pages.inventories.purchase_pv.index', get_defined_vars());
    }

    public function dataProcessingPurchase(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    public function dataProcessinpv(Request $request)
    {
        $json_data = $this->systemService->getpvList($request);
        // dd($json_data);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        
        $title = 'Add New purchase';
        $category_info = Category::with('parent')->get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');
        $ledgers = ChartOfAccount::where('parent_id',0)->get();

        $user = auth()->user();
        $branch = Branch::where('status', 'Active')->where("parent_id",0);
        $branch = $branch->get();

        $wearhouses = Branch::where("parent_id","!=",0)->get();

        $purchaseLastData = Purchases::latest('id')->first();

        if ($purchaseLastData) :
            $purchaseData = $purchaseLastData->id + 1;
        else :
            $purchaseData = 1;
        endif;

        $invoice_no = 'PV' . str_pad($purchaseData, 5, "0", STR_PAD_LEFT);
        $accounts = ChartOfAccount::getaccount(4)->get();
        $projects = Project::where('condition', 'One Going')->get();
        return view('backend.pages.inventories.purchase.create', get_defined_vars());
    }

    public function supplierCreate(Request $request)
    {
        $suppliertLastData = Supplier::latest('id')->first();
        if ($suppliertLastData) :
            $suppliertData = $suppliertLastData->id + 1;
        else :
            $suppliertData = 1;
        endif;
        $supplierCode = 'SP' . str_pad($suppliertData, 5, "0", STR_PAD_LEFT);

        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->supplierCode = $supplierCode;
        $supplier->specialNumber = $supplierCode;
        // $supplier->branch_id = $request->branch_id;
        $supplier->status = 'Active';
        $supplier->created_by = Auth::user()->id;
        $supplier->save();

        $Accounts = new Accounts();
        $Accounts->account_name = $request->name;
        $Accounts->parent_id = 16;
        $Accounts->accountable_id = $supplier->id;
        $Accounts->accountable_type = "App\Models\Supplier";
        $Accounts->bill_by_bill = 1;
        $Accounts->status = 'Active';
        $Accounts->created_by = Auth::user()->id;
        $Accounts->save();

        return response()->json([
            'success' => true,
            'accounts' => $Accounts,
        ]);
    }

    public function pvcreate()
    {
        $title = 'Add New purchase (PV)';
        $category_info = Category::get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');
       
        $user = auth()->user();
        $branch = Branch::where('status', 'Active')->get();
    
        $purchaseorder = PurchaseOrder::get()->where('status', 'Accepted');
        $purchaseLastData = Purchases::latest('id')->first();


        if ($purchaseLastData) :
            $purchaseData = $purchaseLastData->id + 1;
        else :
            $purchaseData = 1;
        endif;
        $invoice_no = 'PV' . str_pad($purchaseData, 5, "0", STR_PAD_LEFT);

        return view('backend.pages.inventories.purchase_pv.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Purchase Invoice';
        $invoice = Purchases::with(['details.product.category', 'branch', 'supplier'])->findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.purchase.invoice', get_defined_vars());
    }

    public function pvinvoice(Request $request, $id)
    {
        // dd($request->all(), $id);

        $title = 'Purchase Voucher Invoice';
        $invoice = Purchases::with(['details.product.category', 'branch', 'supplier'])->findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.purchase_pv.invoice', get_defined_vars());
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
        return redirect()->route('inventorySetup.purchase.index');
    }

    public function pvstore(Request $request)
    {
        
        try {
            $this->validate($request, $this->systemService->prstoreValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    
        $this->systemService->prstore($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.purchase.pvindex');
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
        $ledgers = ChartOfAccount::where('parent_id',0)->get();

        $purchase = $this->systemService->getAllList();
        $category_info = Category::get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        $title = 'Edit Purchase';
        $accounts = ChartOfAccount::getaccount(4)->get();

        $sub_branch = Branch::find($editInfo->branch_id);
        $subWarehouses = Branch::where("parent_id",$sub_branch->parent_id)->where('status', 'Active')->get();
        $account_id = $editInfo->chart_of_account_id;
        $debit = Transection::where('account_id', '=', $account_id)->sum('debit');
        $credit = Transection::where('account_id', '=', $account_id)->sum('credit');

        $remainingBalance = $debit - $credit;

        return view('backend.pages.inventories.purchase.edit', get_defined_vars());
    }

    public function pvedit($id)
    {
        // dd($id);
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
        $title = 'Edit Purchase';
        $accounts = ChartOfAccount::get();

        $account_id = $editInfo->chart_of_account_id;
        $debit = Transection::where('account_id', '=', $account_id)->sum('debit');
        $credit = Transection::where('account_id', '=', $account_id)->sum('credit');

    
        $proejct = Project::find($editInfo->project_id);
        $purchaseOrder = PurchaseOrder::find($editInfo->purchase_order_id);

        $remainingBalance = $debit - $credit;
        // dd($editInfo);
        return view('backend.pages.inventories.purchase_pv.edit', get_defined_vars());
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
            // dd($e->getMessage(), $e->getFile(), $e->getLine());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.purchase.index');
    }

    public function pvupdate(Request $request, $id)
    {

        // dd($request->all());
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
            $this->validate($request, $this->systemService->pvupdateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            // dd($e->getMessage(), $e->getFile(), $e->getLine());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $this->systemService->pvupdate($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.purchase.pvindex');
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

    public function getProductList(Request $request)
    {
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);
        $add = '';
        if (!empty($productList)) :
            $add .= "<option value='all'>All Product</option>";
            foreach ($productList as $key => $value) :
                $add .= "<option proName='" . $value->name . "'   value='" . $value->id . "'> $value->name  " . " </option>";
                foreach ($value->subproduct as $key => $item) :
                    $add .= '<option proName="' . $item->name . '"   value="' . $item->id . '">- ' . $item->name . ' </option>';
                endforeach;
            endforeach;
            echo $add;
            die;
        else :
            echo "<option value='' selected disabled>No Product Available</option>";
            die;
        endif;
    }

    public function unitPrice(Request $request)
    {
        $proid = $request->productId;
        $productprice = Product::find($proid);
        // $supplier = supplierSalePrice::get()->where('supplier_id', $supid)->where('product_id', $proid)->first();

        $lastPurchasePrice = PurchasesDetails::where('product_id', $proid)->latest('id')->pluck('unit_price')->first();
        return response()->json(["purchase_price" => $productprice->purchases_price  ?? 0, 'lastPurchasePrice' => $lastPurchasePrice ?? 0]);
    }

    public function getAccounts(Request $request)
    {
        $accounts = ChartOfAccount::getaccount(4)->get();
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

    public function searchpo(Request $request)
    {
        $data = '';
        $projectRequisitionDetails = PurchaseOrderDetail::where('purchase_order_id', $request->id);

        $purchaseorder = PurchaseOrder::find($request->id);
        $project = '<option selected value="'  .  $purchaseorder->project_id . '"> ' . $purchaseorder->project->name ?? "" . '</option>';
        $supplier = '<option selected value="' . $purchaseorder->supplier_id ?? 'sdf' . '"> ' . $purchaseorder->supplier->name ?? "Nul" . '</option>';
        $advancePay = $purchaseorder->advance_payment;
        foreach ($projectRequisitionDetails->get() as $value) {
            $supplierSelectedPrice = SupplierSelectPrice::where('purchase_order_id', $value->id)->where('status', 1)->first();
            $total_price = $value->qty * ($supplierSelectedPrice->purchases_price ?? 0) ;
            $data .= '<tr class="delrow new_item' . $value->product_id . '">
        <td>
           ' . ($supplierSelectedPrice->supplier->name ?? 0)  . '
            <input type="hidden" name="supplier_nm[]" value="' . ($supplierSelectedPrice->supplier->id ?? 0) . '">
        </td>
        <td>
           ' . $value->category->name . '
            <input type="hidden" name="category_nm[]" value="' . $value->category_id . '">
        </td>
        <td class="text-right">' . $value->product->name . '<input type="hidden" class="add_quantity" name="product_nm[]" value="' . $value->product_id . '"></td>
        <td class="text-right">' . $value->purchasetype . '<input type="hidden" class="add_quantity" name="purchasetype[]" value="' . $value->purchasetype . '"></td>
        <td class="text-right">' .  ' <input  type="number"  class="ttlqty qty qnty form-control" name="qty[]" value="' . $value->qty . '"></td>
        <td class="text-right"> <input class="ttlunitprice unitprice form-control" type="number" id="unitprice" name="unitprice[]" value="' . ($supplierSelectedPrice->purchases_price ?? 0) . '"></td>
        <td class="text-right">' .  ' <input class="total form-control" type="text" readonly name="total[]" value="' . $total_price . '"></td>
        <td>
                <a del_id="' . $value->product_id . '" class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                    <i class="fa fa-times"></i>
                </a>
        </td>
    </tr>';
        }

        return ['prdetails' => $data, "project" => $project, "supplier" => "$supplier"];
    }

    public function pvcloseopen(Request $request)
    {
        // dd($request->all());

        $purchase = Purchases::find($request->id);
        $purchase->status = $request->status;
        $purchase->save();
        echo true;
    }
}
