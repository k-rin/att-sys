<?php

namespace App\Http\Controllers\Web\Department\User;

use App\Enums\LeaveType;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use App\Services\ServiceRecordService;
use Illuminate\Http\Request;

class ServiceRecordController extends Controller
{
    protected $userRepository;
    protected $leaveReportService;
    protected $serviceRecordService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserRepository   $userRepository
     * @param  \App\Services\LeaveReportService   $leaveReportService
     * @param  \App\Services\ServiceRecordService $serviceRecordService
     * @return void
     */
    public function __construct(
        UserRepository       $userRepository,
        LeaveReportService   $leaveReportService,
        ServiceRecordService $serviceRecordService
    ) {
        $this->userRepository       = $userRepository;
        $this->leaveReportService   = $leaveReportService;
        $this->serviceRecordService = $serviceRecordService;
    }

    /**
     * Get user service record list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $user = $this->userRepository->get($id);

        $this->authorize('view', $user);

        $request = $this->completeDate($request);
        $records = $this->serviceRecordService->getList(
            $id, $request->input('year'), $request->input('month'),
        );
        $leave = $this->leaveReportService->getLeaveSummary(
            $id, $request->input('year'), $request->input('month'),
        );
        $records['summary']['sick_leave'] = $leave[LeaveType::SickLeave];
        $records['summary']['prenatal_care_leave'] = $leave[LeaveType::PrenatalCareLeave];

        return view('web.service-record.list')->with([
            'records' => $records['records'],
            'closed'  => $records['closed'],
            'summary' => (object) $records['summary'],
            'user'    => $user,
        ]);
    }
}