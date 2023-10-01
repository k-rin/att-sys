<?php

namespace App\Exports;

use App\Enums\LeaveType;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use App\Services\ServiceRecordService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServiceRecordExport implements FromView
{
    protected $userId;
    protected $year;
    protected $month;
    protected $userRepository;
    protected $leaveReportService;
    protected $serviceRecordService;

    public function __construct(
        int                  $userId,
        int                  $year,
        int                  $month,
        UserRepository       $userRepository,
        LeaveReportService   $leaveReportService,
        ServiceRecordService $serviceRecordService
    ) {
        $this->userId               = $userId;
        $this->year                 = $year;
        $this->month                = $month;
        $this->userRepository       = $userRepository;
        $this->leaveReportService   = $leaveReportService;
        $this->serviceRecordService = $serviceRecordService;
    }

    public function view(): View
    {
        $user    = $this->userRepository->get($this->userId);
        $records = $this->serviceRecordService->getList($this->userId, $this->year, $this->month);
        $leave   = $this->leaveReportService->getLeaveSummary($this->userId, $this->year, $this->month);

        $records['summary']['sick_leave'] = $leave[LeaveType::SickLeave];
        $records['summary']['prenatal_care_leave'] = $leave[LeaveType::PrenatalCareLeave];

        return view('admin.service-record.export')->with([
            'user'    => $user,
            'records' => $records['records'],
            'summary' => (object) $records['summary'],
        ]);
    }
}