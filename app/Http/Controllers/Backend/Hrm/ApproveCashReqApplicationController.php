<?php

namespace App\Http\Controllers\Backend\Hrm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\AccountTransaction;
use App\Models\CashReq;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use App\Models\Employee;
use App\Models\Lone;
use App\Models\Transection;
use App\Services\Hrm\ApproveCashApplicationService;
use App\Transformers\Transformers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApproveCashReqApplicationController extends Controller
{

    private $ApproveCashApplicationService;

    private $systemTransformer;

    public function __construct(ApproveCashApplicationService $ApproveLoneApplicationService, Transformers $transformers)
    {
        $this->ApproveCashApplicationService = $ApproveLoneApplicationService;

        $this->systemTransformer = $transformers;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Cash Requisition List';
        return view('backend.pages.hrm.cash_req_approve.index', get_defined_vars());
    }

    public function makepayment(Request $request, $id){

    return 'hello';
    }

    public function dataProcessing(Request $request)
    {

        $json_data =  $this->ApproveCashApplicationService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Cash Requisition ';
        $employees = Employee::get();
        return view('backend.pages.hrm.leave_application.create', get_defined_vars());
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request,  $this->ApproveCashApplicationService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->ApproveCashApplicationService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('hrm.cash-req.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */



    // public function approve(Request $request, $id)
    // {
    //     DB::beginTransaction();

    //     $cash_req = CashReq::find($id);

    //     try {

    //         $cash_req->update([
    //             'approval_amount'   => $request->amount ?? $cash_req->amount,
    //             'account_id'        => $request->account_id,
    //             'recive_account_id' => $request->recive_account_id,
    //             'check_number'      => $request->check_number,
    //             'status'            => 'approved',
    //             'approve_by'        => auth()->id(),
    //         ]);

    //         $cash_req->refresh(); //  important

    //         $amount = (float) ($request->amount ?? $cash_req->amount);

    //         $invoice = 'CASH-' . str_pad($cash_req->id, 6, '0', STR_PAD_LEFT);

    //         // FROM ACCOUNT (CREDIT)
    //         AccountTransaction::create([
    //             'invoice'    => $invoice,
    //             'table_id'   => $cash_req->id,
    //             'account_id' => $request->account_id,
    //             'type'       => 'journal_voucher',
    //             'debit'      => 0,
    //             'credit'     => $amount,
    //             'remark'     => 'Advance to ' . ($cash_req->employee->name ?? ''),
    //             'created_by' => auth()->id(),
    //             'created_at' => now(),
    //         ]);

    //         // TO ACCOUNT (DEBIT)
    //         AccountTransaction::create([
    //             'invoice'    => $invoice,
    //             'table_id'   => $cash_req->id,
    //             'account_id' => $request->recive_account_id,
    //             'type'       => 'journal_voucher',
    //             'debit'      => $amount,
    //             'credit'     => 0,
    //             'remark'     => 'Advance Cash Received by ' . ($cash_req->employee->name ?? ''),
    //             'created_by' => auth()->id(),
    //             'created_at' => now(),
    //         ]);

    //         DB::commit();

    //         return back()->with('success', 'Approved Successfully!');
    //     } catch (\Throwable $e) {

    //         DB::rollBack();
    //         Log::error($e->getMessage());

    //         return back()->with('error', 'Something went wrong!');
    //     }
    // }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $cash_req = CashReq::findOrFail($id);

            if ($cash_req->status == 'approved') {
                return back()->with('error', 'Already Approved!');
            }

            $amount = (float) ($request->amount ?? $cash_req->amount);

            // =========================
            // UPDATE CASH REQ
            // =========================
            $cash_req->update([
                'approval_amount'   => $amount,
                'account_id'        => $request->account_id,
                'recive_account_id' => $request->recive_account_id,
                'check_number'      => $request->check_number,
                'status'            => 'approved',
                'approve_by'        => auth()->id(),
            ]);

            // =========================
            // VOUCHER NO GENERATE
            // =========================
            $last = DabitVoucher::latest('id')->first();
            $voucherNo = 'DV' . str_pad(($last->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);

            // =========================
            // CREATE VOUCHER (SAVE METHOD)
            // =========================
            $voucher = new DabitVoucher();
            $voucher->voucher_no  = $voucherNo;
            $voucher->employee_id = $cash_req->employee_id;
            $voucher->date        = now();
            $voucher->branch_id   = auth()->user()->branch_id ?? 0;
            $voucher->note        = 'Cash Req #' . $cash_req->id;
            $voucher->approve     = 1;
            $voucher->approved_by = auth()->id();
            $voucher->created_by  = auth()->id();
            $voucher->save();

           
            // =========================
            // VOUCHER DETAILS (FROM)
            // =========================
            $detail1 = new DabitVoucherDetails();
            $detail1->dabit_voucher_id = $voucher->id;
            $detail1->account_id       = $request->account_id;
            $detail1->credit           = $amount;
            $detail1->debit            = 0;
            $detail1->amount           = $amount;
            $detail1->check_number     = $request->check_number ?? '';
            $detail1->save();

            // =========================
            // VOUCHER DETAILS (TO)
            // =========================
            $detail2 = new DabitVoucherDetails();
            $detail2->dabit_voucher_id = $voucher->id;
            $detail2->account_id       = $request->recive_account_id;
            $detail2->debit            = $amount;
            $detail2->credit           = 0;
            $detail2->amount           = $amount;
            $detail2->check_number     = $request->check_number ?? '';
            $detail2->save();

            // =========================
            // TRANSACTION CREATE
            // =========================
            $invoice = 'CASH-' . str_pad($cash_req->id, 6, '0', STR_PAD_LEFT);

            // CREDIT
            $t1 = new AccountTransaction();
            $t1->invoice     = $invoice;
            $t1->table_id    = $voucher->id;
            $t1->account_id  = $request->account_id;
            $t1->type        = 'debit_voucher';
            $t1->debit       = 0;
            $t1->credit      = $amount;
            $t1->remark      = 'Advance to ' . ($cash_req->employee->name ?? '');
            $t1->employee_id = $cash_req->employee_id;
            $t1->created_by  = auth()->id();
            $t1->created_at  = now();
            $t1->save();

            // DEBIT
            $t2 = new AccountTransaction();
            $t2->invoice     = $invoice;
            $t2->table_id    = $voucher->id;
            $t2->account_id  = $request->recive_account_id;
            $t2->type        = 'debit_voucher';
            $t2->debit       = $amount;
            $t2->credit      = 0;
            $t2->remark      = 'Advance received by ' . ($cash_req->employee->name ?? '');
            $t2->employee_id = $cash_req->employee_id;
            $t2->created_by  = auth()->id();
            $t2->created_at  = now();
            $t2->save();

            DB::commit();

            return back()->with('success', 'Approved, Voucher & Transaction Completed!');
        } catch (\Throwable $e) {

            DB::rollBack();

            // debugging purpose only (optional)
            dd($e->getMessage(), $e->getFile(), $e->getLine());

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function cancel(CashReq $lone)
    {

        $lone->status = 'cancel';
        $lone->save();
        session()->flash('success', ' Cash Requisition successfully Cancelled!!');
        return back();
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
        $editInfo =  $this->ApproveCashApplicationService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }

        try {
            // $this->validate($request,  $this->ApproveCashApplicationService->updateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->ApproveCashApplicationService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('hrm.cash-req.index');
    }

    // Leave Application Deatails
    public function show(CashReq $lone)
    {
        $title = 'Approve Cash Requisition Details';
        $accounts = Accounts::where(function ($query) {
            $query->where('parent_id', 6)
                ->orWhereIn('parent_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('chart_of_accounts')
                        ->where('parent_id', 6);
                });
        })->get();

        $recived_accounts = Accounts::where(function ($query) {
            $query->where('parent_id', 4)
                ->orWhereIn('parent_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('chart_of_accounts')
                        ->where('parent_id', 4);
                });
        })
            ->where('account_name', 'like', '%Advance to%')
            ->get();


        return view('backend.pages.hrm.cash_req_approve.details', get_defined_vars());
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
        $detailsInfo =    $this->ApproveCashApplicationService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo =   $this->ApproveCashApplicationService->statusUpdate($id, $status);
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
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo =    $this->ApproveCashApplicationService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo =   $this->ApproveCashApplicationService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }

    public function ac_lager_create(Request $request)
    {

        $account = new Accounts();

        if ($account->where('account_name', $request->account_name)->exists()) {
            return response()->json(['error' => 'Account already exists'], 422);
        }

        $data = [
            'account_name' => $request->account_name,
            'parent_id' => $request->parent_id ?? 4,
            'branch_id' => $request->branch_id  ?? 0,
            'status' => 'Active',
            'opening_balance' => 0,
            'accountable_id' => $request->employee_id,
            'bill_by_bill' => 1,
            'accountable_type' => 'App\Models\Employee',
            'balance_type' => 'debit',
            'unique_identifier ' =>  uniqid('ac-'),
            'created_by' => auth()->id(),
        ];

        $account->create($data);

        return response()->json($account);
    }
}
