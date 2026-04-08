<?php

namespace App\Services;
use App\Repositories\CityRepositories;

class CityService
{
    /**
     * @var AccountRepo
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param accountRepo $accountRepo
     */
    public function __construct(CityRepositories $systemRepo)
    {
        $this->systemRepositories = $systemRepo;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList()
    {
        return $this->systemRepositories->getList();
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
            'province_id'        => 'required',
            'title'              => 'required',
            'status'             => 'required',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($id)
    {
        return [
            'province_id'        => 'required',
            'title'              => 'required',
            'status'             => 'required',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\AccounHead
     */
    public function store($request)
    {

        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\AccounHead
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
    public function delete($id)
    {

        return $this->systemRepositories->delete($id);
    }
}