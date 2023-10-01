<?php

namespace App\Services;

use App\Enums\CompensationType;
use App\Enums\LeaveReject;
use App\Enums\ReportStatus;
use App\Repositories\CalendarRepository;
use App\Repositories\UserCalendarRepository;
use App\Repositories\CloseAttendanceRepository;
use App\Repositories\SubHolidayReportRepository;
use App\Services\Traits\Calendar as TraitCalendar;
use App\Services\Traits\CloseAttendance as TraitCloseAttendance;

class SubHolidayReportService
{
    use TraitCalendar, TraitCloseAttendance;

    protected $calendarRepository;
    protected $userCalendarRepository;
    protected $closeAttendanceRepository;
    protected $subHolidayReportRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CalendarRepository         $calendarRepository
     * @param  \App\Repositories\UserCalendarRepository     $userCalendarRepository
     * @param  \App\Repositories\CloseAttendanceRepository  $closeAttendanceRepository
     * @param  \App\Repositories\SubHolidayReportRepository $subHolidayReportRepository
     * @return void
     */
    public function __construct(
        CalendarRepository         $calendarRepository,
        UserCalendarRepository     $userCalendarRepository,
        CloseAttendanceRepository  $closeAttendanceRepository,
        SubHolidayReportRepository $subHolidayReportRepository
    ) {
        $this->calendarRepository         = $calendarRepository;
        $this->userCalendarRepository     = $userCalendarRepository;
        $this->closeAttendanceRepository  = $closeAttendanceRepository;
        $this->subHolidayReportRepository = $subHolidayReportRepository;
    }

    /**
     * Create sub holiday report.
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

        $this->subHolidayReportRepository->create(array_merge([
            'user_id' => $userId,
            'status'  => ReportStatus::Pending,
        ], $values));

        return;
    }

    /**
     * Get sub holiday report.
     *
     * @param  int $id
     * @return \App\Models\OvertimeReport
     */
    public function get(int $id)
    {
        if ($report = $this->subHolidayReportRepository->get($id)) {
            $report['closed'] = $this->isClosedByDate($report->user_id, $report->date);
        }

        return $report;
    }

    /**
     * Get sub holiday report by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\SubAttendanceReport
     */
    public function getByDate(int $userId, string $date)
    {
        if ($report = $this->subHolidayReportRepository->getByDate($userId, $date)) {
            $report['closed'] = $this->isClosedByDate($userId, $date);
        }

        return $report;
    }

    /**
     * Get sub holiday report list.
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
        return $this->subHolidayReportRepository->getList($userInfo, $period, $paginate);
    }

    /**
     * Update sub holiday report.
     *
     * @param  int   $id
     * @param  array $values
     * @return string|void
     */
    public function update(int $id, array $values)
    {
        $report = $this->get($id);
        if (empty($report) || $report->closed) {
            return LeaveReject::MonthLimit;
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

        $values['status'] = ReportStatus::Pending;
        $this->subHolidayReportRepository->update($id, $values);

        return;
    }

    /**
     * Approve sub holiday report.
     *
     * @param  int   $id
     * @param  array $values
     * @return bool
     */
    public function approve(int $id, array $values)
    {
        $report = $this->get($id);
        if (empty($report) || $report->closed) {
            return false;
        }

        $status = $report->getUpdateStatus($values['layer'], $values['status']);

        $this->subHolidayReportRepository->update($id, [
            'status' => $status,
        ]);

        return true;
    }
}