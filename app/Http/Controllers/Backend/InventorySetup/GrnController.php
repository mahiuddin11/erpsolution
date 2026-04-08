<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use App\Models\Grn;
use App\Models\Branch;
use App\Models\Category;
use App\Models\PrDetails;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchases;
use App\Models\Company;
use App\Models\PurchaseRequisition;
use App\Http\Controllers\Controller;
use App\Models\Grn_detail;
use App\Models\PurchaseOrder;
use App\Models\PurchasesDetails;
use Illuminate\Http\Request;
use App\Services\InventorySetup\GrnService;
use App\Transformers\GrnTransformer;
use Illuminate\Validation\ValidationException;

class GrnController extends Controller
{


    private $systemService;

    private $grnTransformer;


    public function __construct(GrnService $grnService, GrnTransformer $grnTransformer)
    {
        $this->systemService = $grnService;

        $this->grnTransformer = $grnTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Purchase Requisition List';
        return view('backend.pages.inventories.grn.index', get_defined_vars());
    }

    public function datagoodrcvnote(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->grnTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Purchase Requisition';
        $branch = Branch::where('status', 'Active')->get();
        $category_info = Category::where('status', 'Active')->get();
        $Purchases = Purchases::where('status', 'Pending')->where('purchase_type', 'Manual')->get();
        $suppliers = Supplier::where('status', 'Active')->get();
        $goodrcvnote = Grn::latest('id')->first();

        if ($goodrcvnote) :
            $grnCode = $goodrcvnote->id + 1;
        else :
            $grnCode = 1;
        endif;

        $grnInv = 'GRN' . str_pad($grnCode, 5, "0", STR_PAD_LEFT);
        return view('backend.pages.inventories.grn.create', get_defined_vars());
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
        return redirect()->route('inventorySetup.goodrcvnote.index');
    }
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        return back();
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }

        $title = 'Edit Purchase Requisition';
        $branch = Branch::where('status', 'Active')->get();
        $category_info  = Category::where('status', 'Active')->get();
        $requisition = PurchaseRequisition::find($id);
        $requisitionDetails = PrDetails::where('pr_id', $id)->get();
        return view('backend.pages.inventories.grn.edit', get_defined_vars());
    }

    public function invoice($id)
    {
        // dd('good recived invoice id',$id);
        if (!is_numeric($id)) {
            session()->flash('error', 'Invoice id must be numeric!!');
            return redirect()->back();
        }

        $editInfo = $this->systemService->details($id);

        if (!$editInfo) {
            session()->flash('error', 'Invoice info is invalid!!');
            return redirect()->back();
        }
        $title = 'Good Received Note ';
        $grn = Grn::findOrFail($id);
        $grnDetails = Grn_detail::where('good_rcv_note_id', $id)->get();
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.grn.approve', get_defined_vars());
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
        return redirect()->route('inventorySetup.purchaserequisition.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json($this->grnTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->grnTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo = $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->grnTransformer->delete($deleteInfo), 200);
        }
    }

    public function filterproduct(Request $req)
    {

        $product = Product::where('category_id', $req->id)->get();
        $data = "";
        if (count($product) > 0) {
            foreach ($product as $value) {
                $data .= ' <option value="' . $value->id . '">
                ' . $value->productCode . '-' . $value->name . '
                     </option>';
            }
        } else {

            $data .= '<option > Nothing found </option>';
        }

        echo json_encode($data);
    }

    public function searchgrn(Request $request)
    {
        $data = '';
        $purchasedetails = PurchasesDetails::where('purchases_id', $request->id)->get();
        $purchase = Purchases::find($request->id);
        $purchaseorder = PurchaseOrder::where('id', $purchase->purchase_order_id)->pluck('advance_payment')->first();
        $project = '<option selected value="' . $purchase->project_id  . '"> ' . $purchase->project->projectCode . ' - ' .  $purchase->project->name . '</option>';
        $supplier = '<option selected value=""> </option>';

        $grnDetails = Grn_detail::where('purchase_voucher', $request->id)->latest()->orderBy('id', 'asc')->take(count($purchasedetails))->get();
        if (!$grnDetails->isEmpty()) {
            $convertarray = $grnDetails->toArray();
        } else {
            $convertarray = null;
        }
        // dd($convertarray);
        if (empty($convertarray)) {
            // dd('1st');
            foreach ($purchasedetails as $key => $value) {
                $data .= '<tr class="delrow">
        <td>
       ' . $value->category->name . '
        <input type="hidden" name="category_nm[]" value="' . $value->category_id . '">
    </td>
    <td class="text-right">' . $value->product->name . '<input type="hidden" class="add_quantity" name="product_nm[]" value="' . $value->product_id . '"></td>
    <td class="text-right">' . $value->purchasetype . '<input type="hidden" class="add_quantity" name="purchasetype[]" value="' . $value->purchasetype  . '"></td>
    <td class="text-right">' .  ' <input class="ttlqty form-control" type="text" readonly name="qty[]" value="' . $value->quantity . '"></td>
    <td class="text-right">' .  ' <input class="approve form-control  approve_qty" readonly value="0" type="text" name="approve_qty[]"></td>
    <td class="text-right">' .  ' <input class="remaining qty form-control" min="0" type="number" value="' . $value->quantity . '" name="remaining[]"></td>
    <td class="text-right">' . $value->unit_price . ' <input class="ttlunitprice unitprice"  id="unitprice" type="hidden" name="unitprice[]" value="' . $value->unit_price . '"></td>
    <td class="text-right">' . ' <input class="total form-control" style="background:#FFFFFF;border:none" type="number" readonly  id="total" name="total[]" value="' . $value->total_price . '"></td>

      </tr>';
            }
        } else {
            // dd('2st');

            foreach ($purchasedetails as $key => $value) {
                $calculate = $convertarray[$key]['qty'] - $convertarray[$key]['approve_qty'];
                $data .= '<tr class="delrow">
        <td>
       ' . $value->category->name . '
        <input type="hidden" name="category_nm[]" value="' . $value->category_id . '">
    </td>
    <td class="text-right">' . $value->product->name . '<input type="hidden" class="add_quantity" name="product_nm[]" value="' . $value->product_id  . '"></td>
    <td class="text-right">' . $value->purchasetype . '<input type="hidden" class="add_quantity" name="purchasetype[]" value="' . $value->purchasetype  . '"></td>
    <td class="text-right">' .  ' <input class="ttlqty form-control" type="text" readonly name="qty[]" value="' . $convertarray[$key]['qty'] . '"></td>
    <td class="text-right">' .  ' <input class="approve form-control  approve_qty" readonly value="' . $convertarray[$key]['approve_qty'] . '" type="text" name="approve_qty[]"></td>
    <td class="text-right">' .  ' <input class="remaining qty form-control" min="0" type="number" value="' . $calculate . '" name="remaining[]"></td>
    <td class="text-right">' . $value->unit_price . ' <input class="ttlunitprice unitprice"  id="unitprice" type="hidden" name="unitprice[]" value="' . $value->unit_price . '"></td>
    <td class="text-right">' . ' <input class="total form-control" style="background:#FFFFFF;border:none" type="number" readonly  id="total" name="total[]" value="' . $value->total_price . '"></td>

      </tr>';
            }
        }

        return ['prdetails' => $data, 'branch' => $project, 'supplier' => $supplier, 'purchaseorder' => $purchaseorder];
    }
}
