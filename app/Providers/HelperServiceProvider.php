<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path('/Helpers/CustomHrmHelper.php');
        require_once app_path('/Helpers/SuperHelper.php');
                require_once app_path('/Helpers/ZktecoMatching.php');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
