<?php

namespace App\Services\Settings;

use App\Repositories\Settings\ContraVoucherRepositories;

class ContraVoucherService
{


    /**
     * @var ContraVoucherRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param ContraVoucherRepositories $DabitVoucherRepositories
     */

    public function __construct(ContraVoucherRepositories $systemRepositories)
    {
        $this->systemRepositories = $systemRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOpening()
    {
        return $this->systemRepositories->getAllOpening();
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
        // dd($request->all());
        return [
            "invoice_no"            => "required",
            'date'               => 'required',
            'account_id'               => 'required',
            'amount'               => 'required',
            'note'             => 'nullable',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'date'               => 'required',
            'note'             => 'nullable',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Opening
     */
    public function store($request)
    {
        return $this->systemRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Opening
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
