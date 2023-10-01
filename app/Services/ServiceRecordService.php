<?php

namespace App\Services;

use App\Repositories\CalendarRepository;
use App\Repositories\CloseAttendanceRepository;
use App\Repositories\LeaveReportRepository;
use App\Repositories\OvertimeReportRepository;
use App\Repositories\ServiceRecordRepository;
use App\Repositories\ServiceRecordReportRepository;
use App\Repositories\SubAttendanceReportRepository;
use App\Repositories\SubHolidayReportRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserCalendarRepository;
use App\Services\Traits\CloseAttendance as TraitCloseAttendance;
use Carbon\CarbonImmutable as Carbon;

class ServiceRecordService
{
    use TraitCloseAttendance;

    const FLEXTIME = 30;

    protected $calendarRepository;
    protected $closeAttendanceRepository;
    protected $leaveReportRepository;
    protected $overtimeReportRepository;
    protected $serviceRecordRepository;
    protected $serviceRecordReportRepository;
    protected $subAttendanceReportRepository;
    protected $subHolidayReportRepository;
    protected $userRepository;
    protected $userCalendarRepository;

    // 出勤状況サマリー
    protected $records;
    protected $comeLate;
    protected $leaveEarly;
    protected $absence;
    protected $overtime;
    protected $overtimeUnit;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CalendarRepository            $calendarRepository
     * @param  \App\Repositories\CloseAttendanceRepository     $closeAttendanceRepository
     * @param  \App\Repositories\LeaveReportRepository         $leaveReportRepository
     * @param  \App\Repositories\OvertimeReportRepository      $overtimeReportRepository
     * @param  \App\Repositories\ServiceRecordRepository       $serverRecordRepository
     * @param  \App\Repositories\ServiceRecordReportRepository $serviceRecordReportRepository
     * @param  \App\Repositories\SubAttendanceReportRepository $subAttendanceReportRepository
     * @param  \App\Repositories\SubHolidayReportRepository    $subHolidayReportRepository
     * @param  \App\Repositories\UserRepository                $userRepository
     * @param  \App\Repositories\UserCalendarRepository        $userCalendarRepository
     * @return void
     */
    public function __construct(
        CalendarRepository            $calendarRepository,
        CloseAttendanceRepository     $closeAttendanceRepository,
        LeaveReportRepository         $leaveReportRepository,
        OvertimeReportRepository      $overtimeReportRepository,
        ServiceRecordRepository       $serverRecordRepository,
        ServiceRecordReportRepository $serverRecordReportRepository,
        SubAttendanceReportRepository $subAttendanceReportRepository,
        SubHolidayReportRepository    $subHolidayReportRepository,
        UserRepository                $userRepository,
        UserCalendarRepository        $userCalendarRepository
    ) {
        $this->calendarRepository            = $calendarRepository;
        $this->closeAttendanceRepository     = $closeAttendanceRepository;
        $this->leaveReportRepository         = $leaveReportRepository;
        $this->overtimeReportRepository      = $overtimeReportRepository;
        $this->serviceRecordRepository       = $serverRecordRepository;
        $this->serviceRecordReportRepository = $serverRecordReportRepository;
        $this->subAttendanceReportRepository = $subAttendanceReportRepository;
        $this->subHolidayReportRepository    = $subHolidayReportRepository;
        $this->userRepository                = $userRepository;
        $this->userCalendarRepository        = $userCalendarRepository;
    }

