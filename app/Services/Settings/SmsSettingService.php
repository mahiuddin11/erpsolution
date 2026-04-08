<?php

namespace App\Services\Settings;

use App\Repositories\Settings\SmsSettingRepository;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class SmsSettingService
{

    /**
     * @var SmsSettingRepository
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param SmsSettingRepository $branchRepositories
     */
    public function __construct(SmsSettingRepository $systemRepositories)
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
            'api_key'              => 'required|max:100|min:2',
            'api_secret'           => 'required',
            'sender_mobile'        => ['required', 'unique:branches,phone', 'regex:/(^(01))[3-9]{1}(\d){8}$/', new PhoneNumberValidationRules($request)],
            'sales'                => 'required|numeric',
            'purchases'            => 'required|numeric',
            'payment_voucher'      => 'required|numeric',
            'receive_voucher'      => 'required|numeric',
            'status'               => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'api_key'              => 'required|max:100|min:2',
            'api_secret'           => 'required',
            'sender_mobile'        => ['required', 'unique:branches,phone', 'regex:/(^(01))[3-9]{1}(\d){8}$/', new PhoneNumberValidationRules($request)],
            'sales'                => 'required|numeric',
            'purchases'            => 'required|numeric',
            'payment_voucher'      => 'required|numeric',
            'receive_voucher'      => 'required|numeric',
            'status'               => 'nullable',
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