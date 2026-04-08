<?php

namespace App\Services\InventorySetup;

use App\Repositories\InventorySetup\CustomerRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class CustomerService
{

    /**
     * @var CustomerRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param CustomerRepositories $branchRepositories
     */
    public function __construct(CustomerRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        return $this->systemRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->systemRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->systemRepositories->statusUpdate($request, $id);
    }

    public function statusValidation($request)
    {

        return [
            'id' => 'required',
            'status' => 'required',
        ];
    }

    /**
     * @param $request
     * @return array
     */
    public function storeValidation($request)
    {
        return [
            'name' => 'nullable|max:100|min:2|unique:customers,name',
            'co_name' => 'required|max:100|min:2|unique:customers,co_name',
            'customergroup_id' => 'required',
            // 'email' => 'required',
            'phone' => 'nullable',
            // 'branch_id' => 'required',
            'status' => 'nullable',

        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        // dd($request->all());
        return [
            'name' => 'nullable|max:100|min:2|unique:customers,name,' . $id,
            'co_name' => 'required|max:100|min:2|unique:customers,co_name,' . $id,
            'customergroup_id' => 'required',
            // 'email' => 'required',
            'phone' => 'nullable',
            // 'branch_id' => 'required',
            'status' => 'nullable',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        // dd('customerservice', $request->all());
        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->systemRepositories->details($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function update($request, $id)
    {
        return $this->systemRepositories->update($request, $id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->systemRepositories->destroy($id);
    }
}
