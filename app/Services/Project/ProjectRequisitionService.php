<?php

namespace App\Services\Project;

use App\Repositories\Project\ProjectRequisitionRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class ProjectRequisitionService
{

    /**
     * @var branchRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param branchRepositories $branchRepositories
     */

    public function __construct(ProjectRequisitionRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOpening()
    {
        return $this->systemRepositories->getAllOpening();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        return $this->systemRepositories->getList($request);
    }

    public function getpandingList($request)
    {
        return $this->systemRepositories->getpandingList($request);
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
        //    dd($request->all());
        return [
            'requisitionCode' => 'required',
            'date' => 'required',
            // 'unitprice' => 'required',
            'project_id' => 'required',
            // 'branch_id' => 'required',
            // 'total' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            // 'qty' => 'required',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'date' => 'required',
            // 'unitprice' => 'required',
            'project_id' => 'required',
            // 'branch_id' => 'required',
            // 'total' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            // 'qty' => 'required',
        ];
    }

    public function approveupdateValidation($request, $id)
    {
        return [
            // 'unitprice' => 'required',
            // 'total' => 'required',
            // 'branch_id' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            // 'qty' => 'required',
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



    public function approveupdate($request, $id)
    {
        return $this->systemRepositories->approveupdate($request, $id);
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
