<?php

namespace App\Repositories\Sale;

use App\Helpers\Helper;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleReturnRepositories
{
    //code
    private $user_id;
    private $SaleReturn;

    public function __construct(SaleReturn $SaleReturn)
    {
        $this->SaleReturn = $SaleReturn;
    }

    public function getList($request)
    {

        $columns = [
            0  => 'id',
            1  => 'return_no',
            2  => 'original_sale_id', // approximate: sorts by related Sale's id, not invoice text
            3  => 'return_date',
            4  => 'branch_id',
            5  => 'warehouse_id',     // CONFIRM actual column name
            6  => 'ledger_id',
            7  => 'sales_person_id',
            8  => 'id',               // total_return_qty — not a real column, see note above
            9  => 'grand_total',
            10 => 'id',               // condition — not a real column, see note above
            11 => 'status',
        ];


        $edit    = Helper::roleAccess('sale.return.edit') ? 1 : 0;
        $delete  = Helper::roleAccess('sale.return.destroy') ? 1 : 0;
        $view    = Helper::roleAccess('sale.return.show') ? 1 : 0;
        $approve = Helper::roleAccess('sale.return.approve') ? 1 : 0;

        $query = SaleReturn::with([
            'sale',
            'branch',
            'ledger',
            'salesPerson',
            'details',
        ]);

        $totalData = $query->count();

        if ($request->date) {
            $query->whereDate('return_date', $request->date);
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                    ->orWhere('return_date', 'like', "%{$search}%")
                    ->orWhereHas('sale', function ($query) use ($search) {
                        $query->where('invoice_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('branchCode', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ledger', function ($query) use ($search) {
                        $query->where('account_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('salesPerson', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection   = $request->input('order.0.dir', 'desc');
        $order            = $columns[$orderColumnIndex] ?? 'id';

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $saleReturns = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDirection)
            ->get();

        $data = [];

        foreach ($saleReturns as $key => $saleReturn) {

            $nestedData = [];
            $nestedData['id']                = $start + $key + 1;
            $nestedData['return_no']         = $saleReturn->return_no ?? '';
            $nestedData['sale_invoice_no']   = optional($saleReturn->sale)->invoice_no ?? '';
            $nestedData['return_date']       = $saleReturn->return_date
                ? \Carbon\Carbon::parse($saleReturn->return_date)->format('d-m-Y')
                : '';
            $nestedData['branch_name']       =  $saleReturn->branch->name ?? '';
            $nestedData['warehouse']       =  $saleReturn->warehouse->name ?? '';
            $nestedData['customer_name']     = optional($saleReturn->ledger)->account_name ?? '';
            $nestedData['sales_person_name'] = optional($saleReturn->salesPerson)->name ?? 'Not Assigned';
            $nestedData['total_return_qty']  = $saleReturn->details->sum('returned_qty');
            $nestedData['grand_total']       = number_format((float) $saleReturn->grand_total, 2);
            $nestedData['return_type']       = $saleReturn->return_type ?? '';

            $distinctConditions = $saleReturn->details->pluck('condition')->filter()->unique();

            if ($distinctConditions->count() > 1) {
                $nestedData['condition'] = '<span class="badge badge-warning">Mixed</span>';
            } elseif ($distinctConditions->first() === 'damaged') {
                $nestedData['condition'] = '<span class="badge badge-danger">Damaged</span>';
            } elseif ($distinctConditions->first() === 'good') {
                $nestedData['condition'] = '<span class="badge badge-success">Good</span>';
            } else {
                $nestedData['condition'] = '';
            }


            $nestedData['status']            = $this->statusBadge($saleReturn->status);

            $action = '';

            if ($view) {
                $action .= '<a href="' . route('sale.return.show', $saleReturn->id) . '" class="btn btn-xs btn-default" title="View">
            <i class="fa fa-eye" aria-hidden="true"></i>
        </a> ';
            }

            if ($edit && $saleReturn->status === 'pending') {
                $action .= '<a href="' . route('sale.return.edit', $saleReturn->id) . '" class="btn btn-xs btn-default" title="Edit">
            <i class="fa fa-edit" aria-hidden="true"></i>
        </a> ';
            }

            if ($approve && $saleReturn->status === 'pending') {
                $action .= '<a href="javascript:;" onclick="approveReturn(' . $saleReturn->id . ')" class="btn btn-xs btn-success" title="Approve">
            <i class="fa fa-check" aria-hidden="true"></i>
        </a> ';

                $action .= '<a href="javascript:;" onclick="rejectReturn(' . $saleReturn->id . ')" class="btn btn-xs btn-warning" title="Reject">
            <i class="fa fa-ban" aria-hidden="true"></i>
        </a> ';
            }

            if ($delete && $saleReturn->status === 'pending') {
                $action .= '<a delete_route="' . route('sale.return.destroy', $saleReturn->id) . '" delete_id="' . $saleReturn->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $saleReturn->id . '">
            <i class="fa fa-times"></i>
        </a>';
            }

            $nestedData['action'] = $action;

            $data[] = $nestedData;
        }

        return [
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data'            => $data,
        ];
    }

    private function statusBadge($status)
    {
        $map = [
            'pending'  => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
        ];

        $class = $map[$status] ?? 'badge-secondary';
        $label = ucfirst($status ?? '');

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::with('details')->findOrFail($request->original_sale_id);

            $items           = $request->input('items', []);
            $linesToInsert   = [];
            $grandTotal      = 0;
            $allLinesCleared = true;


            foreach ($sale->details as $detail) {

                $submitted    = collect($items)->firstWhere('sale_detail_id', $detail->id);
                $requestedQty = $submitted ? (float) $submitted['returned_qty'] : 0;

                $alreadyReturned = SaleReturnDetails::where('sale_detail_id', $detail->id)
                    ->whereHas('saleReturn', function ($q) {
                        $q->where('status', '!=', 'rejected');
                    })
                    ->sum('returned_qty');

                $remaining = (float) $detail->qty - (float) $alreadyReturned;

                if ($requestedQty > 0) {

                    if ($requestedQty > $remaining) {
                        throw new \InvalidArgumentException(
                            "Return quantity for product ID {$detail->product_id} exceeds remaining quantity."
                        );
                    }

                    $rate = (float) $detail->rate;
                    $vat  = (float) $detail->vat;

                    $lineAmount = round($requestedQty * $rate * (1 + $vat / 100), 2);
                    $grandTotal += $lineAmount;

                    $linesToInsert[] = [
                        'sale_detail_id' => $detail->id,
                        'product_id'     => $detail->product_id,
                        'returned_qty'   => $requestedQty,
                        'unit_price'     => $rate,
                        'vat_percent'    => $vat,
                        'line_amount'    => $lineAmount,
                        'condition'      => $submitted['condition'],
                        'reason'         => $submitted['reason'] ?? null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }

                if (($remaining - $requestedQty) > 0.0001) {
                    $allLinesCleared = false;
                }
            }

            if (empty($linesToInsert)) {
                throw new \InvalidArgumentException('Please enter a return quantity for at least one item.');
            }

            $returnType = $allLinesCleared ? 'Full' : 'Partial';

            $saleReturn = new SaleReturn();
            $saleReturn->return_no        = $request->return_no;
            $saleReturn->original_sale_id = $sale->id;
            $saleReturn->branch_id        = $request->branch_id;
            $saleReturn->warehouse_id     = $request->warehouse_id;
            $saleReturn->ledger_id        = $request->ledger_id;
            $saleReturn->sales_person_id  = $request->sales_person_id;
            $saleReturn->return_date      = $request->return_date;
            $saleReturn->return_type      = $returnType;
            $saleReturn->grand_total      = $grandTotal;
            $saleReturn->remarks          = $request->remarks;
            $saleReturn->status           = 'pending';
            $saleReturn->created_by       = auth()->id();
            $saleReturn->save();

            foreach ($linesToInsert as $line) {
                $line['sale_return_id'] = $saleReturn->id;
                SaleReturnDetails::create($line);
            }

            // ==========================================================
            // TODO (Added: 2026-09-13) — implement বাকি:
            //
            // 1) STOCK REVERSAL — $sale->warehouse_id থাকলে warehouse এ,
            //    নাহলে $sale->branch_id এ প্রতিটা returned product_id +
            //    returned_qty যোগ করতে হবে। Existing Stock Adjustment/GRN
            //    Service এর method জানালে এখানে call বসিয়ে দেওয়া হবে।
            //
            // 2) LEDGER REVERSAL ENTRY — $request->ledger_id (customer
            //    account) এর বিপরীতে $grandTotal পরিমাণ reversal entry
            //    (credit customer / debit sales return) post করতে হবে।
            //    Existing DV/CV/JV Service এর class/method জানালে এখানে
            //    বসিয়ে দেওয়া হবে।
            // ==========================================================

            // dd($saleReturn);
            DB::commit();

            return $saleReturn;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
