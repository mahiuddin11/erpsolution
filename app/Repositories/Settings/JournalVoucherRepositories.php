<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\JournlVoucher;
use App\Models\JournalVoucherDetails;
use App\Models\DabitVoucher;
use App\Models\DabitVoucherDetails;
use App\Models\JournalVoucher;
use Illuminate\Support\Facades\Auth;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;

class JournalVoucherRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var journalVoucher
     */
    private $journalVoucher;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(JournalVoucher $JournalVoucher)
    {
        $this->journalVoucher = $JournalVoucher;
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
        return  $this->journalVoucher::get();
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

        $edit = Helper::roleAccess('settings.journal.voucher.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.journal.voucher.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.journal.voucher.show') ? 1 : 0;
        $ced = $edit + $delete + $view;
        $totalData = $this->journalVoucher::count();
        // dd($totalData);
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = "desc";

        $search = $request->input('search.value');
        $query = $this->journalVoucher
            ->select('id', 'voucher_no', 'date',  'project_id', 'updated_by', 'note')
            ->with([
                'updatedBy:id,name',
                'project:id,name'
            ])
            ->withSum('details as total_amount', 'debit');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {

                $q->where('voucher_no', 'like', "%{$search}%")
                    ->orWhere('date', 'like', "{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")

                    ->orWhereHas('updatedBy', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('project', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalData = $this->journalVoucher->count();
        $totalFiltered  = (clone $query)->count();

        $journalVoucher = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = array();
        if ($journalVoucher) {
            foreach ($journalVoucher as $key => $item) {
                $nestedData['id'] = $key + 1;
                $nestedData['voucher_no'] = $item->voucher_no;
                $nestedData['amount'] = $item->total_amount ?? "0";;
                $nestedData['project_id'] = $item->project->name ?? "N/A";
                $nestedData['updated_by'] = $item->updatedBy->name ?? "N/A";
                $nestedData['date'] = $item->date ?? "N/A";
                $nestedData['note'] = $item->note ?? "N/A";

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('settings.journal.voucher.edit', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('settings.journal.voucher.show', $item->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.journal.voucher.destroy', $item->id) . '" delete_id="' . $item->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $item->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->journalVoucher::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $creditvoucherLastData = JournalVoucher::latest('id')->first();
            if ($creditvoucherLastData) :
                $creditvoucherData = $creditvoucherLastData->id + 1;
            else :
                $creditvoucherData = 1;
            endif;
            $invoice_no = 'JV' . str_pad($creditvoucherData, 5, "0", STR_PAD_LEFT);

            $journalVoucher = new JournalVoucher();
            // $journalVoucher->branch_id = $request->branch_id ?? 0;
            $journalVoucher->voucher_no = $invoice_no;
            // $journalVoucher->project_id = $request->project_id;
            $journalVoucher->supplier_id = $request->supplier_id;
            $journalVoucher->customer_id = $request->customer_id;
            $journalVoucher->employee_id = $request->employee_id;
            $journalVoucher->date = $request->date;
            $journalVoucher->note = $request->note;
            $journalVoucher->created_by = Auth::user()->id;
            $journalVoucher->save();

            $accountIds = $request->account_id;
            $debits = $request->debit;
            $credits = $request->credit;
            $paymentInvoices = $request->payment_invoice ?? [];

            foreach ($accountIds as $i => $accountId) {
                $journalVoucherDetails = new JournalVoucherDetails();
                $journalVoucherDetails->payment_invoice = $paymentInvoices[$i] ?? "";
                $journalVoucherDetails->journal_voucher_id = $journalVoucher->id;
                $journalVoucherDetails->account_id = $accountId;

                if($request->cost_center_type[$i] == "project"){
                    $journalVoucherDetails->project_id = $request->project_id[$i];
                }elseif($request->cost_center_type[$i] == "branch"){
                    $journalVoucherDetails->branch_id = $request->branch_id[$i];
                }

                $journalVoucherDetails->amount = $debits[$i] ?? $credits[$i];
                $journalVoucherDetails->debit = $debits[$i];
                $journalVoucherDetails->credit = $credits[$i];
                $journalVoucherDetails->save();

                $transaction = [
                    'payment_invoice' => $journalVoucherDetails->payment_invoice ?? "",
                    'branch_id' => 0,
                    'invoice' => $journalVoucher->voucher_no,
                    'table_id' => $journalVoucherDetails->id,
                    'account_id' => $accountId,
                    'type' => 8,
                    'credit' => $credits[$i],
                    'debit' => $debits[$i],
                    'remark' => $request->note,
                    'created_by' => Auth::id(),
                    'project_id' => 0,
                    'created_at' => $request->date,
                ];

                // Set cost center dynamically
                if ($request->cost_center_type[$i] === 'project') {
                    $transaction['project_id'] = $request->project_id[$i] ?? null;
                } elseif ($request->cost_center_type[$i] === 'branch') {
                    $transaction['branch_id'] = $request->branch_id[$i] ?? 0;
                }

                AccountTransaction::create($transaction);
            }
            DB::commit();
            return $transaction;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage(),$th->getFile());
            return $th->getMessage();
        }
    }

    public function update($request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            // Find the Journal Voucher by ID
            $journalVoucher = JournalVoucher::findOrFail($id);
            // $journalVoucher->branch_id = $request->branch_id ?? 0;
            // $journalVoucher->project_id = $request->project_id;
            $journalVoucher->supplier_id = $request->supplier_id;
            $journalVoucher->customer_id = $request->customer_id;
            $journalVoucher->employee_id = $request->employee_id;
            $journalVoucher->date = $request->date;
            $journalVoucher->note = $request->note;
            $journalVoucher->updated_by = Auth::user()->id;
            $journalVoucher->save();

            // Delete existing JournalVoucherDetails and AccountTransactions
            JournalVoucherDetails::where('journal_voucher_id', $journalVoucher->id)->delete();
            AccountTransaction::where('invoice', $journalVoucher->voucher_no)->delete();

            $accountIds = $request->account_id;
            $debits = $request->debit;
            $credits = $request->credit;
            $paymentInvoices = $request->payment_invoice ?? [];

            // Recreate JournalVoucherDetails and AccountTransactions
            foreach ($accountIds as $i => $accountId) {
                $journalVoucherDetails = new JournalVoucherDetails();
                $journalVoucherDetails->payment_invoice = $paymentInvoices[$i] ?? "";
                $journalVoucherDetails->journal_voucher_id = $journalVoucher->id;
                $journalVoucherDetails->account_id = $accountId;

                if($request->cost_center_type[$i] == "project"){
                    $journalVoucherDetails->project_id = $request->project_id[$i];
                }elseif($request->cost_center_type[$i] == "branch"){
                    $journalVoucherDetails->branch_id = $request->branch_id[$i];
                }

                $journalVoucherDetails->amount = $debits[$i] ?? $credits[$i];
                $journalVoucherDetails->debit = $debits[$i];
                $journalVoucherDetails->credit = $credits[$i];
                $journalVoucherDetails->save();

                $transaction = [
                    'payment_invoice' => $journalVoucherDetails->payment_invoice ?? "",
                    'branch_id' => 0,
                    'invoice' => $journalVoucher->voucher_no,
                    'table_id' => $journalVoucherDetails->id,
                    'account_id' => $accountId,
                    'type' => 8,
                    'credit' => $credits[$i],
                    'debit' => $debits[$i],
                    'remark' => $request->note,
                    'created_by' => Auth::id(),
                    'project_id' => 0,
                    'created_at' => $request->date,
                ];

                // Set cost center dynamically
                if ($request->cost_center_type[$i] === 'project') {
                    $transaction['project_id'] = $request->project_id[$i] ?? null;
                } elseif ($request->cost_center_type[$i] === 'branch') {
                    $transaction['branch_id'] = $request->branch_id[$i] ?? 0;
                }

                AccountTransaction::create($transaction);
            }
            DB::commit();
            return $transaction;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->journalVoucher::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {

        DB::transaction(function () use ($id) {
            // Find the Journal Voucher by ID
            $journalVoucher = JournalVoucher::findOrFail($id);

            // Delete associated JournalVoucherDetails
            JournalVoucherDetails::where('journal_voucher_id', $journalVoucher->id)->delete();

            // Delete associated AccountTransactions
            AccountTransaction::where('invoice', $journalVoucher->voucher_no)->delete();

            // Delete the Journal Voucher
            $journalVoucher->delete();
        });

        return true;
    }
}
