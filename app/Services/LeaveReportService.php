<?php

namespace App\Services;

use App\Enums\LeaveType;
use App\Enums\LeaveReject;
use App\Enums\ReportStatus;
use App\Repositories\CalendarRepository;
use App\Repositories\CloseAttendanceRepository;
use App\Repositories\LeaveReportRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserCalendarRepository;
use App\Services\Traits\CloseAttendance as TraitCloseAttendance;
use Carbon\CarbonImmutable as Carbon;
use Illuminate\Support\Arr;

class LeaveReportService
{
    use TraitCloseAttendance;

    const PAID_LEAVE_MIN = 12;
    const PAID_LEAVE_MAX = 30;

    protected $user        = null;
    protected $hasCalendar = null;
    protected $currentId   = null;

    protected $calendarRepository;
    protected $closeAttendanceRepository;
    protected $leaveReportRepository;
    protected $userRepository;
    protected $userCalendarRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CalendarRepository        $calendarRepository
     * @param  \App\Repositories\CloseAttendanceRepository $closeAttendanceRepository
     * @param  \App\Repositories\LeaveReportRepository     $leaveReportRepository
     * @param  \App\Repositories\UserRepository            $userRepository
     * @param  \App\Repositories\UserCalendarRepository    $userCalendarRepository
     * @return void
     */
    public function __construct(
        CalendarRepository        $calendarRepository,
        CloseAttendanceRepository $closeAttendanceRepository,
        LeaveReportRepository     $leaveReportRepository,
        UserRepository            $userRepository,
        UserCalendarRepository    $userCalendarRepository
    ) {
        $this->calendarRepository        = $calendarRepository;
        $this->closeAttendanceRepository = $closeAttendanceRepository;
        $this->leaveReportRepository     = $leaveReportRepository;
        $this->userRepository            = $userRepository;
        $this->userCalendarRepository    = $userCalendarRepository;
    }

    /**
     * Create leave report.
     *
     * @param  int   $userId
     * @param  array $values
     * @return string
     */
    public function create(int $userId, array $values)
    {
        if ($this->isClosedByPeriod($userId, $values['start_at'], $values['end_at'])) {
            return LeaveReject::MonthLimit;
        }

        $error = $this->checkLeaveLimit($userId, $values['type'], $values['start_at'], $values['end_at']);
        if (empty($error)) {
            $this->leaveReportRepository->create([
                'user_id'  => $userId,
                'start_at' => $values['start_at'],
                'end_at'   => $values['end_at'],
                'days'     => $this->getLeaveDays($userId, $values['start_at'], $values['end_at']),
                'type'     => $values['type'],
                'reason'   => Arr::get($values, 'reason', null),
                'status'   => Arr::get($values, 'status', ReportStatus::Pending),
                'note'     => Arr::get($values, 'note', null),
            ]);
        }

        return $error;
    }

    /**
     * Get leave report by id.
     *
     * @param  int $id
     * @return \App\Models\LeaveReport
     */
    public function get(int $id)
    {
        if ($report = $this->leaveReportRepository->get($id)) {

            $report['closed'] = $this->isClosedByPeriod($report->user_id, $report->start_at, $report->end_at);
        }

        return $report;
    }

    /**
     * Get leave report list by conditions.
     *
     * @param  array $userInfo
     * @param  array $period
     * @param  array $peginate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList(
        array $userInfo,
        array $period,
        array $paginate = []
    ) {
        return $this->leaveReportRepository->getList($userInfo, $period, $paginate);
    }

    /**
     * Update leave report.
     *
     * @param  int   $id
     * @param  array $values
     * @return string
     */
    public function update(int $id, array $values)
    {
        $report = $this->get($id);
        if ($report->closed ||
            $this->isClosedByPeriod($report->user_id, $values['start_at'], $values['end_at'])) {
            return LeaveReject::MonthLimit;
        }
        // 日数を計算するとき、自分をカウントしないように
        $this->currentId = $id;

        $error = $this->checkLeaveLimit($report->user_id, $values['type'], $values['start_at'], $values['end_at']);
        if (empty($error)) {
            $values['days']   = $this->getLeaveDays($report->user_id, $values['start_at'], $values['end_at']);
            $values['status'] = ReportStatus::Pending;
            $this->leaveReportRepository->update($id, $values);
        }

        return $error;
    }

