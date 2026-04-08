<?php

namespace App\Services\Hrm;

use App\Models\AssetsList;
use App\Models\Award;
use App\Repositories\Hrm\AwardRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class AwardService
{

    /**
     * @var AwardRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param AwardRepositories $branchRepositories
     */
    public function __construct(AwardRepositories $systemRepositories)
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
            'id'                   => 'required',
        ];
    }
    /**
     * @param $request
     * @return array
     */
    public function storeValidation($request)
    {

        return [
            'name'                   => 'required',
            'desc'                   => 'nullable',
            'gift_item'              => 'required',
            'date'                   => 'required',
            'employee_id'               => 'required',
            'award_by'               => 'required'
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {

        return [
            'name'                   => 'required',
            'desc'                   => 'nullable',
            'gift_item'              => 'required',
            'date'                   => 'required',
            'employee_id'            => 'required',
            'award_by'               => 'required'
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
