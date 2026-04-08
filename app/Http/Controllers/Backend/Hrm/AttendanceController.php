<?php

namespace App\Http\Controllers\Backend\Hrm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use App\Transformers\AdjustTransformer;
use App\Transformers\Transformers;
use App\Models\Branch;
use App\Models\Attendance;
use App\Models\Customer;
use App\Models\ChartOfAccount;
use App\Models\Employee;
use App\Services\Hrm\AttendanceService;
use Illuminate\Validation\ValidationException;


class AttendanceController extends Controller
{

    /**
     * @var attendanceService
     */
    private $systemService;
    /**
     * @var Transformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param adjustService $systemService
     * @param adjustTransformer $systemTransformer
     */
    public function __construct(AttendanceService $attendanceService, Transformers $Transformer)
    {
        $this->systemService = $attendanceService;

        $this->systemTransformer = $Transformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {   
        $title = 'Attendance List';
        return view('backend.pages.hrm.attendance.index', get_defined_vars());
    }


    public function dataProcessingattendance(Request $request)
    {   
        session()->put('type', 2);
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Adjust';
        $branch = Branch::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $account = ChartOfAccount::get()->where('status', 'Active');
        $employees = Employee::get();
        return view('backend.pages.hrm.attendance.create', get_defined_vars());
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign_in(Request $request)
    {
        // dd($request->all());
        try {
            $this->validate($request, $this->systemService->signinValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $now = now();
        $attendanceDate = $now->format('Y-m-d');

        // Cross midnight rule (12AM - 5AM previous day)
        if ($now->format('H:i:s') < '05:00:00') {
            $attendanceDate = $now->subDay()->format('Y-m-d');
        }

        $check = Attendance::where('emplyee_id', $request->emplyee_id)
            ->whereDate('date', $attendanceDate)
            ->first();

        if (!empty($check)) {
            session()->flash('error', 'This employee already check in');
            return redirect()->route('hrm.attendance.create');
        }

        $request->merge([
            'date' => $attendanceDate
        ]);

        $this->systemService->signin($request);
        $lock = 0;
        session()->put('sign', "0");

        session()->flash('success', 'Check In successfully!!');
        return redirect()->route('hrm.attendance.create');
    }

    public function sign_out(Request $request)
    {

        try {
            $this->validate($request, $this->systemService->signoutValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // if (!Attendance::where('emplyee_id', $request->emplyee_id)->whereDate('date', today()->format('Y-m-d'))->first()) {
        //     session()->flash('error', 'This employee not check in');
        //     return redirect()->route('hrm.attendance.create');
        // }

        $this->systemService->signout($request);
        session()->put('sign', "1");

        session()->flash('success', 'Check Out successfully!!');
        return redirect()->route('hrm.attendance.create');
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
     * Edit Attendance
     * 
     * @author itwaybd
     * @contributor Sajjad 
     * @param int $id
     * @created 17-09-23
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $title = "Edit Attendance";
        $model = $this->systemService->edit($id);

        return view('backend.pages.hrm.attendance.edit', get_defined_vars());
    }

    /**
     * Update Attendance
     * 
     * @author itwaybd
     * @contributor Sajjad 
     * @param int Request $request
     * @created 17-09-23
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function update(Request $request, $id)
    {   
        try {
            $this->validate($request, $this->systemService->attendanceEditValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
     
        $updated = $this->systemService->update($request, $id);

        if ($updated) {
            session()->flash('success', 'Attendance update successfuly!!');
            return redirect()->route('hrm.attendance.index');
        }
    }

    /**
     * Delete Attendance
     * 
     * @author itwaybd
     * @contributor Sajjad 
     * @param int Request $request
     * @created 17-09-23
     * 
     * @return      * Delete Attendance

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
