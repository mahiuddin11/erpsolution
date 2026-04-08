<?php

namespace App\Http\Controllers\Backend\AssetsManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssetsCategory;
use App\Models\AssetsList;
use App\Models\ChartOfAccount;
use App\Models\Navigation;
use App\Services\AssetsManagement\AssetsListService;
use App\Transformers\AssetsListTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AssetsListController extends Controller
{
    /**
     * @var AssetsListService
     */
    private $systemService;
    /**
     * @var AssetsListTransformer
     */
    private $systemTransformer;
    /**
     * CategoryController constructor.
     * @param AssetsListService $systemService
     * @param AssetsListService $systemTransformer
     */

    public function __construct(AssetsListService $assetsListService, AssetsListTransformer $assetsListTransformer)
    {
        $this->systemService = $assetsListService;
        $this->systemTransformer = $assetsListTransformer;
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Assets List';
        return view('backend.pages.assets_list.index', get_defined_vars());
    }
    public function dataProcessingAssetsList(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Assets List';
        $assetCat = AssetsCategory::all();
        $accounts = ChartOfAccount::getaccount(2)->get();
        $payments = ChartOfAccount::getaccount(6)->get();
        return view('backend.pages.assets_list.create', get_defined_vars());
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
        return redirect()->route('assets.list.index');
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
        $title = 'Add New Asset';
        $assetCat = AssetsCategory::all();
        $accounts = ChartOfAccount::getaccount(2)->get();
        $payments = ChartOfAccount::getaccount(6)->get();
        return view('backend.pages.assets_list.edit', get_defined_vars());
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
        return redirect()->route('assets.list.index');
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
