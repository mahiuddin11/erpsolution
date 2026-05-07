<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {

            // যদি status কলাম থাকে তাহলে Drop করো
            if (Schema::hasColumn('stocks', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('stocks', function (Blueprint $table) {

            $table->enum('status', [
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
            ])->default('Purchase');
        });
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {

            if (Schema::hasColumn('stocks', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->enum('status', [
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
            ])->default('Purchase');
        });
    }
};
