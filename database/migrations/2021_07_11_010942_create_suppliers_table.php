<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            // $table->string('business_name')->nullable();
            // $table->string('supplier_type')->nullable();
            // $table->integer('branch_id')->unsigned();
            $table->string('name', 100)->nullable();
            $table->string('supplierCode', 15);
            $table->string('email', 55)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('specialNumber', 20)->nullable();
            // $table->integer('city')->nullable();
            // $table->integer('state')->nullable();
            // $table->integer('country')->nullable();
            // $table->string('pay_term')->nullable();
            // $table->string('pay_term_type')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Cancel'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
            // $table->tinyInteger('status')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            // $table->index(['branch_id']);
            $table->softDeletes();
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
        Schema::dropIfExists('suppliers');
    }
}
