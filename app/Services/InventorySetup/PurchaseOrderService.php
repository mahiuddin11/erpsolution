<?php

namespace App\Services\InventorySetup;

use App\Repositories\InventorySetup\PurchaseOrderRepositories;

class PurchaseOrderService
{

    /**
     * @var AdjustRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param AdjustRepositories $branchRepositories
     */

    public function __construct(PurchaseOrderRepositories $systemRepositories)
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

    public function getprList($request)
    {
        return $this->systemRepositories->getprList($request);
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
        return $this->systemRepositories->store($request);
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

    public function approvepr($request, $id)
    {
        return $this->systemRepositories->approvepr($request, $id);
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
