<?php

namespace App\Services\Project;

use App\Repositories\Project\ProjectRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class ProjectService
{

    /**
     * @var ProjectRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param ProjectRepositories $branchRepositories
     */
    public function __construct(ProjectRepositories $systemRepositories)
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

// dd('validation' , $request->all());
        return [
            'projectCode' => 'required',
            'name' => 'required',
            'manager_id' => 'required',
            'budget' => 'required',
            'ledger_id' => 'required',
            'branch_id' => 'nullable',
            // 'customer_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'address' => 'required',
            'status' => 'nullable',
            'estimate_profit'
        ];
    }

    /**
     * @param $id
     * @return array
     */
    // public function updateValidation($request, $id)
    // {

    //     return [

    //         'name' => 'required',
    //         'manager_id' => 'required',
    //         'budget' => 'required',
    //         'branch_id' => 'nullable',
    //         // 'customer_id' => 'null',
    //         'ledger_id' => 'null',
    //         'start_date' => 'required',
    //         // 'end_date' => 'required',
    //         'address' => 'required',
    //         'status' => 'nullable',
    //         'estimate_profit'
    //     ];
    // }

    public function updateValidation($request, $id)
    {
        return [
            'name' => 'required|string',
            'manager_id' => 'required|integer',
            'budget' => 'required|numeric',
            'branch_id' => 'nullable|integer',          
            'ledger_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'address' => 'required|string',
            'estimate_profit' => 'nullable|numeric',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        // dd('services',$request->all());
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
