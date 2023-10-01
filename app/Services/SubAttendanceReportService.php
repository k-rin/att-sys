<?php

namespace App\Services;

use App\Enums\CompensationType;
use App\Enums\LeaveReject;
use App\Enums\ReportStatus;
use App\Repositories\CalendarRepository;
use App\Repositories\UserCalendarRepository;
use App\Repositories\CloseAttendanceRepository;
use App\Repositories\SubAttendanceReportRepository;
use App\Services\Traits\Calendar as TraitCalendar;
use App\Services\Traits\CloseAttendance as TraitCloseAttendance;
use Carbon\Carbon;

class SubAttendanceReportService
{
    use TraitCalendar, TraitCloseAttendance;

    protected $calendarRepository;
    protected $userCalendarRepository;
    protected $closeAttendanceRepository;
    protected $subAttendanceReportRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CalendarRepository            $calendarRepository
     * @param  \App\Repositories\UserCalendarRepository        $userCalendarRepository
     * @param  \App\Repositories\CloseAttendanceRepository     $closeAttendanceRepository
     * @param  \App\Repositories\SubAttendanceReportRepository $subAttendanceReportRepository
     * @return void
     */
    public function __construct(
        CalendarRepository            $calendarRepository,
        UserCalendarRepository        $userCalendarRepository,
        CloseAttendanceRepository     $closeAttendanceRepository,
        SubAttendanceReportRepository $subAttendanceReportRepository
    ) {
        $this->calendarRepository            = $calendarRepository;
        $this->userCalendarRepository        = $userCalendarRepository;
        $this->closeAttendanceRepository     = $closeAttendanceRepository;
        $this->subAttendanceReportRepository = $subAttendanceReportRepository;
    }

    /**
     * Create sub attendance report.
     *
     * @param  int   $userId
     * @param  array $values
     * @return string|void
     */
    public function create(int $userId, array $values)
    {
        $date = $this->getDate($userId, $values['date']);
        if (! $date->holiday) {
            return LeaveReject::ConditionLimit;
        }
        if ($report = $this->getByDate($userId, $values['date'])) {
            return LeaveReject::UniqueLimit;
        }
        if ($this->isClosedByDate($userId, $values['date'])) {
            return LeaveReject::MonthLimit;
        }

        $this->subAttendanceReportRepository->create(array_merge([
            'user_id' => $userId,
            'status'  => ReportStatus::Pending,
        ], $values));

        return;
    }

    /**
     * Get sub attendance report.
     *
     * @param  int $id
     * @return \App\Models\OvertimeReport
     */
    public function get(int $id)
    {
        if ($report = $this->subAttendanceReportRepository->get($id)) {
            $report['closed'] = $this->isClosedByDate($report->user_id, $report->date);
        }

        return $report;
    }

    /**
     * Get sub attendance report by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\SubAttendanceReport
     */
    public function getByDate(int $userId, string $date)
    {
        if ($report = $this->subAttendanceReportRepository->getByDate($userId, $date)) {
            $report['closed'] = $this->isClosedByDate($userId, $date);
        }

        return $report;
    }

    /**
     * Get sub attendance report list.
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
        return $this->subAttendanceReportRepository->getList($userInfo, $period, $paginate);
    }

    /**
     * Update sub attendance report.
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
            if (! $date->holiday) {
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
        $this->subAttendanceReportRepository->update($id, $values);

        return;
    }

    /**
     * Approve sub attendance report.
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

        $this->subAttendanceReportRepository->update($id, [
            'note'   => $values['note'],
            'status' => $status,
        ]);

        return true;
    }

    /**
     * Get uncompensated sub attendance report.
     *
     * @param  int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUncompensatedReport(int $userId)
    {
        $date    = Carbon::now()->subMonths(3);
        $reports = $this->subAttendanceReportRepository->getListByDate($userId, $date);

        return $reports->filter(function (\App\Models\SubAttendanceReport $value, int $key) {

            return empty($value->subHolidayReport) &&
                   $value->permitted &&
                   $value->compensation == CompensationType::Holiday;
        });
    }
}