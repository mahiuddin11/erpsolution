<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('branch_id');
            $table->string('amount');
            $table->string('file')->nullable();
            $table->double('lone_adjustment', 10, 2);
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('lones');
    }
}
