<?php

namespace App\Http\Controllers\Backend\Hrm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CashReq;
use App\Models\Employee;
use App\Models\Lone;
use App\Models\Transection;
use App\Services\Hrm\ApproveCashApplicationService;
use App\Transformers\Transformers;
use Illuminate\Validation\ValidationException;

class ApproveCashReqApplicationController extends Controller
{
    /**
     * @var ApproveCashApplicationService
     */
    private $systemService;
    /**
     * @var ApproveCashApplicationService
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param ApproveCashApplicationService $systemService
     * @param ApproveCashApplicationService $systemTransformer
     */

    public function __construct(ApproveCashApplicationService $ApproveLoneApplicationService, Transformers $transformers)
    {
        $this->systemService = $ApproveLoneApplicationService;

        $this->systemTransformer = $transformers;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Cash Requisition List';
        return view('backend.pages.hrm.cash_req_approve.index', get_defined_vars());
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
        $title = 'Add New Cash Requisition ';
        $employees = Employee::get();
        return view('backend.pages.hrm.leave_application.create', get_defined_vars());
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
        return redirect()->route('hrm.cash-req.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function edit(Request $request, CashReq $lone)
    {
        // $lone->amount = $request->amount ?? $lone->amount ;
        $lone->approval_amount = $request->amount ?? $lone->amount;
        $lone->bank_name = $request->bank_name;
        $lone->check_number = $request->check_number;
        $lone->status = 'approved';
        $lone->approve_by = auth()->id();
        $lone->save();

        session()->flash('success', 'Cash Requisition successfully Approve!!');
        return back();
    }

    public function cancel(CashReq $lone)
    {

        $lone->status = 'cancel';
        $lone->save();
        session()->flash('success', ' Cash Requisition successfully Cancelled!!');
        return back();
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
        return redirect()->route('hrm.cash-req.index');
    }

    // Leave Application Deatails
    public function show(CashReq $lone)
    {
        $title = 'Approve Cash Requisition Details';
        return view('backend.pages.hrm.cash_req_approve.details', get_defined_vars());
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
}
