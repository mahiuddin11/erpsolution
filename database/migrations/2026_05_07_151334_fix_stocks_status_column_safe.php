<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

    /**
     * Rollback — নতুন যোগ করা values সরিয়ে আগের enum এ ফেরত।
     * 'Sale Return' বা 'Purchase Return' যদি কোনো row তে থাকে
     * তাহলে rollback fail করবে — তাই নিচে warning দেওয়া আছে।
     */
    public function down()
    {
        // ⚠️ rollback এর আগে নিশ্চিত করুন কোনো row তে
        // 'Sale Return' বা 'Purchase Return' নেই।
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
