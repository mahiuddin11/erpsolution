<?php

namespace App\Services\Settings;

use App\Repositories\Settings\CommissionRulesRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class CommissionRulesService
{


    /**
     * @var CommissionRulesRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param CommissionRulesRepositories $branchRepositories
     */
    public function __construct(CommissionRulesRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllBranch()
    {
        return $this->systemRepositories->getAllBranch();
    }


  /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        return $this->systemRepositories->getList($request);
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

        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'commission_type' => 'required|in:fixed,tiered,product_based',
        ];
    
        if ($request->commission_type === 'fixed') {
            $rules['fixed_percentage'] = 'required|numeric|min:0';
        }
    
        if ($request->commission_type === 'tiered') {
            $rules['min_amount'] = 'required|numeric|min:0';
            $rules['max_amount'] = 'required|numeric|min:0';
            $rules['percentage'] = 'required|numeric|min:0|max:100';
        }
    
        if ($request->commission_type === 'product_based') {
            $rules['percentage'] = 'required|numeric|min:0|max:100';
        }
    
        return  $rules;
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'commission_type' => 'required|in:fixed,tiered,product_based',
            'fixed_percentage' => 'nullable|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Branch
     */
    public function store($request)
    {
        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Branch
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