<?php

namespace App\Services\InventorySetup;

use App\Repositories\InventorySetup\StockTransferRepositories;
use App\Rules\PhoneNumberValidationRules;
use Illuminate\Support\Facades\Validator;

class StockTransferService
{

    /**
     * @var StockTransferRepositories
     */
    private $systemRepositories;
    /**
     * AdminCourseService constructor.
     * @param StockTransferRepositories $branchRepositories
     */
    public function __construct(StockTransferRepositories $systemRepositories)
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
            'invoice_no'                   => 'required',
            'date'                   => 'required',
            'catName'                   => 'required',
            'proName'                   => 'required',
            'from_branch_id'                   => 'required',
            'to_branch_id'                   => 'required',
            'qty'                   => 'required',
            'unitprice'                   => 'required',
            'total'                   => 'required',
            'qty'                   => 'required',
            'status' => 'nullable',
        ];
    }
    public function storeValidation_approval($request)
    {

// dd($request->all());
        return [
            
            'date'                   => 'required',
            'catName'                   => 'required',
            'proName'                   => 'required',
            'from_branch_id'                   => 'required',
            'to_branch_id'                   => 'required',
            'qty'                   => 'required',
            'unitprice'                   => 'required',
            'total'                   => 'required',
            'qty'                   => 'required',
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
            'date'                   => 'required',
            'catName'                   => 'required',
            'proName'                   => 'required',
            'from_branch_id'                   => 'required',
            'to_branch_id'                   => 'required',
            'qty'                   => 'required',
            'unitprice'                   => 'required',
            'total'                   => 'required',
            'qty'                   => 'required',
            'status' => 'nullable',
        ];
    }
    public function transferApprove($request)
    {
        return [
            'qty'                   => 'required',
            'unitprice'                   => 'required',
            'total'                   => 'required',
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
    
    public function stotransferStore($request)
    {
        return $this->systemRepositories->stotransferStore($request);
    }
    public function approval($request)
    {
        return $this->systemRepositories->approval($request);
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