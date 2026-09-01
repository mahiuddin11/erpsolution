<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangePriceColumnsInGrnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grn_details', function (Blueprint $table) {
            //
            DB::statement('ALTER TABLE grn_details MODIFY unit_price DECIMAL(15,2) NULL');
            DB::statement('ALTER TABLE grn_details MODIFY total_price DECIMAL(15,2) NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grn_details', function (Blueprint $table) {
            //
            DB::statement('ALTER TABLE grn_details MODIFY unit_price INT NULL');
            DB::statement('ALTER TABLE grn_details MODIFY total_price INT NULL');
        });
    }
}
