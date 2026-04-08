<?php

namespace App\Services\Production;

use App\Repositories\Production\ProductionRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class ProductionService
{

    /**
     * @var ProductionRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param ProductionRepositories $branchRepositories
     */
    public function __construct(ProductionRepositories $systemRepositories)
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

    /**
     * @param $request
     * @return array
     */
    public function storeValidation($request)
    {
        // dd($request->all());


        return [
            'productionCode' => 'required',
            'date' => 'required',
            'branch_id' => 'required',
            'product_id' => 'required',
            'to_product_id' => 'required',
            'purchases_price' => 'required',
            'sale_price' => 'required',
            'conversion_id' => 'required',
            'deduct_quantiry' => 'required',
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

            'name' => 'required',
            'manager_id' => 'required',
            'budget' => 'required',
            // 'received_amount' => 'required',
            'start_date' => 'required',
            // 'end_date' => 'required',
            'address' => 'required',
            'status' => 'nullable',
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
