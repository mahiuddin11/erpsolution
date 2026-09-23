<?php

namespace App\Services\InventorySetup;

use App\Models\ProductOpeningStock;
use App\Models\ProductOpeningStockDetails;
use App\Models\PurchasesDetails;
use App\Models\sales_Details;
use Illuminate\Support\Facades\DB;

class ProductLedgerService
{

public function getProductLedgerData($product_id, $branch_id, $from_date, $to_date, $purchase_type = 'all', $warehouse_id = 'all') // >>> NEW: $warehouse_id
    {
        $isAllBranch = ($branch_id === 'all' || empty($branch_id));
        $isAllType   = ($purchase_type === 'all' || empty($purchase_type));

        // >>> NEW: warehouse filter
        // $warehouse_id: number = oi warehouse | 'null' = legacy branch-level (warehouse_id NULL) | 'all'/empty/onno kichu = filter nai
        $isNullWarehouse = ($warehouse_id === 'null');
        $isAllWarehouse  = !$isNullWarehouse && !is_numeric($warehouse_id); // 'undefined' / '' / 'all' shob-i filter chhara
        $applyWarehouse  = function ($q, $column) use ($isNullWarehouse, $warehouse_id) {
            return $isNullWarehouse ? $q->whereNull($column) : $q->where($column, (int) $warehouse_id);
        };
        // <<< END NEW

        // >>> NEW: warehouse id -> name (ekbar-i query, N+1 nai)
        // TODO-CONFIRM: table 'warehouses', column 'name'
        $warehouseNames = DB::table('warehouses')->pluck('name', 'id');
        // warehouse_id NULL = legacy branch-level row -> '—'
        $whName = fn($id) => $id ? ($warehouseNames->get($id) ?? '—') : '—';
        // <<< END NEW

        // ── 1. Opening Stock ──────────────────────────────────────────────
        $openingRows = ProductOpeningStockDetails::with(['branch:id,name', 'product:id,name', 'ProductOpeningStock:id,invoice_no'])
            ->where('product_id', $product_id)
            ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'warehouse_id')) // >>> NEW: warehouse filter
            ->when(!$isAllType, fn($q) => $q->where('purchasetype', $purchase_type))
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date ?? '0000-00-00',
                'invoice'    => $item->ProductOpeningStock->invoice_no ?? '—',
                'branch'     => $item->branch->name ?? 'N/A',
                // >>> NEW
                // TODO-CONFIRM: product_opening_stock_details.warehouse_id
                'warehouse'  => $whName($item->warehouse_id ?? null),
                // <<< END NEW
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
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'warehouse_id')) // >>> NEW: warehouse filter
            ->when(!$isAllType, fn($q) => $q->where('purchasetype', $purchase_type))
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
                // >>> NEW
                // TODO-CONFIRM: purchases_details.warehouse_id
                'warehouse'  => $whName($item->warehouse_id ?? null),
                // <<< END NEW
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
                    // >>> NEW
                    'warehouse'  => $whName($item->warehouse_id ?? null),
                    // <<< END NEW
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
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'sad.warehouse_id')) // >>> NEW: warehouse filter
            ->when(!$isAllType, fn($q) => $q->where('sad.purchase_type', $purchase_type))
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
                'sad.warehouse_id', // >>> NEW  TODO-CONFIRM: stock_ajdustment_detailsts.warehouse_id
                'sa.invoice_no',
                'sa.adjustment_type',
                'sa.note',
                'b.name as branch_name',
                'p.name as product_name'
            )
            ->orderBy('sad.created_at')
            ->get()
            ->map(function ($item) use ($whName) { // >>> NEW: use ($whName)
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
                    // >>> NEW
                    'warehouse'  => $whName($item->warehouse_id),
                    // <<< END NEW
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
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'td.to_warehouse_id')) // >>> NEW: warehouse filter
            // ->when(!$isAllType, fn($q) => $q->where('td.purchasetype', $purchase_type))
            ->whereNull('td.deleted_at')
            ->whereBetween('td.date', [$from_date, $to_date])
            // >>> NEW: 'td.to_warehouse_id' added (NULL = legacy branch-level transfer)
            ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'td.to_warehouse_id', 'b.name as branch_name', 'p.name as product_name')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => 'TR-' . $item->transfer_id,
                'branch'     => $item->branch_name ?? 'N/A',
                // >>> NEW
                'warehouse'  => $whName($item->to_warehouse_id),
                // <<< END NEW
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
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'td.from_warehouse_id')) // >>> NEW: warehouse filter
            ->whereNull('td.deleted_at')
            ->whereBetween('td.date', [$from_date, $to_date])
            // >>> NEW: 'td.from_warehouse_id' added
            ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'td.from_warehouse_id', 'b.name as branch_name', 'p.name as product_name')
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => 'TR-' . $item->transfer_id,
                'branch'     => $item->branch_name ?? 'N/A',
                // >>> NEW
                'warehouse'  => $whName($item->from_warehouse_id),
                // <<< END NEW
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
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'warehouse_id')) // >>> NEW: warehouse filter
            ->when(!$isAllType, fn($q) => $q->where('purchasetype', $purchase_type))
            ->whereBetween('date', [$from_date, $to_date])
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => $item->sales->invoice_no ?? '—',
                'branch'     => $item->branch->name ?? 'N/A',
                // >>> NEW
                // TODO-CONFIRM: sales_details.warehouse_id
                'warehouse'  => $whName($item->warehouse_id ?? null),
                // <<< END NEW
                'product'    => $item->product->name ?? 'N/A',
                'type'       => 'Sale',
                'quantity'   => (int) $item->qty,
                'in'         => 0,
                'out'        => (int) $item->qty,
                'sort_key'   => '5',
                'created_at' => $item->created_at,
            ]);


        // ── 7. Project Transfer Out (Branch/Warehouse → Project) ────────────
        $projectTransferOutRows = DB::table('project_transfer_details as ptd')
            ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ptd.branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'ptd.product_id')
            ->leftJoin('projects as pr', 'pr.id', '=', 'ptd.project_id')
            ->where('ptd.product_id', $product_id)
            ->where('pt.transfer_type', 'branch_to_project')
            ->where('ptd.status', 'Accepted')
            ->when(!$isAllBranch, fn($q) => $q->where('ptd.branch_id', $branch_id))
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'ptd.warehouse_id')) // >>> NEW: warehouse filter
            ->when(!$isAllType, fn($q) => $q->where('ptd.purchasetype', $purchase_type))
            ->whereBetween('pt.order_date', [$from_date, $to_date])
            ->select(
                'pt.order_date as date',
                'pt.invoice_no',
                'ptd.project_transfer_id',
                'ptd.qty',
                'ptd.purchasetype',
                'ptd.created_at',
                'ptd.warehouse_id', // >>> NEW  TODO-CONFIRM: project_transfer_details.warehouse_id (na thakle from_warehouse_id kina dekhun)
                'b.name as branch_name',
                'p.name as product_name',
                'pr.name as project_name'
            )
            ->get()
            ->map(function ($item) use ($whName) { 
                $source      = $item->branch_name ?: 'Branch/Warehouse';
                $destination = $item->project_name ?: 'Project';

                return [
                    'date'       => $item->date,
                    'invoice'    => $item->invoice_no ?? ('PT-' . $item->project_transfer_id),
                    
                    'branch'     => $item->branch_name ?: ($item->project_name ?: 'N/A'),
                    'warehouse'  => $whName($item->warehouse_id),
                   
                    'product'    => $item->product_name ?? 'N/A',
                    'type'       => 'Transfer Out (' . ucfirst($item->purchasetype) . '): '
                        . $source . ' → ' . $destination,
                    'quantity'   => (int) $item->qty,
                    'in'         => 0,
                    'out'        => (int) $item->qty,
                    'sort_key'   => '6',
                    'created_at' => $item->created_at,
                ];
            });

        // ── 8. Project Transfer In (Project → Branch/Warehouse, return) ─────
        $projectTransferInRows = DB::table('project_transfer_details as ptd')
            ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ptd.branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'ptd.product_id')
            ->leftJoin('projects as pr', 'pr.id', '=', 'ptd.project_id')
            ->where('ptd.product_id', $product_id)
            ->where('pt.transfer_type', 'project_to_branch')
            ->where('ptd.status', 'Accepted')
            ->when(!$isAllBranch, fn($q) => $q->where('ptd.branch_id', $branch_id))
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, 'ptd.warehouse_id')) // >>> NEW: warehouse filter
            ->whereBetween('pt.order_date', [$from_date, $to_date])
            ->select(
                'pt.order_date as date',
                'pt.invoice_no',
                'ptd.project_transfer_id',
                'ptd.qty',
                'ptd.purchasetype',
                'ptd.created_at',
                'ptd.warehouse_id', 
                'b.name as branch_name',
                'p.name as product_name',
                'pr.name as project_name'
            )
            ->get()
            ->map(function ($item) use ($whName) { 
                $source      = $item->project_name ?: 'Project';
                $destination = $item->branch_name ?: 'Branch/Warehouse';

                return [
                    'date'       => $item->date,
                    'invoice'    => $item->invoice_no ?? ('PT-' . $item->project_transfer_id),
                    
                    'branch'     => $item->branch_name ?: ($item->project_name ?: 'N/A'),
                    'warehouse'  => $whName($item->warehouse_id),
                    // <<< END NEW
                    'product'    => $item->product_name ?? 'N/A',
                    'type'       => 'Transfer In (' . ucfirst($item->purchasetype) . '): '
                        . $source . ' → ' . $destination,
                    'quantity'   => (int) $item->qty,
                    'in'         => (int) $item->qty,
                    'out'        => 0,
                    'sort_key'   => '6',
                    'created_at' => $item->created_at,
                ];
            });

        // >>> NEW ── 9. Sale Return (Customer → Branch/Warehouse, stock IN) ─────────
        // TODO-CONFIRM: sale_returns.status-er approved value ('Approved' dhore nisi)
        // TODO-CONFIRM: 'condition' (jemon Damaged) return stock-e ferot jay kina — ekhon shob approved return IN dhora hocche
        // purchasetype sale_return_details-e nai, tai original sale detail (sale_detail_id) theke filter kora hoyeche
        $salesDetailsTable = (new sales_Details)->getTable();

        $saleReturnRows = DB::table('sale_return_details as srd')
            ->join('sale_returns as sr', 'sr.id', '=', 'srd.sale_return_id')
            ->leftJoin($salesDetailsTable . ' as sd', 'sd.id', '=', 'srd.sale_detail_id')
            ->leftJoin('branches as b', 'b.id', '=', 'sr.branch_id')
            ->leftJoin('products as p', 'p.id', '=', 'srd.product_id')
            ->leftJoin('projects as pr', 'pr.id', '=', 'srd.project_id')
            ->where('srd.product_id', $product_id)
            ->where('sr.status', 'Approved')
            ->whereNull('sr.deleted_at')
            ->when(!$isAllBranch, fn($q) => $q->where('sr.branch_id', $branch_id))
            ->when(!$isAllWarehouse, fn($q) => $applyWarehouse($q, DB::raw('COALESCE(srd.warehouse_id, sr.warehouse_id)'))) // >>> NEW: warehouse filter
            ->when(!$isAllType, fn($q) => $q->where('sd.purchasetype', $purchase_type))
            ->whereBetween('sr.return_date', [$from_date, $to_date])
            ->select(
                'sr.return_date as date',
                'sr.return_no',
                'sr.id as sale_return_id',
                'sr.warehouse_id as header_warehouse_id',
                'srd.warehouse_id as detail_warehouse_id',
                'srd.returned_qty',
                'srd.condition',
                'srd.created_at',
                'b.name as branch_name',
                'p.name as product_name',
                'pr.name as project_name'
            )
            ->get()
            ->map(fn($item) => [
                'date'       => $item->date,
                'invoice'    => $item->return_no ?? ('SR-' . $item->sale_return_id),
                // branch na thakle project name
                'branch'     => $item->branch_name ?: ($item->project_name ?: 'N/A'),
                'warehouse'  => $whName($item->detail_warehouse_id ?: $item->header_warehouse_id),
                'product'    => $item->product_name ?? 'N/A',
                'type'       => 'Sale Return' . ($item->condition ? ' (' . ucfirst($item->condition) . ')' : ''),
                'quantity'   => (int) $item->returned_qty,
                'in'         => (int) $item->returned_qty,
                'out'        => 0,
                'sort_key'   => '7',
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
            ->merge($projectTransferOutRows)   
            ->merge($projectTransferInRows)    
            ->merge($saleReturnRows)           
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

    // public function getProductLedgerData($product_id, $branch_id, $from_date, $to_date, $purchase_type = 'all' ,  $warehouse_id){
    //     $isAllBranch = ($branch_id === 'all' || empty($branch_id));
    //     $isAllType   = ($purchase_type === 'all' || empty($purchase_type));

      
    //     $warehouseNames = DB::table('warehouses')->pluck('name', 'id');
    //     $whName = fn($id) => $id ? ($warehouseNames->get($id) ?? '—') : '—';
       

    //     // ── 1. Opening Stock ──────────────────────────────────────────────
    //     $openingRows = ProductOpeningStockDetails::with(['branch:id,name', 'product:id,name', 'ProductOpeningStock:id,invoice_no'])
    //         ->where('product_id', $product_id)
    //         ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
    //         ->when(!$isAllType, fn($q) => $q->where('purchasetype', $purchase_type))
    //         ->whereNull('deleted_at')
    //         ->get()
    //         ->map(fn($item) => [
    //             'date'       => $item->date ?? '0000-00-00',
    //             'invoice'    => $item->ProductOpeningStock->invoice_no ?? '—',
    //             'branch'     => $item->branch->name ?? 'N/A',
    //             // >>> NEW
    //             // TODO-CONFIRM: product_opening_stock_details.warehouse_id
    //             'warehouse'  => $whName($item->warehouse_id ?? null),
    //             // <<< END NEW
    //             'product'    => $item->product->name ?? 'N/A',
    //             'type'       => 'Opening Stock',
    //             'quantity'   => (int) $item->quantity,
    //             'in'         => (int) $item->quantity,
    //             'out'        => 0,
    //             'sort_key'   => '0',
    //             'created_at' => $item->created_at,
    //         ]);

    //     // ── 2. Purchases ──────────────────────────────────────────────────
    //     $purchaseRowsRaw = PurchasesDetails::with([
    //         'branch:id,name',
    //         'product:id,name',
    //         'purchase:id,invoice_no,type,purchase_type,project_id',
    //         'purchase.project:id,name',
    //     ])
    //         ->where('product_id', $product_id)
    //         ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
    //         ->when(!$isAllType, fn($q) => $q->where('purchasetype', $purchase_type))
    //         ->whereBetween('date', [$from_date, $to_date])
    //         ->get();

    //     $purchaseRows = collect();

    //     foreach ($purchaseRowsRaw as $item) {
    //         $isProjectManual = (
    //             optional($item->purchase)->type === 'Project' &&
    //             optional($item->purchase)->purchase_type === 'Manual'
    //         );

    //         // Stock IN — 
    //         $purchaseRows->push([
    //             'date'       => $item->date,
    //             'invoice'    => $item->purchase->invoice_no ?? '—',
    //             'branch'     => $item->branch->name
    //                 ?? optional($item->purchase?->project)->name
    //                 ?? 'N/A',
    //             // >>> NEW
    //             // TODO-CONFIRM: purchases_details.warehouse_id
    //             'warehouse'  => $whName($item->warehouse_id ?? null),
    //             // <<< END NEW
    //             'product'    => $item->product->name ?? 'N/A',
    //             'type'       => 'Purchase (' . ucfirst($item->purchasetype) . ')',
    //             'quantity'   => (int) $item->quantity,
    //             'in'         => (int) $item->quantity,
    //             'out'        => 0,
    //             'sort_key'   => '1',
    //             'created_at' => $item->created_at,
    //         ]);

    //         // Stock OUT — 
    //         if ($isProjectManual) {
    //             $purchaseRows->push([
    //                 'date'       => $item->date,
    //                 'invoice'    => $item->purchase->invoice_no ?? '—',
    //                 'branch'     => $item->branch->name
    //                     ?? optional($item->purchase?->project)->name
    //                     ?? 'N/A',
    //                 // >>> NEW
    //                 'warehouse'  => $whName($item->warehouse_id ?? null),
    //                 // <<< END NEW
    //                 'product'    => $item->product->name ?? 'N/A',
    //                 'type'       => 'Project Consume (Manual)',
    //                 'quantity'   => (int) $item->quantity,
    //                 'in'         => 0,
    //                 'out'        => (int) $item->quantity,
    //                 'sort_key'   => '1',
    //                 'created_at' => $item->created_at,
    //             ]);
    //         }
    //     }

    //     // ── 3. Stock Adjustments ──────────────────────────────────────────
    //     $adjustRows = DB::table('stock_ajdustment_detailsts as sad')
    //         ->join('stock_ajdustments as sa', 'sa.id', '=', 'sad.purchases_id')
    //         ->leftJoin('branches as b', 'b.id', '=', 'sad.branch_id')
    //         ->leftJoin('products as p', 'p.id', '=', 'sad.product_id')
    //         ->where('sad.product_id', $product_id)
    //         ->when(!$isAllBranch, fn($q) => $q->where('sad.branch_id', $branch_id))
    //         ->when(!$isAllType, fn($q) => $q->where('sad.purchase_type', $purchase_type))
    //         ->whereNotNull('sad.date')
    //         ->where('sad.date', '>=', $from_date)
    //         ->where('sad.date', '<=', $to_date)
    //         ->select(
    //             'sad.id',
    //             'sad.date',
    //             'sad.quantity',
    //             'sad.purchases_id',
    //             'sad.status',
    //             'sad.created_at',
    //             'sad.warehouse_id', // >>> NEW  TODO-CONFIRM: stock_ajdustment_detailsts.warehouse_id
    //             'sa.invoice_no',
    //             'sa.adjustment_type',
    //             'sa.note',
    //             'b.name as branch_name',
    //             'p.name as product_name'
    //         )
    //         ->orderBy('sad.created_at')
    //         ->get()
    //         ->map(function ($item) use ($whName) { // >>> NEW: use ($whName)
    //             $isGain = $item->adjustment_type === 'Gain';
    //             $qty    = abs((int) $item->quantity);
    //             $label  = match ($item->adjustment_type) {
    //                 'Gain'   => 'Adjustment (Gain)',
    //                 'Loss'   => 'Adjustment (Loss)',
    //                 'Damage' => 'Adjustment (Damage)',
    //                 'Others' => 'Adjustment (Others)',
    //                 default  => 'Adjustment',
    //             };
    //             return [
    //                 'date'       => $item->date,
    //                 'invoice'    => $item->invoice_no ?? ('ADJ-' . $item->purchases_id),
    //                 'branch'     => $item->branch_name  ?? 'N/A',
    //                 // >>> NEW
    //                 'warehouse'  => $whName($item->warehouse_id),
    //                 // <<< END NEW
    //                 'product'    => $item->product_name ?? 'N/A',
    //                 'type'       => $label,
    //                 'quantity'   => $qty,
    //                 'in'         => $isGain ? $qty : 0,
    //                 'out'        => $isGain ? 0 : $qty,
    //                 'sort_key'   => '2',
    //                 'created_at' => $item->created_at,
    //             ];
    //         });

    //     // ── 4. Transfer In ────────────────────────────────────────────────
    //     $transferInRows = DB::table('transfer_details as td')
    //         ->leftJoin('branches as b', 'b.id', '=', 'td.to_branch_id')
    //         ->leftJoin('products as p', 'p.id', '=', 'td.product_id')
    //         ->where('td.product_id', $product_id)
    //         ->where('td.status', 'Approved')
    //         ->when(!$isAllBranch, fn($q) => $q->where('td.to_branch_id', $branch_id))
    //         // ->when(!$isAllType, fn($q) => $q->where('td.purchasetype', $purchase_type))
    //         ->whereNull('td.deleted_at')
    //         ->whereBetween('td.date', [$from_date, $to_date])
    //         // >>> NEW: 'td.to_warehouse_id' added (NULL = legacy branch-level transfer)
    //         ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'td.to_warehouse_id', 'b.name as branch_name', 'p.name as product_name')
    //         ->get()
    //         ->map(fn($item) => [
    //             'date'       => $item->date,
    //             'invoice'    => 'TR-' . $item->transfer_id,
    //             'branch'     => $item->branch_name ?? 'N/A',
    //             // >>> NEW
    //             'warehouse'  => $whName($item->to_warehouse_id),
    //             // <<< END NEW
    //             'product'    => $item->product_name ?? 'N/A',
    //             'type'       => 'Transfer In',
    //             'quantity'   => (int) $item->approve_qty,
    //             'in'         => (int) $item->approve_qty,
    //             'out'        => 0,
    //             'sort_key'   => '3',
    //             'created_at' => $item->created_at,
    //         ]);

    //     // ── 5. Transfer Out ───────────────────────────────────────────────
    //     $transferOutRows = DB::table('transfer_details as td')
    //         ->leftJoin('branches as b', 'b.id', '=', 'td.from_branch_id')
    //         ->leftJoin('products as p', 'p.id', '=', 'td.product_id')
    //         ->where('td.product_id', $product_id)
    //         ->where('td.status', 'Approved')
    //         ->when(!$isAllBranch, fn($q) => $q->where('td.from_branch_id', $branch_id))
    //         ->whereNull('td.deleted_at')
    //         ->whereBetween('td.date', [$from_date, $to_date])
    //         // >>> NEW: 'td.from_warehouse_id' added
    //         ->select('td.date', 'td.approve_qty', 'td.transfer_id', 'td.created_at', 'td.from_warehouse_id', 'b.name as branch_name', 'p.name as product_name')
    //         ->get()
    //         ->map(fn($item) => [
    //             'date'       => $item->date,
    //             'invoice'    => 'TR-' . $item->transfer_id,
    //             'branch'     => $item->branch_name ?? 'N/A',
    //             // >>> NEW
    //             'warehouse'  => $whName($item->from_warehouse_id),
    //             // <<< END NEW
    //             'product'    => $item->product_name ?? 'N/A',
    //             'type'       => 'Transfer Out',
    //             'quantity'   => (int) $item->approve_qty,
    //             'in'         => 0,
    //             'out'        => (int) $item->approve_qty,
    //             'sort_key'   => '4',
    //             'created_at' => $item->created_at,
    //         ]);

    //     // ── 6. Sales ──────────────────────────────────────────────────────
    //     $salesRows = sales_Details::with(['branch:id,name', 'product:id,name', 'sales:id,invoice_no'])
    //         ->where('product_id', $product_id)
    //         ->when(!$isAllBranch, fn($q) => $q->where('branch_id', $branch_id))
    //         ->when(!$isAllType, fn($q) => $q->where('purchasetype', $purchase_type))
    //         ->whereBetween('date', [$from_date, $to_date])
    //         ->get()
    //         ->map(fn($item) => [
    //             'date'       => $item->date,
    //             'invoice'    => $item->sales->invoice_no ?? '—',
    //             'branch'     => $item->branch->name ?? 'N/A',
    //             'warehouse'  => $whName($item->warehouse_id ?? null),
    //             'product'    => $item->product->name ?? 'N/A',
    //             'type'       => 'Sale',
    //             'quantity'   => (int) $item->qty,
    //             'in'         => 0,
    //             'out'        => (int) $item->qty,
    //             'sort_key'   => '5',
    //             'created_at' => $item->created_at,
    //         ]);


    //     // ── 7. Project Transfer Out (Branch/Warehouse → Project) ────────────
    //     $projectTransferOutRows = DB::table('project_transfer_details as ptd')
    //         ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
    //         ->leftJoin('branches as b', 'b.id', '=', 'ptd.branch_id')
    //         ->leftJoin('products as p', 'p.id', '=', 'ptd.product_id')
    //         ->leftJoin('projects as pr', 'pr.id', '=', 'ptd.project_id')
    //         ->where('ptd.product_id', $product_id)
    //         ->where('pt.transfer_type', 'branch_to_project')
    //         ->where('ptd.status', 'Accepted')
    //         ->when(!$isAllBranch, fn($q) => $q->where('ptd.branch_id', $branch_id))
    //         ->when(!$isAllType, fn($q) => $q->where('ptd.purchasetype', $purchase_type))
    //         ->whereBetween('pt.order_date', [$from_date, $to_date])
    //         ->select(
    //             'pt.order_date as date',
    //             'pt.invoice_no',
    //             'ptd.project_transfer_id',
    //             'ptd.qty',
    //             'ptd.purchasetype',
    //             'ptd.created_at',
    //             'ptd.warehouse_id', 
    //             'b.name as branch_name',
    //             'p.name as product_name',
    //             'pr.name as project_name'
    //         )
    //         ->get()
    //         ->map(function ($item) use ($whName) { 
    //             $source      = $item->branch_name ?: 'Branch/Warehouse';
    //             $destination = $item->project_name ?: 'Project';

    //             return [
    //                 'date'       => $item->date,
    //                 'invoice'    => $item->invoice_no ?? ('PT-' . $item->project_transfer_id),
                   
    //                 'branch'     => $item->branch_name ?: ($item->project_name ?: 'N/A'),
    //                 'warehouse'  => $whName($item->warehouse_id),
                    
    //                 'product'    => $item->product_name ?? 'N/A',
    //                 'type'       => 'Transfer Out (' . ucfirst($item->purchasetype) . '): '
    //                     . $source . ' → ' . $destination,
    //                 'quantity'   => (int) $item->qty,
    //                 'in'         => 0,
    //                 'out'        => (int) $item->qty,
    //                 'sort_key'   => '6',
    //                 'created_at' => $item->created_at,
    //             ];
    //         });

    //     // ── 8. Project Transfer In (Project → Branch/Warehouse, return) ─────
    //     $projectTransferInRows = DB::table('project_transfer_details as ptd')
    //         ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
    //         ->leftJoin('branches as b', 'b.id', '=', 'ptd.branch_id')
    //         ->leftJoin('products as p', 'p.id', '=', 'ptd.product_id')
    //         ->leftJoin('projects as pr', 'pr.id', '=', 'ptd.project_id')
    //         ->where('ptd.product_id', $product_id)
    //         ->where('pt.transfer_type', 'project_to_branch')
    //         ->where('ptd.status', 'Accepted')
    //         ->when(!$isAllBranch, fn($q) => $q->where('ptd.branch_id', $branch_id))
    //         ->whereBetween('pt.order_date', [$from_date, $to_date])
    //         ->select(
    //             'pt.order_date as date',
    //             'pt.invoice_no',
    //             'ptd.project_transfer_id',
    //             'ptd.qty',
    //             'ptd.purchasetype',
    //             'ptd.created_at',
    //             'ptd.warehouse_id', // >>> NEW  TODO-CONFIRM: same as above
    //             'b.name as branch_name',
    //             'p.name as product_name',
    //             'pr.name as project_name'
    //         )
    //         ->get()
    //         ->map(function ($item) use ($whName) { // >>> NEW: use ($whName)
    //             $source      = $item->project_name ?: 'Project';
    //             $destination = $item->branch_name ?: 'Branch/Warehouse';

    //             return [
    //                 'date'       => $item->date,
    //                 'invoice'    => $item->invoice_no ?? ('PT-' . $item->project_transfer_id),
                    
    //                 'branch'     => $item->branch_name ?: ($item->project_name ?: 'N/A'),
    //                 'warehouse'  => $whName($item->warehouse_id),
                   
    //                 'product'    => $item->product_name ?? 'N/A',
    //                 'type'       => 'Transfer In (' . ucfirst($item->purchasetype) . '): '
    //                     . $source . ' → ' . $destination,
    //                 'quantity'   => (int) $item->qty,
    //                 'in'         => (int) $item->qty,
    //                 'out'        => 0,
    //                 'sort_key'   => '6',
    //                 'created_at' => $item->created_at,
    //             ];
    //         });

      
    //     $salesDetailsTable = (new sales_Details)->getTable();

    //     $saleReturnRows = DB::table('sale_return_details as srd')
    //         ->join('sale_returns as sr', 'sr.id', '=', 'srd.sale_return_id')
    //         ->leftJoin($salesDetailsTable . ' as sd', 'sd.id', '=', 'srd.sale_detail_id')
    //         ->leftJoin('branches as b', 'b.id', '=', 'sr.branch_id')
    //         ->leftJoin('products as p', 'p.id', '=', 'srd.product_id')
    //         ->leftJoin('projects as pr', 'pr.id', '=', 'srd.project_id')
    //         ->where('srd.product_id', $product_id)
    //         ->where('sr.status', 'Approved')
    //         ->whereNull('sr.deleted_at')
    //         ->when(!$isAllBranch, fn($q) => $q->where('sr.branch_id', $branch_id))
    //         ->when(!$isAllType, fn($q) => $q->where('sd.purchasetype', $purchase_type))
    //         ->whereBetween('sr.return_date', [$from_date, $to_date])
    //         ->select(
    //             'sr.return_date as date',
    //             'sr.return_no',
    //             'sr.id as sale_return_id',
    //             'sr.warehouse_id as header_warehouse_id',
    //             'srd.warehouse_id as detail_warehouse_id',
    //             'srd.returned_qty',
    //             'srd.condition',
    //             'srd.created_at',
    //             'b.name as branch_name',
    //             'p.name as product_name',
    //             'pr.name as project_name'
    //         )
    //         ->get()
    //         ->map(fn($item) => [
    //             'date'       => $item->date,
    //             'invoice'    => $item->return_no ?? ('SR-' . $item->sale_return_id),
    //             // branch na thakle project name
    //             'branch'     => $item->branch_name ?: ($item->project_name ?: 'N/A'),
    //             'warehouse'  => $whName($item->detail_warehouse_id ?: $item->header_warehouse_id),
    //             'product'    => $item->product_name ?? 'N/A',
    //             'type'       => 'Sale Return' . ($item->condition ? ' (' . ucfirst($item->condition) . ')' : ''),
    //             'quantity'   => (int) $item->returned_qty,
    //             'in'         => (int) $item->returned_qty,
    //             'out'        => 0,
    //             'sort_key'   => '7',
    //             'created_at' => $item->created_at,
    //         ]);
       

    //     // ── Merge + Sort ──────────────────────────────────────────────────
    //     $allRows = collect()
    //         ->merge($openingRows)
    //         ->merge($purchaseRows)
    //         ->merge($adjustRows)
    //         ->merge($transferInRows)
    //         ->merge($transferOutRows)
    //         ->merge($salesRows)
    //         ->merge($projectTransferOutRows)   // NEW
    //         ->merge($projectTransferInRows)    // NEW
    //         ->merge($saleReturnRows)          
    //         ->sortBy('created_at')
    //         ->values();

    //     // ── Running balance ───────────────────────────────────────────────
    //     $remaining = 0;
    //     return $allRows->map(function ($row, $index) use (&$remaining) {
    //         $remaining += ($row['in'] - $row['out']);
    //         return array_merge($row, [
    //             'sl'        => $index + 1,
    //             'remaining' => $remaining,
    //         ]);
    //     })->toArray();
    // }
}