    /**
     * Get service record list.
     *
     * @param  int $userId
     * @param  int $year
     * @param  int $month
     * @return array
     */
    public function getList(int $userId, int $year, int $month)
    {
        $this->initialSummary();

        $user     = $this->userRepository->get($userId);
        $calendar = $this->getCalendar($userId, $year, $month);
        $records  = $this->serviceRecordRepository->getList($userId, $year, $month);

        // 指定した月でループを回す
        foreach ($calendar as $day) {
            // レコード初期化
            $abnormal = [];
            $detail   = [
                'date'     => $day->date,
                'week'     => $day->week,
                'start_at' => '',
                'end_at'   => '',
                'holiday'  => $day->holiday,
            ];
            $date = $day->date;
            // まだ入社してない
            if ($date < $user->hire_date) {
                $this->addRecord($detail, $abnormal);
                continue;
            }
            // 各種届、申請を取得
            $leaveReport         = $this->leaveReportRepository->getByDate($userId, $date);
            $overtimeReport      = $this->overtimeReportRepository->getByDate($userId, $date);
            $subAttendanceReport = $this->subAttendanceReportRepository->getByDate($userId, $date);
            $subHolidayReport    = $this->subHolidayReportRepository->getByDate($userId, $date);
            $serviceRecordReport = $this->serviceRecordReportRepository->get($userId, $date);
            $detail['leave_report']          = empty($leaveReport->permitted)         ? null : $leaveReport;
            $detail['overtime_report']       = empty($overtimeReport->permitted)      ? null : $overtimeReport;
            $detail['sub_attendance_report'] = empty($subAttendanceReport->permitted) ? null : $subAttendanceReport;
            $detail['sub_holiday_report']    = empty($subHolidayReport->permitted)    ? null : $subHolidayReport;
            $detail['service_record_report'] = empty($serviceRecordReport)            ? null : $serviceRecordReport;

            $startAt = null;
            $endAt   = null;
            // 訂正の勤務時間がある
            if ($serviceRecordReport) {
                $detail['revised_start_at'] = $serviceRecordReport->start_at;
                $detail['revised_end_at']   = $serviceRecordReport->end_at;
                // 訂正申請が許可されたら、訂正した時間とする
                if ($serviceRecordReport->permitted) {
                    $startAt = $detail['revised_start_at'];
                    $endAt   = $detail['revised_end_at'];
                }
            }
            // 所定始業時間を取得
            $time      = $user->getAttendanceTime($date);
            $startTime = Carbon::parse("{$date} {$time->start_time}");
            // 打刻記録を取得
            $record = $records->filter(function ($item) use ($date) {
                return $item['date'] == $date;
            });
            // 打刻記録がある
            if ($record->count()) {
                $detail['start_at'] = $record->first()->start_at;
                $detail['end_at']   = $record->first()->end_at;
                // 訂正の勤務時間がなければ、記録の時間とする
                $startAt = $startAt ?? $detail['start_at'];
                $endAt   = $endAt   ?? $detail['end_at'];
            }

            // 打刻データがない
            if ($startAt == null && $endAt == null) {
                // 祝日の場合
                if ($day->holiday) {
                    $this->addRecord($detail, $abnormal);
                    continue;
                }
                // 全日休暇の場合
                if ($leaveReport &&
                    $leaveReport->permitted &&
                    ! $this->isHalfDayLeave($leaveReport, $date, $startTime)) {
                    $this->addRecord($detail, $abnormal);
                    continue;
                }
                // 振替休暇の場合
                if ($subHolidayReport &&
                    $subHolidayReport->permitted) {
                    $this->addRecord($detail, $abnormal);
                    continue;
                }
                $this->absence ++;
                $abnormal[] = '欠勤';
                $this->addRecord($detail, $abnormal);
                continue;
            }
            // 打刻データ不完全
            if ($startAt == null || $endAt == null) {
                $abnormal[] = '打刻漏れ';
                $this->addRecord($detail, $abnormal);
                continue;
            }
            // 祝日の場合
            if ($day->holiday) {
                if (empty($subAttendanceReport) ||
                    ! $subAttendanceReport->permitted) {
                        $abnormal[] = '祝日出勤';
                        $this->addRecord($detail, $abnormal);
                        continue;
                }
            }
            // 休暇届がある
            if ($leaveReport && $leaveReport->permitted) {
                // 全休の場合
                if (! $this->isHalfDayLeave($leaveReport, $date, $startTime)) {
                    $abnormal[] = '休日出勤';
                // 午前休の場合（半日休フレックスなし）
                } elseif (Carbon::parse($leaveReport->end_at)->eq($startTime->addHours(4))) {
                    $abnormal = $this->checkServiceRecord($date, $startAt, $endAt, $startTime->addHours(5), $startTime->addHours(9));
                // 午後休の場合（半日休フレックスなし）
                } elseif (Carbon::parse($leaveReport->start_at)->eq($startTime->addHours(5))) {
                    $abnormal = $this->checkServiceRecord($date, $startAt, $endAt, $startTime, $startTime->addHours(4));
                }
            } else {
                $abnormal = $this->checkServiceRecord($date, $startAt, $endAt, $startTime, $startTime->addHours(9), true);
            }

            $this->addRecord($detail, $abnormal);
        }

        return [
            'records' => $this->records,
            'closed'  => $this->isClosedByMonth($userId, $year, $month),
            'summary' => [
                'come_late'      => $this->comeLate,
                'leave_early'    => $this->leaveEarly,
                'absence'        => $this->absence,
                'over_time'      => $this->overtime,
                'over_time_unit' => $this->overtimeUnit,
            ],
        ];
    }

    /**
     * Get service record by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\ServiceRecord
     */
    public function get(int $userId, string $date)
    {
        $record = $this->serviceRecordRepository->get($userId, $date);

        $record['closed'] = $this->isClosedByDate($userId, $date);

        return $record;
    }

