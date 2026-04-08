<?php

namespace App\Services\Hrm;

use App\Repositories\Hrm\AttendanceRepositories;

class AttendanceService
{

    /**
     * @var AttendanceRepositories
     */

    private $attendanceRepositories;

    /**
     * AdminCourseService constructor.
     * @param AttendanceRepositories $attendanceRepositories
     */
    public function __construct(AttendanceRepositories $attendanceRepositories)
    {
        $this->attendanceRepositories = $attendanceRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        // dd($request->all());
        return $this->attendanceRepositories->getList($request);
       
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->attendanceRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->attendanceRepositories->statusUpdate($request, $id);
    }

    public function statusValidation($request)
    {
        return [
            'emplyee_id' => 'required',
            'date' => 'required',
            'sign_in' => 'required',
            'sign_out' => 'required',
        ];
    }

    /**
     * @param $request
     * @return array
     */
    public function signinValidation($request)
    {
        return [
            'emplyee_id' => 'required',
            'sign_in' => 'required',
            // 'latitude'  => 'required|numeric',
            // 'longitude' => 'required|numeric',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function signoutValidation($request)
    {
        return [
            'emplyee_id' => 'required',
            'sign_out' => 'required',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function attendanceEditValidation($request)
    {
        return [
            'date' => 'required',
            'sign_in' => 'required',
            'sign_out' => 'required',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function signin($request)
    {
        return $this->attendanceRepositories->signin($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->attendanceRepositories->details($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function signout($request)
    {
        return $this->attendanceRepositories->signout($request);
    }

    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->attendanceRepositories->destroy($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function edit($id)
    {
        return $this->attendanceRepositories->edit($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function update($request , $id)
    {
        return $this->attendanceRepositories->update($request, $id);
    }
}
