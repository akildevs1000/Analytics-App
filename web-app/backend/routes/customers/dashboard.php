<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Community\CommunityController;
use App\Http\Controllers\Dashboards\CustomersDashboard;
use App\Http\Controllers\Dashboards\CustomersDashboardController;

Route::get('/dashboard-statistics', [CustomersDashboardController::class, "dashboardStatistics"]);
Route::get('/dashbaord-statistics-old', [CustomersDashboardController::class, "dashboardStatisticsOld"]);
