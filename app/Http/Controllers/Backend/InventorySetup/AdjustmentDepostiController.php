<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\AdjustTransformer;
use App\Models\Branch;
use App\Models\Navigation;
use App\Models\Adjust;
use App\Models\Transection;
use App\Models\Customer;
use App\Models\ChartOfAccount;
use App\Models\customerLedger;
use App\Models\ReturnDeposit;
use App\Services\InventorySetup\AdjustService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class AdjustmentDepostiController extends Controller
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
    public function __construct(AdjustService $adjustService, AdjustTransformer $adjustTransformer)
    {
        $this->systemService = $adjustService;

        $this->systemTransformer = $adjustTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Deposit Adjust List';
        return view('backend.pages.inventories.adjust.deposit.index', get_defined_vars());
    }

    public function returnindex(Request $request)
    {
        $title = 'Deposit Return List';
        $account = ChartOfAccount::get()->where('status', 'Active');

        return view('backend.pages.inventories.adjust.deposit.return.index', get_defined_vars());
    }


    public function dataProcessingreturnDeposit(Request $request)
    {
        session()->put('type', 1);
        $json_data = $this->systemService->getreturnList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    public function dataProcessingadjustDeposit(Request $request)
    {
        session()->put('type', 1);
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function returncreate()
    {
        $title = 'Add New Return Deposite';
        $branch = Branch::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $account = ChartOfAccount::whereIn('id', [16, 17])->get()->where('status', 'Active');
        return view('backend.pages.inventories.adjust.deposit.return.create', get_defined_vars());
    }

    public function create()
    {
        $title = 'Add New Adjust';
        $branch = Branch::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $account = ChartOfAccount::whereIn('id', [16, 17])->get()->where('status', 'Active');
        return view('backend.pages.inventories.adjust.deposit.create', get_defined_vars());
    }

    public function returnshow()
    {
        $title = 'Invoice';
        // $details = ReturnDeposit::find;
        return view('backend.pages.inventories.adjust.deposit.create', get_defined_vars());
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
        return redirect()->route('inventorySetup.adjustDeposit.index');
    }


    public function returnstore(Request $request)
    {
        try {
            $this->validate($request, $this->systemService->returnstoreValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->returnstore($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.returnDeposit.returnindex');
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
        $branch = Branch::get()->where('status', 'Active');

        $title = 'Edit Adjust';
        $branch = Branch::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $account = ChartOfAccount::whereIn('id', [16, 17])->get()->where('status', 'Active');
        $adjusts = Adjust::findOrFail($id);
        $remainingBalance = Transection::where('account_id', '=', $adjusts->account_id)->sum('debit') - Transection::where('account_id', '=', $adjusts->account_id)->sum('credit');
        return view('backend.pages.inventories.adjust.deposit.edit', get_defined_vars());
    }

    public function returnedit($id)
    {
        $title = 'Edit Return Deposite';
        $branch = Branch::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');
        $editdetails = ReturnDeposit::find($id);
        $account = ChartOfAccount::get()->where('status', 'Active')->where('branch_id', $editdetails->branch_id);
        return view('backend.pages.inventories.adjust.deposit.return.edit', get_defined_vars());
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
        return redirect()->route('inventorySetup.adjustDeposit.index');
    }

    public function returnupdate(Request $request, $id)
    {

        try {
            $this->validate($request, $this->systemService->returnupdateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->returnupdate($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.returnDeposit.returnindex');
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

    public function customerbalance(Request $request)
    {
        $debit = customerLedger::where('customer_id', $request->countomerid)->pluck('debit')->sum();
        $credit = customerLedger::where('customer_id', $request->countomerid)->pluck('credit')->sum();
        $total = abs($debit - $credit);
        echo $total;
    }
}
