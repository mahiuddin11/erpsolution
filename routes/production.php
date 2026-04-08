<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    // Sale setup crud start
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Production'], function () {
        //Sale crud operation start
        Route::get('/production-production-list', 'ProductionController@index')->name('production.production.index');
        Route::get('/dataProcessingProduction', 'ProductionController@dataProcessingProduction')->name('production.production.dataProcessingProduction');
        Route::get('/production-production-create', 'ProductionController@create')->name('production.production.create');
        Route::post('/production-production-store', 'ProductionController@store')->name('production.production.store');
        Route::get('/production-production-edit/{id}', 'ProductionController@edit')->name('production.production.edit');
        Route::get('/production-production-show/{id}', 'ProductionController@show')->name('production.production.show');
        Route::post('/production-production-update/{id}', 'ProductionController@update')->name('production.production.update');
        Route::get('/production-production-delete/{id}', 'ProductionController@destroy')->name('production.production.destroy');
        Route::get('/production-production-status/{id}/{status}', 'ProductionController@statusUpdate')->name('production.production.status');

        // new production
        Route::get('/getProductListForThisBranchWise', 'ProductionController@getProductListForThisBranchWise')->name('production.production.getProductListForThisBranchWise');
        Route::get('/getCurrentStockAndRateofThisProduct', 'ProductionController@getCurrentStockAndRateofThisProduct')->name('production.production.getCurrentStockAndRateofThisProduct');
        Route::get('/getToProPrice', 'ProductionController@getToProPrice')->name('production.production.getToProPrice');
        Route::get('/purchaseDetailsByProduct', 'ProductionController@purchaseDetailsByProduct')->name('production.production.purchaseDetailsByProduct');
    });
});
