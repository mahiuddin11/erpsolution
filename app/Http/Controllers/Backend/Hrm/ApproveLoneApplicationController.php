<?php

namespace App\Http\Controllers\Backend\Hrm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Lone;
use App\Models\Transection;
use App\Transformers\AdjustTransformer;
use App\Services\Hrm\ApproveLoneApplicationService;
use App\Services\InventorySetup\AdjustService;
use App\Transformers\Transformers;
use Illuminate\Validation\ValidationException;


class ApproveLoneApplicationController extends Controller
{

    /**
     * @var adjustService
     */
    private $systemService;
    /**
     * @var adjustTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param adjustService $systemService
     * @param adjustTransformer $systemTransformer
     */

    public function __construct(ApproveLoneApplicationService $ApproveLoneApplicationService, Transformers $transformers)
    {
        $this->systemService = $ApproveLoneApplicationService;

        $this->systemTransformer = $transformers;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Lone Application Applicaitn List';
        return view('backend.pages.hrm.lone_approve.index', get_defined_vars());
    }


    public function dataProcessingApproveLoneApplication(Request $request)
    {

        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Leave application ';
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
        return redirect()->route('hrm.lone.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function edit(Request $request, Lone $lone)
    {
        $lone->amount = $request->amount;
        $lone->lone_adjustment = $request->lone_adjustment;
        $lone->status = 'approved';
        $lone->save();

        $transection['date'] = now();
        $transection['account_id'] = 1;
        $transection['employee_id'] = $lone->employee_id;
        $transection['branch_id'] = $lone->branch_id;
        $transection['type'] =  16;
        $transection['amount'] = $lone->amount;
        $transection['debit'] = $lone->amount;
        Transection::create($transection);

        $transection2['date'] = now();
        $transection2['account_id'] = 4;
        $transection2['payment_id'] = 4;
        $transection2['branch_id'] = $lone->branch_id;
        $transection2['type'] =  16;
        $transection2['amount'] = $lone->amount;
        $transection2['credit'] = $lone->amount;
        Transection::create($transection2);

        session()->flash('success', 'Lone Application successfully Approve!!');
        return back();
    }

    public function cancel(Lone $lone)
    {

        $lone->status = 'cancel';
        $lone->save();
        session()->flash('success', ' Lone Application successfully Cancelled!!');
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
        return redirect()->route('hrm.leave.index');
    }

    // Leave Application Deatails
    public function show(Lone $lone)
    {
        $title = 'Approve Loan Application Details';
        return view('backend.pages.hrm.lone_approve.details', get_defined_vars());
    }

    // 



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
