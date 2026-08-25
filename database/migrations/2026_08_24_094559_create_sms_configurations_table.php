<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('provider');                 // e.g. Alpha SMS, Bulk SMS BD
            $table->string('sender_id')->nullable();     // Sender Mask/ID
            $table->string('api_url');
            $table->text('api_key');                     // stored encrypted via model mutator
            $table->string('username')->nullable();
            $table->text('password')->nullable();        // stored encrypted via model mutator
            $table->boolean('enabled')->default(true);
            $table->decimal('last_known_balance', 12, 2)->nullable();
            $table->timestamp('balance_checked_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('sms_configurations');
    }
}
