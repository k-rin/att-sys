<?php

namespace App\Repositories;

use App\Models\Calendar;

class CalendarRepository
{
    /**
     * Get calendar by date.
     *
     * @param  string $date
     * @return Calendar
     */
    public function get(string $date)
    {
        return Calendar::where('date', $date)->first();
    }

    /**
     * Get calendar list by month.
     *
     * @param  int $year
     * @param  int $month
     * @return Collection
     */
    public function getList(int $year, int $month)
    {
        return Calendar::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
    }

    /**
     * Update calendar by date.
     *
     * @param  string $date
     * @param  array  $values
     * @return int
     */
    public function update(string $date, array $values)
    {
        return Calendar::where('date', $date)
            ->update($values);
    }
}