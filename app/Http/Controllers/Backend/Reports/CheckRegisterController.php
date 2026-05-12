<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use App\Models\CheckRegister;
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
            0 => 'cr.cheque_date',
            1 => 'cr.cheque_no',
            2 => 'from_acc.account_name',
            3 => 'to_acc.account_name',
            4 => 'cr.description',
            5 => 'cr.debit',
            6 => 'cr.credit',
            7 => 'cr.status',
        );

        $limit      = $request->input('length');
        $start      = $request->input('start');
        $orderIndex = $request->input('order.0.column');
        $order      = $columns[$orderIndex] ?? 'cr.cheque_date';
        $dir        = $request->input('order.0.dir', 'desc');

        $bankAccountId = $request->bank_account_id ?? '';
        $fromDate      = $request->from_date ?? date('Y-m-d', strtotime('-30 days'));
        $toDate        = $request->to_date   ?? date('Y-m-d');
        $status        = $request->status    ?? '';

        $query = DB::table('check_registers as cr')
            ->leftJoin('chart_of_accounts as from_acc', 'from_acc.id', '=', 'cr.from_account_id')
            ->leftJoin('chart_of_accounts as to_acc',   'to_acc.id',   '=', 'cr.to_account_id')
            ->whereBetween(DB::raw('DATE(cr.cheque_date)'), [$fromDate, $toDate])
            ->select(
                'cr.id',
                'cr.invoice_ref',
                'cr.cheque_no',
                'cr.cheque_date',
                'cr.cleared_date',
                'cr.debit',
                'cr.credit',
                'cr.amount',
                'cr.description',
                'cr.remark',
                'cr.status',
                'from_acc.account_name as from_account',
                'to_acc.account_name   as to_account'
            );

        if (!empty($bankAccountId)) {
            $query->where(function ($q) use ($bankAccountId) {
                $q->where('cr.from_account_id', $bankAccountId)
                    ->orWhere('cr.to_account_id', $bankAccountId);
            });
        }

        if (!empty($status)) {
            $query->where('cr.status', $status);
        }

        $totalData = $query->count();

        // Search
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('cr.cheque_no',   'like', "%{$search}%")
                    ->orWhere('cr.description', 'like', "%{$search}%")
                    ->orWhere('cr.invoice_ref', 'like', "%{$search}%")
                    ->orWhere('cr.status',      'like', "%{$search}%")
                    ->orWhere('from_acc.account_name', 'like', "%{$search}%")
                    ->orWhere('to_acc.account_name',   'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        $records = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];

        foreach ($records as $key => $row) {

            // Status Badge
            $statusBadge = match ($row->status) {
                'Cleared'  => '<span class="badge badge-success">Cleared</span>',
                'Bounced'  => '<span class="badge badge-danger">Bounced</span>',
                'Void'     => '<span class="badge badge-secondary">Void</span>',
                default    => '<span class="badge badge-success">Issued</span>',
            };

            // Action Buttons
            $action = '
            <a href="' . route('reports.check_register.show', $row->id) . '" 
               class="btn btn-xs btn-info" title="View">
                <i class="fas fa-eye"></i>
            </a>
            <button class="btn btn-xs btn-warning btn-edit" 
                    data-id="' . $row->id . '" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
        ';

            $data[] = [
                'sl'               => $start + $key + 1,
                'transaction_date' => $row->cheque_date
                    ? \Carbon\Carbon::parse($row->cheque_date)->format('d-m-Y')
                    : '-',
                'cheque_no'        => $row->cheque_no ?? '-',
                'from_account'     => $row->from_account ?? 'N/A',
                'to_account'       => $row->to_account   ?? 'N/A',
                'description'      => $row->description  ?? '-',
                'debit'            => $row->debit  ? number_format($row->debit,  2) : '',
                'credit'           => $row->credit ? number_format($row->credit, 2) : '',
                'status'           => $statusBadge,
                'invoice'          => $row->invoice_ref ?? '-',
                'action'           => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ]);
    }


    //transaciton table record store  
    // public function dataProcess(Request $request)
    // {
    //     $columns = array(
    //         0 => 'at.created_at',           // Date
    //         1 => 'at.payment_invoice',      // Cheque No (safe fallback)
    //         2 => 'coa.account_name',        // Account Name
    //         3 => 'at.remark',               // Description
    //         4 => 'at.debit',
    //         5 => 'at.credit',
    //     );

    //     $limit = $request->input('length');
    //     $start = $request->input('start');
    //     $orderIndex = $request->input('order.0.column');
    //     $order = $columns[$orderIndex] ?? 'at.created_at';
    //     $dir   = $request->input('order.0.dir', 'desc');

    //     $bankAccountId = $request->bank_account_id;
    //     $fromDate      = $request->from_date ?? date('Y-m-d', strtotime('-30 days'));
    //     $toDate        = $request->to_date   ?? date('Y-m-d');


    //     $query = DB::table('account_transactions as at')
    //         ->join('chart_of_accounts as bank', 'bank.id', '=', 'at.account_id')


    //         ->leftJoin('account_transactions as opp_trans', function ($join) {
    //             $join->on('opp_trans.invoice', '=', 'at.invoice')
    //                 ->whereColumn('opp_trans.id', '!=', 'at.id');
    //         })
    //         ->leftJoin('chart_of_accounts as from_account', 'from_account.id', '=', 'opp_trans.account_id')
    //         ->leftJoin('suppliers as s', 's.id', '=', 'at.supplier_id')
    //         ->leftJoin('customers as c', 'c.id', '=', 'at.customer_id')
    //         ->where('bank.parent_id', 8)
    //         ->whereBetween(DB::raw('DATE(at.created_at)'), [$fromDate, $toDate])
    //         ->select(
    //             'at.id',
    //             'at.created_at as transaction_date',
    //             'at.payment_invoice',
    //             'bank.account_name as bank_account',
    //             'at.remark',
    //             'at.debit',
    //             'at.credit',
    //             'at.invoice',
    //             // From Account & To Account
    //             DB::raw("CASE 
    //                     WHEN at.debit > 0 THEN bank.account_name 
    //                     ELSE COALESCE(from_account.account_name, s.name, c.name, 'N/A') 
    //                  END as from_account"),

    //             DB::raw("CASE 
    //                     WHEN at.credit > 0 THEN bank.account_name 
    //                     ELSE COALESCE(from_account.account_name, s.name, c.name, 'N/A') 
    //                  END as to_account")
    //         );

    //     if (!empty($bankAccountId)) {
    //         $query->where('at.account_id', $bankAccountId);
    //     }

    //     $totalData = $query->count();


    //     // Search
    //     if (!empty($request->input('search.value'))) {
    //         $search = $request->input('search.value');
    //         $query->where(function ($q) use ($search) {
    //             $q->where('at.payment_invoice', 'like', "%{$search}%")

    //                 ->orWhere('coa.account_name', 'like', "%{$search}%")
    //                 ->orWhere('at.remark', 'like', "%{$search}%")
    //                 ->orWhere('s.name', 'like', "%{$search}%")
    //                 ->orWhere('c.name', 'like', "%{$search}%");
    //         });
    //     }

    //     $totalFiltered = $query->count();

    //     // Ordering & Pagination
    //     $query->offset($start)
    //         ->limit($limit)
    //         ->orderBy($order, $dir);

    //     $transactions = $query->get();

    //     foreach ($transactions as $row) {


    //         $chequeNumber = $this->cheque_no($row->remark) ?? $row->payment_invoice;


    //         CheckRegister::updateOrCreate(
    //             [
    //                 'cheque_no'      => $chequeNumber,
    //             ],
    //             [
    //                 'invoice_ref'       => $row->invoice,
    //                 'cheque_no'         => $chequeNumber,
    //                 'cheque_date'       => $row->transaction_date ?? '',
    //                 'from_account_id'   => $row->from_account_id ?? '',
    //                 'to_account_id'     => $row->to_account_id ?? '',
    //                 'payee_type'        => '',
    //                 'amount'            => $row->debit ?: $row->credit,
    //                 'debit'             => $row->debit ?? '',
    //                 'credit'            => $row->credit ?? '',
    //                 'description'       => $row->remark ?? '',
    //                 'status'            => 'Issued',
    //                 'created_by'        => auth()->id(),
    //             ]
    //         );
    //     }

    //     // Data Formatting
    //     $data = [];


    //     foreach ($transactions as $key => $row) {


    //         $nestedData['sl']               = $key + 1;
    //         $nestedData['transaction_date'] = $row->transaction_date
    //             ? \Carbon\Carbon::parse($row->transaction_date)->format('d-m-Y')
    //             : '';
    //         $nestedData['cheque_no']        = $this->cheque_no($row->remark) ?? '';
    //         // $nestedData['account_name']     = $row->opposite_account;
    //         $nestedData['from_account']     = $row->from_account;     // ← From
    //         $nestedData['to_account']       = $row->to_account;       // ← To
    //         $nestedData['description']      = $row->remark ?: $row->type;
    //         $nestedData['debit']            = $row->debit ? number_format($row->debit, 2) : '';
    //         $nestedData['credit']           = $row->credit ? number_format($row->credit, 2) : '';
    //         $nestedData['status']           = 'Issued';
    //         $nestedData['invoice']          = $row->invoice ?: '-';
    //         $nestedData['action']           = '<button class="btn btn-sm btn-info btn-view" data-id="' . $row->id . '">View</button>';

    //         $data[] = $nestedData;
    //     }

    //     return [
    //         "draw"            => intval($request->input('draw')),
    //         "recordsTotal"    => intval($totalData),
    //         "recordsFiltered" => intval($totalFiltered),
    //         "data"            => $data
    //     ];
    // }


    //previus record store 
    // public function dataProcess(Request $request)
    // {
    //     $columns = array(
    //         0 => 'at.created_at',
    //         1 => 'at.payment_invoice',
    //         2 => 'bank.account_name',
    //         3 => 'at.remark',
    //         4 => 'at.debit',
    //         5 => 'at.credit',
    //     );

    //     $limit = $request->input('length');
    //     $start = $request->input('start');
    //     $orderIndex = $request->input('order.0.column');
    //     $order = $columns[$orderIndex] ?? 'at.created_at';
    //     $dir   = $request->input('order.0.dir', 'desc');

    //     $bankAccountId = $request->bank_account_id;
    //     $fromDate      = $request->from_date ?? date('Y-m-d', strtotime('-30 days'));
    //     $toDate        = $request->to_date   ?? date('Y-m-d');

    //     $query = DB::table('account_transactions as at')
    //         ->join('chart_of_accounts as bank', 'bank.id', '=', 'at.account_id')
    //         ->leftJoin('account_transactions as opp_trans', function ($join) {
    //             $join->on('opp_trans.invoice', '=', 'at.invoice')
    //                 ->whereColumn('opp_trans.id', '!=', 'at.id');
    //         })
    //         ->leftJoin('chart_of_accounts as opp_account', 'opp_account.id', '=', 'opp_trans.account_id')
    //         ->leftJoin('suppliers as s', 's.id', '=', 'at.supplier_id')
    //         ->leftJoin('customers as c', 'c.id', '=', 'at.customer_id')

    //         ->where('bank.parent_id', 8)
    //         ->whereBetween(DB::raw('DATE(at.created_at)'), [$fromDate, $toDate])
    //         ->select(
    //             'at.id',
    //             'at.created_at as transaction_date',
    //             'at.payment_invoice',

    //             'bank.id as bank_account_id',
    //             'bank.account_name as bank_account',
    //             'at.remark',
    //             'at.debit',
    //             'at.credit',
    //             'at.invoice',

    //             // From & To Account IDs
    //             DB::raw("CASE 
    //                     WHEN at.debit > 0 THEN bank.id 
    //                     ELSE opp_account.id 
    //                  END as from_account_id"),

    //             DB::raw("CASE 
    //                     WHEN at.credit > 0 THEN bank.id 
    //                     ELSE opp_account.id 
    //                  END as to_account_id"),

    //             DB::raw("COALESCE(opp_account.account_name, s.name, c.name, 'N/A') as opposite_account")
    //         );

    //     if (!empty($bankAccountId)) {
    //         $query->where('at.account_id', $bankAccountId);
    //     }

    //     $totalData = $query->count();

    //     // Search
    //     if (!empty($request->input('search.value'))) {
    //         $search = $request->input('search.value');
    //         $query->where(function ($q) use ($search) {
    //             $q->where('at.payment_invoice', 'like', "%{$search}%")
    //                 ->orWhere('bank.account_name', 'like', "%{$search}%")
    //                 ->orWhere('at.remark', 'like', "%{$search}%")
    //                 ->orWhere('s.name', 'like', "%{$search}%")
    //                 ->orWhere('c.name', 'like', "%{$search}%");
    //         });
    //     }

    //     $totalFiltered = $query->count();

    //     $transactions = $query->offset($start)
    //         ->limit($limit)
    //         ->orderBy($order, $dir)
    //         ->get();

    //     // ==================== Store in check_registers ====================
    //     foreach ($transactions as $row) {
    //         $chequeNumber = $this->cheque_no($row->remark) ?? $row->payment_invoice;

    //         CheckRegister::updateOrCreate(
    //             [
    //                 'cheque_no'      => $chequeNumber,
    //             ],

    //             [
    //                 'invoice_ref'       => $row->invoice ?? '',
    //                 'cheque_date'       => $row->transaction_date ?? '',
    //                 'bank_account_id'   => $row->bank_account_id ?? '',
    //                 'from_account_id'   => $row->from_account_id ?? '',
    //                 'to_account_id'     => $row->to_account_id ?? '',
    //                 'amount'            => $row->debit ?: $row->credit,
    //                 'debit'             => $row->debit ?? '',
    //                 'credit'            => $row->credit ?? '',
    //                 'description'       => $row->remark ?? '',
    //                 'status'            => 'Issued',
    //                 'created_by'        => auth()->id() ?? 1,
    //             ]
    //         );
    //     }

    //     // ==================== DataTable Response ====================
    //     $data = [];
    //     $runningBalance = 0;

    //     foreach ($transactions as $key => $row) {
    //         $runningBalance += (float)($row->debit ?? 0) - (float)($row->credit ?? 0);

    //         $nestedData['sl']               = $key + 1;
    //         $nestedData['transaction_date'] = $row->transaction_date
    //             ? \Carbon\Carbon::parse($row->transaction_date)->format('d-m-Y') : '';

    //         $nestedData['cheque_no']        = $this->cheque_no($row->remark) ?? $row->payment_invoice ?: 'N/A';
    //         $nestedData['from_account']     = $row->from_account_id;   // ID or name চাইলে পরিবর্তন করা যাবে
    //         $nestedData['to_account']       = $row->to_account_id;
    //         $nestedData['description']      = $row->remark ?: $row->type;
    //         $nestedData['debit']            = $row->debit ? number_format($row->debit, 2) : '';
    //         $nestedData['credit']           = $row->credit ? number_format($row->credit, 2) : '';
    //         $nestedData['status']           = 'Issued';
    //         $nestedData['invoice']          = $row->invoice ?: '-';
    //         $nestedData['action']           = '<button class="btn btn-sm btn-info">View</button>';

    //         $data[] = $nestedData;
    //     }

    //     return [
    //         "draw"            => intval($request->input('draw')),
    //         "recordsTotal"    => intval($totalData),
    //         "recordsFiltered" => intval($totalFiltered),
    //         "data"            => $data
    //     ];
    // }

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
