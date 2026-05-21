<?php

namespace App\Services\Settings;

use App\Repositories\Settings\OpeningRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class OpeningService
{

    private $OpeningRepositories;

    public function __construct(OpeningRepositories $OpeningRepositories)
    {
        $this->OpeningRepositories = $OpeningRepositories;
    }


    public function getAllOpening()
    {
        return $this->OpeningRepositories->getAllOpening();
    }


    public function getList($request)
    {
        return $this->OpeningRepositories->getList($request);
    }


    public function statusUpdate($request, $id)
    {
        return $this->OpeningRepositories->statusUpdate($request, $id);
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

            'status'             => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'date'               => 'required',
            'to_account_id'      => 'required',
            'amount'             => 'required',
            'status'             => 'nullable',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Opening
     */
    public function store($request)
    {
        return $this->OpeningRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Opening
     */
    public function details($id)
    {

        return $this->OpeningRepositories->details($id);
    }


    /**
     * @param $request
     * @param $id
     */
    public function update($request, $id)
    {
        return $this->OpeningRepositories->update($request, $id);
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
