<?php

namespace App\Http\Controllers\Backend\Usermanage;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Services\Usermanage\UserService;
use App\Services\Usermanage\UserRoleService;
use App\Transformers\UserTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * @var UserRoleService
     */
    private $userRoleService;
    /**
     * @var UserService
     */
    private $systemService;
    /**
     * @var UserTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param UserService $systemService
     * @param UserTransformer $systemTransformer
     */

    public function __construct(UserRoleService $userRoleService, UserService $userService, UserTransformer $userTransformer)
    {
        $this->userRoleService = $userRoleService;
        $this->systemService = $userService;
        $this->systemTransformer = $userTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'users List';
        return view('backend.pages.usermanage.users.index', get_defined_vars());
    }


    public function dataProcessingUser(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        //  dd($json_data);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New users';
        $userRoll = $this->userRoleService->getAllRole();
        $branchs = Branch::where('status', 'Active')->get();
        $employess = Employee::get();
        return view('backend.pages.usermanage.users.create', get_defined_vars());
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
        return redirect()->route('usermanage.user.index');
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
        $title = 'Edit Users';
        $userRoll = $this->userRoleService->getAllRole();
        $branchs = Branch::where('status', 'Active')->get();
        $userDetails = User::findOrFail($id);
        $employess = Employee::get();
        return view('backend.pages.usermanage.users.edit', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
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
        return redirect()->route('usermanage.user.index');
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
