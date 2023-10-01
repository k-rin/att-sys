<?php

namespace App\Services\Traits;

use Carbon\Carbon;

Trait CloseAttendance
{
    /**
     * Check if month is closed by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return int
     */
    private function isClosedByDate(int $userId, string $date)
    {
        $date   = Carbon::parse($date);
        $closed = $this->closeAttendanceRepository->get($userId, $date->year, $date->month);

        return empty($closed) ? 0 : $closed->locked;
    }

    /**
     * Check if month is closed by month.
     *
     * @param  int $userId
     * @param  int $year
     * @param  int $month
     * @return int
     */
    private function isClosedByMonth(int $userId, int $year, int $month)
    {
        $closed = $this->closeAttendanceRepository->get($userId, $year, $month);

        return empty($closed) ? 0 : $closed->locked;
    }

    /**
     * Check if month is closed by period.
     *
     * @param  int    $userId
     * @param  string $startAt
     * @param  string $endAt
     * @return int
     */
    private function isClosedByPeriod(int $userId, string $startAt, string $endAt)
    {
        $startAt  = Carbon::parse($startAt);
        $closed   = $this->closeAttendanceRepository->get($userId, $startAt->year, $startAt->month);
        $isClosed = empty($closed) ? 0 : $closed->locked;

        if ($isClosed == 0) {
            $endAt    = Carbon::parse($endAt);
            $closed   = $this->closeAttendanceRepository->get($userId, $endAt->year, $endAt->month);
            $isClosed = empty($closed) ? 0 : $closed->locked;
        }

        return $isClosed;
    }
}