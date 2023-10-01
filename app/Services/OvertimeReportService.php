<?php

namespace App\Services;

use App\Enums\LeaveReject;
use App\Enums\ReportStatus;
use App\Repositories\CalendarRepository;
use App\Repositories\UserCalendarRepository;
use App\Repositories\CloseAttendanceRepository;
use App\Repositories\OvertimeReportRepository;
use App\Services\Traits\Calendar as TraitCalendar;
use App\Services\Traits\CloseAttendance as TraitCloseAttendance;

class OvertimeReportService
{
    use TraitCalendar, TraitCloseAttendance;

    protected $calendarRepository;
    protected $userCalendarRepository;
    protected $closeAttendanceRepository;
    protected $overtimeReportRepository;

    /**
     * Create a new service instance
     *
     * @param  \App\Repositories\CalendarRepository        $calendarRepository
     * @param  \App\Repositories\UserCalendarRepository    $userCalendarRepository
     * @param  \App\Repositories\CloseAttendanceRepository $closeAttendanceRepository
     * @param  \App\Repositories\OvertimeReportRepository  $overtimeReportRepository
     * @return void
     */
    public function __construct(
        CalendarRepository        $calendarRepository,
        UserCalendarRepository    $userCalendarRepository,
        CloseAttendanceRepository $closeAttendanceRepository,
        OvertimeReportRepository  $overtimeReportRepository
    ) {
        $this->calendarRepository        = $calendarRepository;
        $this->userCalendarRepository    = $userCalendarRepository;
        $this->closeAttendanceRepository = $closeAttendanceRepository;
        $this->overtimeReportRepository  = $overtimeReportRepository;
    }

    /**
     * Create overtime report.
     *
     * @param  int   $userId
     * @param  array $values
     * @return string|void
     */
    public function create(int $userId, array $values)
    {
        $date = $this->getDate($userId, $values['date']);
        if ($date->holiday) {
            return LeaveReject::ConditionLimit;
        }
        if ($report = $this->getByDate($userId, $values['date'])) {
            return LeaveReject::UniqueLimit;
        }
        if ($this->isClosedByDate($userId, $values['date'])) {
            return LeaveReject::MonthLimit;
        }

        $this->overtimeReportRepository->create(array_merge([
            'user_id' => $userId,
            'status'  => ReportStatus::Pending,
        ], $values));

        return;
    }

    /**
     * Get overtime report.
     *
     * @param  int $id
     * @return \App\Models\OvertimeReport
     */
    public function get(int $id)
    {
        if ($report = $this->overtimeReportRepository->get($id)) {
            $report['closed'] = $this->isClosedByDate($report->user_id, $report->date);
        }

        return $report;
    }

    /**
     * Get overtime report by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\OvertimeReport
     */
    public function getByDate(int $userId, string $date)
    {
        if ($report = $this->overtimeReportRepository->getByDate($userId, $date)) {
            $report['closed'] = $this->isClosedByDate($userId, $date);
        }

        return $report;
    }

    /**
     * Get overtime report list.
     *
     * @param  array $userInfo
     * @param  array $period
     * @param  array $paginate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList(
        array $userInfo,
        array $period,
        array $paginate = []
    ) {
        return $this->overtimeReportRepository->getList($userInfo, $period, $paginate);
    }

    /**
     * Update overtime report.
     *
     * @param  int   $id
     * @param  array $values
     * @return string|void
     */
    public function update(int $id, array $values)
    {
        $report = $this->get($id);
        if (empty($report) || $report->closed) {
            return LeaveReject::UniqueLimit;
        }

        if ($values['date'] != $report->date) {
            $userId = $report->user_id;
            $date   = $this->getDate($userId, $values['date']);
            if ($date->holiday) {
                return LeaveReject::ConditionLimit;
            }
            if ($this->isClosedByDate($userId, $values['date'])) {
                return LeaveReject::MonthLimit;
            }
            if ($report = $this->getByDate($report->user->id, $values['date'])) {
                return LeaveReject::UniqueLimit;
            }
        }

        $this->overtimeReportRepository->update($id, array_merge($values, [
            'status' => ReportStatus::Pending,
        ]));

        return;
    }

    /**
     * Approve overtime report.
     *
     * @param  int   $id
     * @param  array $values
     * @return bool
     */
    public function approve($id, array $values)
    {
        $report = $this->get($id);
        if (empty($report) || $report->closed) {
            return false;
        }

        $status = $report->getUpdateStatus($values['layer'], $values['status']);

        $this->overtimeReportRepository->update($id, [
            'note'   => $values['note'],
            'status' => $status,
        ]);

        return true;
    }
}
