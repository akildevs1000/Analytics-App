<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerReportController;
use App\Http\Controllers\CustomerSyncController;
use Illuminate\Support\Facades\Route;

Route::get('customer-list', [CustomerController::class, "dropDown"]);
Route::apiResource('customer', CustomerController::class);
Route::apiResource('customer-report', CustomerReportController::class);
Route::get('customer-report-print', [CustomerReportController::class, "print"]);
Route::get('customer-report-download', [CustomerReportController::class, "download"]);
//Route::get('customer-stats-report', [CustomerReportController::class, "stats"]);
Route::get('customer-stats-report', [CustomerReportController::class, "CustomerStatsReport"]);


Route::post('customers_temp_pload', [CustomerController::class, "tempUpload"]);
Route::get('customers-stats-between-dates', [CustomerReportController::class, "CustomerStatsSumBetweenDatesReport"]);
