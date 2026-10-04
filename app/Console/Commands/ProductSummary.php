<?php

namespace App\Console\Commands;

use App\Models\ProductOpeningStockDetails;
use App\Models\PurchasesDetails;
use App\Models\sales_Details;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Put in: app/Console/Commands/ProductSummary.php
 *
 * php artisan product:summary 15                         -> one product, all branches
 * php artisan product:summary 15 --branch=3              -> same product, only branch 3 (everywhere)
 * php artisan product:summary 15 --branch=3 --warehouse=7
 * php artisan product:summary 15 --by-branch             -> branch-wise breakdown too
 * php artisan product:summary --category=4               -> every product of category 4
 * php artisan product:summary --category=4 --branch=3 --from=2026-01-01 --to=2026-09-30
 *
 * Read-only: nothing is written to the database.
 *
 * ASSUMPTIONS (adjust if your schema differs): products table = `products`
 * with `category_id`; categories table = `categories`; branches table = `branches`;
 * warehouse filter is applied only on tables that actually have a `warehouse_id`
 * (or from_/to_warehouse_id) column, otherwise a warning is printed.
 */
class ProductSummary extends Command
{
    protected $signature = 'product:summary
                            {product_id? : Product id}
                            {--category= : Category id: summarise every product in that category}
                            {--branch=all : Branch id, or "all"}
                            {--warehouse=all : Warehouse id, or "all"}
                            {--from=2000-01-01 : From date (Y-m-d)}
                            {--to=2100-01-01 : To date (Y-m-d)}
                            {--by-branch : Also show branch-wise breakdown (single product only)}';

    protected $description = 'Quick summary of a product (or a whole category): purchase, sale, transfer, adjustment, project transfer, stock';

    private ?int $branch = null;
    private ?int $warehouse = null;
    private string $from;
    private string $to;
    private array $warned = [];
    private array $colCache = [];

    private const LABELS = [
        'opening'      => 'Opening Stock',
        'purchase'     => 'Purchase',
        'adjustment'   => 'Stock Adjustment (Gain = IN, others = OUT)',
        'transfer_in'  => 'Transfer In (Approved)',
        'transfer_out' => 'Transfer Out (Approved)',
        'sale'         => 'Sale',
        'project_out'  => 'Project Transfer Out (Branch -> Project)',
        'project_in'   => 'Project Transfer In (Project -> Branch)',
        'consume'      => 'Project Consume (Manual purchase, synthetic)',
    ];

    public function handle()
    {
        $pid = $this->argument('product_id');
        $cat = $this->option('category');

        if (!$pid && !$cat) {
            $this->error('Product id ba --category dite hobe. Example: php artisan product:summary 15   ba   php artisan product:summary --category=4');
            return self::FAILURE;
        }

        $this->branch    = $this->parseId($this->option('branch'));
        $this->warehouse = $this->parseId($this->option('warehouse'));
        $this->from      = $this->option('from');
        $this->to        = $this->option('to');

        $this->newLine();
        $this->info('Scope: ' . $this->scopeLine());
        $this->warnIfWarehouseTypeBranch();
        $this->newLine();

        $code = $cat ? $this->categoryReport((int) $cat) : $this->productReport((int) $pid);

        $this->printHints($cat ? "--category={$cat}" : (string) $pid);

        return $code;
    }

    // ------------------------------------------------------------------
    // Single product
    // ------------------------------------------------------------------
    private function productReport(int $pid): int
    {
        $product = DB::table('products')->where('id', $pid)->first();
        if (!$product) {
            $this->error("Product #{$pid} paoa jay nai.");
            return self::FAILURE;
        }

        $this->info("Product #{$pid}: " . $this->nameOf($product));
        if (isset($product->category_id)) {
            $this->line('Category: ' . $this->categoryName($product->category_id) . " (id {$product->category_id})");
        }
        $this->newLine();

        $m = $this->metrics($pid);

        $rows = [];
        foreach (self::LABELS as $key => $label) {
            $r = $m['rows'][$key];
            $rows[] = [$label, $this->fmt($r['in']), $this->fmt($r['out']), $r['docs']];
        }
        $rows[] = ['TOTAL', $this->fmt($m['in']), $this->fmt($m['out']), ''];
        $rows[] = ['NET STOCK (IN - OUT)', '', '', $this->fmt($m['net'])];
        $this->table(['Type', 'IN qty', 'OUT qty', 'Entries'], $rows);

        $this->stocksBreakdown($pid);

        if ($this->option('by-branch')) {
            $this->branchBreakdown($pid);
        }

        return self::SUCCESS;
    }

