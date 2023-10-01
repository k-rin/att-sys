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
    'prefix'    => 'admin',
    'namespace' => 'App\Http\Controllers\Admin',
], function () {
    Route::get('/', 'AuthController@index')->name('admin.login');
    Route::post('login', 'AuthController@login');
    Route::post('create', 'AuthController@create');
    Route::post('password/reset', 'AuthController@send')->name('password.reset');
    Route::get('password/reset', 'AuthController@reset');
    Route::put('password', 'AuthController@update');

    Route::group([
        'middleware' => [ 'auth:admin', 'locked' ],
    ], function () {

        Route::group([
            'middleware' => 'can:isNotReadonly'
        ], function () {
            Route::get('/users/create', 'UserController@create');
            Route::post('/users', 'UserController@store');
            Route::get('/users/{id}/edit', 'UserController@edit');
            Route::put('/users/{id}', 'UserController@update');
            Route::get('/users/{id}/service-records/{date}', 'User\ServiceRecordController@show')->where(['date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}']);
            Route::put('/users/{id}/service-records/{date}', 'User\ServiceRecordController@update');
            Route::post('/users/{id}/calendar', 'User\CalendarController@store');
            Route::get('/users/{id}/calendar/check', 'User\CalendarController@check');
            Route::get('/users/{id}/calendar/{date}', 'User\CalendarController@show');
            Route::put('/users/{id}/calendar/{date}', 'User\CalendarController@update');
            Route::post('/users/{id}/attendance-times', 'User\AttendanceTimeController@store');
            Route::put('/users/{id}/close-attendances', 'User\CloseAttendanceController@update');
            Route::get('/calendar/import', 'CalendarController@create');
            Route::post('/calendar/import', 'CalendarController@store');
            Route::get('/calendar/{date}', 'CalendarController@show');
            Route::put('/calendar/{date}', 'CalendarController@update');
            Route::get('/service-record/import', 'ServiceRecordController@create');
            Route::post('/service-record/import', 'ServiceRecordController@store');
            Route::post('/users/{id}/leave-reports', 'User\LeaveReportController@store');
            });

        // 届の承認は Gate を使わず（readonly でも権限があれば承認可能）
        Route::put('/users/{id}/service-record-reports/{date}', 'User\ServiceRecordReportController@update');
        Route::put('/leave-reports/{id}', 'LeaveReportController@update');
        Route::put('/overtime-reports/{id}', 'OvertimeReportController@update');
        Route::put('/sub-attendance-reports/{id}', 'SubAttendanceReportController@update');
        Route::put('/sub-holiday-reports/{id}', 'SubHolidayReportController@update');

        Route::post('/logout', 'AuthController@logout');
        Route::get('/users', 'UserController@index');
        Route::get('/users/{id}', 'UserController@show');
        Route::get('/users/{id}/leave-reports', 'User\LeaveReportController@index');
        Route::get('/users/{id}/overtime-reports', 'User\OvertimeReportController@index');
        Route::get('/users/{id}/sub-attendance-reports', 'User\SubAttendanceReportController@index');
        Route::get('/users/{id}/sub-holiday-reports', 'User\SubHolidayReportController@index');
        Route::get('/users/{id}/service-records', 'User\ServiceRecordController@index');
        Route::get('/users/{id}/service-records/export', 'User\ServiceRecordController@export');
        Route::get('/users/{id}/service-record-reports/{date}', 'User\ServiceRecordReportController@show');
        Route::get('/users/{id}/attendance-times', 'User\AttendanceTimeController@index');
        Route::get('/users/{id}/attendance-times/{date}', 'User\AttendanceTimeController@show');
        Route::get('/users/{id}/calendar', 'User\CalendarController@index');
        Route::get('/leave-reports', 'LeaveReportController@index');
        Route::get('/leave-reports/{id}', 'LeaveReportController@show');
        Route::get('/overtime-reports', 'OvertimeReportController@index');
        Route::get('/overtime-reports/{id}', 'OvertimeReportController@show');
        Route::get('/sub-attendance-reports', 'SubAttendanceReportController@index');
        Route::get('/sub-attendance-reports/{id}', 'SubAttendanceReportController@show');
        Route::get('/sub-holiday-reports', 'SubHolidayReportController@index');
        Route::get('/sub-holiday-reports/{id}', 'SubHolidayReportController@show');
        Route::get('/calendar', 'CalendarController@index');

        Route::group([
            'middleware' => 'can:isMaster',
        ], function () {
            Route::get('/departments', 'DepartmentController@index');
            Route::post('/departments', 'DepartmentController@store');
            Route::get('/departments/create', 'DepartmentController@create');
            Route::get('/departments/{id}', 'DepartmentController@show');
            Route::get('/departments/{id}/edit', 'DepartmentController@edit');
            Route::put('/departments/{id}', 'DepartmentController@update');
            Route::post('/admin-users', 'AdminController@store');
            Route::get('/admin-users', 'AdminController@index');
            Route::get('/admin-users/create', 'AdminController@create');
            Route::get('/admin-users/{id}', 'AdminController@show');
            Route::get('/admin-users/{id}/edit', 'AdminController@edit');
            Route::put('/admin-users/{id}', 'AdminController@update');
            Route::get('/report-approvers', 'ReportApproverController@index');
            Route::put('/report-approvers/{id}', 'ReportApproverController@update');
        });
    });
});
