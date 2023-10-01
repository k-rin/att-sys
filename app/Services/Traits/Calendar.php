<?php

namespace App\Services\Traits;

Trait Calendar
{
    /**
     * Check calendar by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\Calendar|\App\Models\UserCalendar
     */
    private function getDate(int $userId, string $date)
    {
        $calendar = $this->userCalendarRepository->get($userId, $date);
        if (empty($date)) {
            $calendar = $this->calendarRepository->get($date);
        }

        return $calendar;
    }
}