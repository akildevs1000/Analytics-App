<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Community\CommunityController;
use App\Http\Controllers\Dashboards\CustomersDashboard;

Route::get('/dashbaord-statistics', [CustomersDashboard::class, "dashboardStatistics"]);
