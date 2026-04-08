<?php

namespace App\Services\Customer;

use App\Repositories\Customer\CustomerPaymentRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class CustomerPaymentService
{

    /**
     * @var $CustomerPaymentRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param $CustomerPaymentRepositories $branchRepositories
     */
    public function __construct(CustomerPaymentRepositories $systemRepositories)
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
        // dd($request->all());
        return [

            'branch_id' => 'required',
            'customer_id' => 'required',
            'account_id' => 'required',
            'check_date' => 'required',
            'check_no' => 'required',
            'bank_name' => 'required',
            'invoice_id' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'status' => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'account_branch_id' => 'required',
            'customer_branch_id' => 'required',
            'account_branch_id' => 'required',
            'check_date' => 'required',
            'check_no' => 'required',
            'bank_name' => 'required',
            'customer_id' => 'required',
            'account_id' => 'required',
            'invoice_id' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'status' => 'nullable',
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
