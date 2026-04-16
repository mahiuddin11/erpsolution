<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdjustmentStartToLonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lones', function (Blueprint $table) {
            //
            $table->date('adjustment_start')->nullable()->after('lone_adjustment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lones', function (Blueprint $table) {
            //
            $table->dropColumn('adjustment_start');
        });
    }
}
