<?php

namespace App\Services\Usermanage;

use App\Repositories\Usermanage\UserRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class UserService
{

    /**
     * @var UserRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param UserRepositories $branchRepositories
     */
    public function __construct(UserRepositories $systemRepositories)
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
        if ($request->type == "Project") {
            return [
                'name'               => 'required|max:100|min:2',
                'phone'              => 'required|unique:users,phone',
                'type'               => 'required',
                'email'              => 'required|email|unique:users,email',
                'password'           => 'required|min:6|',
            ];
        } else {
            return [
                'name'               => 'required|max:100|min:2',
                'branch_id'          => 'nullable',
                'phone'              => 'required|unique:users,phone',
                'type'               => 'required',
                'email'              => 'required|email|unique:users,email',
                'password'           => 'required|min:6|',
            ];
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        if ($request->type == "Project") {
            return [
                'name'               => 'required|max:100|min:2',
                'phone'               => 'required',
                'type'               => 'required',
                'email'              => 'unique:users,email,' . $id,
                'password'           => 'required|min:6|',
            ];
        } else {
            return [
                'name'               => 'required|max:100|min:2',
                'branch_id'               => 'nullable',
                'phone'               => 'required',
                'type'               => 'required',
                'email'              => 'unique:users,email,' . $id,
                'password'           => 'confirmed',
            ];
        }
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
