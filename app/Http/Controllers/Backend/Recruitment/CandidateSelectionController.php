<?php

namespace App\Http\Controllers\Backend\Recruitment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssetsCategory;
use App\Models\CandidateInforamtion;
use App\Models\Navigation;
use App\Services\Recruitment\CandidateSelectionService;
use App\Transformers\CandidateSelectionTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CandidateSelectionController extends Controller
{

    /**
     * @var CandidateSelectionService
     */
    private $systemService;
    /**
     * @var CandidateSelectionTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param CandidateSelectionService $systemService
     * @param CandidateSelectionService $systemTransformer
     */
    public function __construct(CandidateSelectionService $CandidateSelectionService, CandidateSelectionTransformer $candidateselectionTransformer)
    {
        $this->systemService = $CandidateSelectionService;
        $this->systemTransformer = $candidateselectionTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Candidate Selection List';
        return view('backend.pages.candidate_selection.index', get_defined_vars());
    }


    public function dataProcessingCandidateSelection(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Selection';
        $allCandidates = CandidateInforamtion::where('status', 'shortlisted')->get();

        return view('backend.pages.candidate_selection.create', get_defined_vars());
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
        return redirect()->route('candidate.selection.index');
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
        $title = 'Add New selection';
        $allCandidates = CandidateInforamtion::all();
        return view('backend.pages.candidate_selection.edit', get_defined_vars());
    }

    public function show(CandidateInforamtion $candidateInformation)
    {
        // dd($candidateInformation);
        $title = 'Candidate Details';
        return view('backend.pages.candidate_selection.details', get_defined_vars());
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
        return redirect()->route('candidate.selection.index');
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
