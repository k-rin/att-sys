<?php

namespace App\Repositories;

use App\Models\SubHolidayReport;

class SubHolidayReportRepository
{
    /**
     * Create sub holiday report.
     *
     * @param  array $values
     * @return \App\Models\SubHolidayReport
     */
    public function create(array $values)
    {
        return SubHolidayReport::create($values);
    }

    /**
     * Get sub holiday report.
     *
     * @param  int $id
     * @return \App\Models\SubHolidayReport
     */
    public function get(int $id)
    {
        return SubHolidayReport::with('user', 'subAttendanceReport')->find($id);
    }

    /**
     * Get sub holiday report by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\SubHolidayReport
     */
    public function getByDate(int $userId, string $date)
    {
        return SubHolidayReport::with('user', 'subAttendanceReport')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Get sub attendance report list by conditions.
     *
     * @param  array $userIds
     * @param  array $period
     * @param  array $paginate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList(
        array $userInfo,
        array $period,
        array $paginate = []
    ) {
        $query = SubHolidayReport::with('subAttendanceReport');

        if (! empty($userInfo)) {
            $query->withWhereHas('user', function ($query) use ($userInfo) {
                foreach ($userInfo as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    if (in_array($key, ['name', 'alias'])) {
                        $query->where($key, 'like', "%{$value}%");
                    } else {
                        $query->where($key, $value);
                    }
                }
            });
        }

        if (! empty($period['year'])) {
            $query->whereYear('date', $period['year']);
        }

        if (! empty($period['month'])) {
            $query->whereMonth('date', $period['month']);
        }

        return empty($paginate)
            ? $query->get()
            : $query->orderBy($paginate['column'], $paginate['order'])->paginate($paginate['limit']);
    }

    /**
     * Update sub holiday report.
     *
     * @param  int   $id
     * @param  array $values
     * @return int
     */
    public function update(int $id, array $values)
    {
        return SubHolidayReport::find($id)->update($values);
    }
}