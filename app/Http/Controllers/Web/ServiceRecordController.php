<?php

namespace App\Http\Controllers\Web;

use App\Enums\LeaveType;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use App\Services\ServiceRecordService;
use App\Services\SubAttendanceReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRecordController extends Controller
{
    protected $userRepository;
    protected $leaveReportService;
    protected $serviceRecordService;
    protected $subAttendanceReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserRepository         $userRepository
     * @param  \App\Services\LeaveReportService         $leaveReportService
     * @param  \App\Services\ServiceRecordService       $serviceRecordService
     * @param  \App\Services\SubAttendanceReportService $subAttendanceReportService
     * @return void
     */
    public function __construct(
        UserRepository             $userRepository,
        LeaveReportService         $leaveReportService,
        ServiceRecordService       $serviceRecordService,
        SubAttendanceReportService $subAttendanceReportService
    ) {
        $this->userRepository             = $userRepository;
        $this->leaveReportService         = $leaveReportService;
        $this->serviceRecordService       = $serviceRecordService;
        $this->subAttendanceReportService = $subAttendanceReportService;
    }

    /**
     * Get user service record list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = $this->userRepository->get(Auth::id());

        $request = $this->completeDate($request);
        $records = $this->serviceRecordService->getList(
            Auth::id(), $request->input('year'), $request->input('month'),
        );
        $leave = $this->leaveReportService->getLeaveSummary(
            Auth::id(), $request->input('year'), $request->input('month'),
        );
        $records['summary']['sick_leave'] = $leave[LeaveType::SickLeave];
        $records['summary']['prenatal_care_leave'] = $leave[LeaveType::PrenatalCareLeave];

        $reports = $this->subAttendanceReportService->getUncompensatedReport(Auth::id());

        return view('web.service-record.list')->with([
            'records'       => $records['records'],
            'closed'        => $records['closed'],
            'summary'       => (object) $records['summary'],
            'uncompensated' => $reports->count(),
            'user'          => $user,
        ]);
    }
}
