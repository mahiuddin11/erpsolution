<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();

            $table->string('return_no')->unique();
            $table->foreignId('original_sale_id')->constrained('sales');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('branch_id')->constrained('branches');
            $table->unsignedBigInteger('backup_branch_id')->nullable();

            $table->date('return_date');
            $table->enum('return_type', ['full', 'partial'])->default('partial');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->enum('refund_method', ['none', 'cash_bank', 'credit_note'])->default('none');
            $table->foreignId('refund_account_id')->nullable()->constrained('chart_of_accounts');
            $table->enum('overall_condition', ['good', 'damaged', 'mixed'])->default('good');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['original_sale_id']);
            $table->index(['customer_id']);
            $table->index(['branch_id']);
            $table->index(['status']);
            $table->index(['return_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_returns');
    }
}
