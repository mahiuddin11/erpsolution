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
}
