<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_reqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('amount');
            $table->string('reason')->nullable();
            $table->foreignId('approve_by')->nullable();
            $table->enum('status', ['approved', 'pending', 'cancel', 'completed'])->default('Pending');
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
        Schema::dropIfExists('cash_reqs');
    }
}
