<?php

namespace App\Providers;

use App\Models\ContraVoucher;
use App\Models\CreditVoucher;
use App\Models\DabitVoucher;
use App\Models\JournalVoucher;
use App\Models\Purchases;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'sale'     => Sale::class,
            'purchase' => Purchases::class,
        ]);
    }
}
