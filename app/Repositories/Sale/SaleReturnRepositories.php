<?php

namespace App\Repositories\Sale;

use App\Helpers\Helper;
use App\Models\SaleReturn;
use Illuminate\Support\Facades\Auth;

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
            0 => 'id',
            1 => 'invoice_no',
            2 => 'po_invoice',
            3 => 'date',
            4 => 'branch_id',
            5 => 'customer_id',
            6 => 'sales_person_id',
            7 => 'qty',
            8 => 'sub_total',
            9 => 'discount',
            10 => 'net_total',
            11 => 'partialPayment',
            12 => 'status',
        ];

        $edit = Helper::roleAccess('sale.return.edit') ? 1 : 0;
        $delete = Helper::roleAccess('sale.return.destroy') ? 1 : 0;
        $view = Helper::roleAccess('sale.return.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $query = $this->SaleReturn::with([
            'branch',
            'customer',
            'salesPerson'
        ]);

        $totalData = $query->count();

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('po_invoice', 'like', "%{$search}%")
                    ->orWhere('date', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('branchCode', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('account_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('salesPerson', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');

        $order = $columns[$orderColumnIndex] ?? 'id';

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $saleReturns = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDirection)
            ->get();

        $data = [];

        foreach ($saleReturns as $key => $esale) {
            $nestedData = [];

            $nestedData['id'] = $start + $key + 1;
            $nestedData['invoice_no'] = $esale->invoice_no ?? '';
            $nestedData['po_invoice'] = $esale->po_invoice ?? '';
            $nestedData['date'] = $esale->date ?? '';
            $nestedData['branch_id'] = $esale->branch
                ? $esale->branch->branchCode . ' - ' . $esale->branch->name
                : '';
            $nestedData['customer_id'] = $esale->customer->account_name ?? '';
            $nestedData['sales_person_id'] = $esale->salesPerson->name ?? 'Not Assigned';
            $nestedData['qty'] = $esale->qty ?? 0;
            $nestedData['sub_total'] = $esale->sub_total ?? 0;
            $nestedData['discount'] = $esale->discount ?? 0;
            $nestedData['net_total'] = $esale->net_total ?? 0;
            $nestedData['partialPayment'] = $esale->partialPayment ?? 0;
            $nestedData['status'] = $esale->status ?? '';

            $action = '';

            if ($view) {
                $action .= '<a href="' . route('sale.return.show', $esale->id) . '" class="btn btn-xs btn-default" title="View">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </a> ';
            }

            if ($edit) {
                $action .= '<a href="' . route('sale.return.edit', $esale->id) . '" class="btn btn-xs btn-default" title="Edit">
                <i class="fa fa-edit" aria-hidden="true"></i>
            </a> ';
            }

            if ($delete) {
                $action .= '<a delete_route="' . route('sale.return.destroy', $esale->id) . '" delete_id="' . $esale->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $esale->id . '">
                <i class="fa fa-times"></i>
            </a>';
            }

            $nestedData['action'] = $action;

            $data[] = $nestedData;
        }

        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ];
    }

    public function store($request)
    {

        try {
            dd($request->all());
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
