<?php

namespace App\Http\Controllers\Backend\ActivityLogs;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Transformers\Transformers;
use Illuminate\Http\Request;

class ActivityLogsController extends Controller
{
    //
    protected $activitylogs;
    protected $systemTransformer;

    public function __construct(ActivityLogService $activitylogs, Transformers $transformers)
    {
        $this->activitylogs = $activitylogs;
        $this->systemTransformer = $transformers;
    }

    public function index(Request $request)
    {
        $title = 'Activitys Logs ';
        return view('backend.pages.activitylogs.index', get_defined_vars());
    }

    public function dataProcessing(Request $request)
    {


        $json_data = $this->activitylogs->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }
}
