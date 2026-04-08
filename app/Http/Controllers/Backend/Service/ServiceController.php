<?php

namespace App\Http\Controllers\Backend\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\User;
use App\Models\Service;
use App\Models\Company;
use App\Models\Customer;
use App\Services\Service\ServiceService;
use App\Transformers\ServiceTransformer;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{

    /**
     * @var ServiceService
     */
    private $systemService;

    /**
     * @var ServiceTransformer
     */
    private $systemTransformer;

    /**
     * ServiceController constructor.
     * @param ServiceService $systemService
     * @param ServiceService $systemTransformer
     */
    public function __construct(ServiceService $ServiceService, ServiceTransformer $ServiceTransformer)
    {
        $this->systemService = $ServiceService;
        $this->systemTransformer = $ServiceTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $title = 'Service List';
        return view('backend.pages.service.index', get_defined_vars());
    }

    public function dataProcessingService(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $title = 'Add New Service';
        $projectLastData = Service::latest('id')->first();
        if ($projectLastData) :
            $projectData = $projectLastData->id + 1;
        else :
            $projectData = 1;
        endif;

        $projectCode = 'SV' . str_pad($projectData, 5, "0", STR_PAD_LEFT);
        $customer = Customer::get()->where('status', 'Active');

        $user = auth()->user();

        $branchs = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branchs = $branchs->where('id', $user->branch_id);
        }
        $branchs = $branchs->get();
        return view('backend.pages.service.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Service Invoice';

        $invoice = Service::findOrFail($id);

        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.service.invoice', get_defined_vars());
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
        return redirect()->route('service.service.index');
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
        $title = 'Edit Project';
        // $branchs = Branch::get()->where('status', 'Active');
        $user = auth()->user();
        $branchs = Branch::where('status', 'Active');
        if ($user->branch_id !== null) {
            $branchs = $branchs->where('id', $user->branch_id);
        }
        $branchs = $branchs->get();
        $accounts = ChartOfAccount::where('status', 'Active')->get();
        $customer = Customer::get()->where('status', 'Active');

        return view('backend.pages.service.edit', get_defined_vars());
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
        return redirect()->route('service.service.index');
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
