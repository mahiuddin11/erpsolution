<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectPocSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_poc_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->date('period_date')->comment('as-of date, usually month/quarter/year end');
            $table->decimal('estimate_cost', 15, 2);
            $table->decimal('actual_cost_to_date', 15, 2);
            $table->decimal('percent_complete', 5, 2);
            $table->decimal('recognized_revenue', 15, 2);
            $table->decimal('recognized_profit', 15, 2);
            $table->decimal('billed_to_date', 15, 2)->default(0.00);
            $table->decimal('wip_amount', 15, 2)->default(0.00)
                ->comment('positive = unbilled revenue (asset), negative = billing in excess (liability)');
            $table->unsignedBigInteger('journal_voucher_id')->nullable()
                ->comment('link to the posted JV for this period, if posted');
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->index('project_id');
            $table->index('period_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_poc_snapshots');
    }
}
