<?php

namespace App\Services\Hrm;

use App\Models\Lone;
use App\Repositories\Hrm\ApproveCashReqApplicationRepositories;
use App\Repositories\Hrm\ApproveLoneApplicationRepositories;

class ApproveCashApplicationService
{

    
    private $ApproveCashReqApplicationRepositories;

    /**
     * AdminCourseService constructor.
     * @param $CustomerPaymentRepositories $branchRepositories
     */
    public function __construct(ApproveCashReqApplicationRepositories $ApproveCashReqApplicationRepositories)
    {
        $this->ApproveCashReqApplicationRepositories = $ApproveCashReqApplicationRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        // dd('service');
        return $this->ApproveCashReqApplicationRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->ApproveCashReqApplicationRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->ApproveCashReqApplicationRepositories->statusUpdate($request, $id);
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
        return $this->ApproveCashReqApplicationRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function update($request, $id)
    {
        return $this->ApproveCashReqApplicationRepositories->update($request, $id);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->ApproveCashReqApplicationRepositories->details($id);
    }



    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->ApproveCashReqApplicationRepositories->destroy($id);
    }
}
