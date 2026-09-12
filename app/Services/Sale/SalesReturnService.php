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

    public function store($request)
    {
        return $this->SalesReturnRepositories->store($request);
    }
}
