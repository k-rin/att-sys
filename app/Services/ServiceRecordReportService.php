<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Repositories\CloseAttendanceRepository;
use App\Repositories\ServiceRecordReportRepository;
use App\Services\Traits\CloseAttendance as TraitCloseAttendance;
use Carbon\Carbon;

class ServiceRecordReportService
{
    use TraitCloseAttendance;

    protected $closeAttendanceRepository;
    protected $serviceRecordReportRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CloseAttendanceRepository     $closeAttendanceRepository
     * @param  \App\Repositories\ServiceRecordReportRepository $serviceRecordReportRepository
     * @return void
     */
    public function __construct(
        CloseAttendanceRepository     $closeAttendanceRepository,
        ServiceRecordReportRepository $serviceRecordReportRepository
    ) {
        $this->closeAttendanceRepository     = $closeAttendanceRepository;
        $this->serviceRecordReportRepository = $serviceRecordReportRepository;
    }

    /**
     * Get service record report.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\ServiceRecordReport
     */
    public function get(int $userId, string $date)
    {
        $date = Carbon::parse($date);

        if ($report = $this->serviceRecordReportRepository->get($userId, $date)) {
            $closed = $this->closeAttendanceRepository->get($userId, $date->year, $date->month);

            $report['closed'] = empty($closed) ? 0 : $closed->locked;
        }

        return $report;
    }

    /**
     * Update service record report.
     *
     * @param  int    $userId
     * @param  string $date
     * @param  array  $values
     * @return bool
     */
    public function update(int $userId, string $date, array $values)
    {
        if ($this->isClosedByDate($userId, $date)) {
            return false;
        }

        $values['status'] = ReportStatus::Pending;
        $this->serviceRecordReportRepository->updateOrCreate($userId, $date, $values);

        return true;
    }

    /**
     * Approve service record report.
     *
     * @param  int    $userId
     * @param  string $date
     * @param  array  $values
     * @return bool
     */
    public function approve(int $userId, string $date, array $values)
    {
        $report = $this->get($userId, $date);
        if (empty($report) || $report->closed) {
            return false;
        }

        $status = $report->getUpdateStatus($values['layer'], $values['status']);

        $this->serviceRecordReportRepository->updateOrCreate(
            $userId, $date, [ 'status' => $status ]
        );

        return true;
    }
}