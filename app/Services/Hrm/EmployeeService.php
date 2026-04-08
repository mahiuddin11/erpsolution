<?php

namespace App\Services\Hrm;

use App\Repositories\Hrm\EmployeeRepositories;

class EmployeeService
{

    /**
     * @var EmployeeRepositories
     */
    private $employeeRepositories;

    /**
     * 
     * @param EmployeeRepositories $employeeRepositories 
     */
    public function __construct(EmployeeRepositories $employeeRepositories)
    {
        $this->employeeRepositories = $employeeRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        return $this->employeeRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->employeeRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->employeeRepositories->statusUpdate($request, $id);
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
        return [
            'name' => ['required'],
            'dob' => ['nullable'],
            'gender' => ['required'],
            'personal_phone' => ['required'],
            'branch_id' => ['required'],
            'id_card' => ['required'],
            'office_phone' => ['nullable', 'numeric'],
            'marital_status' => ['nullable'],
            'nid' => ['nullable'],
            'email' => ['nullable'],
            'reference' => ['nullable'],
            'last_in_time' => ['required'],
            'department' => ['nullable'],
            'position_id' => ['nullable'],
            'experience' => ['nullable'],
            'present_address' => ['nullable'],
            'permanent_address' => ['nullable'],
            'achieved_degree' => ['nullable'],
            'institution' => ['nullable'],
            'passing_year' => ['nullable'],
            'salary' => ['nullable'],
            'join_date' => ['nullable'],
            'status' => ['nullable'],
            'image' => ['nullable'],
            'auto_checkout' => ['nullable'],
            'emp_signature' => ['nullable'],
            'guardian_numer' => ['nullable'],
            'guardian_nid' => ['nullable'],
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'name' => ['required'],
            'dob' => ['nullable'],
            'gender' => ['required'],
            'personal_phone' => ['required'],
            'branch_id' => ['required'],
            'id_card' => ['required'],
            'office_phone' => ['nullable', 'numeric'],
            'marital_status' => ['nullable'],
            'nid' => ['nullable'],
            'email' => ['nullable'],
            'reference' => ['nullable'],
            'last_in_time' => ['required'],
            'department' => ['nullable'],
            'position_id' => ['nullable'],
            'experience' => ['nullable'],
            'present_address' => ['nullable'],
            'permanent_address' => ['nullable'],
            'achieved_degree' => ['nullable'],
            'institution' => ['nullable'],
            'passing_year' => ['nullable'],
            'salary' => ['nullable'],
            'join_date' => ['nullable'],
            'status' => ['nullable'],
            'image' => ['nullable'],
            'auto_checkout' => ['nullable'],
            'emp_signature' => ['nullable'],
            'guardian_numer' => ['nullable'],
            'guardian_nid' => ['nullable'],
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        // dd('services', $request->all());
        return $this->employeeRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {
        return $this->employeeRepositories->details($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function update($request, $id)
    {
        return $this->employeeRepositories->update($request, $id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->employeeRepositories->destroy($id);
    }
}