    /**
     * Approve leave report.
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

        $this->leaveReportRepository->update($id, [
            'note'   => $values['note'],
            'status' => $status,
        ]);

        return true;
    }

    /**
     * Get leave summary by user.
     *
     * @param  int $userId
     * @param  int $year
     * @param  int $month
     * @return array
     */
    public function getLeaveSummary(int $userId, int $year, int $month)
    {
        $leave = [];
        foreach (LeaveType::getValues() as $type) {
            $leave[$type] = 0;
        }

        $date = Carbon::createFromDate($year, $month);

        $reports = $this->leaveReportRepository->getListByDate(
            $userId, $date->startOfYear(), $date->endOfMonth()
        );
        foreach ($reports as $report) {
            if ($report->rejected) {
                continue;
            }
            $days = $this->getLeaveDays( $userId, $report->start_at, $report->end_at, [
                'from' => $date->startOfYear(),
                'to'   => $date->endOfMonth(),
            ]);
            $leave[$report->type] = $leave[$report->type] + $days;
        }
        // menstrual leave count as sick leave when over 3 days
        if ($leave[LeaveType::MenstrualLeave] > 3) {
            $leave[LeaveType::SickLeave] += $leave[LeaveType::MenstrualLeave] - 3;
        }
        // family care leave count as personal leave
        if ($leave[LeaveType::FamilyCareLeave]) {
            $leave[LeaveType::PersonalLeave] += $leave[LeaveType::FamilyCareLeave];
        }

        return $leave;
    }

    /**
     * Check leave limit by type.
     *
     * @param  int    $userId
     * @param  int    $type
     * @param  string $startAt
     * @param  string $endAt
     * @return string
     */
    protected function checkLeaveLimit(int $userId, int $type, string $startAt, string $endAt)
    {
        $error   = '';
        $startAt = Carbon::parse($startAt);
        $endAt   = Carbon::parse($endAt);

        if (! $this->checkSexLimit($userId, $type)) {
            $error = LeaveReject::SexLimit;
        } elseif ($this->checkDayLimit($type)) {
            // leaves have no limit
        } elseif ($type == LeaveType::MenstrualLeave) {
            $error = $this->checkMenstrualLeave($userId, $startAt, $endAt);
        } elseif ($type == LeaveType::PaidLeave) {
            $error = $this->checkPaidLeave($userId, $startAt, $endAt);
        } elseif ($limit = LeaveType::getDayLimit($type)) {
            $period = $startAt->startOfYear()->toPeriod($endAt->endOfYear(), '1 year');
            foreach ($period as $year) {
                // get take leave days by year
                $take = $this->getLeaveDays($userId, $startAt, $endAt, [
                    'from' => $year->startOfYear(),
                    'to'   => $year->endOfYear(),
                ]);
                // get taked leave days by type
                $taked   = 0;
                $reports = $this->getList([ 'id' => $userId ], [ 'year' => $year ]);
                foreach ($reports as $report) {
                    if ($report->rejected || $report->id == $this->currentId) {
                        continue;
                    }
                    // family care leave count as personal leave
                    if ($report->type == $type
                        || ($type == LeaveType::PersonalLeave && $report->type == LeaveType::FamilyCareLeave)) {
                        $taked += $this->getLeaveDays($userId, $report->start_at, $report->end_at, [
                            'from' => $year->startOfYear(),
                            'to'   => $year->endOfYear(),
                        ]);
                    }
                }
                if (($take + $taked) > $limit) {
                    $error = LeaveReject::DayLimit;
                }
            }
        }

        return $error;
    }


