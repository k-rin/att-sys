<?php

namespace App\Repositories;

use App\Models\ServiceRecord;

class ServiceRecordRepository
{
    /**
     * Get service record.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\ServiceRecord
     */
    public function get(int $userId, string $date)
    {
        return ServiceRecord::where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Get service record list.
     *
     * @param  int $userId
     * @param  int $year
     * @param  int $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList(int $userId, int $year, int $month)
    {
        return ServiceRecord::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
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
        return ServiceRecord::updateOrCreate([
            'user_id' => $userId,
            'date'    => $date,
        ], $values);
    }
}