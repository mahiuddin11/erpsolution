<?php

use App\Http\Controllers\Backend\Reports\CheckRegisterController;
use App\Http\Controllers\Backend\Reports\FinancialStatementController;
use App\Http\Controllers\Backend\Reports\ReportController;
use App\Http\Controllers\Backend\Reports\StatementOfChangesInEquityController;
use Illuminate\Support\Facades\Route;



Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    // report  start
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Reports'], function () {

        //report operation start
        Route::any('/report-purchase-purchase', [ReportController::class, 'purchase'])->name('report.purchase.purchase');
        Route::any('/report.production.production', [ReportController::class, 'production'])->name('report.production.production');
        Route::any('/report-sale-sale', [ReportController::class, 'sale'])->name('report.sale.sale');
        Route::any('/report-transfer-transfer', [ReportController::class, 'transfer'])->name('report.transfer.transfer');

        Route::any('/report-project-project', [ReportController::class, 'project'])->name('report.project.project');
        Route::any('/report-project-projectex', [ReportController::class, 'projectexpence'])->name('report.projectexpence.projectex');

        Route::any('/report-employee-salary', 'ReportController@empSalary')->name('report.employee.salary');
        Route::any('/report-expense-expense', 'ReportController@expense')->name('report.expense.expense');
        Route::any('/report-supledger-supledger', 'ReportController@supledger')->name('report.supledger.supledger');
        Route::any('/report-custledger-custledger', 'ReportController@custledger')->name('report.custledger.custledger');
        Route::any('/report-account-account', 'ReportController@account')->name('report.account.account');
        Route::any('/report-cashbook-cashbook', 'ReportController@cashbook')->name('report.cashbook.cashbook');

        Route::any('/report-cashbook-reqcashbook', 'ReportController@reqcashbook')->name('report.cashbook.reqcashbook');
        Route::any('/report-leave', 'ReportController@leave')->name('report.leave');

        //groupleger-ss
        Route::any('/group-ledger-list', [ReportController::class, 'groupledgerList'])->name('report.group-ledger-list');
        Route::get('/get-sub-groups', [ReportController::class, 'getSubGroups'])->name('get.sub.groups');
        Route::get('group-ledger-data', [ReportController::class, 'groupLedgerData'])->name('group-ledger-data');


        Route::any('/report-group-ledger', [ReportController::class, 'groupledger'])->name('report.group-ledger');
        Route::any('/report-ledger-ledger', [ReportController::class, 'ledger'])->name('report.ledger.ledger');
        Route::any('/report-ledger-account-ledger', [ReportController::class, 'accountledger'])->name('report.ledger.accountledger');
        Route::any('/report-trialbalance-trialbalance', [ReportController::class, 'trialbalance'])->name('report.trialbalance.trialbalance');
        Route::any('/report-dashboard-trialbalance', [ReportController::class, 'dashboardtrialbalance'])->name('report.dashboard.trialbalance');
        Route::any('/report-incomestatement-incomestatement', [ReportController::class, 'incomestatement'])->name('report.incomestatement.incomestatement');
        Route::any('/report-balancesheet-balancesheet', [ReportController::class, 'balancesheet'])->name('report.balancesheet.balancesheet');
        Route::any('/report-stock-stock', [ReportController::class, 'stock'])->name('report.stock.stock');
        Route::any('/report-purchase-pr', [ReportController::class, 'purchasereq'])->name('report.purchase.pr');
        Route::any('/report-purchase-po', [ReportController::class, 'purchaseorder'])->name('report.purchase.po');
        Route::any('/report-purchase-grn', [ReportController::class, 'goodrcvnote'])->name('report.purchase.grn');
        Route::any('/report-stock-productledger', [ReportController::class, 'productledger'])->name('report.stock.productledger');

        Route::any('/report-stock-qty-update', [ReportController::class, 'product_update'])->name('report.stock.qty.update');

        Route::any('/report-stock-lowstocks', [ReportController::class, 'lowstocks'])->name('report.stock.lowstocks');
        Route::any('/report-stock-stocksummery', [ReportController::class, 'stocksummery'])->name('report.stock.stocksummery');
        Route::any('/report-day-book', [ReportController::class, 'daybook'])->name('report.day.book');
        Route::any('/report-cashflow', [ReportController::class, 'cashflow'])->name('report.cashflow');
        Route::any('/report-indirect-cashflow', [ReportController::class, 'indirectcashflow'])->name('report.indirectcashflow');
        Route::any('/report-retained-earning', [ReportController::class, 'retainedearning'])->name('report.retained_earning');
        Route::any('/report-bank-book', [ReportController::class, 'bankbook'])->name('report.bank_book');
        Route::any('/report-expense-book', [ReportController::class, 'newexpense'])->name('report.expense');
        Route::any('/report-voucher-report', [ReportController::class, 'voucher'])->name('report.voucher.report');
        Route::any('/report-income-trans-details', [ReportController::class, 'incomestatementDetails'])->name('report.incomestatement.details');
        Route::match(['get', 'post'], '/changes-in-equity', [StatementOfChangesInEquityController::class, 'changesInEquity'])->name('report.changesinequity');

        Route::match(['get', 'post'], '/notes-to-financial-statements', [FinancialStatementController::class, 'notesToFinancialStatements'])
            ->name('report.financialstatements');


        // cheque register 
        Route::get('check-register-list',        [CheckRegisterController::class, 'index'])->name('report.check_register.index');
        Route::get('/check-register-dataProcess', [CheckRegisterController::class, 'dataProcess'])->name('reports.check_register.dataprocess');
        Route::get('/check-register/{id}',       [CheckRegisterController::class, 'show'])->name('reports.check_register.show');
        Route::get('/check-register-banks',      [CheckRegisterController::class, 'getBankAccounts'])->name('reports.check_register.banks');
    });
});
