<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Community\CommunityController;
use App\Http\Controllers\Dashboards\CustomersDashboard;
use App\Http\Controllers\Dashboards\CustomersDashboardController;

Route::get('/dashboard-statistics', [CustomersDashboardController::class, "dashboardStatistics"]);
Route::get('/dashbaord-statistics-old', [CustomersDashboardController::class, "dashboardStatisticsOld"]);

Route::get('dashboard-get-hourly-in-data', [CustomersDashboardController::class, 'getDashboardHourlyInCount']);
Route::get('dashboard-get-hourly-out-data', [CustomersDashboardController::class, 'getDashboardHourlyOutCount']);

Route::get('alarm_dashboard_get_monthly_data', [CustomersDashboardController::class, 'getDashboardMonthlyCount']);
