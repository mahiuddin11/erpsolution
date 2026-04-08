<?php

namespace App\Services\Hrm;

use App\Models\Lone;
use App\Repositories\Hrm\CashApplicationRepositories;
use App\Repositories\Hrm\LoneApplicationRepositories;

class CashApplicationService
{

    /**
     * @var $CustomerPaymentRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param $CustomerPaymentRepositories $branchRepositories
     */
    public function __construct(CashApplicationRepositories $systemRepositories)
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
            'amount' => 'required',
        ];
    }



    public function updateValidation($request, $id)
    {
        return [
            'employee_id' => 'required',
            'amount' => 'required',
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
