<?php

namespace App\Services\InventorySetup;

use App\Repositories\InventorySetup\PurchaseOrderRepositories;

class PurchaseOrderService
{


    private $purchaseOrderRepositories;

    public function __construct(PurchaseOrderRepositories $purchaseOrderRepositories)
    {
        $this->purchaseOrderRepositories = $purchaseOrderRepositories;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        return $this->purchaseOrderRepositories->getList($request);
    }

    public function getprList($request)
    {
        return $this->purchaseOrderRepositories->getprList($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        return $this->purchaseOrderRepositories->getAllList();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function statusUpdate($request, $id)
    {
        return $this->purchaseOrderRepositories->statusUpdate($request, $id);
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
        // dd('purchase order service',$request->all());
        return [
            'orderCode' => 'required',
            'date' => 'required',
            'purchase_requisition' => 'required',
            // 'subblier_id' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            'qty' => 'required',
            // 'unitprice' => 'required',
            // 'total' => 'required',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        // dd('purchase order service',$request->all());

        return [
            'date' => 'required',
            'purchase_requisition' => 'required',
            // 'subblier_id' => 'required',
            'account_1' => 'required',
            'category_nm' => 'required',
            'product_nm' => 'required',
            'qty' => 'required',
            // 'unitprice' => 'required',
            // 'total' => 'required',
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
        return $this->purchaseOrderRepositories->store($request);
    }

    /**
     * @param $request
     * @return \App\Models\Currency
     */
    public function details($id)
    {

        return $this->purchaseOrderRepositories->details($id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function update($request, $id)
    {
        return $this->purchaseOrderRepositories->update($request, $id);
    }

    public function approvepr($request, $id)
    {
        return $this->purchaseOrderRepositories->approvepr($request, $id);
    }

    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->purchaseOrderRepositories->destroy($id);
    }
}
