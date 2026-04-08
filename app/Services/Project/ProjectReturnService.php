<?php

namespace App\Services\Project;

use App\Repositories\Project\ProjectReturnRepositories;

class ProjectReturnService
{

    /**
     * @var ProjectReturnService
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param ProjectReturnService $branchRepositories
     */
    public function __construct(ProjectReturnRepositories $systemRepositories)
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
    public function storeValidation($request)
    {
        return [
            'grnCode' => 'required',
            'date' => 'required',
            'project_id' => 'required',
            'branch_id' => 'required',
            'product_nm' => 'required',
            'stock' => 'required',
            'return_Qty' => 'required',
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
            'project_id' => 'required',
            'branch_id' => 'required',
            'product_nm' => 'required',
            'stock' => 'required',
            'return_Qty' => 'required',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        // dd($request->all());
        return $this->systemRepositories->store($request);
    }
    public function storeapprove($request)
    {
        // dd($request->all());
        return $this->systemRepositories->storeapprove($request);
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
