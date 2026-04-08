<?php

namespace App\Services\Settings;

use App\Repositories\Settings\StoreRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class StoreService
{


    /**
     * @var storeRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param storeRepositories $storeRepositories
     */
    public function __construct(StoreRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    // public function getAllBranch()
    // {
    //     return $this->systemRepositories->getAllBranch();
    // }


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
    public function statusUpdate($request, $id)
    {
        return $this->systemRepositories->statusUpdate($request, $id);
    }

    public function statusValidation($request)
    {
        return [
            'id'                   => 'required',
            'status'               => 'required',
        ];
    }
    /**
     * @param $request
     * @return array
     */
    public function storeValidation($request)
    {
        return [
            'branch_id'          =>'required',
            'name'               => 'required|max:100|min:2',
            'email'              => 'required|email|unique:branches,email',
            'phone'              => ['required', 'unique:branches,phone', 'regex:/(^(01))[3-9]{1}(\d){8}$/', new PhoneNumberValidationRules($request)],
            'address'            => 'nullable|max:200',
            'status'             => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'branch_id'          =>'required',
            'name'               => 'required|max:100|min:2',
            'email'              => 'required|email|unique:branches,email,' . $id,
            'phone'              => ['required', 'unique:branches,phone,' . $id, 'regex:/(^(01))[3-9]{1}(\d){8}$/'],
            'address'            => 'nullable|max:200',
            'status'             => 'nullable',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Branch
     */
    public function store($request)
    {
        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Branch
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