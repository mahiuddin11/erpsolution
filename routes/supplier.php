<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Payment'], function () {
        Route::get('/payment-supplier-list', 'SupplierPaymentContorller@index')->name('payment.supplier.index');
        Route::get('/dataProcessingSupplierPayment', 'SupplierPaymentContorller@dataProcessingSupplierPayment')->name('payment.supplier.dataProcessingSupplierPayment');
        Route::get('/payment-supplier-create', 'SupplierPaymentContorller@create')->name('payment.supplier.create');
        Route::post('/payment-supplier-store', 'SupplierPaymentContorller@store')->name('payment.supplier.store');
        Route::get('/payment-supplier-edit/{id}', 'SupplierPaymentContorller@edit')->name('payment.supplier.edit');
        Route::get('/payment-supplier-show/{id}', 'SupplierPaymentContorller@show')->name('payment.supplier.show');
        Route::post('/payment-supplier-update/{id}', 'SupplierPaymentContorller@update')->name('payment.supplier.update');
        Route::get('/payment-supplier-delete/{id}', 'SupplierPaymentContorller@destroy')->name('payment.supplier.destroy');
        Route::get('/payment-supplier-status/{id}/{status}', 'SupplierPaymentContorller@statusUpdate')->name('payment.supplier.status');
        Route::get('/dueInvoiceammountsupplier', 'SupplierPaymentContorller@dueInvoiceammountsupplier')->name('payment.supplier.dueInvoiceammountsupplier');
        Route::get('/getSupplierdetails', 'SupplierPaymentContorller@getSupplierdetails')->name('payment.supplier.getSupplierdetails');

        Route::get('/getAllSupplierList', 'SupplierPaymentContorller@getAllSupplierList')->name('payment.supplier.getAllSupplierList');
        Route::get('/getAllSuppDueInvoiceList', 'SupplierPaymentContorller@getAllSuppDueInvoiceList')->name('payment.supplier.getAllSuppDueInvoiceList');
    });
});
