<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\Branch;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Services\Settings\AccountService;
use App\Transformers\AccountTransformer;
use Illuminate\Validation\ValidationException;

class AccountsController extends Controller
{
    /**
     * @var BranchService
     */
    private $systemService;
    /**
     * @var BranchTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param BranchService $systemService
     * @param BranchTransformer $systemTransformer
     */

    public function __construct(AccountService $accountService, AccountTransformer $accountTransformer)
    {
        $this->systemService = $accountService;
        $this->systemTransformer = $accountTransformer;
    }

    public function index(Request $request)
    {
        $title = 'Account List';
        $rootAccount = ChartOfAccount::where('parent_id', 0)->get();
        return view('backend.pages.settings.account.index', get_defined_vars());
    }

    public function dataProcessingAccount(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $title = 'Add New Account';
        $BranchlastData = Accounts::latest('id')->first();
        if ($BranchlastData) :
            $AccountData = $BranchlastData->id + 1;
        else :
            $AccountData = 1;
        endif;
        $accountCode = 'CA' . str_pad($AccountData, 5, "0", STR_PAD_LEFT);
        $branch = Branch::get()->where('status', 'Active');
        $accounts = ChartOfAccount::whereNotIn("parent_id", [0])->get();
        $getFixasset = ChartOfAccount::getaccount(2)->get();

        return view('backend.pages.settings.account.create', get_defined_vars());
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('settings.account.index');
    }
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo =   $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Edit Account';
        $accounts = ChartOfAccount::whereNotIn("parent_id", [0])->get();
        return view('backend.pages.settings.account.edit', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }

        $editInfo = $this->systemService->details($id);
        //dd($editInfo);

        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        try {
            $this->validate($request, $this->systemService->updateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('settings.account.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statusUpdate($id, $status)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo =  $this->systemService->statusUpdate($id, $status);
        if ($statusInfo) {
            return response()->json($this->systemTransformer->statusUpdate($statusInfo), 200);
        }
    }


    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function destroy($id)
    {
        $detailsInfo = Accounts::find($id);
        $check_Account = [
            1,  // ASSETS
            2,  // FIXED ASSET
            3,  // CURRENT ASSETS
            4,  // ADVANCE, DEPOSITS AND PRE-PAYMENTS
            5,  // ACCOUNTS RECEIVABLE
            6,  // CASH AND CASH EQUIVALENTS
            7,  // Cash in Hand
            8,  // Cash in Bank
            9,  // Equity & Liabilities
            10, // Equity
            11, // Share capital
            12, // Reserve and Surplus
            13, // Retained earnings
            14, // Long Term Liabilities
            15, // Current Liabilities
            16, // Accounts Payable
            17, // INCOME
            18, // Sales
            24, // Direct Income
            25, // Indirect Income
            19, // EXPENSES
            20, // Direct Expenses
            21, // Indirect Expenses
            22, // Purchase
            23, // Profit & Loss Accounting
        ];

        try {
            if (in_array($detailsInfo->unique_identifier, $check_Account)) {
                session()->flash('warning', 'Cannot delete this account!');
                return redirect()->route('settings.account.index');
            } else {
                $account = AccountTransaction::where('account_id', $detailsInfo->id)->exists();
                if ($account) {
                    session()->flash('warning', 'This account already has transactions!');
                } else {
                    try {
                        if ($detailsInfo->accountable()->exists()) { // Check if relationship exists
                            $detailsInfo->accountable()->delete(); // Delete related record(s)
                        }
                    } catch (\Exception $e) {
                        // Log the error or handle it as needed
                        // \Log::error('Error deleting accountable records: ' . $e->getMessage());
                    }
                    $detailsInfo->delete(); // Delete the account record itself
                    session()->flash('success', 'Account successfully deleted!');
                }
                return redirect()->route('settings.account.index');
            }
        } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getLine(), $th->getFile());
        }
    }
}
