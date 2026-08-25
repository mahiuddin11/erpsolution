<?php

use App\Http\Controllers\Api\FinancialDashboardApiController;
use App\Http\Controllers\Api\HrmDashboardApiController;
use App\Http\Controllers\Api\StoreDashboardApiController;
use App\Http\Controllers\Api\PosDashboardApiController;
use App\Http\Controllers\Api\SmsConfigurationApiController;
use Illuminate\Support\Facades\Route;

// 'web' ekhon RouteServiceProvider theke ashche, tai shudhu 'auth' likhle hobe
Route::middleware('auth')->prefix('hrm-dashboard')->group(function () {
    Route::get('kpis', [HrmDashboardApiController::class, 'kpis']);
    Route::get('kpi-details', [HrmDashboardApiController::class, 'kpiDetails']);
    Route::get('department-distribution', [HrmDashboardApiController::class, 'departmentDistribution']);
    Route::get('quick-actions', [HrmDashboardApiController::class, 'quickActions']);
    Route::get('employees-on-leave', [HrmDashboardApiController::class, 'employeesOnLeave']);
    Route::get('attendance-list', [HrmDashboardApiController::class, 'attendanceList']);
    Route::get('employee-options', [HrmDashboardApiController::class, 'employeeOptions']);
    Route::get('calendar-events', [HrmDashboardApiController::class, 'calendarEvents']);
    Route::get('recent-leave-applications', [HrmDashboardApiController::class, 'recentLeaveApplications']);
    Route::get('announcements', [HrmDashboardApiController::class, 'announcements']);
    Route::post('announcements', [HrmDashboardApiController::class, 'storeAnnouncement']);
    Route::get('department-options', [HrmDashboardApiController::class, 'departmentOptions']);
    Route::get('position-details', [HrmDashboardApiController::class, 'positionDetails']);

    Route::get('leave-applications/{id}', [HrmDashboardApiController::class, 'leaveApplicationDetail']);
    Route::put('leave-applications/{id}', [HrmDashboardApiController::class, 'updateLeaveApplication']);
    Route::get('announcements/{id}', [HrmDashboardApiController::class, 'announcementDetail']);
    Route::put('announcements/{id}', [HrmDashboardApiController::class, 'updateAnnouncement']);
});

Route::middleware('auth')->prefix('store-dashboard')->group(function () {
    Route::get('kpis', [StoreDashboardApiController::class, 'kpis']);
    Route::get('kpi-details', [StoreDashboardApiController::class, 'kpiDetails']);
    Route::get('quick-actions', [StoreDashboardApiController::class, 'quickActions']);
    Route::get('low-stock-items', [StoreDashboardApiController::class, 'lowStockItems']);
    Route::get('stock-movement', [StoreDashboardApiController::class, 'stockMovement']);
    Route::get('recent-transactions', [StoreDashboardApiController::class, 'recentTransactions']);
    Route::get('warehouse-distribution', [StoreDashboardApiController::class, 'warehouseDistribution']);
    Route::get('warehouse-stock-details', [StoreDashboardApiController::class, 'warehouseStockDetails']);
    Route::get('warehouse-options', [StoreDashboardApiController::class, 'warehouseOptions']);
    Route::get('branch-distribution', [StoreDashboardApiController::class, 'branchDistribution']);
});

Route::middleware('auth')->prefix('financial-dashboard')->group(function () {
    Route::get('kpis', [FinancialDashboardApiController::class, 'kpis']);
    Route::get('kpi-details', [FinancialDashboardApiController::class, 'kpiDetails']);
    Route::get('cash-flow', [FinancialDashboardApiController::class, 'cashFlow']);
    Route::get('expense-breakdown', [FinancialDashboardApiController::class, 'expenseBreakdown']);
    Route::get('revenue-comparison', [FinancialDashboardApiController::class, 'revenueComparison']);
    Route::get('transactions', [FinancialDashboardApiController::class, 'transactions']);
    Route::get('invoices', [FinancialDashboardApiController::class, 'invoices']);
});


Route::middleware('auth')->prefix('pos-dashboard')->group(function () {
    Route::get('quick-actions', [PosDashboardApiController::class, 'quickActions']);
    Route::get('kpis', [PosDashboardApiController::class, 'kpis']);
    Route::get('sales-purchase-trend', [PosDashboardApiController::class, 'salesPurchaseTrend']);
    Route::get('payment-breakdown', [PosDashboardApiController::class, 'paymentBreakdown']);
    Route::get('top-products', [PosDashboardApiController::class, 'topProducts']);
    Route::get('product-invoices/{productId}', [PosDashboardApiController::class, 'productInvoices']);
    Route::get('sale-invoice/{saleId}', [PosDashboardApiController::class, 'saleInvoiceDetail']);
    Route::get('recent-transactions', [PosDashboardApiController::class, 'recentTransactions']);
    Route::get('product-consumption', [PosDashboardApiController::class, 'productConsumption']);
    Route::get('project-options', [PosDashboardApiController::class, 'projectOptions']);
    Route::get('product-options', [PosDashboardApiController::class, 'productOptions']);
    Route::get('sales-performance', [PosDashboardApiController::class, 'salesPerformance']);
    Route::get('top-receivables', [PosDashboardApiController::class, 'topReceivables']);
    Route::get('receivable-invoices/{accountId}', [PosDashboardApiController::class, 'receivableInvoices']);
    Route::get('supplier-due', [PosDashboardApiController::class, 'supplierDue']);
    Route::get('supplier-due-invoices/{supplierId}', [PosDashboardApiController::class, 'supplierDueInvoices']);
});

Route::middleware('auth')->prefix('sms-configuration')->group(function () {
    Route::get('/stats', [SmsConfigurationApiController::class, 'stats']);
    Route::get('/config', [SmsConfigurationApiController::class, 'getConfig']);
    Route::post('/config', [SmsConfigurationApiController::class, 'saveConfig']);
    Route::post('/config/test', [SmsConfigurationApiController::class, 'testConnection']);
    Route::get('/templates', [SmsConfigurationApiController::class, 'templatesIndex']);
    Route::post('/templates', [SmsConfigurationApiController::class, 'templatesStore']);
    Route::put('/templates/{template}', [SmsConfigurationApiController::class, 'templatesUpdate']);
    Route::delete('/templates/{template}', [SmsConfigurationApiController::class, 'templatesDestroy']);
    Route::get('/recipients-count', [SmsConfigurationApiController::class, 'recipientsCount']);
    Route::get('/departments', [SmsConfigurationApiController::class, 'departments']);
    Route::get('/contacts', [SmsConfigurationApiController::class, 'contacts']);
    Route::post('/send', [SmsConfigurationApiController::class, 'send']);
});



// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
