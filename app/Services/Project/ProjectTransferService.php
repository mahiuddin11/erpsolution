<?php

namespace App\Services\Project;

use App\Models\PurchaseRequisition;
use App\Models\StockSummary;
use App\Repositories\InventorySetup\PurchaseOrderRepositories;
use App\Repositories\Project\ProjectTransferRepositories;

class ProjectTransferService
{

    /**
     * @var ProjectTransferRepositories
     */
    private $systemRepositories;

    /**
     * AdminCourseService constructor.
     * @param ProjectTransferRepositories $branchRepositories
     */

    public function __construct(ProjectTransferRepositories $systemRepositories)
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
    // public function storeValidation($request)
    // {
    //     // dd($request->all());
    //     return [
    //         'orderCode' => 'required',
    //         'date' => 'required',
    //         'purchase_requisition' => 'required',
    //         'branch_id' => 'required',
    //         'category_nm' => 'required',
    //         'product_nm' => 'required',
    //         'qty' => 'required',
    //         // 'unitprice' => 'required',
    //         // 'total' => 'required',
    //     ];
    // }

    public function storeValidation($request)
    {
        return [
            'transfer_type'         => 'required|in:branch_to_project,project_to_project,project_to_branch',
            'date'                  => 'required|date',
            'category_nm'           => 'required|array|min:1',
            'product_nm'            => 'required|array|min:1',
            'qty'                   => 'required|array|min:1',
            'qty.*'                 => 'required|numeric|min:1',

            // branch_to_project ONLY
            'from_branch_id'        => 'required_if:transfer_type,branch_to_project',
            'to_project_id_a'       => 'required_if:transfer_type,branch_to_project',
            'purchase_requisition'  => 'required_if:transfer_type,branch_to_project',

            // project_to_project / project_to_branch
            'from_project_id'       => 'required_if:transfer_type,project_to_project,project_to_branch',

            // project_to_project ONLY (+ same-project block)
            'to_project_id_b'       => 'required_if:transfer_type,project_to_project|different:from_project_id',

            // project_to_branch ONLY
            'to_branch_id'          => 'required_if:transfer_type,project_to_branch',
        ];
    }


    public function storeBusinessRules($request)
    {
        $type = $request->transfer_type;

        // ---- requisition must belong to the destination project ----
        if ($type === 'branch_to_project') {
            $requisition = PurchaseRequisition::find($request->purchase_requisition);
            if (!$requisition || (int) $requisition->project_id !== (int) $request->to_project_id_a) {
                return 'The selected requisition is not for this project. Select the correct Requisition.';
            }
        }

        // ---- stock availability check (purchasetype-aware) ----
        $product  = $request->product_nm;
        $qty      = $request->qty;
        $purchase = $request->purchasetype;

        for ($i = 0; $i < count($product); $i++) {
            $purchaseType = $purchase[$i] ?? 'local';

            if ($type === 'branch_to_project') {
                $sourceId   = $request->from_branch_id;
                $sourceType = 'Branch';
            } else {
                $sourceId   = $request->from_project_id;
                $sourceType = 'Project';
            }

            $available = StockSummary::where([
                'branch_id'    => $sourceId,
                'product_id'   => $product[$i],
                'type'         => $sourceType,
                'purchasetype' => $purchaseType,
            ])->value('quantity') ?? 0;

            if ($qty[$i] > $available) {
                return 'Adequate ' . $purchaseType . ' Out of stock. Available: ' . $available . ' (Product row ' . ($i + 1) . ')';
            }
        }

        return null;
    }

    /**
     * @param $id
     * @return array
     */
    public function updateValidation($request, $id)
    {
        // dd($request->all());
        return [
            'date' => 'required',
            'purchase_requisition' => 'required',
            'branch_id' => 'required',
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


    /**
     * @param $request
     * @param $id
     */
    public function destroy($id)
    {
        return $this->systemRepositories->destroy($id);
    }
}
