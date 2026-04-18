<?php

namespace App\Http\Controllers\Backend\Hrm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Employee;
use App\Models\Lone;
use App\Models\LoanDetail;
use App\Models\Transection;
use App\Transformers\AdjustTransformer;
use App\Services\Hrm\ApproveLoneApplicationService;
use App\Services\InventorySetup\AdjustService;
use App\Transformers\Transformers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ApproveLoneApplicationController extends Controller
{

    private $systemService;
   
    private $systemTransformer;

   
    public function __construct(ApproveLoneApplicationService $ApproveLoneApplicationService, Transformers $transformers)
    {
        $this->systemService = $ApproveLoneApplicationService;

        $this->systemTransformer = $transformers;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Lone Application Applicaitn List';
        return view('backend.pages.hrm.lone_approve.index', get_defined_vars());
    }


    public function dataProcessingApproveLoneApplication(Request $request)
    {
        // dd($request->all());
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Leave application ';
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
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('hrm.lone.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */



    // public function approve(Request $request, Lone $lone)
    // {

    //     if (in_array($lone->status, ['approved', 'completed'])) {
    //         return back()->with('error', 'This loan is already approved!');
    //     }

    //     DB::beginTransaction();

    //     try {

    //         $lone->update([
    //             'amount'          =>  $request->amount ?? $lone->amount ,
    //             'lone_adjustment' => $request->lone_adjustment ?? $request->lone_adjustment,
    //             'adjustment_start' => $request->adjustment_start ? $request->adjustment_start . '-01' : $lone->adjustment_start,
    //             'status'          => 'approved',
    //             'approved_by'     => auth()->id(),
    //             'note'            => $request->note ?? 'Loan approved',
    //         ]);

    //         $amount = $request->amount;
    //         $invoice = 'LOAN-' . str_pad($lone->id, 6, '0', STR_PAD_LEFT);
    //         $amount   = (float) $request->amount;
    //         $invoice  = 'LOAN-' . str_pad($lone->id, 6, '0', STR_PAD_LEFT);

    //         AccountTransaction::create([
    //             'invoice'       => $invoice,
    //             'table_id'      => $lone->id,
    //             'branch_id'     => $lone->branch_id,
    //             'account_id'    => 1349,                   
    //             'type'          => 'employee_loan',
    //             'debit'         => $amount,
    //             'credit'        => 0,
    //             'remark'        => 'Employee Loan Approved - ' . ($lone->employee->name ?? ''),
    //             'employee_id'   => $lone->employee_id,
    //             'created_by'    => auth()->id(),
    //             'created_at'    => now(),
    //         ]);

    //         AccountTransaction::create([
    //             'invoice'       => $invoice,
    //             'table_id'      => $lone->id,
    //             'branch_id'     => $lone->branch_id,
    //             'account_id'    => 7,                       // Cash / Bank Account
    //             'type'          => 'employee_loan',
    //             'debit'         => 0,
    //             'credit'        => $amount,
    //             'remark'        => 'Loan Disbursed to Employee - ' . ($lone->employee->name ?? ''),
    //             'employee_id'   => $lone->employee_id,
    //             'created_by'    => auth()->id(),
    //             'created_at'    => now(),
    //         ]);

    //         DB::commit();

    //         session()->flash('success', 'Loan Application successfully Approved!');
    //         return back();
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         \Log::error('Loan Approve Error: ' . $th->getMessage());
    //         session()->flash('error', 'Something went wrong!');
    //         return back();
    //     }
    // }

    public function approve(Request $request, Lone $lone)
    {
        if (in_array($lone->status, ['approved', 'completed'])) {
            return back()->with('error', 'This loan is already approved!');
        }


        DB::beginTransaction();

        try {

            //  Loan Update
            $lone->update([
                'amount' => $request->amount,
                'lone_adjustment' => $request->lone_adjustment,
                'adjustment_start' => $request->adjustment_start . '-01',
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'note' => $request->note ?? 'Loan approved',
            ]);

            //  Prepare Data
            $amount = (float) $request->amount;
            $installment = (float) $request->lone_adjustment;
            $startDate = Carbon::parse($request->adjustment_start);

            $months = ceil($amount / $installment);

            $remaining = $amount;
            
        //    dd($amount, $installment, $startDate,  $months );

            //  EMI Generate
            for ($i = 0; $i < $months; $i++) {

                $pay = ($remaining >= $installment) ? $installment : $remaining;

                LoanDetail::create([
                    'lone_id'     => $lone->id,
                    'employee_id' => $lone->employee_id,
                    'month'       => $startDate->copy()->addMonths($i),
                    'amount'      => $pay,
                    'status'      => 'unpaid',
                    'note'      => 'lone',
                ]);

                $remaining -= $pay;
            }

            //  Invoice
            $invoice = 'LOAN-' . str_pad($lone->id, 6, '0', STR_PAD_LEFT);

            //  Debit Entry (Loan Asset)
            AccountTransaction::create([
                'invoice'       => $invoice,
                'table_id'      => $lone->id,
                'branch_id'     => $lone->branch_id,
                'account_id'    => 1349,
                'type'          => 'employee_loan',
                'debit'         => $amount,
                'credit'        => 0,
                'remark'        => 'Employee Loan Approved - ' . ($lone->employee->name ?? ''),
                'employee_id'   => $lone->employee_id,
                'created_by'    => auth()->id(),
                'created_at'    => now(),
            ]);

            //  Credit Entry (Cash/Bank)
            AccountTransaction::create([
                'invoice'       => $invoice,
                'table_id'      => $lone->id,
                'branch_id'     => $lone->branch_id,
                'account_id'    => 7,
                'type'          => 'employee_loan',
                'debit'         => 0,
                'credit'        => $amount,
                'remark'        => 'Loan Disbursed to Employee - ' . ($lone->employee->name ?? ''),
                'employee_id'   => $lone->employee_id,
                'created_by'    => auth()->id(),
                'created_at'    => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Loan Application successfully Approved!');
        } catch (\Throwable $th) {

            DB::rollBack();
            \Log::error('Loan Approve Error: ' . $th->getMessage());

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function cancel(Lone $lone)
    {
        
        $lone->status = 'cancel';
        $lone->save();
        session()->flash('success', ' Lone Application successfully Cancelled!!');
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
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        try {
            $this->validate($request,  $this->systemService->storeValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('hrm.leave.index');
    }

    // Leave Application Deatails
    public function show(Lone $lone)
    {
        $title = 'Approve Loan Application Details';
   
        return view('backend.pages.hrm.lone_approve.details', get_defined_vars());
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
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo =  $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }
}
