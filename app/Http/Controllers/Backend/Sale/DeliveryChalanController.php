<?php

namespace App\Http\Controllers\Backend\Sale;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
use App\Models\customerLedger;
use App\Models\sales_Details;
use App\Models\deliveryChalan;
use App\Models\deliveryChalanDetails;
use DB;
use App\Services\Sale\DeliveryChallanService;
use App\Transformers\DeliveryChallanTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\ValifdationException;
use Illuminate\Support\Facades\Validator;

class DeliveryChalanController extends Controller
{

    /**
     * @var DeliveryChallanService
     */
    private $systemService;

    /**
     * @var DeliveryChallanTransformer
     */
    private $systemTransformer;

    /**
     * DeliveryChalanController constructor.
     * @param DeliveryChallanService $systemService
     * @param DeliveryChallanService $systemTransformer
     */
    public function __construct(DeliveryChallanService $saleService, DeliveryChallanTransformer $saleTransformer)
    {
        $this->systemService = $saleService;
        $this->systemTransformer = $saleTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = ' Delivery Challan List';
        return view('backend.pages.sale.challan.index', get_defined_vars());
    }

    public function dataProcessingChallan(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Delivery Challan';
        $category_info = Category::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');
        $saleInvoice = Sale::get()->where('sale_type', 'Regular');
        $sales = "";
        foreach ($saleInvoice as $value) {
            $salesDetails = (int) sales_Details::where('sale_id', $value->id)->sum('qty');
            $deliverychallan =
                (int) deliveryChalanDetails::where(
                    'sale_id',
                    $value->id
                )->sum('delivary_qty');
            // dd($salesDetails, $deliverychallan);
            if ($salesDetails > $deliverychallan) {
                $sales .= '<option value="' . $value->id . '">
                                        ' . $value->invoice_no . '
                                    </option>';
            }
        }
        // dd($sales);

        $saleLastData = deliveryChalan::latest('id')->first();
        if ($saleLastData) :
            $saleData = $saleLastData->id + 1;
        else :
            $saleData = 1;
        endif;

        $invoice_no = 'DC' . str_pad($saleData, 5, "0", STR_PAD_LEFT);

        return view('backend.pages.sale.challan.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Delivery Chalans Invoice';
        $invoice = deliveryChalan::findOrFail($id);
        $sale = Sale::findOrFail($invoice->sale_id);
        $deliverydetails = deliveryChalanDetails::where('chalan_id', $id)->get();
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.sale.challan.invoice', get_defined_vars());
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
        return redirect()->route('sale.challan.index');
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
        $title = 'Edit CHalan';
        $category_info = Category::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');
        $saleInvoice = Sale::get()->where('sale_type', 'Regular');

        $deliveryCHalan = deliveryChalan::findOrFail($id);
        $sales = "<option selected value=" . $deliveryCHalan->sale->id . ">" . $deliveryCHalan->sale->invoice_no . "</option>";
        $branch = Branch::get()->where('status', 'Active');

        $deliveryCHalandetails = deliveryChalanDetails::where('chalan_id', $id)->get();
        return view('backend.pages.sale.challan.edit', get_defined_vars());
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
        return redirect()->route('sale.challan.index');
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

    public function getCustomerBalance(Request $request)
    {

        $finalValue = 0;
        $conditionalArray = array(
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
        );

        $debit = Adjust::where($conditionalArray)->sum('debit');
        $credit = Adjust::where($conditionalArray)->sum('credit');
        $customerlager = customerLedger::where($conditionalArray)->sum('credit');

        $adjustArray = array(
            'customer_id' => $request->customer_id,
            'payment_type' => 'Credit',
        );

        $expireData = Adjust::where($adjustArray)->orderBy('id', 'desc')->first();
        $finalValue = $debit - $credit - $customerlager;
        echo json_encode(array('finalBalance' => $finalValue, 'expireData' => $expireData['expire_date']));
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

    public function salesDetails(Request $request)
    {

        $sales = $this->getSaleList($request); // child function in this controller
        echo json_encode($sales);
    }

    function getSaleList($request)
    {
        $data = '';
        $salesDetails = sales_Details::where('sale_id', $request->saleId);

        $sale = sale::find($request->saleId);

        $branch = '<option selected value="' . $sale->branch_id  . '"> ' . $sale->branch->branchCode . ' - ' .  $sale->branch->name . '</option>';
        $customer = '<option selected value="' . $sale->customer_id  . '"> ' . $sale->customer->customerCode . ' - ' .  $sale->customer->name . '</option>';

        foreach ($salesDetails->get() as $value) {


            $wheredeliveryDetails = array(
                'sale_id' => $request->saleId,
                'product_id' => $value->product_id
            );

            $deliverychallan = (int) deliveryChalanDetails::where($wheredeliveryDetails)->sum('delivary_qty');
            // dd($value->qty, $deliverychallan);
            if ($value->qty > $deliverychallan) {
                $mines = $value->qty - $deliverychallan;
                $data .= '<tr class="delrow new_item' . $value->product_id . '">
        <td >
           ' . $value->category->name . '
            <input type="hidden" name="category_nm[]" value="' . $value->category_id . '">
        </td>
        <td class="text-right">' . $value->product->name . '<input type="hidden" name="product_nm[]" value="' . $value->product_id . '"></td>
        <td class="text-right">' . ' <input class="ttlqty qnty form-control" type="number"  name="qty[]" value="' . $mines . '"></td>
        <td class="text-right">' . ' <input class="saleqty form-control" type="hidden"  value="' . $mines . '"></td>
        <td>
                <a del_id="' . $value->product_id . '" class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                    <i class="fa fa-times"></i>
                </a>
              </td>
        </tr>';
            }
        }
        return ['prdetails' => $data, 'branch' => $branch, 'customer' => $customer];
    }
}
