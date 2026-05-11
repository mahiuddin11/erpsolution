<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckRegisterController extends Controller
{
    //
    public function index()
    {
        $title = 'Cheque Register';
        return view('backend.pages.report.checkRegister', get_defined_vars());
    }

    public function dataProcess(Request $request)
    {


        $columns = array(
            0 => 'at.created_at',
            1 => 'coa.account_name',
            2 => 'at.remark',
            3 => 'at.debit',
            4 => 'at.credit',
            5 => 'running_balance',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        // Default Filter
        $bankAccountId = $request->bank_account_id;
        $fromDate      = $request->from_date ?? date('Y-m-d', strtotime('-30 days'));
        $toDate        = $request->to_date   ?? date('Y-m-d');
        $status        = $request->status;

        // Main Query
        $query = DB::table('account_transactions as at')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'at.account_id')
            ->where('coa.parent_id', 8)
            ->whereBetween(DB::raw('DATE(at.created_at)'), [$fromDate, $toDate])
            ->select(
                'at.id',
                'at.created_at as transaction_date',
                'at.payment_invoice',
                'coa.account_name',
                'at.remark',
                'at.type',
                'at.debit',
                'at.credit',
                'at.invoice'
            );

        // Bank Account Filter
        if (!empty($bankAccountId)) {
            $query->where('at.account_id', $bankAccountId);
        }

        $totalData = $query->count();

        // Search
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->Where('at.payment_invoice', 'like', "%{$search}%")
                    ->orWhere('coa.account_name', 'like', "%{$search}%")
                    ->orWhere('at.remark', 'like', "%{$search}%")
                    ->orWhere('at.invoice', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // Ordering & Pagination
        $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir);

        $transactions = $query->get();

        // Running Balance Calculation + Formatting for DataTable
        $data = [];


        foreach ($transactions as $key => $row) {


            $nestedData['sl']               = $key + 1;
            $nestedData['transaction_date'] = $row->transaction_date
                ? \Carbon\Carbon::parse($row->transaction_date)->format('d-m-Y')
                : '';
            $nestedData['cheque_no'] = $this->cheque_no($row->remark) ??  'N/A';
            $nestedData['account_name']     = $row->account_name;
            $nestedData['description']      = $row->remark ?: $row->type;
            $nestedData['debit']            = $row->debit ? number_format($row->debit, 2) : '';
            $nestedData['credit']           = $row->credit ? number_format($row->credit, 2) : '';
            $nestedData['status']           = 'Issued';
            $nestedData['invoice']          = $row->invoice ?: '-';
            $nestedData['action']           = '<button class="btn btn-sm btn-info btn-view" data-id="' . $row->id . '">View</button>';

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return $json_data;
    }

    public function getBankAccounts()
    {
        $banks = DB::table('chart_of_accounts')
            ->where('parent_id', 8)
            ->where('status', 'Active')
            ->select('id', 'account_name', 'bank_name', 'account_code', 'opening_balance', 'balance_type')
            ->orderBy('account_name')
            ->get();

        return response()->json($banks);
    }

    private function cheque_no($remark)
    {
        if (empty($remark)) {
            return null;
        }

        $patterns = [
            '/Cheque\s*no-?\s*([0-9]+)/i',           // Cheque no-0399577
            '/Cheque\s*No[:\s-]*([0-9]+)/i',         // Cheque No: 0399577
            '/Cheque[:\s-]*([0-9]+)/i',              // Cheque 0399577
            '/CHK[:\s-]*([0-9]+)/i',                 // CHK-0399577
            '/([0-9]{5,10})/'                        //  fallback
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $remark, $matches)) {
                $number = trim($matches[1]);
                return "Cheque no-" . $number;
            }
        }

        return null;
    }
}
