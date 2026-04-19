<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use Illuminate\Support\Facades\Auth;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;

class DabitVoucherRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var dabitVoucher
     */
    private $dabitVoucher;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(DabitVoucher $DabitVoucher)
    {
        $this->dabitVoucher = $DabitVoucher;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOpening()
    {
        return  $this->dabitVoucher::get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'amount',
        );

        $edit = Helper::roleAccess('settings.dabit.voucher.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.dabit.voucher.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.dabit.voucher.show') ? 1 : 0;
        $approve = Helper::roleAccess('settings.dabit.voucher.approve') ? 1 : 0;
        $ced = $edit + $delete + $view + $approve;
        $totalData = $this->dabitVoucher::count();
        // dd($totalData);
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = 'desc';
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $dabitvoucher = $this->dabitVoucher::offset($start);
            // if ($auth->branch_id !== null) {
            //     $dabitvoucher = $dabitvoucher->where('branch_id', $auth->branch_id);
            // }
            $dabitvoucher = $dabitvoucher->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->dabitVoucher::count();
        } else {
            $search = $request->input('search.value');
            $dabitvoucher = $this->dabitVoucher->where('voucher_no', 'like', "%{$search}%");;

            $dabitvoucher = $dabitvoucher->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->dabitVoucher::count();
        }

        $data = array();
        if ($dabitvoucher) {
            foreach ($dabitvoucher as $key => $item) {
                $nestedData['id'] = $key + 1;
                $nestedData['voucher_no'] = $item->voucher_no;
                $nestedData['amount'] = $item->details->sum("debit") ?? "N/A";
                $nestedData['project_id'] = $item->project->name ?? "N/A";
                $nestedData['approved_by'] = $item->user->name ?? "Admin still not view";
                $nestedData['viewed'] = $item->viewed == 1 ? "Viewed" : "N/A";
                $nestedData['updated_by'] = $item->updatedBy->name ?? "N/A";

                $nestedData['date'] = $item->date;
                $nestedData['note'] = $item->note;

                if ($ced != 0) :
                    if ($edit != 0 && $item->approve != 1)
                        $edit_data = '<a href="' . route('settings.dabit.voucher.edit', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0 && $item->approve == 1)
                        $view_data = '<a href="' . route('settings.dabit.voucher.show', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.dabit.voucher.destroy', $item->id) . '" delete_id="' . $item->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $item->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';

                    if ($approve != 0 && $item->approve != 1)
                        $approve_data = '<a href="' . route('settings.dabit.voucher.approve', $item->id) . '" onclick="return confirm(`Are You Sure!`)" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></a>';
                    else
                        $approve_data = '';
                    $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data . ' ' . $approve_data;
                else :
                    $nestedData['action'] = '';
                endif;

                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return $json_data;
    }
    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {
        $result = $this->dabitVoucher::find($id);
        return $result;
    }

    public function store($request)
    {

    

        try {
            DB::beginTransaction();

            $dabitvoucherLastData = DabitVoucher::latest('id')->first();
            if ($dabitvoucherLastData) :
                $dabitvoucherData = $dabitvoucherLastData->id + 1;
            else :
                $dabitvoucherData = 1;
            endif;
            $invoice_no = 'DV' . str_pad($dabitvoucherData, 5, "0", STR_PAD_LEFT);

            $dabitvoucher = new DabitVoucher();
            $dabitvoucher->voucher_no = $invoice_no;
            // $dabitvoucher->branch_id = $request->branch_id ?? 0;
            // $dabitvoucher->project_id = $request->project_id;
            $dabitvoucher->supplier_id = $request->supplier_id;
            $dabitvoucher->customer_id = $request->customer_id;
            $dabitvoucher->employee_id = $request->employee_id;
            $dabitvoucher->date = $request->date;
            $dabitvoucher->note = $request->note;
            $dabitvoucher->created_by = Auth::user()->id;
            $dabitvoucher->save();

            for ($i = 0; $i < count($request->account_id); $i++) {
                $dabitvoucherdetails = new DabitVoucherDetails();
                $dabitvoucherdetails->payment_invoice = $request->payment_invoice[$i] ?? "";
                $dabitvoucherdetails->dabit_voucher_id = $dabitvoucher->id;

                if($request->cost_center_type[$i] == "project"){
                    $dabitvoucherdetails->project_id = $request->project_id[$i];
                }elseif($request->cost_center_type[$i] == "branch"){
                    $dabitvoucherdetails->branch_id = $request->branch_id[$i];
                }

                $dabitvoucherdetails->check_number = isset($request->voucher_number[$i]) ? $request->voucher_number[$i] : null;
                $dabitvoucherdetails->check_date = isset($request->voucher_date[$i]) ? $request->voucher_date[$i] : null;
                $dabitvoucherdetails->debit = $request->debit[$i];
                $dabitvoucherdetails->credit = $request->credit[$i];
                $dabitvoucherdetails->account_id = $request->account_id[$i];
                $dabitvoucherdetails->amount = $request->debit[$i] ?? $request->credit[$i];
                $dabitvoucherdetails->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage(),$th->getFile(),$th->getLine() );
            return $th->getMessage();
        }

        return $dabitvoucher;
    }

    public function update($request, $id)
    {
        $dabitvoucher = DabitVoucher::find($id);
        $dabitvoucher->branch_id = $request->branch_id ?? 0;
        // $dabitvoucher->account_id = $request->credit_account_id;
        $dabitvoucher->project_id = $request->project_id;
        $dabitvoucher->date = $request->date;
        $dabitvoucher->note = $request->note;
        $dabitvoucher->updated_by = auth()->id();
        $dabitvoucher->save();

        $dabitvoucher->details()->delete();

        if (!empty($request->debit) || !empty($request->credit)) {
            for ($i = 0; $i < count($request->account_id); $i++) {
                $dabitvoucherdetails = new DabitVoucherDetails();
                $dabitvoucherdetails->payment_invoice = $request->payment_invoice[$i] ?? "";
                $dabitvoucherdetails->dabit_voucher_id = $dabitvoucher->id;

                if($request->cost_center_type[$i] == "project"){
                    $dabitvoucherdetails->project_id = $request->project_id[$i];
                }elseif($request->cost_center_type[$i] == "branch"){
                    $dabitvoucherdetails->branch_id = $request->branch_id[$i];
                }

                $dabitvoucherdetails->check_number = isset($request->voucher_number[$i]) ? $request->voucher_number[$i] : null;
                $dabitvoucherdetails->check_date = isset($request->voucher_date[$i]) ? $request->voucher_date[$i] : null;


                $dabitvoucherdetails->account_id = $request->account_id[$i];
                $dabitvoucherdetails->debit = $request->debit[$i];
                $dabitvoucherdetails->credit = $request->credit[$i];
                $dabitvoucherdetails->amount = $request->debit[$i] ?? $request->credit[$i];
                $dabitvoucherdetails->save();
            }
        }

        return $dabitvoucher;
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->dabitVoucher::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $opening = $this->dabitVoucher::find($id);
        DabitVoucherDetails::where('dabit_voucher_id', $id)->delete();
        AccountTransaction::where('type', 5)->where('table_id', $id)->delete();
        $opening->delete();
        return true;
    }

    /**
     * Approve debit voucher to store account information
     * @param int $id
     */
    public function approve($id)
    {
        try {

            DB::beginTransaction();
            $debitVoucher = DabitVoucher::findOrFail($id);
            $debitVoucherDetails = DabitVoucherDetails::where('dabit_voucher_id', $id)->get();

            foreach ($debitVoucherDetails as  $debitVoucherDetail) {

                $transaction['branch_id'] = $debitVoucherDetail->branch_id;
                $transaction['payment_invoice'] = $debitVoucherDetail->payment_invoice;
                $transaction['invoice'] = $debitVoucher->voucher_no;
                $transaction['table_id'] = $debitVoucher->id;
                $transaction['account_id'] = $debitVoucherDetail->account_id;
                $transaction['type'] = 5;
                $transaction['credit'] = $debitVoucherDetail->credit;
                $transaction['debit'] = $debitVoucherDetail->debit;
                $transaction['remark'] = $debitVoucher->note;
                $transaction['created_by'] = Auth::id();
                $transaction['supplier_id'] = $debitVoucher->supplier_id;
                $transaction['customer_id'] = $debitVoucher->customer_id;
                $transaction['employee_id'] = $debitVoucher->employee_id;
                $transaction['project_id'] = $debitVoucherDetail->project_id;
                $transaction['created_at'] = $debitVoucher->date;
                AccountTransaction::create($transaction);

                // $transactionPay['payment_invoice'] = $debitVoucherDetail->payment_invoice;
                // $transactionPay['invoice'] = $debitVoucher->voucher_no;
                // $transactionPay['table_id'] = $debitVoucher->id;
                // $transactionPay['account_id'] = $debitVoucherDetail->account_id; // ->purchase
                // $transactionPay['type'] = 5;
                // $transactionPay['debit'] =   $debitVoucherDetail->amount;
                // $transactionPay['remark'] = $debitVoucher->note;
                // $transactionPay['created_by'] = Auth::id();
                // $transactionPay['supplier_id'] = $debitVoucher->supplier_id;
                // $transactionPay['customer_id'] = $debitVoucher->customer_id;
                // $transactionPay['employee_id'] = $debitVoucher->employee_id;
                // $transactionPay['project_id'] = $debitVoucher->project_id;
                // $transactionPay['created_at'] = $debitVoucher->date;
                // AccountTransaction::create($transactionPay);
            }

            $debitVoucher->approve = 1;
            $debitVoucher->approved_by = Auth::user()->id;
            $debitVoucher->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return $th->getMessage();
        }


        return true;
    }
}
