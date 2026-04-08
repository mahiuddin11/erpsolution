<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\Holiday;

class HolidayRepository
{

    protected $model;

    public function __construct(Holiday $model)
    {
        $this->model = $model;
    }

  

}
