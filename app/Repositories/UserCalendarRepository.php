<?php

namespace App\Repositories;

use App\Models\Calendar;
use App\Models\UserCalendar;

class UserCalendarRepository
{
    /**
     * Create user calendar.
     *
     * @param  int $userId
     * @return void
     */
    public function create(int $userId)
    {
        UserCalendar::insertUsing([
            'user_id',
            'date',
            'holiday',
            'note',
            'created_at',
            'updated_at'
        ], Calendar::selectRaw("{$userId} as user_id , date, holiday, note, now() as created_at, now() as updated_at"));

        return;
    }

    /**
     * Check if user calendar exist.
     *
     * @param  int $userId
     * @return Boolean
     */
    public function check(int $userId)
    {
        return UserCalendar::where('user_id', $userId)->exists();
    }

    /**
     * Get calendar by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return Calendar
     */
    public function get(int $userId, string $date)
    {
        return UserCalendar::where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Get calendar list by month.
     *
     * @param  int $userId
     * @param  int $year
     * @param  int $month
     * @return Collection
     */
    public function getList(int $userId, int $year, int $month)
    {
        return UserCalendar::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
    }

    /**
     * Update calendar by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @param  array  $values
     * @return int
     */
    public function update(int $userId, string $date, array $values)
    {
        return UserCalendar::where('user_id', $userId)
            ->where('date', $date)
            ->update($values);
    }
}