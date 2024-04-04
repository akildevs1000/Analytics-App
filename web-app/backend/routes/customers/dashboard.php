<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Community\CommunityController;
use App\Http\Controllers\Dashboards\CustomersDashboard;

Route::get('/dashbaord-statistics', [CustomersDashboard::class, "dashboardStatistics"]);
Route::get('/dashbaord-statistics-old', [CustomersDashboard::class, "dashboardStatisticsOld"]);
