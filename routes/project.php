<?php

use App\Http\Controllers\Backend\Project\ProjectController;
use Illuminate\Support\Facades\Route;



Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    // project setup crud start
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Project'], function () {

        //project crud operation start
        Route::get('/project-project-list', [ProjectController::class, 'index'])->name('project.project.index');
        Route::get('/dataProcessingProject', [ProjectController::class, 'dataProcessingProject'])->name('project.project.dataProcessingProject');
        Route::get('/project-project-create', [ProjectController::class, 'create'])->name('project.project.create');
        Route::post('/project-project-store', [ProjectController::class, 'store'])->name('project.project.store');
        Route::get('/project-project-edit/{id}', [ProjectController::class,'edit'])->name('project.project.edit');
        Route::get('/project-project-show/{id}', [ProjectController::class,'show'])->name('project.project.show');

        
        Route::get('/project-project-loadmanager', [ProjectController::class, 'loadmanager'])->name('project.project.loadmanager');
        Route::post('/project-project-complete', [ProjectController::class, 'complete'])->name('project.project.complete');
        Route::post('/project-project-update/{id}', [ProjectController::class, 'update'])->name('project.project.update');
        Route::get('/project-project-delete/{id}', [ProjectController::class, 'destroy'])->name('project.project.destroy');
        Route::get('/project-project-status/{id}/{status}', [ProjectController::class, 'statusUpdate'])->name('project.project.status');
        //project crud operation end

        //project Balance crud operation start
        Route::get('/project-balance-list', 'ProjectMoneyController@index')->name('project.balance.index');
        Route::get('/dataProcessingBalance', 'ProjectMoneyController@dataProcessingBalance')->name('project.balance.dataProcessingBalance');
        Route::get('/project-balance-create', 'ProjectMoneyController@create')->name('project.balance.create');
        Route::post('/project-balance-store', 'ProjectMoneyController@store')->name('project.balance.store');
        Route::get('/project-balance-edit/{id}', 'ProjectMoneyController@edit')->name('project.balance.edit');
        Route::get('/project-balance-show/{id}', 'ProjectMoneyController@show')->name('project.balance.show');
        Route::post('/project-balance-update/{id}', 'ProjectMoneyController@update')->name('project.balance.update');
        Route::get('/project-balance-delete/{id}', 'ProjectMoneyController@destroy')->name('project.balance.destroy');
        Route::get('/project-balance-status/{id}/{status}', 'ProjectMoneyController@statusUpdate')->name('project.balance.status');
        //project Balance crud operation end

        //project expense  crud operation start
        Route::get('/project-projectexpense-list', 'ProjectExpenseController@index')->name('project.projectexpense.index');
        Route::get('/dataProcessingProjectExpense', 'ProjectExpenseController@dataProcessingProjectExpense')->name('project.projectexpense.dataProcessingProjectExpense');
        Route::get('/project-projectexpense-create', 'ProjectExpenseController@create')->name('project.projectexpense.create');
        Route::post('/project-projectexpense-store', 'ProjectExpenseController@store')->name('project.projectexpense.store');
        Route::get('/project-projectexpense-edit/{id}', 'ProjectExpenseController@edit')->name('project.projectexpense.edit');
        Route::get('/project-projectexpense-show/{id}', 'ProjectExpenseController@show')->name('project.projectexpense.show');
        Route::post('/project-projectexpense-update/{id}', 'ProjectExpenseController@update')->name('project.projectexpense.update');
        Route::get('/project-projectexpense-delete/{id}', 'ProjectExpenseController@destroy')->name('project.projectexpense.destroy');
        Route::get('/project-projectexpense-status/{id}/{status}', 'ProjectExpenseController@statusUpdate')->name('project.projectexpense.status');
        //project expense crud operation end

        //project requisition  crud operation start
        Route::get('/project-Productrequisition-list', 'ProjectRequisitionController@index')->name('project.Productrequisition.index');
        Route::get('/dataProcessingrequisition', 'ProjectRequisitionController@dataProcessingrequisition')->name('project.Productrequisition.dataProcessingrequisition');
        Route::get('/project-Productrequisition-create', 'ProjectRequisitionController@create')->name('project.Productrequisition.create');
        Route::post('/project-Productrequisition-store', 'ProjectRequisitionController@store')->name('project.Productrequisition.store');
        Route::get('/project-Productrequisition-edit/{id}', 'ProjectRequisitionController@edit')->name('project.Productrequisition.edit');
        Route::get('/project-Productrequisition-show/{id}', 'ProjectRequisitionController@show')->name('project.Productrequisition.show');
        Route::post('/project-Productrequisition-update/{id}', 'ProjectRequisitionController@update')->name('project.Productrequisition.update');
        Route::get('/project-Productrequisition-delete/{id}', 'ProjectRequisitionController@destroy')->name('project.Productrequisition.destroy');
        Route::get('/project-Productrequisition-status/{id}/{status}', 'ProjectRequisitionController@statusUpdate')->name('project.Productrequisition.status');
        //project requisition crud operation end

        //transferproject  crud operation start
        Route::get('/project-transferproject-list', 'ProjectTransferController@index')->name('project.transferproject.index');
        Route::get('/dataProcessingtransferproject', 'ProjectTransferController@dataProcessing')->name('project.transferproject.dataProcessing');
        Route::post('/project-transferproject-store', 'ProjectTransferController@store')->name('project.transferproject.store');
        Route::get('/project-transferproject-searchpr', 'ProjectTransferController@searchpr')->name('project.transferproject.searchpr');
        Route::get('/project-transferproject-create', 'ProjectTransferController@create')->name('project.transferproject.create');
        Route::get('/project-transferproject-edit/{id}', 'ProjectTransferController@edit')->name('project.transferproject.edit');
        Route::post('/project-transferproject-update/{id}', 'ProjectTransferController@update')->name('project.transferproject.update');
        Route::get('/project-transferproject-delete/{id}', 'ProjectTransferController@destroy')->name('project.transferproject.destroy');
        Route::get('/project-transferproject-filterproduct', 'ProjectTransferController@filterproduct')->name('project.transferproject.filterproduct');
        Route::get('/project-transferproject-invoice/{id}', 'ProjectTransferController@invoice')->name('project.transferproject.invoice');
        //transferproject  crud operation end

        //project requisition approve crud operation start
        Route::get('/project-RequisitionAction-list', 'ProjectRequisitionController@actionindex')->name('project.RequisitionAction.index');
        Route::get('/dataProcessingRequisitionAction', 'ProjectRequisitionController@dataProcessingRequisitionAction')->name('project.RequisitionAction.dataProcessingRequisitionAction');
        Route::get('/checkstock', 'ProjectRequisitionController@checkstock')->name('project.RequisitionAction.checkstock');
        Route::get('/project-RequisitionAction-approve/{id}', 'ProjectRequisitionController@approve')->name('project.RequisitionAction.approve');
        Route::post('/project-RequisitionAction-storeapprove/{id}', 'ProjectRequisitionController@storeapprove')->name('project.RequisitionAction.storeapprove');
        Route::get('/project-RequisitionAction-destroy/{id}', 'ProjectRequisitionController@destroy')->name('project.RequisitionAction.destroy');
        //project requisition  approve crud operation end

        //Use Product crud operation start
        Route::get('/project-productuse-list', 'ProductUseController@index')->name('project.productuse.index');
        Route::get('/dataProcessinguseprj', 'ProductUseController@dataProcessingusepro')->name('project.productuse.dataProcessinguseprj');
        Route::get('/project-productuse-create', 'ProductUseController@create')->name('project.productuse.create');
        Route::get('/project-productuse-searchpu', 'ProductUseController@searchpu')->name('project.productuse.searchpu');
        Route::post('/project-productuse-store', 'ProductUseController@store')->name('project.productuse.store');
        Route::get('/project-productuse-getstockdata', 'ProductUseController@getstockdata')->name('project.productuse.getstockdata');
        Route::get('/project-productuse-edit/{id}', 'ProductUseController@edit')->name('project.productuse.edit');
        Route::get('/project-productuse-show/{id}', 'ProductUseController@show')->name('project.productuse.show');
        Route::post('/project-productuse-update/{id}', 'ProductUseController@update')->name('project.productuse.update');
        Route::get('/project-productuse-delete/{id}', 'ProductUseController@destroy')->name('project.productuse.destroy');
        Route::get('/project-productuse-status/{id}/{status}', 'ProductUseController@statusUpdate')->name('project.productuse.status');
        //Use Product crud operation end

        //Product return crud operation start
        Route::get('/project-projectreturn-list', 'ProjectReturnController@index')->name('project.projectreturn.index');
        Route::get('/dataProcessingProductReturn', 'ProjectReturnController@dataProcessingProductReturn')->name('project.projectreturn.dataProcessingProductReturn');
        Route::get('/project-projectreturn-create', 'ProjectReturnController@create')->name('project.projectreturn.create');
        Route::get('/project-projectreturn-searchproduct', 'ProjectReturnController@searchproduct')->name('project.projectreturn.searchproduct');
        Route::get('/project-projectreturn-getstockdata', 'ProductUseController@getstockdata')->name('project.projectreturn.getstockdata');
        Route::post('/project-projectreturn-store', 'ProjectReturnController@store')->name('project.projectreturn.store');
        Route::get('/project-projectreturn-edit/{id}', 'ProjectReturnController@edit')->name('project.projectreturn.edit');
        Route::post('/project-projectreturn-approve', 'ProjectReturnController@approve')->name('project.projectreturn.approve');
        Route::get('/project-projectreturn-show/{id}', 'ProjectReturnController@show')->name('project.projectreturn.show');
        Route::post('/project-projectreturn-update/{id}', 'ProjectReturnController@update')->name('project.projectreturn.update');
        Route::get('/project-projectreturn-delete/{id}', 'ProjectReturnController@destroy')->name('project.projectreturn.destroy');
        Route::get('/project-projectreturn-status/{id}/{status}', 'ProjectReturnController@statusUpdate')->name('project.projectreturn.status');
        //Use Product crud operation end

        //project incoive crud operation start
        Route::get('/project-invoiceCreate-list', 'InvoiceController@index')->name('project.invoiceCreate.index');
        Route::get('/dataProcessingInvoiceCreate', 'InvoiceController@dataProcessingInvoiceCreate')->name('project.invoiceCreate.dataProcessingInvoiceCreate');
        Route::get('/project-invoiceCreate-create', 'InvoiceController@create')->name('project.invoiceCreate.create');
        Route::post('/project-invoiceCreate-store', 'InvoiceController@store')->name('project.invoiceCreate.store');
        Route::get('/project-invoiceCreate-edit/{id}', 'InvoiceController@edit')->name('project.invoiceCreate.edit');
        Route::get('/project-invoiceCreate-show/{id}', 'InvoiceController@show')->name('project.invoiceCreate.show');
        Route::post('/project-invoiceCreate-update/{id}', 'InvoiceController@update')->name('project.invoiceCreate.update');
        Route::get('/project-invoiceCreate-delete/{id}', 'InvoiceController@destroy')->name('project.invoiceCreate.destroy');
        Route::get('/project-invoiceCreate-status/{id}/{status}', 'InvoiceController@statusUpdate')->name('project.invoiceCreate.status');
        //project incoive crud operation end
    });
});
