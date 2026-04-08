<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Services\Settings\HrmSetupService;
use App\Transformers\HrmSetupTransformer;
use Illuminate\Http\Request;

class HrmSetupController extends Controller
{
    /**
     * @var HrmSetupService
     */
    private $systemService;
    /**
     * @var HrmSetupTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param BranchService $systemService
     * @param BranchTransformer $systemTransformer
     */
    public function __construct(HrmSetupService $hrmSetupService, HrmSetupTransformer $hrmSetupTransformer)
    {
        $this->systemService = $hrmSetupService;
        $this->systemTransformer = $hrmSetupTransformer;
    }

    /**
     * Show Hrm Setup List
     */
    public function index()
    {
        $title = "Hrm Setup List";
        return view('backend.pages.settings.hrm_setup.index', get_defined_vars());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Hrm Setup';
        return view('backend.pages.settings.hrm_setup.create', get_defined_vars());
    }
}
