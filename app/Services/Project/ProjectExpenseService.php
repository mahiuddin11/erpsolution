<?php

namespace App\Services\Project;

use App\Repositories\Project\ProjectExpenseRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class ProjectExpenseService
{


    /**
     * @var branchRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param branchRepositories $branchRepositories
     */

    public function __construct(ProjectExpenseRepositories $systemRepositories)
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
            'category_id'               => 'required',
            'subcategory_id'               => 'nullable',
            'account_id'               => 'required',
            'amount'               => 'required',
            'date'             => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'category_id'               => 'required',
            'subcategory_id'               => 'nullable',
            'account_id'               => 'required',
            'amount'               => 'required',
            'date'             => 'nullable',
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




    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->systemRepositories->destroy($id);
    }
}