    /**
     * Update or create service record.
     *
     * @param  int    $userId
     * @param  string $date
     * @param  array  $values
     * @return int
     */
    public function updateOrCreate(int $userId, string $date, array $values)
    {
        if ($this->isClosedByDate($userId, $date)) {
            return false;
        }
        $this->serviceRecordRepository->updateOrCreate($userId, $date, $values);

        return true;
    }


    /**
     * Initialize summary.
     */
    private function initialSummary()
    {
        $this->records      = collect([]);
        $this->comeLate     = 0;
        $this->leaveEarly   = 0;
        $this->absence      = 0;
        $this->overtime     = 0;
        $this->overtimeUnit = 0;
    }

    /**
     * Add formatted detail to records.
     *
     * @param  array $detail
     * @param  array $abnormal
     * @return void
     */
    private function addRecord(array $detail, array $abnormal)
    {
        $detail['abnormal'] = implode(' | ', $abnormal);
        $this->records->push((object) $detail);
    }

    /**
     * Check if half-day leave.
     *
     * @param  \App\Models\LeaveReport $report
     * @param  string $date
     * @param  string $startTime
     * @return bool
     */
    private function isHalfDayLeave(
        \App\Models\LeaveReport $report,
        string $date,
        string $startTime
    ) {
        $startAt   = Carbon::parse($report->start_at);
        $endAt     = Carbon::parse($report->end_at);
        $startTime = Carbon::parse($startTime);

        if ($startAt->toDateString() == $date || $endAt->toDateString() == $date) {
            if ($startAt == $startTime->addHours(5) || $endAt == $startTime->addHours(4)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Check service record.
     *
     * @param  string $date
     * @param  string $startAt
     * @param  string $endAt
     * @param  string $startTime
     * @param  string $endTime
     * @param  bool   $flex
     * @return array
     */
    private function checkServiceRecord(
        string $date,
        string $startAt,
        string $endAt,
        string $startTime,
        string $endTime,
        bool   $flex = false
    ) {
        // 所定の始業、終業と勤務時間
        $startTime  = Carbon::parse($startTime);
        $endTime    = Carbon::parse($endTime);
        $workTime   = $startTime->diffInMinutes($endTime);
        // 実際の始業、終業と勤務時間
        $startAt    = Carbon::parse("{$date} {$startAt}");
        // 実際の始業時間が所定の始業時間より早い場合、所定の始業時間を実際の始業時間とする
        $startAt    = $startAt < $startTime ? $startTime : $startAt;
        $endAt      = Carbon::parse("{$date} {$endAt}");
        $recordTime = $startAt->diffInMinutes($endAt);

        if ($flex) {
            $startTime = $startTime->addMinutes(SELF::FLEXTIME);
            $endTime   = $endTime->addMinutes(SELF::FLEXTIME);
        }

        $abnormal = [];
        $comeLate = $startTime->diffInMinutes($startAt, false);
        if ($comeLate > 0) {
            $this->comeLate += $comeLate;
            $abnormal[] = "遅刻: {$comeLate}分";
            // 遅刻した場合、所定の終業時間までいないと早退になる
            $leaveEarly = $endAt->diffInMinutes($endTime, false);
            if ($leaveEarly > 0) {
                $this->leaveEarly += $leaveEarly;
                $abnormal[] = "早退: {$leaveEarly}分";
            }
        } else {
            if ($recordTime < $workTime) {
                // 所定の勤務時間を達していない場合、早退になる
                $leaveEarly = $workTime - $recordTime;
                $this->leaveEarly += $leaveEarly;
                $abnormal[] = "早退: {$leaveEarly}分";
            }
        }
        // 残業時間
        if ($recordTime > 9 * 60) {
            $overtimeStartAt = ($comeLate > 0) ? $endTime : $startAt->addHours(9);
            $overtime = $overtimeStartAt->diffInMinutes($endAt, false);
            if ($overtime > 0) {
                $overtimeUnit = floor($overtime / 15);
                $this->overtime += $overtime;
                $this->overtimeUnit += $overtimeUnit;
                $abnormal[] = "残業: {$overtime}分({$overtimeUnit}単位)";
            }
        }

        return $abnormal;
    }

    /**
     * Get calendar.
     *
     * @param  int $userId
     * @param  int $year
     * @param  int $month
     * @return \App\Models\Calendar|\App\Models\UserCalendar
     */
    private function getCalendar(int $userId, int $year, int $month)
    {
        if ($this->userCalendarRepository->check($userId)) {
            $calendar = $this->userCalendarRepository->getList($userId, $year, $month);
        } else {
            $calendar = $this->calendarRepository->getList($year, $month);
        }

        return $calendar;
    }
}