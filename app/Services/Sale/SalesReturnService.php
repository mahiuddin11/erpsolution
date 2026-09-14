<?php

namespace App\Services\Sale;

use App\Repositories\Sale\SaleReturnRepositories;

class SalesReturnService
{
    private $SalesReturnRepositories;

    public function __construct(SaleReturnRepositories $SalesReturnRepositories)
    {
        $this->SalesReturnRepositories = $SalesReturnRepositories;
    }

    public function getList($request)
    {
        // dd('Sales Service', $request->all());
        return $this->SalesReturnRepositories->getList($request);
    }

    public function storeValidation($request)
    {
        return [
            'return_no'              => 'required|string',
            'original_sale_id'       => 'required|integer|exists:sales,id',
            'return_date'            => 'required|date',
            'sales_person_id'        => 'nullable|integer',
            'branch_id'              => 'required|integer',
            'warehouse_id'           => 'nullable|integer',
            'ledger_id'              => 'required|integer',
            'remarks'                => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.sale_detail_id' => 'required|integer',
            'items.*.returned_qty'   => 'required|numeric|min:0',
            'items.*.condition'      => 'required|in:good,damaged',
            'items.*.reason'         => 'nullable|string',
        ];
    }

    public function store($request)
    {
        return $this->SalesReturnRepositories->store($request);
    }

    public function approve($id, $userId)
    {
        return $this->SalesReturnRepositories->approve($id, $userId);
    }
}
