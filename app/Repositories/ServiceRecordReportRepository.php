<?php

namespace App\Repositories;

use App\Models\ServiceRecordReport;

class ServiceRecordReportRepository
{
    /**
     * Get service record report.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\ServiceRecordReport
     */
    public function get(int $userId, string $date)
    {
        return ServiceRecordReport::where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Update or create service record report.
     *
     * @param  int    $userId
     * @param  string $date
     * @param  array  $values
     * @return int
     */
    public function updateOrCreate(int $userId, string $date, array $values)
    {
        return ServiceRecordReport::updateOrCreate([
            'user_id' => $userId,
            'date'    => $date,
        ], $values);
    }
}
