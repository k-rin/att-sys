<?php

namespace App\Repositories;

use App\Models\SubAttendanceReport;

class SubAttendanceReportRepository
{
    /**
     * Create sub attendance report.
     *
     * @param  array $values
     * @return \App\Models\SubAttendanceReport
     */
    public function create(array $values)
    {
        return SubAttendanceReport::create($values);
    }

    /**
     * Get sub attendance report.
     *
     * @param  int $id
     * @return \App\Models\SubAttendanceReport
     */
    public function get(int $id)
    {
        return SubAttendanceReport::with([ 'user', 'subHolidayReport' ])->find($id);
    }

    /**
     * Get sub attendance report by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\SubAttendanceReport
     */
    public function getByDate(int $userId, string $date)
    {
        return SubAttendanceReport::with([ 'user', 'subHolidayReport' ])
            ->where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Get sub attendance report list by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getListByDate(int $userId, string $date)
    {
        return SubAttendanceReport::with([ 'user', 'subHolidayReport' ])
            ->where('user_id', $userId)
            ->where('date', '>=', $date)
            ->get();
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
        $query = SubAttendanceReport::query();

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
     * Update sub attendance report.
     *
     * @param  int   $id
     * @param  array $values
     * @return int
     */
    public function update(int $id, array $values)
    {
        return SubAttendanceReport::find($id)->update($values);
    }
}