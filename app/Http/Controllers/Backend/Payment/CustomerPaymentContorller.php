<?php

namespace App\Http\Controllers\Backend\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CustomerPaymentTransformer;
use App\Models\Branch;
use App\Models\Navigation;
use App\Models\Adjust;
use App\Models\Customer;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\customerLedger;
use App\Services\Customer\CustomerPaymentService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Symfony\Contracts\Service\Attribute\Required;

class CustomerPaymentContorller extends Controller
{

    /**
     * @var customerPaymentService
     */
    private $systemService;
    /**
     * @var customerPaymentTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param customerPaymentService $systemService
     * @param customerPaymentTransformer $systemTransformer
     */
    public function __construct(CustomerPaymentService $customerPaymentService, CustomerPaymentTransformer $customerPaymentTransformer)
    {
        $this->systemService = $customerPaymentService;

        $this->systemTransformer = $customerPaymentTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $title = 'Payment List';
        return view('backend.pages.customer_payment.index', get_defined_vars());
    }


    public function dataProcessingCustomerPayment(Request $request)
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
        $customer = Customer::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');

        $invoice = DB::table('sales')
            ->select('id', 'invoice_no')
            ->get();

        return view('backend.pages.customer_payment.create', get_defined_vars());
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
        return redirect()->route('payment.customer.index');
    }
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function show(Request $request, $id)
    {
        $title = 'Receive Voucher';
        $invoice = customerLedger::where('customer_ledgers.id', $id)
            ->join('sales', 'sales.id', '=', 'customer_ledgers.sale_id')
            ->first();
        // pops($invoice);
        $companyInfo = Company::latest('id')->first();
        // pops($companyInfo);
        return view('backend.pages.customer_payment.invoice', get_defined_vars());
    }

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
        $customer = Customer::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');

        $invoice = DB::table('sales')
            ->select('id', 'invoice_no')
            ->get();

        $title = 'Edit Payment';
        return view('backend.pages.customer_payment.edit', get_defined_vars());
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
        return redirect()->route('payment.customer.index');
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

    public function dueInvoiceAmount(Request $request)
    {

        // dd($request->all());
        $debit = 0;
        $credit = 0;

        $conditionalArray = array(
            'sale_id' => $request->sale_id,
        );
        $conditionalArray2 = array(
            'sale_id' => $request->sale_id,
        );
        $credit = CustomerLedger::where($conditionalArray2)->sum('credit');
        $debit = CustomerLedger::where($conditionalArray)->sum('debit');
        //  dd($debit);

        echo $credit - $debit;
    }


    public function getCustomerDetails(Request $request)
    {

        $sale_id = $request->sale_id;


        $customerdetails =  customerLedger::where('sale_id', $sale_id)
            ->join('customers', 'customers.id', '=', 'customer_ledgers.customer_id')
            ->select('customer_ledgers.customer_id', 'customers.id', 'customers.name')
            ->first();

        echo  json_encode($customerdetails);
    }

    public function getAllBranchCustomeList(request $request)
    {
        $branchId = $request->branch_id;

        $customer = customerLedger::where('customer_ledgers.branch_id', $branchId)
            ->join('customers', 'customers.id', '=', 'customer_ledgers.customer_id')
            ->groupBy('customer_ledgers.customer_id')
            ->get();



        $html = '';
        if ($customer->isNotEmpty()) {
            $html .= "<option value='' selected disabled>--Select Customer--</option>";
            foreach ($customer as $key => $cust) {
                $html .= "<option value='" . $cust->id . "'>$cust->customerCode - $cust->name</option>";
            }
        } else {
            $html .= "<option value='' selected disabled>--No Customer Available--</option>";
        }
        return $html;
    }

    public function getAllDueInvoiceList(Request $request)
    {
        $customerId = $request->customer_id;

        $invoice = customerLedger::where('customer_ledgers.customer_id', $customerId)
            ->where('customer_ledgers.payment_type', 'Credit')
            ->groupBy('customer_ledgers.sale_id')
            ->join('sales', 'sales.id', '=', 'customer_ledgers.sale_id')
            ->get();


        $html = '';
        if ($invoice->isNotEmpty()) {
            $html .= "<option value='' selected disabled>--Select Invoice--</option>";
            foreach ($invoice as $key => $inv) {
                $credit = customerLedger::where('sale_id', '=', $inv->id)->sum('credit');
                $debit = customerLedger::where('sale_id', '=', $inv->id)->sum('debit');

                $checkBalance = $credit - $debit;
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
