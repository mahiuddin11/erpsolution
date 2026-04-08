<?php

namespace App\Services\Settings;

use App\Repositories\Settings\FiscalYearRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class FiscalYearService
{


    /**
     * @var fiscalYearRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param fiscalYearRepositories $FiscalYearRepositories
     */
    public function __construct(FiscalYearRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllBranch()
    {
        return $this->systemRepositories->getAllBranch();
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
            'branch_id'          =>'required',
            'date'               => 'required',
            'fiscal_year'        => 'required',
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
            'date'               => 'required',
            'fiscal_year'        => 'required',
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