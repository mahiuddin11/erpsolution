<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedByAndNoteToLonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lones', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->text('note')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lones', function (Blueprint $table) {
            //
            $table->dropColumn(['approved_by', 'note']);
        });
    }
}
