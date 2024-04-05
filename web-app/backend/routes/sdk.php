<?php

use App\Http\Controllers\RecordController;
use App\Http\Controllers\SDKController;
use App\Http\Controllers\SDKControllerV1;
use Illuminate\Support\Facades\Route;

Route::post('/setUserExpiry/{id}', [SDKController::class, 'setUserExpiry']);

Route::get('/get_devices', [RecordController::class, 'get_devices']);

Route::get('/get_logs_from_sdk', [RecordController::class, 'get_logs_from_sdk']);

Route::post('/getDevicesCountForTimezone', [SDKController::class, 'getDevicesCountForTimezone']);
Route::post('/{id}/WriteTimeGroup', [SDKController::class, 'processTimeGroup']);
//Route::post('/Person/AddRange', [SDKController::class, 'PersonAddRange']);
Route::post('/Person/AddRange/Photos', [SDKController::class, 'PersonAddRangePhotos']);
Route::post('/Person/AddRange/Photos/V1', [SDKControllerV1::class, 'PersonAddRangePhotos']);
Route::post('/uploadCustomersToCamera', [SDKController::class, 'uploadCustomersToCamera']);






Route::post('/SDK/{id}/{command}', [SDKController::class, 'handleCommand']);
//Route::post('/SDK/get-device-person-details', [SDKController::class, 'getPersonDetails']);
