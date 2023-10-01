<?php

namespace App\Repositories;

use App\Models\AttendanceTime;

class AttendanceTimeRepository
{
    /**
     * Get user attendance time list.
     *
     * @param  int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList(int $userId)
    {
        return AttendanceTime::where('user_id', $userId)->get();
    }

    /**
     * Create user attendance time with default.
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function create(\App\Models\User $user)
    {
        AttendanceTime::create([
            'user_id'    => $user->id,
            'date'       => $user->hire_date,
            'start_time' => '09:00:00',
            'end_time'   => '18:00:00',
        ]);

        return;
    }

    /**
     * Update or create user attendance time.
     *
     * @param  int    $userId
     * @param  array  $values
     * @return \App\Models\AttendanceTime
     */
    public function updateOrCreate(int $userId, array $values)
    {
        $time = AttendanceTime::where([
            'user_id' => $userId,
            'date'    => $values['date'],
        ])->first();

        if ($time) {
            AttendanceTime::where([
                'user_id' => $userId,
                'date'    => $values['date'],
            ])
            ->update([
                'start_time' => $values['start_time'],
                'end_time'   => $values['end_time'],
            ]);
        } else {
            $time = AttendanceTime::create([
                'user_id'    => $userId,
                'date'       => $values['date'],
                'start_time' => $values['start_time'],
                'end_time'   => $values['end_time'],
            ]);
        }

        return $time;
    }
}