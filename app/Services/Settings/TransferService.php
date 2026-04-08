<?php

namespace App\Services\Settings;

use App\Repositories\Settings\TransferRepositories;

class TransferService
{

    /**
     * @var branchRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param branchRepositories $branchRepositories
     */
    public function __construct(TransferRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllBranch()
    {
        return $this->systemRepositories->getAllAccount();
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
            'date' => 'required',
            'branch_id' => 'required',
            'from_account_id' => 'required',
            'to_account_id' => 'required',
            'amount' => 'required',
            'status' => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {

        //  dd($request->all());
        return [
            'date' => 'required',
            'branch_id' => 'required',
            'from_account_id' => 'required',
            'to_account_id' => 'required',
            'amount' => 'required',
            'status' => 'nullable',
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
