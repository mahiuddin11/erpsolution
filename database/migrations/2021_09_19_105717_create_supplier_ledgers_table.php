<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_ledgers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('purchase_id')->nullable();
            $table->integer('supplier_id')->nullable();
            // $table->integer('account_branch_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('payment_type', 120)->nullable();
            $table->integer('account_id')->nullable();
            $table->float('debit', 10, 2)->nullable();
            $table->float('credit', 10, 2)->nullable();
            // $table->float('amount', 10, 2)->nullable();
            // $table->float('total_pay', 10, 2)->nullable();
            // $table->float('total_due', 10, 2)->nullable();
            // $table->float('discount', 10, 2)->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_ledgers');
    }
}
