<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoiceCode', 10)->uniqid();
            $table->date('date')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('account_id')->nullable();
            $table->longText('note')->nullable();
            $table->float('profit', 20, 2)->nullable();
            $table->float('total_value', 20, 2)->nullable();
            $table->float('collect', 20, 2)->nullable();
            $table->enum('status', ['Pending', 'Done', 'Partial'])->default('Pending')->comment('default status set Penidng , Active status waiting for approbal');
            $table->integer('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
