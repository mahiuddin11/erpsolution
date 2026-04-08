<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransferLog;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Transection;
use App\Services\Settings\TransferService;
use App\Transformers\TransferTransformer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransferController extends Controller
{

    /**
     * @var BranchService
     */
    private $systemService;

    /**
     * @var BranchTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param BranchService $systemService
     * @param BranchTransformer $systemTransformer
     */
    public function __construct(TransferService $transferService, TransferTransformer $transferTransformer)
    {
        $this->systemService = $transferService;
        $this->systemTransformer = $transferTransformer;
    }

    public function index(Request $request)
    {
        $title = 'Transfer List';
        return view('backend.pages.settings.transfer.index', get_defined_vars());
    }

    public function dataProcessingBalanceTransfer(Request $request)
    {

        $json_data = $this->systemService->getList($request);


        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $title = 'Add New Transfer';
        $accounts = ChartOfAccount::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        return view('backend.pages.settings.transfer.create', get_defined_vars());
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
        return redirect()->route('settings.transfer.index');
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

        $title = 'Edit Transfer';
        $accounts = ChartOfAccount::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        $balancetransfar = BalanceTransferLog::findORFail($id);
        $remainingBalance = Transection::where('account_id', '=', $balancetransfar->from_account_id)->sum('debit') - Transection::where('account_id', '=', $balancetransfar->from_account_id)->sum('credit');
        return view('backend.pages.settings.transfer.edit', get_defined_vars());
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
        //dd($editInfo);

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
        return redirect()->route('settings.transfer.index');
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
    public function getAccountBalance(Request $request)
    {
        return AccountBalance($request->account_id); 
    }
}
