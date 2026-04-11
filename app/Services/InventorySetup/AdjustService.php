<?php

namespace App\Services\InventorySetup;

use App\Repositories\InventorySetup\AdjustRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class AdjustService
{

    /**
     * @var AdjustRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param AdjustRepositories $branchRepositories
     */
    public function __construct(AdjustRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        dd('adjust service ',$request->all());
        return $this->systemRepositories->getList($request);
    }

    public function getreturnList($request)
    {
        // dd('adjust service ', $request->all());
        return $this->systemRepositories->getreturnList($request);
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
            'id' => 'required',
            'status' => 'required',
        ];
    }

    /**
     * @param $request
     * @return array
     */
    public function returnstoreValidation($request)
    {
        // dd($request->all());
        return [
            'branch_id' => 'required',
            'customer_id' => 'required',
            'credit' => 'required',
            'account_id' => 'required',
            'date' => 'required',
        ];
    }
    public function returnupdateValidation($request)
    {
        return [
            'branch_id' => 'required',
            'customer_id' => 'required',
            'credit' => 'required',
            'account_id' => 'required',
            'date' => 'required',
        ];
    }

    public function storeValidation($request)
    {
        // dd($request->all());

        if ($request->payment_type == "Credit") {
            return [
                'branch_id' => 'required',
                'customer_id' => 'required',
                'amount' => 'required',
                'date' => 'required',
                'payment_type' => 'required',
                'expire_date' => 'required',
                'status' => 'nullable',
            ];
        } elseif ($request->payment_type == "Deposit") {
            return [
                'account_id' => 'required',
                'branch_id' => 'required',
                'check_date' => 'required',
                // 'check_no' => 'required',
                // 'bank_name' => 'required',
                'customer_id' => 'required',
                'amount' => 'required',
                'date' => 'required',
                'payment_type' => 'required',
                'status' => 'nullable',
            ];
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        // dd($request->all());
        if ($request->payment_type == "Credit") {
            return [
                'branch_id' => 'required',
                'customer_id' => 'required',
                'amount' => 'required',
                'date' => 'required',
                'payment_type' => 'required',
                'expire_date' => 'required',
                'status' => 'nullable',
            ];
        } elseif ($request->payment_type == "Deposit") {
            return [
                'account_id' => 'required',
                'branch_id' => 'required',
                'customer_id' => 'required',
                'check_date' => 'required',
                // 'bank_name' => 'required',
                // 'check_no' => 'required',
                'amount' => 'required',
                'date' => 'required',
                'payment_type' => 'required',
                'status' => 'nullable',
            ];
        }
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        return $this->systemRepositories->store($request);
    }

    public function returnstore($request)
    {
        return $this->systemRepositories->returnstore($request);
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

    public function returnupdate($request, $id)
    {
        return $this->systemRepositories->returnupdate($request, $id);
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
