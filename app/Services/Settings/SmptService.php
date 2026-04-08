<?php

namespace App\Services\Settings;

use App\Repositories\Settings\SmptRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class SmptService
{

    /**
     * @var SmptRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param SmptRepositories $branchRepositories
     */
    public function __construct(SmptRepositories $systemRepositories)
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
            'protocol'               => 'required|max:100|min:2',
            'smtp_host'              => 'required',
            'smtp_port'              => 'required',
            'sender_mail'            => 'required|max:200',
            'password'               => 'required|max:200',
            'status'                 => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'protocol'               => 'required|max:100|min:2',
            'smtp_host'              => 'required',
            'smtp_port'              => 'required',
            'sender_mail'            => 'required|max:200',
            'password'               => 'required|max:200',
            'status'                 => 'nullable',
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