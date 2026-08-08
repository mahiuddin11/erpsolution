<?php

use App\Http\Controllers\Api\FinancialDashboardApiController;
use App\Http\Controllers\Api\HrmDashboardApiController;
use App\Http\Controllers\Api\StoreDashboardApiController;
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
    Route::get('branch-distribution', [StoreDashboardApiController::class, 'branchDistribution']);
    Route::get('quick-actions', [StoreDashboardApiController::class, 'quickActions']);
    Route::get('low-stock-items', [StoreDashboardApiController::class, 'lowStockItems']);
    Route::get('stock-movement', [StoreDashboardApiController::class, 'stockMovement']);
    Route::get('branch-options', [StoreDashboardApiController::class, 'branchOptions']);
    Route::get('recent-transactions', [StoreDashboardApiController::class, 'recentTransactions']);
    Route::get('branch-stock-details', [StoreDashboardApiController::class, 'branchStockDetails']);
});

Route::middleware('auth')->prefix('financial-dashboard')->group(function () {
    Route::get('kpis', [FinancialDashboardApiController::class, 'kpis']);
    Route::get('kpi-details', [FinancialDashboardApiController::class, 'kpiDetails']); // NEW -- KPI card drill-down modal
    Route::get('cash-flow', [FinancialDashboardApiController::class, 'cashFlow']);
    Route::get('expense-breakdown', [FinancialDashboardApiController::class, 'expenseBreakdown']);
    Route::get('revenue-comparison', [FinancialDashboardApiController::class, 'revenueComparison']);
    Route::get('transactions', [FinancialDashboardApiController::class, 'transactions']);
    Route::get('invoices', [FinancialDashboardApiController::class, 'invoices']);
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
