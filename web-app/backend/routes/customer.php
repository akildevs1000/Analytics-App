<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerReportController;

use Illuminate\Support\Facades\Route;

Route::get('customer-list', [CustomerController::class, "dropDown"]);
Route::apiResource('customer', CustomerController::class);
Route::apiResource('customer-report', CustomerReportController::class);
Route::get('customer-report-print', [CustomerReportController::class, "print"]);
Route::get('customer-report-download', [CustomerReportController::class, "download"]);
