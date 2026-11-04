<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         $row = DB::selectOne("SHOW COLUMNS FROM `account_transactions` LIKE 'type'");
        preg_match_all("/'([^']*)'/", $row->Type, $matches);
        $values = $matches[1];

        if (in_array('purchase_return', $values, true)) {
            return; // already present — nothing to do
        }

        $values[] = 'purchase_return';
        $list = "'" . implode("','", $values) . "'";
        DB::statement("ALTER TABLE `account_transactions` MODIFY COLUMN `type` ENUM($list) NOT NULL");
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            //
        });
    }
};
