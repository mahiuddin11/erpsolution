<?php

namespace App\Services\Project;

use App\Repositories\Project\InvoiceRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class InvoiceService
{

    /**
     * @var InvoiceRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param InvoiceRepositories $branchRepositories
     */
    public function __construct(InvoiceRepositories $systemRepositories)
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
    public function completeValidation($request)
    {
        return [
            'close_date' => 'required',
            'projectid' => 'required',
        ];
    }

    /**
     * @param $request
     * @return array
     */
    public function storeValidation($request)
    {

        return [
            'invoiceCode' => 'required',
            'date' => 'required',
            'branch_id' => 'required',
            'project_id' => 'required',
            'customer_id' => 'required',
            'account_id' => 'required',
            'details' => 'required',
            'profit' => 'required',
            'total_value' => 'required',
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
            'date' => 'required',
            'branch_id' => 'required',
            'project_id' => 'required',
            'customer_id' => 'required',
            'account_id' => 'required',
            'details' => 'required',
            'profit' => 'required',
            'total_value' => 'required',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        // dd($request->all());
        return $this->systemRepositories->store($request);
    }

    public function completestore($request)
    {
        // dd($request->all());
        return $this->systemRepositories->completestore($request);
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
