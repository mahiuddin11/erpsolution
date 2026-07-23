<?php

namespace App\Services\Project;

use App\Models\Grn;
use App\Models\ProjectTransfer;
use App\Models\ProjectMoney;
use App\Models\AccountTransaction;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\Purchases;
use App\Models\PurchasesDetails;
use Illuminate\Support\Collection;

class ProjectTransactionAggregator
{
    protected $projectId;
    protected $ledgerId;

    public function __construct($projectId, $ledgerId = null)
    {
        $this->projectId = $projectId;
        $this->ledgerId  = $ledgerId;
    }


    public function getAll(array $types = [])
    {
        $all = collect();

        $map = [
            'requisition' => 'mapRequisitions',
            'order'       => 'mapOrders',
            'voucher'     => 'mapPurchaseVouchers',
            'grn'         => 'mapGrn',
            'transfer'    => 'mapTransfer',
            'expense'     => 'mapExpenses',
            'income'      => 'mapIncome',
            'money'       => 'mapProjectMoney',
        ];

        foreach ($map as $type => $method) {
            if (!empty($types) && !in_array($type, $types)) {
                continue;
            }
            $all = $all->merge($this->{$method}());
        }

        return $all->sortByDesc('date')->values();
    }

    public function getPaginated(int $page = 1, int $perPage = 20, array $types = [])
    {
        $all = $this->getAll($types);

        $total  = $all->count();
        $offset = ($page - 1) * $perPage;

        $pageData = $all->slice($offset, $perPage)->values();
        $hasMore  = ($offset + $perPage) < $total;

        return [
            'data'     => $pageData,
            'has_more' => $hasMore,
            'total'    => $total,
            'page'     => $page,
        ];
    }

    protected function mapRequisitions()
    {
        return PurchaseRequisition::with('details')
            ->where('project_id', $this->projectId)
            ->get()
            ->map(function ($row) {

                $amount = $row->total_qty ?? 0;
                // $amount = $row->details->sum(function ($d) {
                //     return $d->total_qty;
                // });


                return [
                    'date'   => optional($row->created_at)->format('Y-m-d'),
                    'type'   => 'requisition',
                    'invoice'    => $row->invoice_no ?? 'N/A',
                    'desc'   => 'Purchase Requisition',
                    'amount' =>  $amount,
                    'status' => $row->status ?? '-',
                    'id'     => $row->id,
                ];
            });
    }

    protected function mapOrders()
    {
        return PurchaseOrder::with('details')
            ->where('project_id', $this->projectId)
            ->get()
            ->map(function ($row) {
                $amount = $row->details->sum(function ($d) {
                    return ($d->qty ?? 0) * ($d->unit_price ?? 0);
                });

                return [
                    'date'   => $row->order_date ?? '-',
                    'type'   => 'order',
                    'invoice'    => $row->invoice_no ?? 'N/A',
                    'desc'   => 'Purchase Order',
                    'amount' => (float) $amount,
                    'status' => $row->status ?? '-',
                    'id'     => $row->id,
                ];
            });
    }

    protected function mapPurchaseVouchers()
    {
        return Purchases::with('details')
            ->where('project_id', $this->projectId)
            ->get()
            ->map(function ($row) {

                $amount = $row->details->sum(function ($d) {
                    return $d->total_price;
                });

                return [
                    'date'   => $row->date ?? optional($row->created_at)->format('Y-m-d'),
                    'type'   => 'voucher',
                    'invoice'    => $row->invoice_no ?? 'N/A',
                    'desc'   => 'Purchase Voucher',
                    'amount' => (float) $amount,
                    'status' => $row->status ?? '-',
                    'id'     => $row->id,
                ];
            });
    }

