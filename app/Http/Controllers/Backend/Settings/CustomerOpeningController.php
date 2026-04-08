<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CustomerOpening;
use App\Models\Accounts;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Navigation;
use App\Models\Transection;
use helper;
use App\Services\Settings\CustomerOpeningService;
use App\Transformers\CustomerOpeningTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class CustomerOpeningController extends Controller {

    /**
     * @var CustomerOpeningService
     */
    private $systemService;

    /**
     * @var CustomerOpeningTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param CustomerOpeningService $systemService
     * @param CustomerOpeningTransformer $systemTransformer
     */
    public function __construct(CustomerOpeningService $customerOpeningService, CustomerOpeningTransformer $customerOpeningTransformer) {
        $this->systemService = $customerOpeningService;
        $this->systemTransformer = $customerOpeningTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $title = 'Customer Opening List';
        return view('backend.pages.settings.customerOpening.index', get_defined_vars());
    }

    public function dataProcessingOpeningBalance(Request $request) {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        $title = 'Add New Customer Opening';
//        $BranchlastData = Branch::latest('id')->first();
//        if($BranchlastData):
//            $BranchData = $BranchlastData->id+1;
//        else:
//            $BranchData = 1;
//        endif;
//        $branchCode = 'BR' . str_pad($BranchData, 5, "0", STR_PAD_LEFT);
        $branch = Branch::get()->where('status', 'Active');
        $accounts = ChartOfAccount::get()->where('status', 'Active');
        $customer = Customer::get()->where('status', 'Active');

        return view('backend.pages.settings.customerOpening.create', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

      // dd($request->all());
        
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('settings.customerOpening.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Edit Customer Opening';
        return view('backend.pages.settings.customerOpening.edit', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
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
        return redirect()->route('settings.customerOpening.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statusUpdate($id, $status) {
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
    public function destroy($id) {
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

    public function getAccountBalance(Request $request) {
        $account_id = $request->account_id;
        $debit = Transection::where('account_id','=',$account_id)->sum('debit -credit'); 
        $credit = Transection::where('from_account','=',$account_id)->sum('credit'); 
      //  $account =        

       return $remainingBalance = $debit - $credit;

    }

}
