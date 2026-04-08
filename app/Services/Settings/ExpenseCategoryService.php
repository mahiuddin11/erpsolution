<?php

namespace App\Services\Settings;

use App\Repositories\Settings\ExpenseCategoryRepositorie;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryService
{
    /**
     * @var branchRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param branchRepositories $branchRepositories
     */
    public function __construct(ExpenseCategoryRepositorie $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
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
            'name'               => 'required',
            'parent_id'               => 'required',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'name'               => 'required',
            'parent_id'               => 'required',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Opening
     */
    public function store($request)
    {
        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Opening
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