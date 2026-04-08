<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchasetypeToProjectTransferDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_transfer_details', function (Blueprint $table) {
            $table->enum('purchasetype', ['local', 'imported'])->comment('Product purchase local or imported');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_transfer_details', function (Blueprint $table) {
            //
        });
    }
}
