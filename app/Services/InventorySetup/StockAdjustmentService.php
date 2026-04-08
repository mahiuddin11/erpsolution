<?php

namespace App\Services\InventorySetup;

use App\Repositories\InventorySetup\StockAdjustmentRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class StockAdjustmentService
{

    /**
     * @var StockAdjustmentRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param StockAdjustmentRepositories $branchRepositories
     */

    public function __construct(StockAdjustmentRepositories $systemRepositories)
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
        //  dd($request->all());
        return [
            'date' => 'required',
            'invoice_no' => 'required',
            'adjustment_type' => 'required',
            'branch_id' => 'required',
            'narration' => 'required',
            'status' => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {

        // dd($request->all());
        return [
            'date' => 'required',
            'branch_id' => 'required',
            'adjustment_type' => 'required',
            'narration' => 'required',
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

    public function storeapproval($request, $id)
    {
        return $this->systemRepositories->storeapproval($request, $id);
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