    /**
     * Check menstrual leave.
     *
     * @param  int    $userId
     * @param  Carbon $startAt
     * @param  Carbon $endAt
     * @return string
     */
    protected function checkMenstrualLeave(int $userId, Carbon $startAt, Carbon $endAt)
    {
        $error  = '';
        $period = Carbon::parse($startAt->firstOfMonth())->toPeriod($endAt->lastOfMonth(), '1 month');
        foreach ($period as $date) {
            // get take leave days by month
            $take = $this->getLeaveDays($userId, $startAt, $endAt, [
                'from' => $date->startOfMonth(),
                'to'   => $date->endOfMonth(),
            ]);
            // get taked leave days by month
            $taked   = 0;
            $reports = $this->getList([ 'id' => $userId ], [
                'year'  => $date->year,
                'month' => $date->month,
            ]);
            foreach ($reports as $report) {
                if ($report->rejected || $report->id == $this->currentId) {
                    continue;
                }
                if ($report->type == LeaveType::MenstrualLeave) {
                    $taked += $this->getLeaveDays($userId, $report->start_at, $report->end_at, [
                        'from' => $date->startOfMonth(),
                        'to'   => $date->endOfMonth(),
                    ]);
                }
            }
            if (($take + $taked) > 1) {
                $error = LeaveReject::DayLimit;
            }
        }

        return $error;
    }

    /**
     * Check paid leave.
     *
     * @param  int    $userId
     * @param  Carbon $startAt
     * @param  Carbon $endAt
     * @return string
     */
    protected function checkPaidLeave(int $userId, Carbon $startAt, Carbon $endAt)
    {
        $error = '';
        $user  = $this->getUser($userId);
        if (Carbon::parse($user->hire_date)->floatDiffInMonths($startAt) < 3) {
            $error = LeaveReject::HireLimit;
        } else {
            $years = [];
            $date  = Carbon::parse("{$startAt->year}-04-01");
            if ($date > $startAt) {
                $years[] = $date->subYear();
            }
            if ($endAt > $date) {
                $years[] = $date;
            }
            foreach ($years as $year) {
                // get take leave days by fiscal year
                $take = $this->getLeaveDays($userId, $startAt, $endAt, [
                    'from' => $year,
                    'to'   => $year->addYear()->subSecond(),
                ]);
                // get unexpired paid leave
                $leave = $this->getPaidLeave($userId, $year->year);
                if ($take > $leave['unexpired']) {
                    $error = LeaveReject::DayLimit;
                }
            }
        }

        return $error;
    }

    /**
     * Get user paid leave.
     *
     * @param  int      $userId
     * @param  int|null $to
     * @return array
     */
    public function getPaidLeave(int $userId, int $to = null)
    {
        $user = $this->getUser($userId);
        $hire = Carbon::parse($user->hire_date);
        $from = ($hire >= Carbon::parse("{$hire->year}-04-01"))
              ? $hire->year
              : $hire->year - 1;
        if (empty($to)) {
            $now = Carbon::now('Asia/Taipei');
            $to  = ($now >= Carbon::parse("{$now->year}-04-01"))
                 ? $now->year
                 : $now->year - 1;
        }

        $unexpired = 0;
        $expired   = 0;
        $period    = Carbon::parse("{$from}-04-01")->toPeriod("{$to}-04-01", '1 year');
        foreach ($period as $ordinal => $year) {
            if ($ordinal == 0) {
                $months = $hire->floatDiffInMonths($year->addYear()->subDay());
                $days   = ($months > round($months))
                        ? round($months) + 0.5
                        : round($months);
            } else {
                $days = self::PAID_LEAVE_MIN + $ordinal > 30
                      ? self::PAID_LEAVE_MAX
                      : self::PAID_LEAVE_MIN + $ordinal;
            }

            $taked   = 0;
            $reports = $this->leaveReportRepository->getListByDate(
                $userId, $year, $year->addYear()->subSecond()
            );
            foreach ($reports as $report) {
                if ($report->rejected || $report->id == $this->currentId) {
                    continue;
                }
                if ($report->type == LeaveType::PaidLeave) {
                    $taked += $this->getLeaveDays($userId, $report->start_at, $report->end_at, [
                        'from' => $year,
                        'to'   => $year->addYear()->subSecond(),
                    ]);
                }
            }
            $days -= $taked;
            if ($ordinal >= ($period->count() - 2)) {
                $unexpired += $days;
            } else {
                $expired += $days;
            }
        }

        return [
            'unexpired' => $unexpired,
            'expired'   => $expired,
        ];
    }

