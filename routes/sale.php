<?php

use App\Http\Controllers\Backend\Sale\SaleController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    // Sale setup crud start
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Sale'], function () {
        //Sale crud operation start
     
        Route::get('/sale-sale-list', [SaleController::class, 'index'])->name('sale.sale.index');
        Route::get('/dataProcessingSale', [SaleController::class, 'dataProcessingSale'])->name('sale.sale.dataProcessingSale');
        

        Route::get('/sale-sale-create', [SaleController::class, 'create'])->name('sale.sale.create');

        Route::post('/sale-sale-store', 'SaleController@store')->name('sale.sale.store');
        Route::get('/sale-sale-edit/{id}', 'SaleController@edit')->name('sale.sale.edit');
       
        Route::get('/sale-sale-show/{id}',[SaleController::class, 'show'])->name('sale.sale.show');


        Route::get('/sale-sale-challan/{id}', 'SaleController@challan')->name('sale.sale.challan');
        Route::post('/sale-sale-update/{id}', 'SaleController@update')->name('sale.sale.update');
        Route::get('/sale-sale-delete/{id}', 'SaleController@destroy')->name('sale.sale.destroy');
        Route::get('/sale-sale-status/{id}/{status}', 'SaleController@statusUpdate')->name('sale.sale.status');
        Route::get('/getProductListForSale', 'SaleController@getProductListForSale')->name('sale.sale.getProductListForSale');
        Route::get('/unitPiceForSale', 'SaleController@unitPiceForSale')->name('sale.sale.unitPiceForSale');
        Route::post('/sale-sale-quiceAddCustomer', 'SaleController@quiceAddCustomer')->name('sale.sale.quiceAddCustomer');

        //Sale crud operation end
        Route::get('/getCustomerBalance', 'SaleController@getCustomerBalance')->name('sale.sale.getCustomerBalance');
        Route::get('/getProductStock', 'SaleController@getProductStock')->name('sale.sale.getProductStock');
        Route::get('/saleunitPrice', 'SaleController@unitPrice')->name('sale.sale.saleunitPrice');

        // delivery chalan start
        Route::get('/sale-challan-list', 'DeliveryChalanController@index')->name('sale.challan.index');
        Route::get('/dataProcessingChallan', 'DeliveryChalanController@dataProcessingChallan')->name('sale.challan.dataProcessingChallan');
        Route::get('/sale-challan-create', 'DeliveryChalanController@create')->name('sale.challan.create');
        Route::post('/sale-challan-store', 'DeliveryChalanController@store')->name('sale.challan.store');
        Route::get('/sale-challan-edit/{id}', 'DeliveryChalanController@edit')->name('sale.challan.edit');
        Route::get('/sale-challan-show/{id}', 'DeliveryChalanController@show')->name('sale.challan.show');
        Route::post('/sale-challan-update/{id}', 'DeliveryChalanController@update')->name('sale.challan.update');
        Route::get('/sale-challan-delete/{id}', 'DeliveryChalanController@destroy')->name('sale.challan.destroy');
        Route::get('/sale-challan-status/{id}/{status}', 'DeliveryChalanController@statusUpdate')->name('sale.challan.status');
        // delivery chalan start
        Route::get('/salesDetails', 'DeliveryChalanController@salesDetails')->name('sale.challan.salesDetails');
    });
});
