<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\ContraVoucher;
use App\Models\ContraVoucherDetails;
use App\Models\ExpenseCategory;
use App\Services\Settings\ContraVoucherService;
use App\Transformers\ExpenseTransformer;
use Illuminate\Validation\ValidationException;

class ContraVoucherController extends Controller
{
    /**
     * @var ContraVoucherService
     */
    private $systemService;
    /**
     * @var ExpenseTransformer
     */
    private $systemTransformer;
    /**
     * CategoryController constructor.
     * @param ContraVoucherService $systemService
     * @param ExpenseTransformer $systemTransformer
     */

    public function __construct(ContraVoucherService $ContraVoucherService, ExpenseTransformer $expenseTransformer)
    {
        $this->systemService = $ContraVoucherService;
        $this->systemTransformer = $expenseTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //$this->expenseFix();
        $title = 'Contra Voucher List';
        return view('backend.pages.settings.contra_voucher.index', get_defined_vars());
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
        $title = 'Add New Contra Voucher';
        $branch = Branch::get()->where('status', 'Active');
        $accounts = ChartOfAccount::whereIN('parent_id', [6,14,15])->get();
        $creditvoucher = ContraVoucher::get();
        $creditvoucherLastData = ContraVoucher::latest('id')->first();
        if ($creditvoucherLastData) :
            $creditvoucherData = $creditvoucherLastData->id + 1;
        else :
            $creditvoucherData = 1;
        endif;
        $invoice_no = 'CON' . str_pad($creditvoucherData, 5, "0", STR_PAD_LEFT);
        return view('backend.pages.settings.contra_voucher.create', get_defined_vars());
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
        return redirect()->route('settings.contra.voucher.index');
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
        $title = 'Edit Contra Voucher';
        $branch = Branch::get()->where('status', 'Active');
        $creditaccountheas = ChartOfAccount::whereIn('id', [16, 17])->get();
        $accounts = ChartOfAccount::where('parent_id', 6)->get();

        return view('backend.pages.settings.contra_voucher.edit', get_defined_vars());
    }

    public function show($id)
    {
        $title = "Contra Voucher";
        $contraVoucher = ContraVoucher::find($id);
        $contraVoucherDetails = $contraVoucher->details;
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.settings.contra_voucher.contra_voucher_show', get_defined_vars());
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
        return redirect()->route('settings.contra.voucher.index');
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

    public function getAccountBalance(Request $request)
    {
        echo AccountBalance($request->account_id);
    }

    public function singledestroy($id)
    {
        $local = ContraVoucherDetails::find($id);
        AccountTransaction::where('type', 7)->where('table_id', $id)->delete();
        $local->delete();
        session()->flash('success', 'Data successfully Delete!!');
        return redirect()->route('settings.contra.voucher.index');
    }

    public function getSubCategory(Request $request)
    {
        $category_id = $request->catId;
        $subcetegoris = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', $category_id);
        if ($subcetegoris) {
            return view('backend.pages.settings.contra_voucher.subcategory', get_defined_vars());
        } else {
            echo '<option> No Data Records</option>';
        }
    }
    public function accountsearch(Request $request)
    {
        // dd($request->all());
        $data = "";
        $account = ChartOfAccount::where('branch_id', $request->branch_id)->get();
        if (!$account->isEmpty()) {
            $data .= '<option selected disabled>Select Account</option>';
            foreach ($account as $value) {
                $data .= '<option value="' . $value->id . '">' . $value->accountCode . ' - ' . $value->account_name . '</option>';
            }
        } else {
            $data .= '<option selected disabled>No account found</option>';
        }
        echo $data;
    }
}
