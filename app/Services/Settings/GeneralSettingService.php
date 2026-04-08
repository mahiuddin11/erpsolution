<?php

namespace App\Services\Settings;

use App\Repositories\Settings\GeneralSettingRepository;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class GeneralSettingService
{

    /**
     * @var GeneralSettingRepository
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param GeneralSettingRepository $branchRepositories
     */
    public function __construct(GeneralSettingRepository $systemRepositories)
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
            'currency'              => 'required|max:100|min:2',
            'currency_position'     => 'required',
            'language'              => 'required',
            'timezone'              => 'required',
            'dateformat'            => 'required|date',
            'decimal_separate'      => 'required|numeric',
            'thousand_separate'     => 'required|numeric',
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
            'currency'              => 'required|max:100|min:2',
            'currency_position'     => 'required',
            'language'              => 'required',
            'timezone'              => 'required',
            'dateformat'            => 'required|date',
            'decimal_separate'      => 'required|numeric',
            'thousand_separate'     => 'required|numeric',
            'status'                 => 'nullable',
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