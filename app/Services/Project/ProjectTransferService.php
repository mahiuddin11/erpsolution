<?php

namespace App\Services\Project;

use App\Models\PrDetails;
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


    public function getprProduct($request)
    {
        return $this->systemRepositories->getprListByTransfer($request);
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
        'qty.*'                 => 'required|numeric|min:0.01',   // blade step=0.01, tai min:1 na
 
        // branch_to_project ONLY
        'from_branch_id'        => 'nullable|required_if:transfer_type,branch_to_project',
        'from_warehouse_id'     => 'nullable|required_if:transfer_type,branch_to_project',
        'to_project_id_a'       => 'nullable|required_if:transfer_type,branch_to_project',
        'purchase_requisition'  => 'nullable|required_if:transfer_type,branch_to_project',
 
        // project_to_project / project_to_branch
        'from_project_id'       => 'nullable|required_if:transfer_type,project_to_project,project_to_branch',
 
        // project_to_project ONLY
        'to_project_id_b'       => 'nullable|required_if:transfer_type,project_to_project|different:from_project_id',
 
        // project_to_branch ONLY
        'to_branch_id'          => 'nullable|required_if:transfer_type,project_to_branch',
        'to_warehouse_id'       => 'nullable|required_if:transfer_type,project_to_branch',
    ];
}
    public function hasRemainingItems($prId)
    {
        return PrDetails::where('pr_id', $prId)
            ->where(function ($q) {
                $q->where('remaining_qty', '>', 0)
                    ->orWhereNull('remaining_qty');
            })
            ->exists();
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
 
    // ---- stock availability (product + purchasetype pool) ----
    $products = $request->product_nm ?? [];
    $qtys     = $request->qty ?? [];
    $ptypes   = $request->purchasetype ?? [];
 
    $pools = [];
    foreach ($products as $i => $productId) {
        $ptype = $ptypes[$i] ?? 'local';
        $key   = $productId . '|' . $ptype;
 
        if (!isset($pools[$key])) {
            $pools[$key] = ['product_id' => $productId, 'purchasetype' => $ptype, 'qty' => 0, 'row' => $i + 1];
        }
        $pools[$key]['qty'] += (float) ($qtys[$i] ?? 0);
    }
 
    foreach ($pools as $pool) {
        $query = StockSummary::where('product_id', $pool['product_id'])
            ->where('purchasetype', $pool['purchasetype']);
 
        if ($type === 'branch_to_project') {
            $query->where('type', 'Branch')
                ->where('branch_id', $request->from_branch_id)
                ->where('warehouse_id', $request->from_warehouse_id);
        } else { // project_to_project, project_to_branch
            $query->where('type', 'Project')
                ->where('project_id', $request->from_project_id)
                ->where('branch_id', 0);
        }
 
        $available = (float) $query->sum('quantity');
 
        if ($pool['qty'] > $available) {
            return 'Insufficient ' . $pool['purchasetype'] . ' stock. Available: ' . $available
                . ', requested: ' . $pool['qty'] . ' (Product row ' . $pool['row'] . ')';
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
    return [
        'date'                  => 'required|date',
        'category_nm'           => 'required|array|min:1',
        'product_nm'            => 'required|array|min:1',
        'qty'                   => 'required|array|min:1',
        'qty.*'                 => 'required|numeric|min:0.01',
 
        // branch_to_project ONLY
        'from_branch_id'        => 'nullable|required_if:transfer_type,branch_to_project',
        'to_project_id_a'       => 'nullable|required_if:transfer_type,branch_to_project',
        'purchase_requisition'  => 'nullable|required_if:transfer_type,branch_to_project',
 
        // project_to_project / project_to_branch
        'from_project_id'       => 'nullable|required_if:transfer_type,project_to_project,project_to_branch',
 
        // project_to_project ONLY
        'to_project_id_b'       => 'nullable|required_if:transfer_type,project_to_project|different:from_project_id',
 
        // project_to_branch ONLY
        'to_branch_id'          => 'nullable|required_if:transfer_type,project_to_branch',
        'to_warehouse_id'       => 'nullable|required_if:transfer_type,project_to_branch',
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
