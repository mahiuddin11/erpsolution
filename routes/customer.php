<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Payment'], function () {
        Route::get('/payment-customer-list', 'CustomerPaymentContorller@index')->name('payment.customer.index');
        Route::get('/dataProcessingCustomerPayment', 'CustomerPaymentContorller@dataProcessingCustomerPayment')->name('payment.customer.dataProcessingCustomerPayment');
        Route::get('/payment-customer-create', 'CustomerPaymentContorller@create')->name('payment.customer.create');
        Route::post('/payment-customer-store', 'CustomerPaymentContorller@store')->name('payment.customer.store');
        Route::get('/payment-customer-edit/{id}', 'CustomerPaymentContorller@edit')->name('payment.customer.edit');
        Route::get('/payment-customer-show/{id}', 'CustomerPaymentContorller@show')->name('payment.customer.show');
        Route::post('/payment-customer-update/{id}', 'CustomerPaymentContorller@update')->name('payment.customer.update');
        Route::get('/payment-customer-delete/{id}', 'CustomerPaymentContorller@destroy')->name('payment.customer.destroy');
        Route::get('/payment-customer-status/{id}/{status}', 'CustomerPaymentContorller@statusUpdate')->name('payment.customer.status');
        Route::get('/dueInvoiceAmount', 'CustomerPaymentContorller@dueInvoiceAmount')->name('payment.customer.dueInvoiceAmount');
        Route::get('/getCustomerDetails', 'CustomerPaymentContorller@getCustomerDetails')->name('payment.customer.getCustomerDetails');
        Route::get('/getAllBranchCustomeList', 'CustomerPaymentContorller@getAllBranchCustomeList')->name('payment.customer.getAllBranchCustomeList');
        Route::get('/getAllDueInvoiceList', 'CustomerPaymentContorller@getAllDueInvoiceList')->name('payment.customer.getAllDueInvoiceList');
    });
});
