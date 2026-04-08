<?php

namespace App\Http\Controllers\Backend\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Transection;
use helper;
use App\Services\Project\ProjectExpenseService;
use App\Transformers\ProjectExpenseTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ProjectExpenseController extends Controller
{

    /**
     * @var ProjectExpenseService
     */
    private $systemService;
    /**
     * @var ProjectExpenseTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param ProjectExpenseService $systemService
     * @param ProjectExpenseTransformer $systemTransformer
     */
    public function __construct(ProjectExpenseService $ProjectExpenseService, ProjectExpenseTransformer $ProjectExpenseTransformer)
    {

        $this->systemService = $ProjectExpenseService;
        $this->systemTransformer = $ProjectExpenseTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Project Expense List';
        $user = Auth::user();
        $project = Project::where('manager_id', $user->id)->pluck('condition')->first();
        return view('backend.pages.project.expense.index', get_defined_vars());
    }

    public function dataProcessingProjectExpense(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Project Expense';
        $category = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', 0);
        $projects = Project::where('status', 'Active')->get();
        $accounts = ChartOfAccount::getaccount(4)->get();
        return view('backend.pages.project.expense.create', get_defined_vars());
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
        return redirect()->route('project.projectexpense.index');
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

        $title = 'Edit Expense';
        $category = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', 0);
        $expence = ProjectExpense::find($id);
        $projects = Project::where('status', 'Active')->get();

        $sobecategory = ExpenseCategory::find($expence->subcategorie_id);

        return view('backend.pages.project.expense.edit', get_defined_vars());
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
        return redirect()->route('project.projectexpense.index');
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

    public function getSubCategory(Request $request)
    {
        $category_id = $request->catId;
        $subcetegoris = ExpenseCategory::get()->where('status', 'Active')->where('parent_id', $category_id);
        if ($subcetegoris) {
            return view('backend.pages.settings.expense.subcategory', get_defined_vars());
        } else {
            echo '<option> No Data Records</option>';
        }
    }
}
