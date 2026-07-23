<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_billings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('invoice_no', 30);
            $table->date('billing_date');
            $table->string('milestone_name', 150)->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Paid'])->default('Draft');
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->index('project_id');
            $table->unique('invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_billings');
    }
}
