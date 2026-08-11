<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReminingQtyToPrDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pr_details', function (Blueprint $table) {
            //code..
            $table->decimal('remaining_qty', 15, 2)->nullable()->default(null)->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pr_details', function (Blueprint $table) {
            //code..
            $table->dropColumn('remaining_qty');
        });
    }
}
