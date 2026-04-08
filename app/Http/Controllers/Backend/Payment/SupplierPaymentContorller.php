<?php

namespace App\Http\Controllers\Backend\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\SupplierPaymentTransformer;
use App\Models\Branch;
use App\Models\Navigation;
use App\Models\Adjust;
use App\Models\Supplier;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\customerLedger;
use App\Models\supplierLedger;
use App\Services\Supplier\SupplierPaymentService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class SupplierPaymentContorller extends Controller
{

    /**
     * @var SupplierPaymentService
     */
    private $systemService;
    /**
     * @var SupplierPaymentTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param SupplierPaymentService $systemService
     * @param SupplierPaymentTransformer $systemTransformer
     */
    public function __construct(SupplierPaymentService $SupplierPaymentService, SupplierPaymentTransformer $SupplierPaymentTransformer)
    {
        $this->systemService = $SupplierPaymentService;

        $this->systemTransformer = $SupplierPaymentTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $title = 'Payment List';
        return view('backend.pages.supplier_payment.index', get_defined_vars());
    }


    public function dataProcessingSupplierPayment(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Payment';
        $branch = Branch::get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');
        $accounts = ChartOfAccount::getaccount(4)->get();

        $invoice = DB::table('purchases')
            ->select('id', 'invoice_no')
            ->get();

        return view('backend.pages.supplier_payment.create', get_defined_vars());
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
        return redirect()->route('payment.supplier.index');
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
        $editInfo =   $this->systemService->details($id);

        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }

        $branch = Branch::get()->where('status', 'Active');
        $supplier = Supplier::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');

        $invoice = DB::table('sales')
            ->select('id', 'invoice_no')
            ->get();
        $title = 'Add New Adjust';
        return view('backend.pages.supplier_payment.edit', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
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
        return redirect()->route('payment.supplier.index');
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
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo =  $this->systemService->statusUpdate($id, $status);
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
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo =  $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }

    public function dueInvoiceammountsupplier(Request $request)
    {
        $debit = 0;
        $credit = 0;

        $conditionalArray = array(
            'purchase_id' => $request->purchase_id,
        );
        $conditionalArray2 = array(
            'purchase_id' => $request->purchase_id,
            // 'payment_type' => 'due',
        );

        $debit = supplierLedger::where($conditionalArray)->sum('debit');
        $credit = supplierLedger::where($conditionalArray2)->sum('credit');
        // if ($Due) {
        echo  $debit - $credit;
        // } else {
        //     echo   $debit;
        // }
    }

    public function getSupplierdetails(Request $request)
    {

        $invoice_id = $request->purchase_id;

        $supplierDetails =  supplierLedger::where('purchase_id', $invoice_id)
            ->join('suppliers', 'suppliers.id', '=', 'supplier_ledgers.supplier_id')
            ->select('supplier_ledgers.supplier_id', 'suppliers.id', 'suppliers.name')
            ->first();

        echo  json_encode($supplierDetails);
    }


    public function getAllSupplierList(request $request)
    {
        $branch_id = $request->branch_id;

        $supplier = supplierLedger::where('supplier_ledgers.branch_id', $branch_id)
            ->join('suppliers', 'suppliers.id', '=', 'supplier_ledgers.supplier_id')
            ->groupBy('supplier_ledgers.supplier_id')
            ->get();

        $html = '';
        if ($supplier->isNotEmpty()) {
            $html .= "<option value='' selected disabled>--Select Supplier--</option>";
            foreach ($supplier as $key => $sup) {
                $html .= "<option value='" . $sup->id . "'>$sup->supplierCode - $sup->name</option>";
            }
        } else {
            $html .= "<option value='' selected disabled>--No Supplier Available--</option>";
        }
        return $html;
    }

    public function show(Request $request, $id)
    {
        $title = 'Payment Voucher';
        $invoice = supplierLedger::where('supplier_ledgers.id', $id)
            ->join('purchases', 'purchases.id', '=', 'supplier_ledgers.purchase_id')
            ->first();
        // pops($invoice);
        $companyInfo = Company::latest('id')->first();
        // pops($companyInfo);
        return view('backend.pages.supplier_payment.invoice', get_defined_vars());
    }


    public function getAllSuppDueInvoiceList(Request $request)
    {

        $supplier_id = $request->supplier_id;

        $invoice = supplierLedger::where('supplier_ledgers.supplier_id', $supplier_id)
            ->groupBy('supplier_ledgers.purchase_id')
            ->join('purchases', 'purchases.id', '=', 'supplier_ledgers.purchase_id')
            ->get();



        $html = '';
        if ($invoice->isNotEmpty()) {
            $html .= "<option value='' selected disabled>--Select Invoice--</option>";
            foreach ($invoice as $key => $inv) {
                $credit = supplierLedger::where('purchase_id', '=', $inv->id)->sum('credit');
                $debit = supplierLedger::where('purchase_id', '=', $inv->id)->sum('debit');
                $checkBalance =  $debit - $credit;
                if ($checkBalance > 0) :
                    $html .= "<option value='" . $inv->id . "'> $inv->invoice_no</option>";
                endif;
            }
        } else {
            $html .= "<option value='' selected disabled>-- No Due Invoice --</option>";
        }
        return $html;
    }
}
