<?php

namespace App\Services\Hrm;

use App\Models\Lone;
use App\Repositories\Hrm\CashApplicationRepositories;
use App\Repositories\Hrm\LoneApplicationRepositories;

class CashApplicationService
{

  
    private $CashApplicationRepositories;

   
    public function __construct(CashApplicationRepositories $CashApplicationRepositories)
    {
        $this->CashApplicationRepositories = $CashApplicationRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
       
        return $this->CashApplicationRepositories ->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->CashApplicationRepositories ->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->CashApplicationRepositories ->statusUpdate($request, $id);
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
        return $this->CashApplicationRepositories ->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function update($request, $id)
    {
        return $this->CashApplicationRepositories ->update($request, $id);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->CashApplicationRepositories ->details($id);
    }



    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->CashApplicationRepositories ->destroy($id);
    }
}
