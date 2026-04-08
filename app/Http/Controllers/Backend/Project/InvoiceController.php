<?php

namespace App\Http\Controllers\Backend\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Project;
use DB;
use App\Services\Project\InvoiceService;
use App\Transformers\InvoiceTransformer;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    /**
     * @var InvoiceService
     */
    private $systemService;

    /**
     * @var InvoiceTransformer
     */
    private $systemTransformer;

    /**
     * ProjectController constructor.
     * @param InvoiceService $systemService
     * @param InvoiceService $systemTransformer
     */
    public function __construct(InvoiceService $InvoiceService, InvoiceTransformer $InvoiceTransformer)
    {
        $this->systemService = $InvoiceService;
        $this->systemTransformer = $InvoiceTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $title = 'Invoice List';
        return view('backend.pages.project.invoice.index', get_defined_vars());
    }

    public function dataProcessingInvoiceCreate(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $title = 'Add New Invoice';

        $projectLastData = Invoice::latest('id')->first();
        if ($projectLastData) :
            $projectData = $projectLastData->id + 1;
        else :
            $projectData = 1;
        endif;
        $projectCode = 'PI' . str_pad($projectData, 5, "0", STR_PAD_LEFT);

        $customer = Customer::get()->where('status', 'Active');

        $user = auth()->user();

        if ($user->branch_id !== null) {
            $accounts =  ChartOfAccount::where('id', $user->branch_id)->get();
        } else {
            $accounts = ChartOfAccount::where('status', 'Active')->get();
        }

        if ($user->branch_id !== null) {
            $project =  Project::where('id', $user->branch_id)->get();
        } else {
            $project = Project::get()->where('status', 'Active');
        }

        $branchs = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branchs = $branchs->where('id', $user->branch_id);
        }
        $branchs = $branchs->get();

        return view('backend.pages.project.invoice.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Project Invoice';

        $invoice = Invoice::findOrFail($id);

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.project.invoice.invoice', get_defined_vars());
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
        return redirect()->route('project.invoiceCreate.index');
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
        $title = 'Edit Invoice';

        $user = auth()->user();

        if ($user->branch_id !== null) {
            $accounts =  ChartOfAccount::where('id', $user->branch_id)->get();
        } else {
            $accounts = ChartOfAccount::where('status', 'Active')->get();
        }

        if ($user->branch_id !== null) {
            $project =  Project::where('id', $user->branch_id)->get();
        } else {
            $project = Project::get()->where('status', 'Active');
        }

        $branchs = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branchs = $branchs->where('id', $user->branch_id);
        }
        $branchs = $branchs->get();
        $customer = Customer::get()->where('status', 'Active');

        return view('backend.pages.project.invoice.edit', get_defined_vars());
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
        return redirect()->route('project.invoiceCreate.index');
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
}
