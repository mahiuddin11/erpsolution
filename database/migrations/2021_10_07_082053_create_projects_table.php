<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable()->index();
            $table->string('projectCode', 15)->unique();
            $table->integer('manager_id')->nullable()->unsigned();
            $table->integer('branch_id')->nullable()->unsigned();
            $table->integer('customer_id')->nullable()->unsigned();
            $table->float('budget', 20, 2)->nullable();
            $table->float('estimate_profit', 20, 2)->nullable();
            // $table->float('received_amount', 20, 2)->nullable();
            $table->text('address')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Cancel'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
            $table->enum('condition', ['One Going', 'Complete'])->default('One Going')->comment('project status');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
