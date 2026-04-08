<?php

namespace App\Services\Hrm;
use App\Repositories\Hrm\HolidayRepository;

class HolidayService{

    protected $holidayRepository;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

}