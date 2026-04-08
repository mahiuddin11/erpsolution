<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountIdToSupplierSelectPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_select_prices', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('account_id')->nullable()->after('supplier_id');
            $table->unsignedBigInteger('customer_id')->nullable()->after('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_select_prices', function (Blueprint $table) {
            //
            $table->dropColumn('account_id');
            $table->dropColumn('customer_id');
        });
    }
}
