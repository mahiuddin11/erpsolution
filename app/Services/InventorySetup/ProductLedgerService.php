<?php

namespace App\Services\InventorySetup;

use App\Models\ProductOpeningStock;
use App\Models\ProductOpeningStockDetails;
use App\Models\PurchasesDetails;
use App\Models\sales_Details;
use Illuminate\Support\Facades\DB;

class ProductLedgerService
{

    public function getProductLedgerData($product_id, $branch_id, $from_date, $to_date)
    {
        $isAllBranch = ($branch_id === 'all' || empty($branch_id));




        // ── 1. Opening Stock ──────────────────────────────────────────────
        $openingRows = ProductOpeningStockDetails::with(['branch:id,name', 'product:id,name', 'ProductOpeningStock:id,invoice_no'])
            ->where('product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date ?? '0000-00-00',
                'invoice'    => $item->ProductOpeningStock->invoice_no ?? '—',
                'branch'     => $item->branch->name ?? 'N/A',
                'product'    => $item->product->name ?? 'N/A',
                'type'       => 'Opening Stock',
                'quantity'   => (int) $item->quantity,
                'in'         => (int) $item->quantity,
                'out'        => 0,
                'sort_key'   => '0',
                'created_at' => $item->created_at,
            ]);

        // ── 2. Purchases ──────────────────────────────────────────────────
        $purchaseRowsRaw = PurchasesDetails::with([
            'branch:id,name',
            'product:id,name',
            'purchase:id,invoice_no,type,purchase_type,project_id',
            'purchase.project:id,name',
        ])
            ->where('product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
            ->whereBetween('date', [$from_date, $to_date])
            ->get();

        $purchaseRows = collect();

        foreach ($purchaseRowsRaw as $item) {
            $isProjectManual = (
                optional($item->purchase)->type === 'Project' &&
                optional($item->purchase)->purchase_type === 'Manual'
            );

            // Stock IN — 
            $purchaseRows->push([
                'date'       => $item->date,
                'invoice'    => $item->purchase->invoice_no ?? '—',
                'branch'     => $item->branch->name
                    ?? optional($item->purchase?->project)->name
                    ?? 'N/A',
                'product'    => $item->product->name ?? 'N/A',
                'type'       => 'Purchase (' . ucfirst($item->purchasetype) . ')',
                'quantity'   => (int) $item->quantity,
                'in'         => (int) $item->quantity,
                'out'        => 0,
                'sort_key'   => '1',
                'created_at' => $item->created_at,
            ]);

            // Stock OUT — 
            if ($isProjectManual) {
                $purchaseRows->push([
                    'date'       => $item->date,
                    'invoice'    => $item->purchase->invoice_no ?? '—',
                    'branch'     => $item->branch->name
                        ?? optional($item->purchase?->project)->name
                        ?? 'N/A',
                    'product'    => $item->product->name ?? 'N/A',
                    'type'       => 'Project Consume (Manual)',
                    'quantity'   => (int) $item->quantity,
                    'in'         => 0,
                    'out'        => (int) $item->quantity,
                    'sort_key'   => '1',
                    'created_at' => $item->created_at,
                ]);
            }
        }

        // ── 3. Stock Adjustments ──────────────────────────────────────────
        $adjustRows = DB::table('stock_ajdustment_detailsts as sad')
            ->join('stock_ajdustments as sa', 'sa.id', '=', 'sad.purchases_id')
            ->leftJoin('branches as b', 'b.id', '=', 'sad.branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'sad.product_id')
            ->where('sad.product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('sad.branch_id', $branch_id))
            ->whereNotNull('sad.date')
            ->where('sad.date', '>=', $from_date)
            ->where('sad.date', '<=', $to_date)
            ->select(
                'sad.id',
                'sad.date',
                'sad.quantity',
                'sad.purchases_id',
                'sad.status',
                'sad.created_at',
                'sa.invoice_no',
                'sa.adjustment_type',
                'sa.note',
                'b.name as branch_name',
                'p.name as product_name'
            )
            ->orderBy('sad.created_at')
            ->get()
            ->map(function ($item) {
                $isGain = $item->adjustment_type === 'Gain';
                $qty    = abs((int) $item->quantity);
                $label  = match ($item->adjustment_type) {
                    'Gain'   => 'Adjustment (Gain)',
                    'Loss'   => 'Adjustment (Loss)',
                    'Damage' => 'Adjustment (Damage)',
                    'Others' => 'Adjustment (Others)',
                    default  => 'Adjustment',
                };
                return [
                    'date'       => $item->date,
                    'invoice'    => $item->invoice_no ?? ('ADJ-' . $item->purchases_id),
                    'branch'     => $item->branch_name  ?? 'N/A',
                    'product'    => $item->product_name ?? 'N/A',
                    'type'       => $label,
                    'quantity'   => $qty,
                    'in'         => $isGain ? $qty : 0,
                    'out'        => $isGain ? 0 : $qty,
                    'sort_key'   => '2',
                    'created_at' => $item->created_at,
                ];
            });

        // ── 4. Transfer In ────────────────────────────────────────────────
        $transferInRows = DB::table('transfer_details as td')
            ->leftJoin('branches as b', 'b.id', '=', 'td.to_branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'td.product_id')
            ->where('td.product_id', $product_id)
            ->where('td.status', 'Approved')
            ->when(!$isAllBranch, fn($q) => $q->where('td.to_branch_id', $branch_id))
            ->whereNull('td.deleted_at')
            ->whereBetween('td.date', [$from_date, $to_date])
            ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'b.name as branch_name', 'p.name as product_name')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => 'TR-' . $item->transfer_id,
                'branch'     => $item->branch_name ?? 'N/A',
                'product'    => $item->product_name ?? 'N/A',
                'type'       => 'Transfer In',
                'quantity'   => (int) $item->approve_qty,
                'in'         => (int) $item->approve_qty,
                'out'        => 0,
                'sort_key'   => '3',
                'created_at' => $item->created_at,
            ]);

        // ── 5. Transfer Out ───────────────────────────────────────────────
        $transferOutRows = DB::table('transfer_details as td')
            ->leftJoin('branches as b', 'b.id', '=', 'td.from_branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'td.product_id')
            ->where('td.product_id', $product_id)
            ->where('td.status', 'Approved')
            ->when(!$isAllBranch, fn($q) => $q->where('td.from_branch_id', $branch_id))
            ->whereNull('td.deleted_at')
            ->whereBetween('td.date', [$from_date, $to_date])
            ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'b.name as branch_name', 'p.name as product_name')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => 'TR-' . $item->transfer_id,
                'branch'     => $item->branch_name ?? 'N/A',
                'product'    => $item->product_name ?? 'N/A',
                'type'       => 'Transfer Out',
                'quantity'   => (int) $item->approve_qty,
                'in'         => 0,
                'out'        => (int) $item->approve_qty,
                'sort_key'   => '4',
                'created_at' => $item->created_at,
            ]);

        // ── 6. Sales ──────────────────────────────────────────────────────
        $salesRows = sales_Details::with(['branch:id,name', 'product:id,name', 'sales:id,invoice_no'])
            ->where('product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
            ->whereBetween('date', [$from_date, $to_date])
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => $item->sales->invoice_no ?? '—',
                'branch'     => $item->branch->name ?? 'N/A',
                'product'    => $item->product->name ?? 'N/A',
                'type'       => 'Sale',
                'quantity'   => (int) $item->qty,
                'in'         => 0,
                'out'        => (int) $item->qty,
                'sort_key'   => '5',
                'created_at' => $item->created_at,
            ]);

        // ── Merge + Sort ──────────────────────────────────────────────────
        $allRows = collect()
            ->merge($openingRows)
            ->merge($purchaseRows)
            ->merge($adjustRows)
            ->merge($transferInRows)
            ->merge($transferOutRows)
            ->merge($salesRows)
            ->sortBy('created_at')
            ->values();

        // ── Running balance ───────────────────────────────────────────────
        $remaining = 0;
        return $allRows->map(function ($row, $index) use (&$remaining) {
            $remaining += ($row['in'] - $row['out']);
            return array_merge($row, [
                'sl'        => $index + 1,
                'remaining' => $remaining,
            ]);
        })->toArray();
    }
}
