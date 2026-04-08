<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Transection;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Opening;
use App\Models\OpeningBalance;
use App\Models\Project;
use App\Models\Supplier;
use helper;
use App\Services\Settings\OpeningService;
use App\Transformers\OpeningTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class OpeningController extends Controller
{

    /**
     * @var OpeningService
     */
    private $systemService;
    /**
     * @var OpeningTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param OpeningService $systemService
     * @param OpeningTransformer $systemTransformer
     */
    public function __construct(OpeningService $openingService, OpeningTransformer $openingTransformer)
    {
        $this->systemService = $openingService;
        $this->systemTransformer = $openingTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Opening List';
        return view('backend.pages.settings.opening.index', get_defined_vars());
    }


    public function dataProcessingOpeningBalance(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $title = 'Add New Opening';
        $accounts = ChartOfAccount::whereIn('id',[1,9])->get();

        return view('backend.pages.settings.opening.create', get_defined_vars());
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $openingbalance = Transection::where('account_id', $request->to_account_id)->where('type', 1)->first();

        if ($openingbalance) {
            session()->flash('error', 'Already opening balance added !!');
            return redirect()->back()->withErrors("Already opening balance added")->withInput();
        }
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('settings.openingbalance.create');
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
        // dd($editInfo);
        $title = 'Edit Opening';
        $branch = Project::get()->where('condition', 'One Going');
        $accounts = ChartOfAccount::get()->where('parent_id', 0);
        $transections = OpeningBalance::find($id);

        $projects = Project::get();
        $employees = Employee::get();
        $customers = Customer::get();
        $suppliers = Supplier::get();
        return view('backend.pages.settings.opening.edit', get_defined_vars());
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
        return redirect()->route('settings.openingbalance.index');
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

    public function getAllAccountHead(Request $request)
    {
        $branchId = $request->branchId;

        $Accounts = ChartOfAccount::get();

        $html = '';
        if ($Accounts->isNotEmpty()) {
            $html .= "<option value='' selected disabled>--Select Accounts--</option>";
            foreach ($Accounts as $key => $inv) {
                $html .= "<option value='" . $inv->id . "'> $inv->accountCode - $inv->account_name </option>";
            }
        } else {
            $html .= "<option value='' selected disabled>-- No Accounts --</option>";
        }
        return $html;
    }
}
