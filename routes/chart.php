<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    // report  start
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Chart'], function () {
        Route::get('/chart/chart', 'ChartController@chart');
        Route::get('google-pie-chart', 'ChartController@googlePieChart');
        Route::get('chartjs', 'ChartController@index')->name('chartjs.index');
    });
});
