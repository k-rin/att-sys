<?php

namespace App\Repositories;

use App\Models\CloseAttendance;

class CloseAttendanceRepository
{
    /**
     * Get close attendance.
     *
     * @param  int $userId
     * @param  int $year
     * @param  int $month
     * @return \App\Models\CloseAttendance
     */
    public function get(int $userId, int $year, int $month)
    {
        return CloseAttendance::where('user_id', $userId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    /**
     * Update or create service record.
     *
     * @param  int   $userId
     * @param  array $values
     * @return int
     */
    public function updateOrCreate(int $userId, array $values)
    {
        return CloseAttendance::updateOrCreate([
            'user_id' => $userId,
            'year'    => $values['year'],
            'month'   => $values['month'],
        ], [
            'locked'  => $values['locked'],
        ]);
    }
}