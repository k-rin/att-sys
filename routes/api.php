<?php

use App\Enums\LeaveType;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\LeaveReportController;
use App\Http\Controllers\Api\V1\ManageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::get('users/me', [UserController::class, 'get']);
    Route::get('users/me/service-records', [UserController::class, 'getRecordList']);

    Route::post('users/me/leave-reports', [LeaveReportController::class, 'store']);
    Route::get('users/me/leave-reports', [LeaveReportController::class, 'getList']);
    Route::get('users/me/leave-reports/{id}', [LeaveReportController::class, 'get']);
    Route::put('users/me/leave-reports/{id}', [LeaveReportController::class, 'update']);
    Route::get('leave-reports/types', [LeaveReportController::class, 'getTypes']);

    Route::get('leave-reports', [ManageController::class, 'getReportList']);
    Route::get('leave-reports/{id}', [ManageController::class, 'getReport']);
    Route::put('leave-reports/{id}', [ManageController::class, 'updateReport']);
});