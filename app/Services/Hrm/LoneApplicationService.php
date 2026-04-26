<?php

namespace App\Services\Hrm;

use App\Models\Lone;
use App\Repositories\Hrm\LoneApplicationRepositories;

class LoneApplicationService
{

    
    private $LoneApplicationRepositories;

    public function __construct(LoneApplicationRepositories $LoneApplicationRepositories)
    {
        $this->LoneApplicationRepositories = $LoneApplicationRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        // dd('lonaneApicaitonservice',$request->all());
        return $this->LoneApplicationRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->LoneApplicationRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->LoneApplicationRepositories->statusUpdate($request, $id);
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


    /**
     * @param $id
     * @return array
     */
    public function storeValidation($request)
    {

        return [
            'employee_id' => 'required',
            'branch_id' => 'required',
            'amount' => 'required',
            'lone_adjustment' => 'required',
            'file' => 'nullable|mimes:pdf,doc,docx,txt,jpg,png,jpeg|max:2048',
        ];
    }



    public function updateValidation($request, $id)
    {
        return [

            'employee_id' => 'required',
            'branch_id' => 'required',
            'amount' => 'required',
            'lone_adjustment' => 'required',
            'file' => 'nullable|mimes:pdf,doc,docx,txt,jpg,png,jpeg|max:2048',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        return $this->LoneApplicationRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function update($request, $id)
    {
        return $this->LoneApplicationRepositories->update($request, $id);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->LoneApplicationRepositories->details($id);
    }



    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->LoneApplicationRepositories->destroy($id);
    }
}
