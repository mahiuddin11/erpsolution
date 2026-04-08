<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // $customerType=array('Corporate','Local','Hole Salar','Others');
    // $customer->customer_type=$customerType[rand(0,3)];

    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->nullable();
            $table->foreignId('customergroup_id');
            $table->string('customer_type')->nullable();
            $table->integer('branch_id')->unsigned();
            $table->string('customerCode', 15);
            $table->string('name', 100)->nullable();
            $table->string('email', 55)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('bin')->nullable();
            $table->text('address')->nullable();
            $table->integer('city')->nullable();
            $table->integer('state')->nullable();
            $table->integer('country')->nullable();
            $table->string('pay_term')->nullable();
            $table->string('pay_term_type')->nullable();
            $table->string('co_name')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Cancel'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
            // $table->tinyInteger('status')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['branch_id']);

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
        Schema::dropIfExists('customers');
    }
}
