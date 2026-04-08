<?php

namespace App\Services\Sale;

use App\Repositories\Sale\SaleRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class SalesService
{

    /**
     * @var SaleRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param SaleRepositories $branchRepositories
     */
    public function __construct(SaleRepositories $systemRepositories)
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
        if ($request->payment_type == "Cash") {
            return [
                'date' => 'required',
                'invoice_no' => 'required',
                'branch_id' => 'required',
                'sub_warehouse_id' => 'required',
                'ledger_id' => 'required',
                'qty' => 'required',
                'proName' => 'required',
                'catName' => 'required',
                'payment_type' => 'required',
                'account_id' => 'required',
                'status' => 'nullable',
            ];
        } else {
            return [
                'date' => 'required',
                'invoice_no' => 'required',
                'branch_id' => 'required',
                'sub_warehouse_id' => 'required',
                'ledger_id' => 'required',
                'qty' => 'required',
                'proName' => 'required',
                'catName' => 'required',
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
        if ($request->payment_type == "Cash") {
            return [
                'date' => 'required',
                'invoice_no' => 'required',
                'branch_id' => 'required',
                'sub_warehouse_id' => 'required',
                'customer_id' => 'required',
                'qty' => 'required',
                'proName' => 'required',
                'catName' => 'required',
                'payment_type' => 'required',
                'account_id' => 'required',
                'status' => 'nullable',
            ];
        } else {
            return [
                'date' => 'required',
                'invoice_no' => 'required',
                'branch_id' => 'required',
                'sub_warehouse_id' => 'required',
                'ledger_id' => 'required',
                'qty' => 'required',
                'proName' => 'required',
                'catName' => 'required',
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
        //dd($request->all());
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
