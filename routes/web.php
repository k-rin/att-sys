<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'namespace' => 'App\Http\Controllers\Web',
], function () {
    Route::get('/', 'AuthController@index')->name('web.login');
    Route::get('/login', 'AuthController@login');
    Route::get('/login/callback', 'AuthController@callback');

    Route::group([
        'middleware' => [ 'auth:web', 'locked' ],
    ], function () {
        Route::post('/logout', 'AuthController@logout');
        Route::get('/profile', 'UserController@show');
        Route::get('/service-records', 'ServiceRecordController@index');
        Route::get('/service-record-reports/{date}', 'ServiceRecordReportController@show');
        Route::put('/service-record-reports/{date}', 'ServiceRecordReportController@update');
        Route::get('/leave-reports', 'LeaveReportController@index');
        Route::get('/leave-reports/create', 'LeaveReportController@create');
        Route::post('/leave-reports', 'LeaveReportController@store');
        Route::get('/leave-reports/{id}', 'LeaveReportController@show');
        Route::put('/leave-reports/{id}', 'LeaveReportController@update');
        Route::get('/overtime-reports', 'OvertimeReportController@index');
        Route::get('/overtime-reports/create', 'OvertimeReportController@create');
        Route::post('/overtime-reports', 'OvertimeReportController@store');
        Route::get('/overtime-reports/{id}', 'OvertimeReportController@show')->where('id', '[0-9]+');
        Route::put('/overtime-reports/{id}', 'OvertimeReportController@update');
        Route::get('/sub-attendance-reports', 'SubAttendanceReportController@index');
        Route::get('/sub-attendance-reports/create', 'SubAttendanceReportController@create');
        Route::post('/sub-attendance-reports', 'SubAttendanceReportController@store');
        Route::get('/sub-attendance-reports/{id}', 'SubAttendanceReportController@show')->where('id', '[0-9]+');
        Route::put('/sub-attendance-reports/{id}', 'SubAttendanceReportController@update');
        Route::get('/sub-attendance-reports/uncompensated', 'SubAttendanceReportController@uncompensated');
        Route::get('/sub-holiday-reports', 'SubHolidayReportController@index');
        Route::get('/sub-holiday-reports/create', 'SubHolidayReportController@create');
        Route::post('/sub-holiday-reports', 'SubHolidayReportController@store');
        Route::get('/sub-holiday-reports/{id}', 'SubHolidayReportController@show')->where('id', '[0-9]+');
        Route::put('/sub-holiday-reports/{id}', 'SubHolidayReportController@update');
        Route::get('/attendance-times/{date}', 'AttendanceTimeController@show');

        Route::group([
            'prefix'     => 'departments',
            'namespace'  => 'Department',
            'middleware' => 'can:isManager',
        ], function () {
            Route::get('/users', 'UserController@index');
            Route::get('/users/{id}', 'UserController@show');
            Route::get('/users/{id}/service-records', 'User\ServiceRecordController@index');
            Route::get('/users/{id}/service-record-reports/{date}', 'User\ServiceRecordReportController@show');
            Route::put('/users/{id}/service-record-reports/{date}', 'User\ServiceRecordReportController@update');
            Route::get('/users/{id}/leave-reports', 'User\LeaveReportController@index');
            Route::get('/users/{id}/overtime-reports', 'User\OvertimeReportController@index');
            Route::get('/users/{id}/sub-attendance-reports', 'User\SubAttendanceReportController@index');
            Route::get('/users/{id}/sub-holiday-reports', 'User\SubHolidayReportController@index');
            Route::get('/leave-reports', 'LeaveReportController@index');
            Route::get('/leave-reports/{id}', 'LeaveReportController@show');
            Route::put('/leave-reports/{id}', 'LeaveReportController@update');
            Route::get('/overtime-reports', 'OvertimeReportController@index');
            Route::get('/overtime-reports/{id}', 'OvertimeReportController@show');
            Route::put('/overtime-reports/{id}', 'OvertimeReportController@update');
            Route::get('/sub-attendance-reports', 'SubAttendanceReportController@index');
            Route::get('/sub-attendance-reports/{id}', 'SubAttendanceReportController@show');
            Route::put('/sub-attendance-reports/{id}', 'SubAttendanceReportController@update');
            Route::get('/sub-holiday-reports', 'SubHolidayReportController@index');
            Route::get('/sub-holiday-reports/{id}', 'SubHolidayReportController@show');
            Route::put('/sub-holiday-reports/{id}', 'SubHolidayReportController@update');
        });
    });
});