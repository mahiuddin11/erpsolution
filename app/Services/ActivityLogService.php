<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use Illuminate\Http\Request;

class ActivityLogService
{
    protected $acitvitylogsRepo;

    public function __construct(ActivityLogRepository $acitvitylogsRepo)
    {
        $this->acitvitylogsRepo = $acitvitylogsRepo;
    }

    public function getList($request)
    {

        return $this->acitvitylogsRepo->getList($request);
    }
}