    /**
     * Get leave days.
     *
     * @param  int    $userId
     * @param  string $startAt
     * @param  string $endAt
     * @param  array  $period
     * @return int
     */
    protected function getLeaveDays(int $userId, string $startAt, string $endAt, array $period = [])
    {
        $user    = $this->getUser($userId);
        $startAt = Carbon::parse($startAt);
        $endAt   = Carbon::parse($endAt);
        // 期間が設定された場合
        if (! empty($period)) {
            $from = Carbon::parse($period['from']);
            $to   = Carbon::parse($period['to']);
            $startAt = ($startAt > $from)
                ? $startAt
                : Carbon::parse("{$from->toDateString()} {$user->getAttendanceTime($from->toDateString())->start_time}");
            $endAt = ($endAt < $to)
                ? $endAt
                : Carbon::parse("{$to->toDateString()} {$user->getAttendanceTime($to->toDateString())->end_time}");
        }
        $days   = 0;
        $period = Carbon::parse($startAt->toDateString())->toPeriod($endAt->toDateString());
        foreach ($period as $date) {
            if ($this->isHoliday($userId, $date->toDateString())) {
                continue;
            }
            $time = $user->getAttendanceTime($date->toDateString());
            if ($startAt->toDateString() == $endAt->toDateString()) {
                if ($startAt->toTimeString() == $time->start_time) {
                    $days += 0.5;
                }
                if ($endAt->toTimeString() == $time->end_time) {
                    $days += 0.5;
                }
            } elseif ($date->toDateString() == $startAt->toDateString()) {
                if ($startAt->toTimeString() == $time->start_time) {
                    $days += 1;
                } else {
                    $days += 0.5;
                }
            } elseif ($date->toDateString() == $endAt->toDateString()) {
                if ($endAt->toTimeString() == $time->end_time) {
                    $days += 1;
                } else {
                    $days += 0.5;
                }
            } else {
                $days += 1;
            }
        }

        return $days;
    }

    /**
     * Get user.
     *
     * @param  int $userId
     * @return \App\Models\User
     */
    private function getUser(int $userId)
    {
        if (is_null($this->user)) {
            $this->user = $this->userRepository->get($userId);
        }

        return $this->user;
    }

    /**
     * Check leave sex limit.
     *
     * @param  int $userId
     * @param  int $type
     * @return bool
     */
    private function checkSexLimit(int $userId, int $type)
    {
        $check = true;
        if (LeaveType::getSexLimit($type)) {
            $user  = $this->getUser($userId);
            $check = LeaveType::getSexLimit($type) == $user->sex;
        }

        return $check;
    }

    /**
     * Check leave day limit.
     *
     * @param  int $type
     * @return bool
     */
    private function checkDayLimit(int $type)
    {
        return ($type == LeaveType::BereavementLeave ||
                $type == LeaveType::MedicalLeave     ||
                $type == LeaveType::MaternityLeave   ||
                $type == LeaveType::OfficialLeave);
    }

    /**
     * Check if date was holiday.
     *
     * @param  int    $userId
     * @param  string $date
     * @return bool
     */
    private function isHoliday(int $userId, string $date)
    {
        if (is_null($this->hasCalendar)) {
            $this->hasCalendar = $this->userCalendarRepository->check($userId);
        }

        if ($this->hasCalendar) {
            $day = $this->userCalendarRepository->get($userId, $date);
        } else {
            $day = $this->calendarRepository->get($date);
        }

        return $day->holiday;
    }
}