    protected function mapGrn()
    {
        return Grn::with('details.product')
            ->where('project_id', $this->projectId)
            ->get()
            ->map(function ($val) {
                $amount = $val->details->sum(function ($d) {
                    return ($d->qty ?? 0) * ($d->unit_price ?? 0);
                });
                $itemCount = $val->details->count();

                return [
                    'date'   => $val->date,
                    'type'   => 'grn',
                    'invoice'    => $val->invoice_no ?? '-',
                    'desc'   => 'Good Receive — ' . $itemCount . ' item(s)',
                    'amount' => (float) $amount,
                    'status' => 'Posted',
                    'id'     => $val->id,
                ];
            });
    }

    protected function mapTransfer()
    {

        $transfers = ProjectTransfer::with('details.product')
            ->where('project_id', $this->projectId)
            ->get();

        $productIds = $transfers->flatMap(function ($val) {
            return $val->details->pluck('product_id');
        })->unique();

        $latestPrices = PurchasesDetails::whereIn('product_id', $productIds)
            ->orderByDesc('id')
            ->get()
            ->groupBy('product_id')
            ->map(function ($rows) {
                return $rows->first()->unit_price;
            });

        return $transfers->map(function ($val) use ($latestPrices) {
            $amount = $val->details->sum(function ($d) use ($latestPrices) {
                $price = $latestPrices[$d->product_id] ?? 0;
                return $price * ($d->qty ?? 0);
            });
            $itemCount = $val->details->count();

            return [
                'date'   => $val->order_date,
                'type'   => 'transfer',
                'invoice'    => $val->invoice_no,
                'desc'   => 'Stock Transfer — ' . $itemCount . ' item(s)',
                'amount' => (float) $amount,
                'status' => 'Posted',
                'id'     => $val->id,
            ];
        });
    }

    protected function mapExpenses()
    {
        $direct = AccountTransaction::with('account')
            ->whereIn('account_id', getOldAccount(20)->pluck('id'))
            ->where('project_id', $this->projectId)
            ->get()
            ->map(function ($row) {
                return $this->formatAccountTxn($row, 'expense', 'Direct', $row->debit);
            });

        $indirect = AccountTransaction::with('account')
            ->whereIn('account_id', getOldAccount(21)->pluck('id'))
            ->where('project_id', $this->projectId)
            ->get()
            ->map(function ($row) {
                return $this->formatAccountTxn($row, 'expense', 'Indirect', $row->debit);
            });

        return $direct->merge($indirect);
    }

    protected function mapIncome()
    {

        $direct = collect();
        if ($this->ledgerId) {
            $direct = AccountTransaction::with('account')
                ->where('account_id', $this->ledgerId)
                ->where('project_id', $this->projectId)
                ->whereNotNull('credit')
                ->get()
                ->map(function ($row) {
                    return $this->formatAccountTxn($row, 'income', 'Direct', $row->credit);
                });
        }

        $indirect = AccountTransaction::with('account')
            ->whereIn('account_id', getOldAccount(25)->pluck('id'))
            ->where('project_id', $this->projectId)
            ->get()
            ->map(function ($row) {
                return $this->formatAccountTxn($row, 'income', 'Indirect', $row->credit);
            });

        return $direct->merge($indirect);
    }

    protected function mapProjectMoney()
    {

        return ProjectMoney::where('project_id', $this->projectId)
            ->get()
            ->map(function ($row) {
                $net = (float) ($row->credit ?? 0) - (float) ($row->debit ?? 0);

                return [
                    'date'   => $row->date,
                    'type'   => 'money',
                    'invoice'    => $row->projectBananceCode ?? 'N/A',
                    'desc'   => $row->note ?? 'Project Money',
                    'amount' => $net,
                    'status' => $net >= 0 ? 'Received' : 'Adjustment',
                    'id'     => $row->id,
                ];
            });
    }

    protected function formatAccountTxn($row, $type, $subType, $amount)
    {
        return [
            'date'   => optional($row->created_at)->format('Y-m-d'),
            'type'   => $type,
            'invoice'    => $row->invoice ?? '',
            'desc'   => $subType . ' — ' . ($row->account->account_name ?? 'N/A'),
            'amount' => (float) ($amount ?? 0),
            'status' => 'Posted',
            'id'     => $row->id,
        ];
    }
}
