<?php

namespace App\Services\Hrm;

use App\Models\LeaveApplication;
use App\Repositories\Hrm\LeaveApplicationRepositories;

class LeaveApplicationService
{

    /**
     * @var $CustomerPaymentRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param $CustomerPaymentRepositories $branchRepositories
     */
    public function __construct(LeaveApplicationRepositories $systemRepositories)
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


    /**
     * @param $id
     * @return array
     */
    public function storeValidation($request)
    {

        return [
            'employee_id' => 'required',
            'branch_id' => 'required',
            'apply_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required',
            'payment_status' => 'required',
            'file' => 'mimes:pdf,doc,docx,txt,jpg,png,jpeg|max:2048',
        ];
    }



    public function updateValidation($request, $id)
    {
        return [

            'apply_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required',
            'payment_status' => 'required',
            'file' => 'mimes:pdf,doc,docx,txt,jpg,png,jpeg|max:2048',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function update($request, $id)
    {
        return $this->systemRepositories->update($request, $id);
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
    public function destroy($id)
    {
        return $this->systemRepositories->destroy($id);
    }
}
