<?php

namespace App\Services\Settings;

use App\Repositories\Settings\CustomerOpeningRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class CustomerOpeningService {

    /**
     * @var branchRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param branchRepositories $branchRepositories
     */
    public function __construct(CustomerOpeningRepositories $systemRepositories) {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOpening() {
        return $this->systemRepositories->getAllOpening();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request) {
        return $this->systemRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id) {
        return $this->systemRepositories->statusUpdate($request, $id);
    }

    public function statusValidation($request) {
        return [
            'id' => 'required',
            'status' => 'required',
        ];
    }

    /**
     * @param $request
     * @return array
     */
    public function storeValidation($request) {
        
        return [
            'date' => 'required',
         
            'branch_id' => 'required',
            'customer_id' => 'required',
            'amount' => 'required',
            'status' => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id) {
        return [
//            'name'               => 'required|max:100|min:2',
//            'email'              => 'required|email|unique:branches,email,' . $id,
//            'phone'              => ['required', 'unique:branches,phone,' . $id, 'regex:/(^(01))[3-9]{1}(\d){8}$/'],
//            'address'            => 'nullable|max:200',
            'status' => 'nullable',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Opening
     */
    public function store($request) {
        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Opening
     */
    public function details($id) {

        return $this->systemRepositories->details($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function update($request, $id) {
        return $this->systemRepositories->update($request, $id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function destroy($id) {
        return $this->systemRepositories->destroy($id);
    }

}
