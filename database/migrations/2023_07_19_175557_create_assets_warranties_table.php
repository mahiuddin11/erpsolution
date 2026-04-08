<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsWarrantiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets_warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assetlist_id');
            $table->string('form_date');
            $table->string('to_date');
            $table->string('desc');
            $table->enum('type', ['guarantee', 'warranty', 'both']);
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
        Schema::dropIfExists('assets_warranties');
    }
}
