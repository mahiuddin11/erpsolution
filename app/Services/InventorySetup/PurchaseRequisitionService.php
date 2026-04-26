<?php

namespace App\Services\InventorySetup;

use App\Repositories\InventorySetup\PurchaseRequisitionRepositories;

class PurchaseRequisitionService
{

    
    private $purchaseRequisitionRepositories;

    /**
     * AdminCourseService constructor.
     * @param AdjustRepositories $branchRepositories
     */
    public function __construct(PurchaseRequisitionRepositories $purchaseRequisitionRepositories)
    {
        $this->purchaseRequisitionRepositories = $purchaseRequisitionRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        return $this->purchaseRequisitionRepositories->getList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->purchaseRequisitionRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->purchaseRequisitionRepositories->statusUpdate($request, $id);
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
            'requisitionCode' => 'required',
            'date' => 'required',
            'project_id' => 'required',
            // 'unitprice' => 'required',
            // 'total' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            'qty' => 'required',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        return [
            'date' => 'required',
            'project_id' => 'required',
            // 'unitprice' => 'required',
            // 'total' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            'qty' => 'required',
        ];
    }

    public function approveValidation($request, $id)
    {
        return [
            'date' => 'required',
            'branch_id' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            'qty' => 'required',
        ];
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function store($request)
    {
        return $this->purchaseRequisitionRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->purchaseRequisitionRepositories->details($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function update($request, $id)
    {
        return $this->purchaseRequisitionRepositories->update($request, $id);
    }

    public function approvepr($request, $id)
    {
        return $this->purchaseRequisitionRepositories->approvepr($request, $id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->purchaseRequisitionRepositories->destroy($id);
    }
}
