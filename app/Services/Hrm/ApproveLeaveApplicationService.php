<?php

namespace App\Services\Hrm;

use App\Models\LeaveApplication;
use App\Repositories\Hrm\ApproveLeaveApplicationRepositories;

class ApproveLeaveApplicationService
{

    
    private $ApproveLeaveApplicationRepositories;

    
    public function __construct(ApproveLeaveApplicationRepositories $ApproveLeaveApplicationRepositories)
    {
        $this->ApproveLeaveApplicationRepositories = $ApproveLeaveApplicationRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {

        return $this->ApproveLeaveApplicationRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->ApproveLeaveApplicationRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->ApproveLeaveApplicationRepositories->statusUpdate($request, $id);
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
    }



    public function updateValidation($request, $id)
    {
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        return $this->ApproveLeaveApplicationRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function update($request, $id)
    {
        return $this->ApproveLeaveApplicationRepositories->update($request, $id);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->ApproveLeaveApplicationRepositories->details($id);
    }



    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->ApproveLeaveApplicationRepositories->destroy($id);
    }
}
