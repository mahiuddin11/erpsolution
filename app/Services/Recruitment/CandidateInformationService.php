<?php

namespace App\Services\Recruitment;

use App\Models\CandidateInformation;
use App\Repositories\Recruitment\CandidateInformationRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class CandidateInformationService
{

    /**
     * @var CandidateInformationRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param CandidateInformationRepositories $branchRepositories
     */
    public function __construct(CandidateInformationRepositories $systemRepositories)
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
            'status'               => 'required',
        ];
    }
    /**
     * @param $request
     * @return array
     */
    public function storeValidation($request)
    {
        // dd($request->all());
        return [
            'first_name'                   => 'required|max:100|min:2',
            'last_name'                    => 'required',
            'email'                    => 'required',
            'phone'                    => 'required',
            'alternate_phone'              => 'nullable',
            'ssn'                          => 'nullable',
            'present_address'              => 'required',
            'permanent_address'            => 'required',
            'obtain_degree'                    => 'nullable',
            'university'                    => 'nullable',
            'cgpa'                          => 'nullable',
            'comments'                    => 'nullable',
            'company_name'                    => 'nullable',
            'work_experience'                 => 'nullable',
            'supervisor'                    => 'nullable',
            'image'                       => 'required'

        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'first_name'                   => 'required|max:100|min:2',
            'last_name'                    => 'required',
            'email'                    => 'required',
            'phone'                    => 'required',
            'alternate_phone'              => 'nullable',
            'ssn'                          => 'nullable',
            'present_address'              => 'required',
            'permanent_address'            => 'required',
            'obtain_degree'                    => 'nullable',
            'university'                    => 'nullable',
            'cgpa'                          => 'nullable',
            'comments'                    => 'nullable',
            'company_name'                    => 'nullable',
            'work_experience'                 => 'nullable',
            'supervisor'                    => 'nullable',
            'image'                       => 'required'

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
