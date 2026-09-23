<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseColumnsToTransfersAndTransferDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (['transfers', 'transfer_details'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'from_warehouse_id')) {
                    $table->unsignedBigInteger('from_warehouse_id')->nullable()->after('to_branch_id');
                }
                if (!Schema::hasColumn($tableName, 'to_warehouse_id')) {
                    $table->unsignedBigInteger('to_warehouse_id')->nullable()->after('from_warehouse_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (['transfers', 'transfer_details'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'from_warehouse_id')) {
                    $table->dropColumn('from_warehouse_id');
                }
                if (Schema::hasColumn($tableName, 'to_warehouse_id')) {
                    $table->dropColumn('to_warehouse_id');
                }
            });
        }
    }
}
