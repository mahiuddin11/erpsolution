<?php

namespace App\Http\Controllers\Backend\Usermanage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use helper;
use App\Models\Navigation;
use App\Models\User;
use App\Services\Usermanage\UserRoleService;
use App\Services\Settings\BranchService;
use App\Transformers\UserRoleTransformer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{

    /**
     * @var UserRoleService
     */
    private $systemService;

    /**
     * @var BranchService
     */
    private $branchService;


    /**
     * @var UserRoleTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param UserRoleService $systemService
     * @param UserRoleTransformer $systemTransformer
     */
    public function __construct(BranchService $branchService, UserRoleService $userRoleService, UserRoleTransformer $userRoleTransformer)
    {
        $this->branchService = $branchService;
        $this->systemService = $userRoleService;
        $this->systemTransformer = $userRoleTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'User Role List';
        return view('backend.pages.usermanage.userRole.index', get_defined_vars());
    }

    public function dataProcessinguserRole(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New User Role';
        $branch = $this->branchService->getAllBranch();
        $userRole = $this->systemService->getNavigation();
        return view('backend.pages.usermanage.userRole.create', get_defined_vars());
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
        return redirect()->route('usermanage.userRole.index');
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
        $title = 'Edit User Role';
        $dashboard_id = explode(",", $editInfo->dashboard_id);
        $parent_info = explode(",", $editInfo->parent_id);
        $navigation_info = explode(",", $editInfo->navigation_id);
        $branch_info = explode(",", $editInfo->branch_id);
        $branch = $this->branchService->getAllBranch();
        $userRole = $this->systemService->getNavigation();
        return view('backend.pages.usermanage.userRole.edit', get_defined_vars());
    }
    public function profile($id)
    {
        $title = 'Edit User Profile';
        $user = User::find($id);
        return view('backend.pages.usermanage.userRole.profile', get_defined_vars());
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
        // return redirect()->route('settings.branch.index');
        return redirect()->route('usermanage.userRole.index');
    }

    public function profileupdate(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name;
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        $user->save();
        session()->flash('success', 'Data successfully updated!!');
        // return redirect()->route('settings.branch.index');
        return redirect()->back();
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
