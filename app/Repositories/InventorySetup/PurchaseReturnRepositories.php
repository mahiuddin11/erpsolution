<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Purchases;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class PurchaseReturnRepositories
{
    /**
     * Server-side DataTable list. Same 12 keys as the old
     * invoice-linked list, so the list/script blades keep working.
     * purchase_invoice_no is '—' for direct (voucher-less) returns.
     */
    public function getList($request)
    {
        $columns = [
            0  => 'id',
            1  => 'return_no',
            2  => 'id', // purchase invoice (virtual)
            3  => 'return_date',
            4  => 'branch_id',
            5  => 'warehouse_id',
            6  => 'id', // supplier (virtual)
            7  => 'id', // total qty (virtual)
            8  => 'grand_total',
            9  => 'return_type',
            10 => 'status',
        ];

        $edit    = \App\Helpers\Helper::roleAccess('inventorySetup.purchase.return.edit') ? 1 : 0;
        $delete  = \App\Helpers\Helper::roleAccess('inventorySetup.purchase.return.destroy') ? 1 : 0;
        $view    = \App\Helpers\Helper::roleAccess('inventorySetup.purchase.return.show') ? 1 : 0;
        $approve = \App\Helpers\Helper::roleAccess('inventorySetup.purchase.return.approvePage') ? 1 : 0;
      

        $query = PurchaseReturn::with([
            'purchase',
            'branch',
            'warehouse',
            'project',
            'supplier',
            'ledger',
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
                    ->orWhereHas('purchase', function ($query) use ($search) {
                        $query->where('invoice_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('branchCode', 'like', "%{$search}%");
                    })
                    ->orWhereHas('warehouse', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ledger', function ($query) use ($search) {
                        $query->where('account_name', 'like', "%{$search}%");
                    });
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection   = $request->input('order.0.dir', 'desc');
        $order            = $columns[$orderColumnIndex] ?? 'id';

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $purchaseReturns = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDirection)
            ->get();

        $data = [];

        foreach ($purchaseReturns as $key => $purchaseReturn) {
            $nestedData = [];
            $nestedData['id']                  = $start + $key + 1;
            $nestedData['return_no']           = $purchaseReturn->return_no ?? '';
            $nestedData['return_date']         = $purchaseReturn->return_date
                ? \Carbon\Carbon::parse($purchaseReturn->return_date)->format('d-m-Y')
                : '';
            $nestedData['branch_name'] = $purchaseReturn->branch->name ?? '';
            $nestedData['warehouse']   = $purchaseReturn->warehouse->name ?? '';
            if ($purchaseReturn->project) {
                $nestedData['warehouse'] .= ' (' . $purchaseReturn->project->name . ')';
            }
            $nestedData['supplier_name'] = optional($purchaseReturn->supplier)->name
                ?? optional($purchaseReturn->ledger)->account_name
                ?? '';
            $nestedData['total_return_qty'] = number_format((float) $purchaseReturn->details->sum('returned_qty'), 2);
            $nestedData['grand_total']      = number_format((float) $purchaseReturn->grand_total, 2);
            $nestedData['return_type']      = $purchaseReturn->return_type ?? '';
            $nestedData['status']           = $this->statusBadge($purchaseReturn->status);

            $action = '';

            if ($view) {
                $action .= '<a href="' . route('inventorySetup.purchase.return.show', $purchaseReturn->id) . '" class="btn btn-xs btn-default" title="View">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </a> ';
            }

            if ($edit && $purchaseReturn->status === 'pending') {
                $action .= '<a href="' . route('inventorySetup.purchase.return.edit', $purchaseReturn->id) . '" class="btn btn-xs btn-default" title="Edit">
                <i class="fa fa-edit" aria-hidden="true"></i>
            </a> ';
            }

            if ($approve && $purchaseReturn->status === 'pending') {
                $action .= '<a href="' . route('inventorySetup.purchase.return.approvePage', $purchaseReturn->id) . '" class="btn btn-xs btn-success" title="Review &amp; Approve/Reject">
                <i class="fa fa-gavel" aria-hidden="true"></i>
            </a> ';
            }

            if ($delete && in_array($purchaseReturn->status, ['pending', 'rejected'], true)) {
                $action .= '<a delete_route="' . route('inventorySetup.purchase.return.destroy', $purchaseReturn->id) . '" delete_id="' . $purchaseReturn->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $purchaseReturn->id . '">
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


    

   
    /**
     * Store a direct return as PENDING. Posts NOTHING to stock or
     * accounting — that happens only in approve().
     */

    public function store($request)
{
   

    return DB::transaction(function () use ($request) {

        $supplierId = $request->supplier_id;
        if (!$supplierId) {
            $ledgerAccount = ChartOfAccount::find($request->ledger_id);
            $supplierId = ($ledgerAccount
                && $ledgerAccount->accountable_type === \App\Models\Supplier::class)
                ? $ledgerAccount->accountable_id
                : 0;
        }

        $purchaseReturn = new PurchaseReturn();
        $purchaseReturn->original_purchase_id = null;
        $purchaseReturn->supplier_id          = $supplierId;
        $purchaseReturn->ledger_id            = $request->ledger_id;
        $purchaseReturn->branch_id            = $request->branch_id;
        $purchaseReturn->warehouse_id         = $request->warehouse_id;
        $purchaseReturn->project_id           = $request->project_id ?: null;
        $purchaseReturn->return_date          = $request->return_date;
        $purchaseReturn->return_type          = 'Full';
        $purchaseReturn->remarks              = $request->remarks;
        $purchaseReturn->status               = 'pending';
        $purchaseReturn->created_by           = Auth::id();
        $purchaseReturn->updated_by           = Auth::id();
        $purchaseReturn->save();

        // Return number comes from the inserted id (same as before).
        $purchaseReturn->return_no = 'PRT' . str_pad($purchaseReturn->id, 5, '0', STR_PAD_LEFT);

        $grandTotal = 0;

        foreach ($request->product_id as $i => $productId) {
            $qty       = (float) $request->returned_qty[$i];
            $unitPrice = (float) $request->unit_price[$i];
            $lineTotal = round($qty * $unitPrice, 2);
            $grandTotal += $lineTotal;

            PurchaseReturnDetail::create([
                'purchase_return_id' => $purchaseReturn->id,
                'purchase_detail_id' => null,
                'product_id'         => $productId,
                'purchasetype'       => $request->purchasetype[$i],
                'returned_qty'       => $qty,
                'unit_price'         => $unitPrice,
                'line_amount'        => $lineTotal,
                'reason'             => $request->reason[$i] ?? null,
            ]);
        }

        $purchaseReturn->grand_total = round($grandTotal, 2);
        $purchaseReturn->save();

      

        return $purchaseReturn;
    });
}


    /**
     * Data for the edit form: the header plus one plain array per line.
     */

     public function edit($id)
    {
        $purchaseReturn = PurchaseReturn::with(['details.product.category', 'purchase'])->findOrFail($id);

        if ($purchaseReturn->status !== 'pending') {
            throw new \Exception('Only pending returns can be edited.');
        }

        $items = $purchaseReturn->details->map(function ($detail) {
            return [
                'id'                 => $detail->id,
                'purchase_detail_id' => $detail->purchase_detail_id,
                'product_id'         => $detail->product_id,
                'category_id'        => optional($detail->product)->category_id,
                'category_name'      => $detail->product->category->name ?? '',
                'product_name'       => optional($detail->product)->name ?? '',
                'returned_qty'       => (float) $detail->returned_qty,
                'unit_price'         => (float) $detail->unit_price,
                'purchasetype'       => $detail->purchasetype,
                'reason'             => $detail->reason,
            ];
        })->all();

        return [
            'purchaseReturn' => $purchaseReturn,
            'purchase'       => $purchaseReturn->purchase,
            'items'          => $items,
        ];
    }



        public function update($request, $id)
        {
            
            $purchaseReturn = PurchaseReturn::findOrFail($id);
        

            if ($purchaseReturn->status !== 'pending') {
                throw new \Exception('Only pending returns can be edited.');
            }

            return DB::transaction(function () use ($request, $purchaseReturn) {
        
                $supplierId = $request->supplier_id;
                if (!$supplierId) {
                    $ledgerAccount = ChartOfAccount::find($request->ledger_id);
                    $supplierId = ($ledgerAccount
                        && $ledgerAccount->accountable_type === \App\Models\Supplier::class)
                        ? $ledgerAccount->accountable_id
                        : 0;
                }

                $purchaseReturn->supplier_id  = $supplierId;
                $purchaseReturn->ledger_id    = $request->ledger_id;
                $purchaseReturn->branch_id    = $request->branch_id;
                $purchaseReturn->warehouse_id = $request->warehouse_id;
                
                if ($request->has('project_id')) {
                    $purchaseReturn->project_id = $request->project_id ?: null;
                }
                $purchaseReturn->return_date = $request->return_date;
                $purchaseReturn->remarks     = $request->remarks;
                $purchaseReturn->updated_by  = Auth::id();

                $purchaseReturn->details()->delete();

                $grandTotal = 0;

                foreach ($request->product_id as $i => $productId) {
                    $qty       = (float) $request->returned_qty[$i];
                    $unitPrice = (float) $request->unit_price[$i];
                    $lineTotal = round($qty * $unitPrice, 2);
                    $grandTotal += $lineTotal;

                    PurchaseReturnDetail::create([
                        'purchase_return_id' => $purchaseReturn->id,
                        'purchase_detail_id' => null,
                        'product_id'         => $productId,
                        'purchasetype'       => $request->purchasetype[$i],
                        'returned_qty'       => $qty,
                        'unit_price'         => $unitPrice,
                        'line_amount'        => $lineTotal,
                        'reason'             => $request->reason[$i] ?? null,
                    ]);
                }

                $purchaseReturn->grand_total = round($grandTotal, 2);
                $purchaseReturn->save();

                return $purchaseReturn;
            });
        }
 

    public function destroy($id)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($id);

        if ($purchaseReturn->status === 'approved') {
            throw new \Exception('An approved return cannot be deleted. It already posted stock and accounting.');
        }

        return DB::transaction(function () use ($purchaseReturn) {
            $purchaseReturn->details()->delete();
            $purchaseReturn->delete();

            return true;
        });
    }

    protected function statusBadge($status)
    {
        if ($status === 'approved') {
            return '<span class="badge badge-success">Approved</span>';
        }

        if ($status === 'rejected') {
            return '<span class="badge badge-danger">Rejected</span>';
        }

        return '<span class="badge badge-warning">Pending</span>';
    }
}
