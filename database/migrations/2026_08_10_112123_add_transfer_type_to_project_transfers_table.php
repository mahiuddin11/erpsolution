<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTransferTypeToProjectTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('project_transfers', 'transfer_type')) {
            Schema::table('project_transfers', function (Blueprint $table) {
                $table->enum('transfer_type', ['branch_to_project', 'project_to_project', 'project_to_branch'])
                    ->default('branch_to_project')
                    ->after('id');
            });
        }

        if (!Schema::hasColumn('project_transfers', 'to_project_id')) {
            Schema::table('project_transfers', function (Blueprint $table) {
                $table->unsignedBigInteger('to_project_id')->nullable()->after('project_id');
            });
        } else {
            DB::statement("ALTER TABLE project_transfers MODIFY to_project_id BIGINT UNSIGNED NULL");
        }

        $fkExists = collect(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'project_transfers'
              AND COLUMN_NAME = 'to_project_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->isNotEmpty();

        if (!$fkExists) {
            DB::statement("ALTER TABLE project_transfers ADD CONSTRAINT project_transfers_to_project_id_foreign FOREIGN KEY (to_project_id) REFERENCES projects(id) ON DELETE SET NULL");
        }

        DB::statement("ALTER TABLE project_transfers MODIFY purchase_requisition_id INT UNSIGNED NULL");

        DB::table('project_transfers')->update(['transfer_type' => 'branch_to_project']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_transfers', function (Blueprint $table) {
            $table->dropForeign(['to_project_id']);
            $table->dropColumn(['transfer_type', 'to_project_id']);
        });

        DB::statement("ALTER TABLE project_transfers MODIFY purchase_requisition_id INT UNSIGNED NOT NULL");
    }
}
