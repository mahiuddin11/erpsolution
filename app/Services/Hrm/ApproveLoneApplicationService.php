<?php

namespace App\Services\Hrm;

use App\Models\Lone;
use App\Repositories\Hrm\ApproveLoneApplicationRepositories;

class ApproveLoneApplicationService
{

    
    private $approveLoneApplicationRepositories;

    public function __construct(ApproveLoneApplicationRepositories $approveLoneApplicationRepositories)
    {
        $this->approveLoneApplicationRepositories = $approveLoneApplicationRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        // dd($request->all(), 'ApprovalLone');
        return $this->approveLoneApplicationRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->approveLoneApplicationRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->approveLoneApplicationRepositories->statusUpdate($request, $id);
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
        return $this->approveLoneApplicationRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function update($request, $id)
    {
        return $this->approveLoneApplicationRepositories->update($request, $id);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->approveLoneApplicationRepositories->details($id);
    }



    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->approveLoneApplicationRepositories->destroy($id);
    }
}
