<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CustomerTransformer;
use App\Models\Branch;
use App\Models\Navigation;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Services\InventorySetup\CustomerService;
use App\Services\Settings\BranchService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;





class CustomerController extends Controller
{


    private $customerService;

    private $customerTransformer;



    public function __construct(CustomerService $customerService, CustomerTransformer $customerTransformer)
    {
        $this->customerService = $customerService;

        $this->customerTransformer = $customerTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Customer List';
        return view('backend.pages.inventories.customer.index', get_defined_vars());
    }


    public function dataProcessingCustomer(Request $request)
    {
        $json_data = $this->customerService->getList($request);
        return json_encode($this->customerTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Customer';

        $customertLastData = Customer::latest('id')->first();
        if ($customertLastData) :
            $customerData = $customertLastData->id + 1;
        else :
            $customerData = 1;
        endif;
        $customerCode = 'CU' . str_pad($customerData, 5, "0", STR_PAD_LEFT);

        $branch = Branch::get()->where('status', 'Active');
        $customerGroup = CustomerGroup::all();
        return view('backend.pages.inventories.customer.create', get_defined_vars());
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // dd('controller', $request->all());
        try {
            $this->validate($request, $this->customerService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->customerService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.customer.index');
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
        $editInfo =   $this->customerService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $branch = Branch::get()->where('status', 'Active');
        $customerGroup = CustomerGroup::all();
        $title = 'Add New Customer';
        return view('backend.pages.inventories.customer.edit', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->customerService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        try {
            $this->validate($request, $this->customerService->updateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->customerService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.customer.index');
    }


    public function statusUpdate($id, $status)
    {
        if (!is_numeric($id)) {
            return response()->json($this->customerTransformer->invalidId($id), 200);
        }
        $detailsInfo =   $this->customerService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->customerTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo =  $this->customerService->statusUpdate($id, $status);
        if ($statusInfo) {
            return response()->json($this->customerTransformer->statusUpdate($statusInfo), 200);
        }
    }


    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json($this->customerTransformer->invalidId($id), 200);
        }
        $detailsInfo =   $this->customerService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->customerTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo =  $this->customerService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->customerTransformer->delete($deleteInfo), 200);
        }
    }
}
