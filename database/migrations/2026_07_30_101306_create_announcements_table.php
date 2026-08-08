<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['public', 'department'])->default('public');
            // type='department' hole ei column e department name thakbe, 'public' hole null
            $table->string('department_id')->nullable();
            $table->date('expire_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
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
        Schema::dropIfExists('announcements');
    }
}