    private function stocksBreakdown(int $pid): void
    {
        $table = (new Stock)->getTable();
        $q = Stock::where('product_id', $pid)->whereBetween('date', [$this->from, $this->to]);
        $this->loc($q, $table);

        $rows = $q->select('status', DB::raw('SUM(quantity) as qty'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')
            ->get();

        $this->newLine();
        $this->info('`stocks` table (status wise, jeta stock()/stocksummery() report e jay):');
        if ($rows->isEmpty()) {
            $this->warn('  Kono row nai.');
            return;
        }
        $this->table(
            ['status', 'rows', 'qty sum'],
            $rows->map(fn($r) => [$r->status, $r->cnt, $this->fmt($r->qty)])->all()
        );
    }

    private function branchBreakdown(int $pid): void
    {
        $saved = $this->branch;
        $nameCol = $this->firstColumn('branches', ['name', 'branch_name']);

        $branches = DB::table('branches')
            ->whereRaw('COALESCE(parent_id, 0) = 0')
            ->orderBy('id')
            ->get();

        $rows = [];
        foreach ($branches as $b) {
            $this->branch = (int) $b->id;
            $m = $this->metrics($pid);
            if ($m['in'] == 0 && $m['out'] == 0) {
                continue;
            }
            $rows[] = [
                $b->id,
                $nameCol ? $b->{$nameCol} : '-',
                $this->fmt($m['rows']['opening']['in']),
                $this->fmt($m['rows']['purchase']['in']),
                $this->fmt($m['rows']['sale']['out']),
                $this->fmt($m['rows']['transfer_in']['in']),
                $this->fmt($m['rows']['transfer_out']['out']),
                $this->fmt($m['net']),
            ];
        }
        $this->branch = $saved;

        $this->newLine();
        $this->info('Branch wise (shudhu jeigulate kaj ache):');
        if (empty($rows)) {
            $this->warn('  Kono branch e movement paoa jay nai.');
            return;
        }
        $this->table(['Branch ID', 'Branch', 'Opening', 'Purchase', 'Sale', 'Transfer In', 'Transfer Out', 'Net'], $rows);
    }

    // ------------------------------------------------------------------
    // Category
    // ------------------------------------------------------------------
    private function categoryReport(int $catId): int
    {
        if (!$this->hasCol('products', 'category_id')) {
            $this->error('products table e category_id column nai. Command e column name ta thik koro.');
            return self::FAILURE;
        }

        $products = DB::table('products')->where('category_id', $catId)->orderBy('id')->get();

        $this->info('Category: ' . $this->categoryName($catId) . " (id {$catId}) — {$products->count()} ta product");
        $this->newLine();

        if ($products->isEmpty()) {
            $this->warn('Ei category te kono product nai.');
            return self::SUCCESS;
        }

        $rows = [];
        $tot = array_fill_keys(['open', 'purch', 'sale', 'tin', 'tout', 'adjin', 'adjout', 'pin', 'pout', 'cons', 'net'], 0);

        foreach ($products as $p) {
            $m = $this->metrics((int) $p->id);
            $r = $m['rows'];

            $line = [
                'open'   => $r['opening']['in'],
                'purch'  => $r['purchase']['in'],
                'sale'   => $r['sale']['out'],
                'tin'    => $r['transfer_in']['in'],
                'tout'   => $r['transfer_out']['out'],
                'adjin'  => $r['adjustment']['in'],
                'adjout' => $r['adjustment']['out'],
                'pin'    => $r['project_in']['in'],
                'pout'   => $r['project_out']['out'],
                'cons'   => $r['consume']['out'],
                'net'    => $m['net'],
            ];
            foreach ($line as $k => $v) {
                $tot[$k] += $v;
            }

            $rows[] = array_merge([$p->id, mb_strimwidth($this->nameOf($p), 0, 28, '..')], array_map([$this, 'fmt'], array_values($line)));
        }

        $rows[] = array_merge(['', 'TOTAL'], array_map([$this, 'fmt'], array_values($tot)));

        $this->table(
            ['ID', 'Product', 'Opening', 'Purchase', 'Sale', 'Tr In', 'Tr Out', 'Adj +', 'Adj -', 'Prj In', 'Prj Out', 'Consume', 'Net'],
            $rows
        );

        return self::SUCCESS;
    }

    // ------------------------------------------------------------------
    // Core numbers for one product, honouring $this->branch / $this->warehouse
    // ------------------------------------------------------------------
    private function metrics(int $pid): array
    {
        $rows = [];
        $zero = fn() => ['in' => 0, 'out' => 0, 'docs' => 0];
        foreach (array_keys(self::LABELS) as $k) {
            $rows[$k] = $zero();
        }

        // Opening stock (no date filter, same as the ledger)
        $t = (new ProductOpeningStockDetails)->getTable();
        $q = ProductOpeningStockDetails::where('product_id', $pid)->whereNull('deleted_at');
        $this->loc($q, $t);
        $rows['opening']['in']   = (float) $q->sum('quantity');
        $rows['opening']['docs'] = (clone $q)->count();

        // Purchases (+ synthetic consume for Project/Manual purchases)
        $t = (new PurchasesDetails)->getTable();
        $q = PurchasesDetails::with('purchase:id,type,purchase_type')
            ->where('product_id', $pid)
            ->whereBetween('date', [$this->from, $this->to]);
        $this->loc($q, $t);
        $consume = 0;
        $docs = 0;
        $in = 0;
        foreach ($q->get() as $row) {
            $in += (float) $row->quantity;
            $docs++;
            if (optional($row->purchase)->type === 'Project' && optional($row->purchase)->purchase_type === 'Manual') {
                $consume += (float) $row->quantity;
            }
        }
        $rows['purchase'] = ['in' => $in, 'out' => 0, 'docs' => $docs];
        $rows['consume']  = ['in' => 0, 'out' => $consume, 'docs' => 0];

        // Stock adjustments
        $q = DB::table('stock_ajdustment_detailsts as sad')
            ->join('stock_ajdustments as sa', 'sa.id', '=', 'sad.purchases_id')
            ->where('sad.product_id', $pid)
            ->whereNotNull('sad.date')
            ->whereBetween('sad.date', [$this->from, $this->to]);
        $this->loc($q, 'stock_ajdustment_detailsts', 'branch_id', 'warehouse_id', 'sad');
        $adj = $q->select('sad.quantity', 'sa.adjustment_type')->get();
        $aIn = 0;
        $aOut = 0;
        foreach ($adj as $a) {
            $qty = abs((float) $a->quantity);
            $a->adjustment_type === 'Gain' ? $aIn += $qty : $aOut += $qty;
        }
        $rows['adjustment'] = ['in' => $aIn, 'out' => $aOut, 'docs' => $adj->count()];

        // Transfers (Approved)
        $q = DB::table('transfer_details')->where('product_id', $pid)->where('status', 'Approved')
            ->whereNull('deleted_at')->whereBetween('date', [$this->from, $this->to]);
        $this->loc($q, 'transfer_details', 'to_branch_id', 'to_warehouse_id');
        $rows['transfer_in'] = ['in' => (float) $q->sum('approve_qty'), 'out' => 0, 'docs' => (clone $q)->count()];

        $q = DB::table('transfer_details')->where('product_id', $pid)->where('status', 'Approved')
            ->whereNull('deleted_at')->whereBetween('date', [$this->from, $this->to]);
        $this->loc($q, 'transfer_details', 'from_branch_id', 'from_warehouse_id');
        $rows['transfer_out'] = ['in' => 0, 'out' => (float) $q->sum('approve_qty'), 'docs' => (clone $q)->count()];

        // Sales
        $t = (new sales_Details)->getTable();
        $q = sales_Details::where('product_id', $pid)->whereBetween('date', [$this->from, $this->to]);
        $this->loc($q, $t);
        $rows['sale'] = ['in' => 0, 'out' => (float) $q->sum('qty'), 'docs' => (clone $q)->count()];

        // Project transfers (Accepted)
        foreach (['branch_to_project' => 'project_out', 'project_to_branch' => 'project_in'] as $type => $key) {
            $q = DB::table('project_transfer_details as ptd')
                ->join('project_transfers as pt', 'pt.id', '=', 'ptd.project_transfer_id')
                ->where('ptd.product_id', $pid)
                ->where('pt.transfer_type', $type)
                ->where('ptd.status', 'Accepted')
                ->whereBetween('pt.order_date', [$this->from, $this->to]);
            $this->loc($q, 'project_transfer_details', 'branch_id', 'warehouse_id', 'ptd');
            $sum = (float) $q->sum('ptd.qty');
            $rows[$key] = $key === 'project_out'
                ? ['in' => 0, 'out' => $sum, 'docs' => (clone $q)->count()]
                : ['in' => $sum, 'out' => 0, 'docs' => (clone $q)->count()];
        }

        $in  = array_sum(array_column($rows, 'in'));
        $out = array_sum(array_column($rows, 'out'));

        return ['rows' => $rows, 'in' => $in, 'out' => $out, 'net' => $in - $out];
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** Apply the --branch / --warehouse filter to a query. */
    private function loc($q, string $table, string $bCol = 'branch_id', string $wCol = 'warehouse_id', ?string $alias = null)
    {
        $qual = $alias ?: $table;

        if ($this->branch !== null) {
            $q->where("{$qual}.{$bCol}", $this->branch);
        }

        if ($this->warehouse !== null) {
            if ($this->hasCol($table, $wCol)) {
                $q->where("{$qual}.{$wCol}", $this->warehouse);
            } elseif (!isset($this->warned["{$table}.{$wCol}"])) {
                $this->warned["{$table}.{$wCol}"] = true;
                $this->warn("  (!) {$table}.{$wCol} column nai — ei table e warehouse filter apply hoy nai.");
            }
        }

        return $q;
    }

    private function hasCol(string $table, string $col): bool
    {
        return $this->colCache["{$table}.{$col}"] ??= Schema::hasColumn($table, $col);
    }

    private function firstColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if ($this->hasCol($table, $c)) {
                return $c;
            }
        }
        return null;
    }

    private function nameOf(object $row): string
    {
        foreach (['name', 'product_name', 'title'] as $c) {
            if (isset($row->{$c})) {
                return (string) $row->{$c};
            }
        }
        return '#' . $row->id;
    }

    private function categoryName($id): string
    {
        if (!Schema::hasTable('categories')) {
            return '-';
        }
        $col = $this->firstColumn('categories', ['name', 'category_name', 'title']);
        $row = DB::table('categories')->where('id', $id)->first();
        return ($row && $col) ? (string) $row->{$col} : '-';
    }

    private function parseId($v): ?int
    {
        return ($v === null || $v === '' || strtolower((string) $v) === 'all') ? null : (int) $v;
    }

    private function fmt($n): string
    {
        $n = (float) $n;
        return $n == (int) $n ? (string) (int) $n : rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
    }

    private function scopeLine(): string
    {
        $branch = 'ALL';
        if ($this->branch !== null) {
            $col = $this->firstColumn('branches', ['name', 'branch_name']);
            $b = DB::table('branches')->where('id', $this->branch)->first();
            $branch = $this->branch . ($b && $col ? " ({$b->{$col}})" : '');
        }
        return "branch=" . $branch
            . "  warehouse=" . ($this->warehouse ?? 'ALL')
            . "  {$this->from} to {$this->to}";
    }

    private function warnIfWarehouseTypeBranch(): void
    {
        if ($this->branch === null) {
            return;
        }
        $b = DB::table('branches')->where('id', $this->branch)->first();
        if (!$b) {
            $this->warn("Branch #{$this->branch} branches table e nai.");
        } elseif ((int) ($b->parent_id ?? 0) !== 0) {
            $this->warn("Branch #{$this->branch} ekta warehouse-type row (parent_id={$b->parent_id}). Backfill er pore data real branch id {$b->parent_id} + warehouse_id diye save hoy — tai --branch={$b->parent_id} --warehouse=" . ($b->warehouse_id ?? '?') . ' diye chalao.');
        }
    }

    private function printHints(string $target): void
    {
        $this->newLine();
        $this->comment('Branch / warehouse change korte chaile command abar chalao:');
        $this->comment("  php artisan product:summary {$target} --branch=<id> --warehouse=<id>");
        $this->comment('Date range:  --from=2026-01-01 --to=2026-09-30   |   Branch wise: --by-branch (single product)');
        $this->comment('NET = Opening + Purchase + Adj Gain + Transfer In + Project In - Sale - Adj Loss/Damage - Transfer Out - Project Out - Consume');
    }
}