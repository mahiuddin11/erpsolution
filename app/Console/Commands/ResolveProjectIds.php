<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResolveProjectIds extends Command
{
    protected $signature = 'project:resolve-ids
                            {--dry : Show what would happen without changing anything}
                            {--dry-run : Same as --dry}
                            {--fill-project-id : Before clearing, copy the original id into project_id (only ids that exist in projects)}
                            {--force : Skip the confirmation prompt}';

    protected $description = 'Rows with type = Project do not use branch / warehouse: branch_id = 0 and warehouse_id = NULL. '
        . 'backup_branch_id is never changed. project_id is not changed either, unless --fill-project-id is given. '
        . 'A CSV of the old values is written before anything changes. '
        . 'Safe to rerun: rows that are already clean are skipped.';

    /**
     * table => which rows are "project rows" (alias t).
     * Add a table here only after checking how it stores project ids.
     */
    protected array $tables = [
        'stock_summaries' => [
            'where' => "t.type = 'Project'",
        ],
    ];

    /** columns every listed table must have */
    private const REQUIRED = ['branch_id', 'warehouse_id', 'backup_branch_id', 'project_id'];

    public function handle()
    {
        $dryRun = (bool) ($this->option('dry') || $this->option('dry-run'));
        $fill   = (bool) $this->option('fill-project-id');
        $force  = (bool) $this->option('force');

        $this->info($dryRun ? 'DRY RUN — no database changes will be made.' : 'Starting project row cleanup...');
        $this->line('Tables to process: ' . implode(', ', array_keys($this->tables)));
        $this->newLine();

        /* ---------- plan ---------- */
        $plan = [];
        foreach ($this->tables as $tableName => $cfg) {
            $plan[$tableName] = $this->inspectTable($tableName, $cfg);
        }

        $this->table(
            ['Table', 'Columns', 'Rows with type = Project', 'Already clean (branch 0, warehouse NULL)', 'Rows to fix', 'Project id kept in project_id / backup_branch_id', 'Project id ONLY in branch_id'],
            collect($plan)->map(function ($p, $table) {
                if (!$p['columns_ok']) {
                    return [$table, 'MISSING: ' . implode(', ', $p['missing']), '-', '-', '-', '-', '-'];
                }
                return [$table, 'ok', $p['total'], $p['already_clean'], $p['to_change'], $p['id_kept'], $p['id_only_in_branch']];
            })->toArray()
        );

        $onlyInBranch = 0;

        foreach ($plan as $tableName => $p) {
            if (!$p['columns_ok']) {
                $this->error("{$tableName}: missing column(s): " . implode(', ', $p['missing']));
                continue;
            }

            $onlyInBranch += $p['id_only_in_branch'];

            if ($p['to_change'] > 0) {
                $this->showSample($tableName, $this->tables[$tableName]);
                $this->showFillPreview($tableName, $this->tables[$tableName]);
            }
        }

        if ($onlyInBranch > 0 && !$fill) {
            $this->newLine();
            $this->warn("WARNING: {$onlyInBranch} row(s) keep their project id ONLY in branch_id.");
            $this->warn('After the real run that id survives only in the CSV backup (project_id and backup_branch_id are empty on those rows).');
            $this->warn('Use --fill-project-id to copy it into project_id first (ids that exist in projects).');
        }

        if ($dryRun) {
            $this->newLine();
            $this->info('Dry run complete. No changes were made.');
            return self::SUCCESS;
        }

        if (collect($plan)->contains(fn($p) => !$p['columns_ok'])) {
            $this->error('Fix the missing columns above and rerun.');
            return self::FAILURE;
        }

        $processable = array_filter($plan, fn($p) => $p['to_change'] > 0);

        if (empty($processable)) {
            $this->info('Nothing to fix.');
            return self::SUCCESS;
        }

        $question = 'Apply the changes shown above? (take a database backup first)';
        if ($onlyInBranch > 0 && !$fill) {
            $question = "{$onlyInBranch} project id(s) will exist only in the CSV backup afterwards. Apply anyway? (take a database backup first)";
        }

        if (!$force && !$this->confirm($question)) {
            $this->warn('Aborted — no changes made.');
            return self::SUCCESS;
        }

        foreach ($processable as $tableName => $info) {
            $this->newLine();
            $this->info("Processing {$tableName}...");

            try {
                $this->resolve($tableName, $this->tables[$tableName], $fill);
                $this->info("Done: {$tableName}");
            } catch (\Throwable $e) {
                $this->error("Failed on {$tableName}: " . $e->getMessage());
                $this->warn('This table was rolled back. Earlier tables in this run (if any) are already committed. Fix the issue and rerun — clean rows are skipped, so rerunning is safe.');
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('All listed tables processed successfully.');

        return self::SUCCESS;
    }

    /* ======================================================================
     |  SQL pieces (alias t)
     * ====================================================================== */

    /** row still uses branch / warehouse */
    private function dirty(): string
    {
        return '(COALESCE(t.branch_id,0) <> 0 OR t.warehouse_id IS NOT NULL)';
    }

    /** the original project id, read only: backup_branch_id if set, else branch_id */
    private function orig(): string
    {
        return 'COALESCE(NULLIF(t.backup_branch_id,0), NULLIF(t.branch_id,0))';
    }

    /* ======================================================================
     |  Inspect
     * ====================================================================== */

    private function inspectTable(string $tableName, array $cfg): array
    {
        $missing = [];
        foreach (self::REQUIRED as $col) {
            if (!$this->columnExists($tableName, $col)) {
                $missing[] = $col;
            }
        }

        $empty = [
            'columns_ok' => empty($missing),
            'missing' => $missing,
            'total' => 0,
            'already_clean' => 0,
            'to_change' => 0,
            'id_kept' => 0,
            'id_only_in_branch' => 0,
        ];

        if (!empty($missing)) {
            return $empty;
        }

        $dirty = $this->dirty();

        $s = DB::selectOne("
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(NOT {$dirty}), 0) AS already_clean,
                COALESCE(SUM({$dirty}), 0)     AS to_change,
                COALESCE(SUM({$dirty}
                    AND (COALESCE(t.project_id,0) > 0 OR COALESCE(t.backup_branch_id,0) > 0)), 0) AS id_kept,
                COALESCE(SUM({$dirty}
                    AND COALESCE(t.project_id,0) = 0 AND COALESCE(t.backup_branch_id,0) = 0
                    AND COALESCE(t.branch_id,0) > 0), 0) AS id_only_in_branch
            FROM `{$tableName}` t
            WHERE {$cfg['where']}
        ");

        return [
            'columns_ok'        => true,
            'missing'           => [],
            'total'             => (int) $s->total,
            'already_clean'     => (int) $s->already_clean,
            'to_change'         => (int) $s->to_change,
            'id_kept'           => (int) $s->id_kept,
            'id_only_in_branch' => (int) $s->id_only_in_branch,
        ];
    }

    private function showSample(string $tableName, array $cfg): void
    {
        $rows = DB::table("{$tableName} as t")
            ->whereRaw($cfg['where'])
            ->whereRaw($this->dirty())
            ->orderBy('t.id')->limit(15)
            ->selectRaw("t.id, t.branch_id, t.warehouse_id, t.backup_branch_id, t.project_id,
                IF(COALESCE(t.project_id,0) > 0 OR COALESCE(t.backup_branch_id,0) > 0, 'yes', 'ONLY in branch_id') AS id_kept_elsewhere")
            ->get();

        $this->newLine();
        $this->line("{$tableName}: first rows that will change  (result: branch_id = 0, warehouse_id = NULL; backup_branch_id and project_id stay)");
        $this->table(
            ['id', 'branch_id', 'warehouse_id', 'backup_branch_id', 'project_id', 'id kept elsewhere?'],
            $rows->map(fn($r) => (array) $r)->all()
        );
    }

    /** what --fill-project-id would do (information only) */
    private function showFillPreview(string $tableName, array $cfg): void
    {
        $orig = $this->orig();

        $fillable = DB::selectOne("
            SELECT COUNT(*) AS c
            FROM `{$tableName}` t
            JOIN `projects` pr ON pr.id = {$orig}
            WHERE {$cfg['where']} AND COALESCE(t.project_id,0) = 0
        ")->c;

        $strangers = DB::select("
            SELECT {$orig} AS rid, COUNT(*) AS rows_count
            FROM `{$tableName}` t
            LEFT JOIN `projects` pr ON pr.id = {$orig}
            WHERE {$cfg['where']} AND COALESCE(t.project_id,0) = 0
              AND {$orig} IS NOT NULL AND pr.id IS NULL
            GROUP BY rid
        ");

        $this->newLine();
        $this->line("With --fill-project-id: project_id would be filled on {$fillable} row(s).");

        if (!empty($strangers)) {
            $this->warn('These ids are NOT in the projects table (project_id can not hold them, they are skipped by --fill-project-id):');
            $this->table(['id', 'rows'], array_map(fn($r) => (array) $r, $strangers));
        }
    }


    private function resolve(string $tableName, array $cfg, bool $fill): void
    {
        $dirty = $this->dirty();
        $orig  = $this->orig();

        // 1. keep the old values (rollback material) before touching anything
        $file = $this->writeBackupCsv($tableName, $cfg);
        $this->line("  old values saved to {$file}");

        DB::beginTransaction();

        try {
            // 2. optional: keep the project id in project_id (only ids that exist in projects, FK safe)
            if ($fill) {
                $filled = DB::update("
                    UPDATE `{$tableName}` t
                    JOIN `projects` pr ON pr.id = {$orig}
                    SET t.project_id = pr.id
                    WHERE {$cfg['where']} AND COALESCE(t.project_id,0) = 0
                ");
                $this->line("  project_id filled on {$filled} row(s)");
            }

            // 3. project rows do not use branch / warehouse. backup_branch_id and project_id are NOT touched here.
            $cleared = DB::update("
                UPDATE `{$tableName}` t
                SET t.branch_id = 0,
                    t.warehouse_id = NULL
                WHERE {$cfg['where']}
                  AND {$dirty}
            ");
            $this->line("  branch_id -> 0, warehouse_id -> NULL on {$cleared} row(s)");

            // 4. verify inside the transaction
            $left = DB::selectOne("
                SELECT COUNT(*) AS c FROM `{$tableName}` t
                WHERE {$cfg['where']} AND {$dirty}
            ")->c;

            if ((int) $left !== 0) {
                throw new \RuntimeException("{$left} row(s) still need a fix after the update — rolled back.");
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * CSV of the rows that will change, with their OLD branch_id / warehouse_id / backup_branch_id / project_id.
     * To roll back a row:  UPDATE stock_summaries SET branch_id=?, warehouse_id=? WHERE id=?
     */


    private function writeBackupCsv(string $tableName, array $cfg): string
    {
        $dir = storage_path('app/project-id-backup');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $file = $dir . '/' . $tableName . '_' . date('Ymd_His') . '.csv';
        $fh   = fopen($file, 'w');
        fputcsv($fh, ['id', 'branch_id', 'warehouse_id', 'backup_branch_id', 'project_id']);

        $query = DB::table("{$tableName} as t")
            ->whereRaw($cfg['where'])
            ->whereRaw($this->dirty())
            ->orderBy('t.id')
            ->select('t.id', 't.branch_id', 't.warehouse_id', 't.backup_branch_id', 't.project_id');

        foreach ($query->cursor() as $r) {
            fputcsv($fh, [$r->id, $r->branch_id, $r->warehouse_id, $r->backup_branch_id, $r->project_id]);
        }

        fclose($fh);

        return $file;
    }

    private function columnExists(string $table, string $column): bool
    {
        $row = DB::selectOne("
            SELECT COLUMN_NAME
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ", [$table, $column]);

        return (bool) $row;
    }
}
