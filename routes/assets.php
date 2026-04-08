<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    // Inventory setup crud start
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'AssetsManagement'], function () {


        //Assets Category crud operation start
        Route::get('/assets-category-list', 'AssetsCategoryController@index')->name('assets.category.index');
        Route::get('/dataProcessingCategory', 'AssetsCategoryController@dataProcessingUnit')->name('assets.category.dataProcessingCategory');
        Route::get('/assets-category-create', 'AssetsCategoryController@create')->name('assets.category.create');
        Route::post('/assets-category-store', 'AssetsCategoryController@store')->name('assets.category.store');
        Route::get('/assets-category-edit/{id}', 'AssetsCategoryController@edit')->name('assets.category.edit');
        Route::get('/assets-category-show/{id}', 'AssetsCategoryController@show')->name('assets.category.show');
        Route::post('/assets-category-update/{id}', 'AssetsCategoryController@update')->name('assets.category.update');
        Route::get('/assets-category-delete/{id}', 'AssetsCategoryController@destroy')->name('assets.category.destroy');
        Route::get('/assets-category-status/{id}/{status}', 'AssetsCategoryController@statusUpdate')->name('assets.category.status');
        //Assets Category crud operation end

        //Assets List crud operation start
        Route::get('/assets-list', 'AssetsListController@index')->name('assets.list.index');
        Route::get('/dataProcessingAssetsList', 'AssetsListController@dataProcessingAssetsList')->name('assets.list.dataProcessingList');
        Route::get('/assets-list-create', 'AssetsListController@create')->name('assets.list.create');
        Route::post('/assets-list-store', 'AssetsListController@store')->name('assets.list.store');
        Route::get('/assets-list-edit/{id}', 'AssetsListController@edit')->name('assets.list.edit');
        Route::get('/assets-list-show/{id}', 'AssetsListController@show')->name('assets.list.show');
        Route::post('/assets-list-update/{id}', 'AssetsListController@update')->name('assets.list.update');
        Route::get('/assets-list-delete/{id}', 'AssetsListController@destroy')->name('assets.list.destroy');
        Route::get('/assets-list-status/{id}/{status}', 'AssetsListController@statusUpdate')->name('assets.list.status');
        //Assets List crud operation end

        //Assets List crud operation start
        Route::get('/assets-destroy-items', 'AssetsDestroyController@index')->name('assets.destroy.index');
        Route::get('/dataProcessingAssetDestroy', 'AssetsDestroyController@dataProcessingAssetsDestroy')->name('assets.destroy.dataProcessingDestroy');
        Route::get('/assets-destroy-create', 'AssetsDestroyController@create')->name('assets.destroy.create');
        Route::post('/assets-destroy-store', 'AssetsDestroyController@store')->name('assets.destroy.store');
        Route::get('/assets-destroy-edit/{id}', 'AssetsDestroyController@edit')->name('assets.destroy.edit');
        Route::get('/assets-destroy-show/{id}', 'AssetsDestroyController@show')->name('assets.destroy.show');
        Route::post('/assets-destroy-update/{id}', 'AssetsDestroyController@update')->name('assets.destroy.update');
        Route::get('/assets-destroy-delete/{id}', 'AssetsDestroyController@destroy')->name('assets.destroy.destroy');
        Route::get('/assets-destroy-status/{id}/{status}', 'AssetsDestroyController@statusUpdate')->name('assets.destroy.status');
        //Assets List crud operation end

        //Financial year crud operation start
        Route::get('/finacial-year-list', 'FinancialYearController@index')->name('financial.year.index');
        Route::get('/dataProcessingFinancialYear', 'FinancialYearController@dataProcessingFinacialYear')->name('financial.year.dataProcessing');
        Route::get('/finacial-year-create', 'FinancialYearController@create')->name('financial.year.create');
        Route::post('/financial-year-store', 'FinancialYearController@store')->name('financial.year.store');
        Route::get('/financial-year-edit/{id}', 'FinancialYearController@edit')->name('financial.year.edit');
        Route::get('/financial-year-show/{id}', 'FinancialYearController@show')->name('financial.year.show');
        Route::post('/financial-year-update/{id}', 'FinancialYearController@update')->name('financial.year.update');
        Route::get('/financial-year-delete/{id}', 'FinancialYearController@destroy')->name('financial.year.destroy');
        Route::get('/financial-year-status/{id}/{status}', 'FinancialYearController@statusUpdate')->name('financial.year.status');
        //Assets List crud operation end

        //Asset Warranty crud operation start
        Route::get('/asset-warranty-list', 'AssetsWarrantyController@index')->name('assets.warranty.index');
        Route::get('/dataProcessingWarranty', 'AssetsWarrantyController@dataProcessingWarranty')->name('assets.warranty.dataProcessingWarranty');
        Route::get('/assets-warranty-create', 'AssetsWarrantyController@create')->name('assets.warranty.create');
        Route::post('/assets-warranty-store', 'AssetsWarrantyController@store')->name('assets.warranty.store');
        Route::get('/assets-warranty-edit/{id}', 'AssetsWarrantyController@edit')->name('assets.warranty.edit');
        Route::get('/assets-warranty-show/{id}', 'AssetsWarrantyController@show')->name('assets.warranty.show');
        Route::post('/assets-warranty-update/{id}', 'AssetsWarrantyController@update')->name('assets.warranty.update');
        Route::get('/assets-warranty-delete/{id}', 'AssetsWarrantyController@destroy')->name('assets.warranty.destroy');
        Route::get('/assets-warranty-status/{id}/{status}', 'AssetsWarrantyController@statusUpdate')->name('asset.warranty.status');
        //Assets Warranty crud operation end
    });
    // Inventory setup crud end
});
