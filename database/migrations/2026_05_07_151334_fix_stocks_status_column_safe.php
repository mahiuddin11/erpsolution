<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            ALTER TABLE `stocks`
            MODIFY COLUMN `status` ENUM(
                'Opening',
                'Purchase',
                'Manual Purchase',
                'Production Sale',
                'Production',
                'Production Out',
                'Sale',
                'Damage',
                'Lost',
                'Gain',
                'Others',
                'Transfer Out',
                'Transfer In',
                'Project',
                'Project In',
                'Project Out',
                'Project Use',
                'Return',
                'Sale Return',
                'Purchase Return'
            ) NOT NULL DEFAULT 'Purchase'
        ");
    }

    public function down()
    {

        DB::statement("
            ALTER TABLE `stocks`
            MODIFY COLUMN `status` ENUM(
                'Opening',
                'Purchase',
                'Manual Purchase',
                'Production Sale',
                'Production',
                'Production Out',
                'Sale',
                'Damage',
                'Lost',
                'Gain',
                'Others',
                'Transfer Out',
                'Transfer In',
                'Project',
                'Project In',
                'Project Out',
                'Project Use',
                'Return'
            ) NOT NULL DEFAULT 'Purchase'
        ");
    }
};
