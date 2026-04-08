<?php

namespace App\Http\Controllers\Backend\Sale;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Brand;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Adjust;
use App\Models\Product;
use App\Models\Navigation;
use App\Models\ChartOfAccount;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\CustomerGroup;
use App\Models\customerLedger;
use App\Models\PurchasesDetails;
use App\Models\ReturnDeposit;
use App\Models\sales_Details;
use App\Models\Transection;
use DB;
use App\Services\Sale\SalesService;
use App\Transformers\SalesTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\ValifdationException;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{

    /**
     * @var SalesService
     */
    private $systemService;

    /**
     * @var SalesTransformer
     */
    private $systemTransformer;

    /**
     * SaleController constructor.
     * @param SalesService $systemService
     * @param SalesService $systemTransformer
     */
    public function __construct(SalesService $saleService, SalesTransformer $saleTransformer)
    {
        $this->systemService = $saleService;
        $this->systemTransformer = $saleTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Sale List';
        return view('backend.pages.sale.index', get_defined_vars());
    }

    public function dataProcessingSale(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }


    public function quiceAddCustomer(Request $request)
    {
            $customertLastData = Customer::latest('id')->first();
            if ($customertLastData) :
                $customerData = $customertLastData->id + 1;
            else :
                $customerData = 1;
            endif;
            $customerCode = 'CU' . str_pad($customerData, 5, "0", STR_PAD_LEFT);

            $customer = new Customer();
            $customer->customergroup_id = $request->customergroup_id;
            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->address = $request->address;
            $customer->bin = $request->bin;
            $customer->co_name = $request->co_name;
            $customer->customerCode = $customerCode;
            $customer->status = 'Active';
            $customer->created_by = Auth::user()->id;
            $customer->save();

            $Accounts = new Accounts();
            $Accounts->account_name = $request->co_name;
            $Accounts->parent_id = 5;
            $Accounts->accountable_id = $customer->id;
            $Accounts->accountable_type = "App\Models\Customer";
            $Accounts->bill_by_bill = 1;
            $Accounts->status = 'Active';
            $Accounts->created_by = Auth::user()->id;
            $Accounts->save();

            return response()->json([
                'success' => true,
                'accounts' => $Accounts,
            ]);

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New sale';

      
        $category_info = Category::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $ledgers = ChartOfAccount::whereIn('id', [getAccountByUniqueID(5)->id, getAccountByUniqueID(16)->id])->get();
        // $branch = Branch::get()->where('status', 'Active');
        $user = auth()->user();
        $branch = Branch::where("parent_id", 0)->where('status', 'Active');
        if ($user->branch_id) {
            $branch = $branch->where('id', $user->branch_id);
        }
        $branch = $branch->get();
        $customerGroup = CustomerGroup::all();

        $wearhouses = Branch::where("parent_id", "!=", 0)->where('status', 'Active')->get();

        if ($user->type == "Admin" || $user->branch_id) {
            $account = ChartOfAccount::whereIn('id', [16, 17])->get()->where('status', 'Active');
        } elseif ($user->type == "Admin" || !$user->branch_id) {
            $account = ChartOfAccount::whereIn('id', [16, 17])->get()->where('status', 'Active')->where('branch_id', $user->branch_id);
        }

        $saleLastData = Sale::latest('id')->first();
        if ($saleLastData) :
            $saleData = $saleLastData->id + 1;
        else :
            $saleData = 1;
        endif;

        $invoice_no = 'SV' . str_pad($saleData, 5, "0", STR_PAD_LEFT);
        return view('backend.pages.sale.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
       
        $title = 'Sale Invoice';

        $invoice = Sale::with(['details.product.category', 'branch', 'customer'])->findOrFail($id);

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.sale.invoice', get_defined_vars());
    }

    public function challan(Request $request, $id)
    {
        $title = 'Delivery Chalans Invoice';

        $invoice = Sale::with(['details.product.category', 'branch', 'customer'])->findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.sale.chalans', get_defined_vars());
    }
    public function unitPrice(Request $request)
    {
        $proid = $request->productId;
        $productPrice = Product::get()->where('id', $proid)->first();
        $lastPurchasePrice = PurchasesDetails::where('product_id', $proid)->latest('id')->pluck('unit_price')->first();
        return response()->json(["sale_price" => $productPrice->sale_price ?? 0, 'lastPurchasePrice' => $lastPurchasePrice ?? 0]);
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
        return redirect()->route('sale.sale.index');
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
        $transection = Transection::where('type', 10)->orWhere('payment_id', $id)->first();
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Add New Sale';
        $ledgers = ChartOfAccount::whereIn('id', [getAccountByUniqueID(5)->id, getAccountByUniqueID(16)->id])->get();
        $category_info = Category::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $user = auth()->user();
        $customerGroup = CustomerGroup::all();

        $branch = Branch::where("parent_id", 0)->where('status', 'Active');
        if ($user->branch_id) {
            $branch = $branch->where('id', $user->branch_id);
        }
        $branch = $branch->get();

        if ($user->type == "Admin" || !$user->branch_id) {
            $account = ChartOfAccount::get()->where('status', 'Active');
        } elseif ($user->type == "Admin" || $user->branch_id) {
            $account = ChartOfAccount::get()->where('status', 'Active')->where('branch_id', $user->branch_id);
        }
        $saletlist = Sale::findOrFail($id);
        $subWarehouses = Branch::where("parent_id", "!=", 0)->where('status', 'Active')->get();
        $saledetails = sales_Details::where('sale_id', $id)->get();
        return view('backend.pages.sale.edit', get_defined_vars());
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
        return redirect()->route('sale.sale.index');
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
        // dd($request->all());
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);
        $add = '';
        if (!empty($productList)) :
            $add .= "<option value=''>Select Product</option>";

            foreach ($productList as $key => $value) :
                // $stocksummerylst = StockSummary::where('branch_id', $request->branch_id)->where('product_id', $value->id)->first();
                $add .= '<option proName="' . $value->name . '"   value="' . $value->id . '">' . $value->name . '</option>';
                if (!$value->subproduct->isEmpty()) {
                    foreach ($value->subproduct as $key => $itel) :
                        $add .= '<option proName="' . $itel->name . '"   value="' . $itel->id . '">- ' . $itel->name . '</option>';
                    endforeach;
                }
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
        $return = ReturnDeposit::where('customer_id', $request->customer_id)->sum('amount');
        $customerlager = customerLedger::where($conditionalArray)->sum('credit');

        $adjustArray = array(
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
        );

        $expireData = Adjust::where($adjustArray)->orderBy('id', 'desc')->first();
        $finalValue = ($debit - $credit - $return) - $customerlager;
        echo json_encode(array('finalBalance' => $finalValue, 'expireData' => $expireData->expire_date ?? 0));
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
        $productStock = StockSummary::get()->where('product_id', $product_id)->whereIn('branch_id', [$request->sub_branch_id])->where('type', 'Branch')->where('purchasetype', $request->type)->first();
        // dd($request->branch_id,$request->sub_branch_id,$product_id,$request->type);
        if (!empty($productStock->quantity) && $productStock->quantity > 0) :
            echo $productStock->quantity;
        endif;
    }
}
