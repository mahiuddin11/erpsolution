<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\ContraVoucher;
use App\Models\ContraVoucherDetails;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use Illuminate\Support\Facades\Auth;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;

class ContraVoucherRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var contraVoucher
     */
    private $contraVoucher;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(ContraVoucher $ContraVoucher)
    {
        $this->contraVoucher = $ContraVoucher;
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
        return  $this->contraVoucher::get();
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

        $edit = Helper::roleAccess('settings.contra.voucher.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.contra.voucher.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.contra.voucher.show') ? 1 : 0;
        $ced = $edit + $delete + $view;
        $totalData = $this->contraVoucher::count();
        // dd($totalData);
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = "desc";
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $contravoucher = $this->contraVoucher::offset($start);
            // if ($auth->branch_id !== null) {
            //     $contravoucher = $contravoucher->where('branch_id', $auth->branch_id);
            // }
            $contravoucher = $contravoucher->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->contraVoucher::count();
        } else {
            $search = $request->input('search.value');
            $contravoucher = $this->contraVoucher;

            $contravoucher = $contravoucher->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->contraVoucher::count();
        }

        $data = array();
        if ($contravoucher) {
            foreach ($contravoucher as $key => $item) {
                $nestedData['id'] = $key + 1;
                $nestedData['voucher_no'] = $item->voucher_no;
                // $nestedData['account_id'] = $item->account->account_name ?? "N/A";
                $nestedData['date'] = $item->date;
                $nestedData['note'] = $item->note;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('settings.contra.voucher.edit', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('settings.contra.voucher.show', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.contra.voucher.destroy', $item->id) . '" delete_id="' . $item->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $item->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';
                    $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;
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
        $result = $this->contraVoucher::find($id);
        return $result;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $contravoucher = new ContraVoucher();
            $contravoucher->voucher_no = $request->invoice_no;
            $contravoucher->date = $request->date;
            $contravoucher->note = $request->note;
            $contravoucher->created_by = Auth::user()->id;
            $contravoucher->save();


            for ($i = 0; $i < count($request->account_id); $i++) {
                $contravoucherdetails = new ContraVoucherDetails();
                $contravoucherdetails->contra_voucher_id = $contravoucher->id;
                $contravoucherdetails->account_id = $request->account_id[$i];
                $contravoucherdetails->to_account_id = $request->to_account_id[$i];
                $contravoucherdetails->amount = $request->amount[$i];
                $contravoucherdetails->save();

                $lastaccount = getFirstAccount($request->account_id[$i]);

                $transaction['invoice'] = $contravoucher->voucher_no;
                $transaction['table_id'] = $contravoucherdetails->id;
                $transaction['account_id'] = $request->account_id[$i]; // from account
                $transaction['type'] = 7;
                $transaction["credit"] = $request->amount[$i];
                $transaction['remark'] = $request->note;
                $transaction['created_by'] = Auth::id();
                $transaction['created_at'] = $request->date;
                // $transaction['supplier_id'] = $request->supplier_id;
                AccountTransaction::create($transaction);

                $transactionPay['invoice'] = $contravoucher->voucher_no;
                $transactionPay['table_id'] = $contravoucherdetails->id;
                $transactionPay['account_id'] = $request->to_account_id[$i]; // to account
                $transactionPay['type'] = 7;
                $transactionPay["debit"] =   $request->amount[$i];
                $transactionPay['remark'] = $request->note;
                $transactionPay['created_by'] = Auth::id();
                $transactionPay['created_at'] = $request->date;
                // $transactionPay['supplier_id'] = $request->supplier_id;
                // dd($transactionPay);
                AccountTransaction::create($transactionPay);
            }
            DB::commit();
            return $contravoucher;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            session()->flash('error', $th->getMessage());
            return redirect()->route('settings.contra.voucher.index');
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $contravoucher = ContraVoucher::find($id);
            $contravoucher->date = $request->date;
            $contravoucher->note = $request->note;
            $contravoucher->save();


            for ($i = 0; $i < count($request->account_id); $i++) {
                $contravoucherdetails = new ContraVoucherDetails();
                $contravoucherdetails->contra_voucher_id = $contravoucher->id;
                $contravoucherdetails->account_id = $request->account_id[$i];
                $contravoucherdetails->to_account_id = $request->to_account_id[$i];
                $contravoucherdetails->amount = $request->amount[$i];
                $contravoucherdetails->save();

                $transaction['invoice'] = $contravoucher->voucher_no;
                $transaction['table_id'] = $contravoucherdetails->id;
                $transaction['account_id'] = $request->account_id[$i]; // from account
                $transaction['type'] = 7;
                $transaction['branch_id'] = $request->branch_id ?? 0;
                $transaction['credit'] = $request->amount[$i];
                $transaction['remark'] = $request->note;
                $transaction['created_by'] = Auth::id();
                $transaction['created_at'] = $request->date;
                AccountTransaction::create($transaction);

                $transactionPay['invoice'] = $contravoucher->voucher_no;
                $transactionPay['table_id'] = $contravoucherdetails->id;
                $transactionPay['account_id'] = $request->to_account_id[$i]; // to account
                $transactionPay['type'] = 7;
                $transactionPay['branch_id'] = $request->branch_id ?? 0;
                $transactionPay['debit'] =   $request->amount[$i];
                $transactionPay['remark'] = $request->note;
                $transactionPay['created_by'] = Auth::id();
                $transactionPay['created_at'] = $request->date;
                AccountTransaction::create($transactionPay);
            }
            DB::commit();
            return $contravoucher;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage(), $th->getLine(), $th->getFile());
            return 0;
        }
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->contraVoucher::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $opening = $this->contraVoucher::find($id);
        AccountTransaction::where('type', 7)->whereIn('table_id', $opening->details->pluck("id"))->delete();
        ContraVoucherDetails::where('contra_voucher_id', $id)->delete();
        $opening->delete();
        return true;
    }
}
