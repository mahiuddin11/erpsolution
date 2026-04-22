<?php

// use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Backend\Hrm\AttendanceController;
use App\Http\Controllers\Backend\Settings\ContraVoucherController;
use App\Http\Controllers\Backend\Settings\CreditVoucherController;
use App\Http\Controllers\Backend\Settings\DabitVoucherController;
use App\Models\Accounts;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\AttendanceController;

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

Route::get('/', function () {
    return view('frontant.login');
})->middleware('guest');


Route::resource('attends',  AttendanceController::class);
// Route::get('/attends', [AttendanceController::class], 'index');

// Route::get('/lenk', function () {
//     Artisan::call('storage:link');
// });

Route::group(['prefix' => 'auth'], function () {
    Auth::routes();
});

Route::get('/comparison', function () {

    $jan = 110;
    $feb = 110;
    $change = monthPercentageChange($jan, $feb);

    if ($change > 0) {
        return "↑ " . $change . "% Up from last month";
    } elseif ($change < 0) {
        return "↓ " . abs($change) . "% Down from last month";
    } else {
        return "0";
    }

});


Route::get('/attendance', 'ZktecoController@zktectoAttendance');
Route::get('/attendance/store', 'ZktecoController@storeAtten');
Route::get('/attendance/reset/{id?}', 'ZktecoController@reset');
Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {

    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Dashboard'], function () {
        Route::get('/home', 'HomeController@index')->name('home');
    });
    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Settings'], function () {

        //Commission Role crud operation start
        Route::get('/settings-commissionRules-list', 'CommissionRulesController@index')->name('settings.commissionRules.index');
        Route::get('/dataProcessing-commissionRules', 'CommissionRulesController@dataProcessing')->name('settings.commissionRules.dataProcessing');
        Route::get('/settings-commissionRules-create', 'CommissionRulesController@create')->name('settings.commissionRules.create');
        Route::post('/settings-commissionRules-store', 'CommissionRulesController@store')->name('settings.commissionRules.store');
        Route::get('/settings-commissionRules-edit/{id}', 'CommissionRulesController@edit')->name('settings.commissionRules.edit');
        Route::get('/settings-commissionRules-show/{id}', 'CommissionRulesController@show')->name('settings.commissionRules.show');
        Route::post('/settings-commissionRules-update/{id}', 'CommissionRulesController@update')->name('settings.commissionRules.update');
        Route::get('/settings-commissionRules-delete/{id}', 'CommissionRulesController@destroy')->name('settings.commissionRules.destroy');
        //Commission Role crud operation end

        //branch crud operation start
        Route::get('/settings-branch-list', 'BranchController@index')->name('settings.branch.index');
        Route::get('/dataProcessingBranch', 'BranchController@dataProcessingBranch')->name('settings.branch.dataProcessingBranch');
        Route::get('/settings-branch-create', 'BranchController@create')->name('settings.branch.create');
        Route::get('/settings-get-sub-warehouses/{branch}', 'BranchController@getSubWare')->name('settings.sub.warehouses');
        Route::post('/settings-branch-store', 'BranchController@store')->name('settings.branch.store');
        Route::get('/settings-branch-edit/{id}', 'BranchController@edit')->name('settings.branch.edit');
        Route::get('/settings-branch-show/{id}', 'BranchController@show')->name('settings.branch.show');
        Route::post('/settings-branch-update/{id}', 'BranchController@update')->name('settings.branch.update');
        Route::get('/settings-branch-delete/{id}', 'BranchController@destroy')->name('settings.branch.destroy');
        Route::get('/settings-branch-status/{id}/{status}', 'BranchController@statusUpdate')->name('settings.branch.status');
        //branch crud operation end

        //Warehouses crud operation start
        Route::get('/settings-warehouses-list', 'WarehousesController@index')->name('settings.warehouses.index');
        Route::get('/dataProcessingWarehouses', 'WarehousesController@dataProcessingBranch')->name('settings.warehouses.dataProcessingWarehouses');
        Route::get('/settings-warehouses-create', 'WarehousesController@create')->name('settings.warehouses.create');
        Route::post('/settings-warehouses-store', 'WarehousesController@store')->name('settings.warehouses.store');
        Route::get('/settings-warehouses-edit/{id}', 'WarehousesController@edit')->name('settings.warehouses.edit');
        Route::get('/settings-warehouses-show/{id}', 'WarehousesController@show')->name('settings.warehouses.show');
        Route::post('/settings-warehouses-update/{id}', 'WarehousesController@update')->name('settings.warehouses.update');
        Route::get('/settings-warehouses-delete/{id}', 'WarehousesController@destroy')->name('settings.warehouses.destroy');
        Route::get('/settings-warehouses-status/{id}/{status}', 'WarehousesController@statusUpdate')->name('settings.warehouses.status');
        //Warehouses crud operation end

        //store crud operation start
        Route::get('/settings-store-list', 'StoreController@index')->name('settings.store.index');
        Route::get('/dataProcessingStore', 'StoreController@dataProcessingStore')->name('settings.store.dataProcessingStore');
        Route::get('/settings-store-create', 'StoreController@create')->name('settings.store.create');
        Route::post('/settings-store-store', 'StoreController@store')->name('settings.store.store');
        Route::get('/settings-store-edit/{id}', 'StoreController@edit')->name('settings.store.edit');
        Route::get('/settings-store-show/{id}', 'StoreController@show')->name('settings.store.show');
        Route::post('/settings-store-update/{id}', 'StoreController@update')->name('settings.store.update');
        Route::get('/settings-store-delete/{id}', 'StoreController@destroy')->name('settings.store.destroy');
        Route::get('/settings-store-status/{id}/{status}', 'StoreController@statusUpdate')->name('settings.store.status');
        //store crud operation end

        //navigation crud operation start
        Route::get('/navigation', 'NavigationController@index')->name('setup.index');
        Route::get('/navigation-add', 'NavigationController@create')->name('setup.create');
        Route::post('/navigation-store', 'NavigationController@store')->name('setup.store');
        Route::get('/navigation-edit/{id}', 'NavigationController@edit')->name('setup.edit');
        Route::post('/navigation-edit/{id}', 'NavigationController@update')->name('setup.update');
        Route::delete('/navigation-delete/{id}', 'NavigationController@destroy')->name('setup.destroy');
        //navigation crud operation start

        //smpt crud operation start
        Route::get('/settings-smpt-list', 'SmtpController@index')->name('settings.smpt.index');
        Route::get('/dataProcessingSmpt', 'SmtpController@dataProcessingSmpt')->name('settings.smpt.dataProcessingSmpt');
        Route::get('/settings-smpt-create', 'SmtpController@create')->name('settings.smpt.create');
        Route::post('/settings-smpt-store', 'SmtpController@store')->name('settings.smpt.store');
        Route::get('/settings-smpt-edit/{id}', 'SmtpController@edit')->name('settings.smpt.edit');
        Route::get('/settings-smpt-show/{id}', 'SmtpController@show')->name('settings.smpt.show');
        Route::post('/settings-smpt-update/{id}', 'SmtpController@update')->name('settings.smpt.update');
        Route::get('/settings-smpt-delete/{id}', 'SmtpController@destroy')->name('settings.smpt.destroy');
        Route::get('/settings-smpt-status/{id}/{status}', 'SmtpController@statusUpdate')->name('settings.smpt.status');

        //Currency crud operation start
        Route::get('/settings-currency-list', 'CurrencyController@index')->name('settings.currency.index');
        Route::get('/dataProcessingCurrency', 'CurrencyController@dataProcessingCurrency')->name('settings.currency.dataProcessingCurrency');
        Route::get('/settings-currency-create', 'CurrencyController@create')->name('settings.currency.create');
        Route::post('/settings-currency-store', 'CurrencyController@store')->name('settings.currency.store');
        Route::get('/settings-currency-edit/{id}', 'CurrencyController@edit')->name('settings.currency.edit');
        Route::get('/settings-currency-show/{id}', 'CurrencyController@show')->name('settings.currency.show');
        Route::post('/settings-currency-update/{id}', 'CurrencyController@update')->name('settings.currency.update');
        Route::get('/settings-currency-delete/{id}', 'CurrencyController@destroy')->name('settings.currency.destroy');
        Route::get('/settings-currency-status/{id}/{status}', 'CurrencyController@statusUpdate')->name('settings.currency.status');
        //Currency crud operation end

        //language crud operation start
        Route::get('/settings-language-list', 'LanguageController@index')->name('settings.language.index');
        Route::get('/dataProcessingLanguage', 'LanguageController@dataProcessingLanguage')->name('settings.language.dataProcessingLanguage');
        Route::get('/settings-language-create', 'LanguageController@create')->name('settings.language.create');
        Route::post('/settings-language-store', 'LanguageController@store')->name('settings.language.store');
        Route::get('/settings-language-edit/{id}', 'LanguageController@edit')->name('settings.language.edit');
        Route::get('/settings-language-show/{id}', 'LanguageController@show')->name('settings.language.show');
        Route::post('/settings-language-update/{id}', 'LanguageController@update')->name('settings.language.update');
        Route::get('/settings-language-delete/{id}', 'LanguageController@destroy')->name('settings.language.destroy');
        Route::get('/settings-language-status/{id}/{status}', 'LanguageController@statusUpdate')->name('settings.language.status');
        //language crud operation end

        //company crud operation start
        Route::get('/settings-company-list', 'CompanyController@index')->name('settings.company.index');
        Route::get('/dataProcessingCompany', 'CompanyController@dataProcessingCompany')->name('settings.company.dataProcessingCompany');
        Route::get('/settings-company-create', 'CompanyController@create')->name('settings.company.create');
        Route::post('/settings-company-store', 'CompanyController@store')->name('settings.company.store');
        Route::get('/settings-company-edit/{id}', 'CompanyController@edit')->name('settings.company.edit');
        Route::get('/settings-company-show/{id}', 'CompanyController@show')->name('settings.company.show');
        Route::post('/settings-company-update/{id}', 'CompanyController@update')->name('settings.company.update');
        Route::get('/settings-company-delete/{id}', 'CompanyController@destroy')->name('settings.company.destroy');
        Route::get('/settings-company-status/{id}/{status}', 'CompanyController@statusUpdate')->name('settings.company.status');
        //company crud operation end

        //fiscal_year crud operation start
        Route::get('/settings-fiscal_year-list', 'FiscalYearController@index')->name('settings.fiscal_year.index');
        Route::get('/dataProcessingFiscalYear', 'FiscalYearController@dataProcessingFiscalYear')->name('settings.fiscal_year.dataProcessingFiscalYear');
        Route::get('/settings-fiscal_year-create', 'FiscalYearController@create')->name('settings.fiscal_year.create');
        Route::post('/settings-fiscal_year-store', 'FiscalYearController@store')->name('settings.fiscal_year.store');
        Route::get('/settings-fiscal_year-edit/{id}', 'FiscalYearController@edit')->name('settings.fiscal_year.edit');
        Route::get('/settings-fiscal_year-show/{id}', 'FiscalYearController@show')->name('settings.fiscal_year.show');
        Route::post('/settings-fiscal_year-update/{id}', 'FiscalYearController@update')->name('settings.fiscal_year.update');
        Route::get('/settings-fiscal_year-delete/{id}', 'FiscalYearController@destroy')->name('settings.fiscal_year.destroy');
        Route::get('/settings-fiscal_year-status/{id}/{status}', 'FiscalYearController@statusUpdate')->name('settings.fiscal_year.status');
        //fiscal_year crud operation end

        //account crud operation start
        Route::get('/settings-account-list', 'AccountsController@index')->name('settings.account.index');
        Route::get('/dataProcessingAccount', 'AccountsController@dataProcessingAccount')->name('settings.account.dataProcessingAccount');
        Route::get('/settings-account-create', 'AccountsController@create')->name('settings.account.create');
        Route::post('/settings-account-store', 'AccountsController@store')->name('settings.account.store');
        Route::get('/settings-account-edit/{id}', 'AccountsController@edit')->name('settings.account.edit');
        Route::get('/settings-account-show/{id}', 'AccountsController@show')->name('settings.account.show');
        Route::post('/settings-account-update/{id}', 'AccountsController@update')->name('settings.account.update');
        Route::get('/settings-account-delete/{id}', 'AccountsController@destroy')->name('settings.account.destroy');
        Route::get('/settings-account-status/{id}/{status}', 'AccountsController@statusUpdate')->name('settings.account.status');
        //account crud operation end

        //account crud operation start
        Route::get('/settings-transfer-list', 'TransferController@index')->name('settings.transfer.index');
        Route::get('/dataProcessingBalanceTransfer', 'TransferController@dataProcessingBalanceTransfer')->name('settings.transfer.dataProcessingBalanceTransfer');
        Route::get('/settings-transfer-create', 'TransferController@create')->name('settings.transfer.create');
        Route::post('/settings-transfer-store', 'TransferController@store')->name('settings.transfer.store');
        Route::get('/settings-transfer-edit/{id}', 'TransferController@edit')->name('settings.transfer.edit');
        Route::get('/settings-transfer-show/{id}', 'TransferController@show')->name('settings.transfer.show');
        Route::post('/settings-transfer-update/{id}', 'TransferController@update')->name('settings.transfer.update');
        Route::get('/settings-transfer-delete/{id}', 'TransferController@destroy')->name('settings.transfer.destroy');
        Route::get('/settings-transfer-status/{id}/{status}', 'TransferController@statusUpdate')->name('settings.transfer.status');
        Route::get('/getAccountBalance', 'TransferController@getAccountBalance')->name('settings.transfer.checkBalance');
        //account crud operation end

        //Expance       crud operation start
        Route::get('/settings-expense-list', 'ExpenseController@index')->name('settings.expense.index');
        Route::get('/dataProcessingExpense', 'ExpenseController@dataProcessingExpense')->name('settings.expense.dataProcessingExpense');
        Route::get('/settings-expense-create', 'ExpenseController@create')->name('settings.expense.create');
        Route::get('/settings-expense-accountsearch', 'ExpenseController@accountsearch')->name('settings.expense.accountsearch');
        Route::post('/settings-expense-store', 'ExpenseController@store')->name('settings.expense.store');
        Route::get('/settings-expense-edit/{id}', 'ExpenseController@edit')->name('settings.expense.edit');
        Route::get('/settings-expense-show/{id}', 'ExpenseController@show')->name('settings.expense.show');
        Route::post('/settings-expense-update/{id}', 'ExpenseController@update')->name('settings.expense.update');
        Route::get('/settings-expense-delete/{id}', 'ExpenseController@destroy')->name('settings.expense.destroy');
        Route::get('/settings-expense-status/{id}/{status}', 'ExpenseController@statusUpdate')->name('settings.expense.status');
        Route::get('/getSubCategory', 'ExpenseController@getSubCategory')->name('settings.expense.getSubCategory');
        //account crud operation end

        //Debit Voicher crud operation start
        Route::get('/settings-dabit-voucher-list', [DabitVoucherController::class, 'index'])->name('settings.dabit.voucher.index');
        Route::get('/dataProcessingDabitvoucher', [DabitVoucherController::class, 'dataProcessing'])->name('settings.dabit.voucher.dataProcessingDabitVoucher');
        Route::get('/settings-dabit-voucher-create', [DabitVoucherController::class, 'create'])->name('settings.dabit.voucher.create');
        Route::get('/settings-dabit-voucher-accountsearch', [DabitVoucherController::class, 'accountsearch'])->name('settings.dabit.voucher.accountsearch');
        Route::post('/settings-dabit-voucher-store', [DabitVoucherController::class, 'store'])->name('settings.dabit.voucher.store');
        Route::get('/settings-dabit-voucher-edit/{id}', [DabitVoucherController::class, 'edit'])->name('settings.dabit.voucher.edit');
        Route::get('/settings-dabit-voucher-show/{id}', [DabitVoucherController::class, 'show'])->name('settings.dabit.voucher.show');
        Route::get('/settings-dabit-voucher-approve/{id}', [DabitVoucherController::class, 'approve'])->name('settings.dabit.voucher.approve');
        Route::post('/settings-dabit-voucher-update/{id}', [DabitVoucherController::class, 'update'])->name('settings.dabit.voucher.update');
        Route::get('/settings-dabit-voucher-delete/{id}', [DabitVoucherController::class, 'destroy'])->name('settings.dabit.voucher.destroy');
        Route::get('/settings-dabit-voucher-singledestroy/{id}', [DabitVoucherController::class, 'singledestroy'])->name('settings.dabit.voucher.singledestroy');
        Route::get('/settings-dabit-voucher-status/{id}/{status}', [DabitVoucherController::class, 'statusUpdate'])->name('settings.dabit.voucher.status');
        Route::get('/getSubCategory', [DabitVoucherController::class, 'getSubCategory'])->name('settings.dabit.voucher.getSubCategory');
        Route::get('/settings-dabit-voucher-purchasevoucher', [DabitVoucherController::class, 'purchasevoucher'])->name('settings.dabit.voucher.purchasevoucher');
        Route::get('/settings-dabit-voucher-employeevoucher', [DabitVoucherController::class, 'employeevoucher'])->name('settings.dabit.voucher.employeevoucher');
        Route::get('/settings-dabit-voucher-customervoucher', [DabitVoucherController::class, 'customervoucher'])->name('settings.dabit.voucher.customervoucher');
        Route::get('/settings-check-bill-by-bill', [DabitVoucherController::class, 'checkBillByBill'])->name('settings.dabit.voucher.checkBillByBill');
        //Debit Voicher operation end

        //Credit Voicher crud operation start
        Route::get('/settings-credit-voucher-list', [CreditVoucherController::class, 'index'])->name('settings.credit.voucher.index');
        Route::get('/dataProcessingcreditvoucher', [CreditVoucherController::class, 'dataProcessing'])->name('settings.credit.voucher.dataProcessingDabitVoucher');
        Route::get('/settings-credit-voucher-create', [CreditVoucherController::class, 'create'])->name('settings.credit.voucher.create');
        Route::get('/settings-credit-voucher-accountsearch', [CreditVoucherController::class, 'accountsearch'])->name('settings.credit.voucher.accountsearch');
        Route::post('/settings-credit-voucher-store', [CreditVoucherController::class, 'store'])->name('settings.credit.voucher.store');
        Route::get('/settings-credit-voucher-edit/{id}', [CreditVoucherController::class, 'edit'])->name('settings.credit.voucher.edit');
        Route::get('/settings-credit-voucher-show/{id}', [CreditVoucherController::class, 'show'])->name('settings.credit.voucher.show');
        Route::get('/settings-credit-voucher-approve/{id}', [CreditVoucherController::class, 'approve'])->name('settings.credit.voucher.approve');
        Route::post('/settings-credit-voucher-update/{id}', [CreditVoucherController::class, 'update'])->name('settings.credit.voucher.update');
        Route::get('/settings-credit-voucher-delete/{id}', [CreditVoucherController::class, 'destroy'])->name('settings.credit.voucher.destroy');
        Route::get('/settings-credit-voucher-singledestroy/{id}', [CreditVoucherController::class, 'singledestroy'])->name('settings.credit.voucher.singledestroy');
        Route::get('/settings-credit-voucher-status/{id}/{status}', [CreditVoucherController::class, 'statusUpdate'])->name('settings.credit.voucher.status');
        Route::get('/getSubCategory', [CreditVoucherController::class, 'getSubCategory'])->name('settings.credit.voucher.getSubCategory');
        Route::get('/settings-credit-voucher-purchasevoucher', [CreditVoucherController::class, 'purchasevoucher'])->name('settings.credit.voucher.purchasevoucher');
        Route::get('/settings-credit-voucher-employeevoucher', [CreditVoucherController::class, 'employeevoucher'])->name('settings.credit.voucher.employeevoucher');
        Route::get('/settings-credit-voucher-customervoucher', [CreditVoucherController::class, 'customervoucher'])->name('settings.credit.voucher.customervoucher');
        //Credit Voicher operation end


        //Contra Voicher crud operation start
        Route::get('/settings-contra-voucher-list', [ContraVoucherController::class,'index'])->name('settings.contra.voucher.index');
        Route::get('/dataProcessingcontravoucher', [ContraVoucherController::class,'dataProcessing'])->name('settings.contra.voucher.dataProcessingContraVoucher');
        Route::get('/settings-contra-voucher-create', [ContraVoucherController::class,'create'])->name('settings.contra.voucher.create');
        Route::get('/settings-contra-voucher-accountsearch', [ContraVoucherController::class,'accountsearch'])->name('settings.contra.voucher.accountsearch');
        Route::post('/settings-contra-voucher-store', [ContraVoucherController::class,'store'])->name('settings.contra.voucher.store');
        Route::get('/settings-contra-voucher-edit/{id}', [ContraVoucherController::class,'edit'])->name('settings.contra.voucher.edit');
        Route::get('/settings-contra-voucher-show/{id}', [ContraVoucherController::class,'show'])->name('settings.contra.voucher.show');
        Route::post('/settings-contra-voucher-update/{id}', [ContraVoucherController::class,'update'])->name('settings.contra.voucher.update');
        Route::get('/settings-contra-voucher-delete/{id}', [ContraVoucherController::class,'destroy'])->name('settings.contra.voucher.destroy');
        Route::get('/settings-contra-voucher-singledestroy/{id}', [ContraVoucherController::class,'singledestroy'])->name('settings.contra.voucher.singledestroy');
        Route::get('/settings-contra-voucher-status/{id}/{status}', [ContraVoucherController::class,'statusUpdate'])->name('settings.contra.voucher.status');
        Route::get('/getSubCategory', [ContraVoucherController::class,'getSubCategory'])->name('settings.contra.voucher.getSubCategory');
        Route::get('/contra-getAccountBalance', [ContraVoucherController::class,'getAccountBalance'])->name('settings.contra.checkBalance');




        //Contra Voicher crud operation start
        Route::get('/settings-journal-voucher-list', 'JournalVoucherController@index')->name('settings.journal.voucher.index');
        Route::get('/dataProcessingjournalvoucher', 'JournalVoucherController@dataProcessing')->name('settings.journal.voucher.dataProcessingJournalVoucher');
        Route::get('/settings-journal-voucher-create', 'JournalVoucherController@create')->name('settings.journal.voucher.create');
        Route::get('/settings-journal-voucher-accountsearch', 'JournalVoucherController@accountsearch')->name('settings.journal.voucher.accountsearch');
        Route::post('/settings-journal-voucher-store', 'JournalVoucherController@store')->name('settings.journal.voucher.store');
        Route::get('/settings-journal-voucher-edit/{id}', 'JournalVoucherController@edit')->name('settings.journal.voucher.edit');
        Route::get('/settings-journal-voucher-show/{id}', 'JournalVoucherController@show')->name('settings.journal.voucher.show');
        Route::post('/settings-journal-voucher-update/{id}', 'JournalVoucherController@update')->name('settings.journal.voucher.update');
        Route::get('/settings-journal-voucher-delete/{id}', 'JournalVoucherController@destroy')->name('settings.journal.voucher.destroy');
        Route::get('/settings-journal-voucher-singledestroy/{id}', 'JournalVoucherController@singledestroy')->name('settings.journal.voucher.singledestroy');
        Route::get('/settings-journal-voucher-status/{id}/{status}', 'JournalVoucherController@statusUpdate')->name('settings.contra.voucher.status');
        Route::get('/getSubCategory', 'JournalVoucherController@getSubCategory')->name('settings.contra.voucher.getSubCategory');
        //Contra Voicher operation end

        //Expance category crud operation start
        Route::get('/settings-category-list', 'ExpenseCategoryController@index')->name('settings.category.index');
        Route::get('/dataProcessingExpensecategory', 'ExpenseCategoryController@dataProcessingExpensecategory')->name('settings.category.dataProcessingExpensecategory');
        Route::get('/settings-category-create', 'ExpenseCategoryController@create')->name('settings.category.create');
        Route::post('/settings-category-store', 'ExpenseCategoryController@store')->name('settings.category.store');
        Route::get('/settings-category-edit/{id}', 'ExpenseCategoryController@edit')->name('settings.category.edit');
        // Route::get('/settings-category-show/{id}', 'ExpenseCategoryController@show')->name('settings.category.show');
        Route::post('/settings-category-update/{id}', 'ExpenseCategoryController@update')->name('settings.category.update');
        Route::get('/settings-category-delete/{id}', 'ExpenseCategoryController@destroy')->name('settings.category.destroy');
        Route::get('/settings-category-status/{id}/{status}', 'ExpenseCategoryController@statusUpdate')->name('settings.category.status');
        //Expance crud operation end

        //opening balance crud operation start
        Route::get('/settings-openingbalance-list', 'OpeningController@index')->name('settings.openingbalance.index');
        Route::get('/dataProcessingOpeningBalance', 'OpeningController@dataProcessingOpeningBalance')->name('settings.openingbalance.dataProcessingOpeningBalance');
        Route::get('/settings-openingbalance-create', 'OpeningController@create')->name('settings.openingbalance.create');
        Route::post('/settings-openingbalance-store', 'OpeningController@store')->name('settings.openingbalance.store');
        Route::get('/settings-openingbalance-edit/{id}', 'OpeningController@edit')->name('settings.openingbalance.edit');
        Route::get('/settings-openingbalance-show/{id}', 'OpeningController@show')->name('settings.openingbalance.show');
        Route::post('/settings-openingbalance-update/{id}', 'OpeningController@update')->name('settings.openingbalance.update');
        Route::get('/settings-openingbalance-delete/{id}', 'OpeningController@destroy')->name('settings.openingbalance.destroy');
        Route::get('/settings-openingbalance-status/{id}/{status}', 'OpeningController@statusUpdate')->name('settings.openingbalance.status');
        Route::get('/getAllAccountHead', 'OpeningController@getAllAccountHead')->name('settings.openingbalance.getAllAccountHead');
        //opening balance crud operation end

        //customer opening balance crud operation start
        Route::get('/settings-customerOpening-list', 'CustomerOpeningController@index')->name('settings.customerOpening.index');
        Route::get('/dataProcessingCustomerOpening', 'CustomerOpeningController@dataProcessingOpeningBalance')->name('settings.customerOpening.dataProcessingCustomerOpening');
        Route::get('/settings-customerOpening-create', 'CustomerOpeningController@create')->name('settings.customerOpening.create');
        Route::post('/settings-customerOpening-store', 'CustomerOpeningController@store')->name('settings.customerOpening.store');
        Route::get('/settings-customerOpening-edit/{id}', 'CustomerOpeningController@edit')->name('settings.customerOpening.edit');
        Route::get('/settings-customerOpening-show/{id}', 'CustomerOpeningController@show')->name('settings.customerOpening.show');
        Route::post('/settings-customerOpening-update/{id}', 'CustomerOpeningController@update')->name('settings.customerOpening.update');
        Route::get('/settings-customerOpening-delete/{id}', 'CustomerOpeningController@destroy')->name('settings.customerOpening.destroy');
        Route::get('/settings-customerOpening-status/{id}/{status}', 'CustomerOpeningController@statusUpdate')->name('settings.customerOpening.status');
        //customer opening balance crud operation end

        //Hrm setup start
        Route::get('/settings-hrm-setup-list', 'HrmSetupController@index')->name('settings.hrm.setup.index');
        Route::get('/settings-hrm-setup-create', 'HrmSetupController@create')->name('settings.hrm.setup.create');
        Route::get('/dataProcessingHrmSetup', 'HrmSetupController@dataProcessingHrmSetup')->name('settings.hrm.setup.dataProcessingHrm');
        Route::post('/settings-hrm-setup-store', 'HrmSetupController@store')->name('settings.hrm.setup.store');
        Route::get('/settings-hrm-setup-edit/{id}', 'HrmSetupController@edit')->name('settings.hrm.setup.edit');
        Route::get('/settings-hrm-setup-show/{id}', 'HrmSetupController@show')->name('settings.hrm.setup.show');
        Route::post('/settings-hrm-setup-update/{id}', 'HrmSetupController@update')->name('settings.hrm.setup.update');
        Route::get('/settings-hrm-setup-delete/{id}', 'HrmSetupController@destroy')->name('settings.hrm.setup.destroy');
        Route::get('/settings-hrm-setup-status/{id}/{status}', 'HrmSetupController@statusUpdate')->name('settings.hrm.setup.status');
        //Hrm setup end

        Route::get('paidup/capital', 'PaidupcapitalController@index')->name('paidup.capital.index');
        Route::post('/paidup/capital', 'PaidupcapitalController@paidupcapital')->name('paidup.capital.store');

        Route::post('/authorized/capital/store', 'AuthorizedcapitalController@Authorized_capital')->name('authorized.capital.store');
        Route::get('authorized/capital', 'AuthorizedcapitalController@index')->name('authorized_capital.index');
    });


    Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Usermanage'], function () {
        //admin role operation start
        Route::get('/usermanage-userRole-list', 'UserRoleController@index')->name('usermanage.userRole.index');
        Route::get('/dataProcessingUserRole', 'UserRoleController@dataProcessinguserRole')->name('usermanage.userRole.dataProcessingRole');
        Route::get('/usermanage-userRole-create', 'UserRoleController@create')->name('usermanage.userRole.create');
        Route::post('/usermanage-userRole-store', 'UserRoleController@store')->name('usermanage.userRole.store');
        Route::get('/usermanage-userRole-edit/{id}', 'UserRoleController@edit')->name('usermanage.userRole.edit');
        Route::get('/usermanage-userRole-show/{id}', 'UserRoleController@show')->name('usermanage.userRole.show');
        Route::post('/usermanage-userRole-update/{id}', 'UserRoleController@update')->name('usermanage.userRole.update');
        Route::get('/usermanage-userRole-delete/{id}', 'UserRoleController@destroy')->name('usermanage.userRole.destroy');
        Route::get('/usermanage-userRole-status/{id}/{status}', 'UserRoleController@statusUpdate')->name('usermanage.userRole.status');

        Route::get('/usermanage-userRole-profile/{id}', 'UserRoleController@profile')->name('usermanage.userRole.profile');

        Route::post('/usermanage-userRole-profileupdate/{id}', 'UserRoleController@profileupdate')->name('usermanage.userRole.profileupdate');
        //admin role operation end

        //user role operation start
        Route::get('/usermanage-user-list', 'UserController@index')->name('usermanage.user.index');
        Route::get('/dataProcessingUser', 'UserController@dataProcessinguser')->name('usermanage.user.dataProcessingUser');
        Route::get('/usermanage-user-create', 'UserController@create')->name('usermanage.user.create');
        Route::post('/usermanage-user-store', 'UserController@store')->name('usermanage.user.store');
        Route::get('/usermanage-user-edit/{id}', 'UserController@edit')->name('usermanage.user.edit');
        Route::get('/usermanage-user-show/{id}', 'UserController@show')->name('usermanage.user.show');
        Route::post('/usermanage-user-update/{id}', 'UserController@update')->name('usermanage.user.update');
        Route::get('/usermanage-user-delete/{id}', 'UserController@destroy')->name('usermanage.user.destroy');
        Route::get('/usermanage-user-status/{id}/{status}', 'UserController@statusUpdate')->name('usermanage.user.status');
        //user role operation end
    });
});
