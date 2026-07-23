<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterProjectsTableForPoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('estimate_cost', 15, 2)->nullable()->after('budget');
        });

        DB::statement('ALTER TABLE `projects` MODIFY COLUMN `budget` DECIMAL(15,2) DEFAULT NULL');
        DB::statement('ALTER TABLE `projects` MODIFY COLUMN `estimate_profit` DECIMAL(15,2) DEFAULT NULL');

        DB::statement("ALTER TABLE `projects` MODIFY COLUMN `condition` 
            ENUM('One Going','On Hold','Over Budget','Complete','Closed') 
            NOT NULL DEFAULT 'One Going' COMMENT 'project execution status'");

        DB::statement('UPDATE `projects` 
            SET `estimate_cost` = `budget` - `estimate_profit` 
            WHERE `estimate_cost` IS NULL AND `estimate_profit` IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('estimate_cost');
        });

        DB::statement('ALTER TABLE `projects` MODIFY COLUMN `budget` DOUBLE(20,2) DEFAULT NULL');
        DB::statement('ALTER TABLE `projects` MODIFY COLUMN `estimate_profit` DOUBLE(20,2) DEFAULT NULL');

        DB::statement("ALTER TABLE `projects` MODIFY COLUMN `condition` 
            ENUM('One Going','Complete') 
            NOT NULL DEFAULT 'One Going' COMMENT 'project status'");
    }
}
