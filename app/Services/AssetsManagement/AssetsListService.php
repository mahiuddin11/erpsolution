<?php

namespace App\Services\AssetsManagement;

use App\Models\AssetsList;
use App\Repositories\AssetsManagement\AssetsListRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class AssetsListService
{

    /**
     * @var AssetsListRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param AssetsListRepositories $branchRepositories
     */
    public function __construct(AssetsListRepositories $systemRepositories)
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
        return [
            'name'                   => 'required',
            'account_id'             => 'required',
            'payment_account'             => 'required',
            'category_asset_id'      => 'required',
            '_date'                   => 'required',
            'qty'                     => 'required',
            'amount'                   => 'required'
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
            'account_id'             => 'required',
            'category_asset_id'      => 'required',
            '_date'                   => 'required',
            'payment_account'           => 'required',
            'qty'                     => 'required',
            'amount'                   => 'required'
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
