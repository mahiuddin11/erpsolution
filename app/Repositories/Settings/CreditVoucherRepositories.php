<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\CreditVoucher;
use App\Models\CreditVoucherDetails;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use Illuminate\Support\Facades\Auth;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;

class CreditVoucherRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var creditVoucher
     */
    private $creditVoucher;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(CreditVoucher $CreditVoucher)
    {
        $this->creditVoucher = $CreditVoucher;
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
        return  $this->creditVoucher::get();
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

        $edit = Helper::roleAccess('settings.credit.voucher.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.credit.voucher.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.credit.voucher.show') ? 1 : 0;
        $approve = Helper::roleAccess('settings.credit.voucher.approve') ? 1 : 0;
        $ced = $edit + $delete + $view;
        $totalData = $this->creditVoucher::count();
        // dd($totalData);
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = 'desc';
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $dabitvoucher = $this->creditVoucher::offset($start);
            $dabitvoucher = $dabitvoucher->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->creditVoucher::count();
        } else {
            $search = $request->input('search.value');
            $dabitvoucher = $this->creditVoucher->where('voucher_no', 'like', "%{$search}%");

            $dabitvoucher = $dabitvoucher->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->creditVoucher::count();
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
                $nestedData['date'] = $item->date ?? "N/A";
                $nestedData['note'] = $item->note ?? "N/A";

                if ($ced != 0) :
                    if ($edit != 0 && $item->approve != 1)
                        $edit_data = '<a href="' . route('settings.credit.voucher.edit', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0 && $item->approve == 1)
                        $view_data = '<a href="' . route('settings.credit.voucher.show', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.credit.voucher.destroy', $item->id) . '" delete_id="' . $item->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $item->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';
                    if ($approve != 0 && $item->approve != 1)
                        $approve_data = '<a href="' . route('settings.credit.voucher.approve', $item->id) . '" onclick="return confirm(`Are You Sure!`)" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></a>';
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
        $result = $this->creditVoucher::find($id);
        return $result;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $creditvoucherLastData = CreditVoucher::latest('id')->first();
            if ($creditvoucherLastData) :
                $creditvoucherData = $creditvoucherLastData->id + 1;
            else :
                $creditvoucherData = 1;
            endif;

            $invoice_no = 'CV' . str_pad($creditvoucherData, 5, "0", STR_PAD_LEFT);
            $dabitvoucher = new CreditVoucher();
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
                $dabitvoucherdetails = new CreditVoucherDetails();
                $dabitvoucherdetails->payment_invoice = $request->payment_invoice[$i] ?? "";
                $dabitvoucherdetails->credit_voucher_id = $dabitvoucher->id;

                if($request->cost_center_type[$i] == "project"){
                    $dabitvoucherdetails->project_id = $request->project_id[$i];
                }elseif($request->cost_center_type[$i] == "branch"){
                    $dabitvoucherdetails->branch_id = $request->branch_id[$i];
                }

                $dabitvoucherdetails->debit = $request->debit[$i];
                $dabitvoucherdetails->credit = $request->credit[$i];
                $dabitvoucherdetails->account_id = $request->account_id[$i];
                $dabitvoucherdetails->amount =  $request->debit[$i] ?? $request->credit[$i];
                $dabitvoucherdetails->save();
            }

            DB::commit();
            return $dabitvoucher;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage(), $th->getLine());
            return $th->getMessage();
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {


            $creditvoucher = CreditVoucher::find($id);
            $creditvoucher->branch_id = $request->branch_id ?? 0;
            // $creditvoucher->account_id = $request->credit_account_id;
            $creditvoucher->project_id = $request->project_id;
            $creditvoucher->date = $request->date;
            $creditvoucher->note = $request->note;
            $creditvoucher->updated_by = auth()->id();
            $creditvoucher->save();

            $creditvoucher->details()->delete();

            if (!empty($request->debit) || !empty($request->credit)) {
                for ($i = 0; $i < count($request->account_id); $i++) {
                    $creditvoucherdetails = new CreditVoucherDetails();
                    $creditvoucherdetails->payment_invoice = $request->payment_invoice[$i] ?? "";
                    $creditvoucherdetails->credit_voucher_id = $creditvoucher->id;


                if($request->cost_center_type[$i] == "project"){
                    $creditvoucherdetails->project_id = $request->project_id[$i];
                }elseif($request->cost_center_type[$i] == "branch"){
                    $creditvoucherdetails->branch_id = $request->branch_id[$i];
                }

                    $creditvoucherdetails->account_id = $request->account_id[$i];
                    $creditvoucherdetails->debit = $request->debit[$i];
                    $creditvoucherdetails->credit = $request->credit[$i];
                    $creditvoucherdetails->amount = $request->debit[$i] ?? $request->credit[$i];
                    $creditvoucherdetails->save();
                }
            }

            DB::commit();
            return $creditvoucherdetails;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return $creditvoucherdetails;
        }
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->creditVoucher::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $opening = $this->creditVoucher::find($id);
        CreditVoucherDetails::where('credit_voucher_id', $id)->delete();
        AccountTransaction::where('type', 6)->where('table_id', $id)->delete();
        $opening->delete();
        return true;
    }

    /**
     * Approved Credit Voucher
     *
     * @author itwaybd
     * @contributor Sajjad <sajjad.develpr@gmail.com>
     * @param $id
     *
     * @return true
     */
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $creditVoucher = CreditVoucher::findOrFail($id);
            $creditVoucherDetails = CreditVoucherDetails::where('credit_voucher_id', $id)->get();

            foreach ($creditVoucherDetails as  $creditVoucherDetail) {
                $transaction['payment_invoice'] = $creditVoucherDetail->payment_invoice;
                $transaction['branch_id'] = $creditVoucherDetail->branch_id ?? 0;
                $transaction['invoice'] = $creditVoucher->voucher_no;
                $transaction['table_id'] = $creditVoucher->id;
                $transaction['account_id'] = $creditVoucherDetail->account_id;
                $transaction['type'] = 6;
                $transaction['credit'] = $creditVoucherDetail->credit;
                $transaction['debit'] = $creditVoucherDetail->debit;
                $transaction['remark'] = $creditVoucher->note;
                $transaction['created_by'] = Auth::id();
                $transaction['supplier_id'] = $creditVoucher->supplier_id;
                $transaction['customer_id'] = $creditVoucher->customer_id;
                $transaction['employee_id'] = $creditVoucher->employee_id;
                $transaction['project_id'] = $creditVoucherDetail->project_id;
                $transaction['created_at'] = $creditVoucher->date;
                AccountTransaction::create($transaction);

                // $transactionPay['invoice'] = $creditVoucher->voucher_no;
                // $transactionPay['table_id'] = $creditVoucher->id;
                // $transactionPay['account_id'] = $creditVoucherDetail->account_id; // ->purchase
                // $transactionPay['type'] = 6;
                // $transactionPay['credit'] =   $creditVoucherDetail->amount;
                // $transactionPay['remark'] = $creditVoucher->note;
                // $transactionPay['created_by'] = Auth::id();
                // $transactionPay['supplier_id'] = $creditVoucher->supplier_id;
                // $transactionPay['customer_id'] = $creditVoucher->customer_id;
                // $transactionPay['employee_id'] = $creditVoucher->employee_id;
                // $transactionPay['project_id'] = $creditVoucher->project_id;
                // $transactionPay['created_at'] = $creditVoucher->date;
                // AccountTransaction::create($transactionPay);
            }

            $creditVoucher->approve = 1;
            $creditVoucher->approved_by = Auth::user()->id;
            $creditVoucher->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return  $e->getMessage();
        }
    }
}
