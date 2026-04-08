<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarySheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->date('month');
            $table->float('salary')->default('0')->nullable();
            $table->float('paid_amount')->default('0')->nullable();
            $table->float('overtime')->default('0')->nullable();
            $table->float('incentive')->default('0')->nullable();
            $table->float('bonus')->default('0')->nullable();
            $table->float('due')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('type');
            $table->longText('reason')->nullable();
            $table->foreignId('create_by')->nullable();
            $table->foreignId('update_by')->nullable();
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
        Schema::dropIfExists('salary_sheets');
    }
}
