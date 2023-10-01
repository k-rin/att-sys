<?php

namespace App\Repositories;

use App\Models\LeaveReport;

class LeaveReportRepository
{
    /**
     * Create leave report.
     *
     * @param  array $values
     * @return \App\Models\LeaveReport
     */
    public function create(array $values)
    {
        return LeaveReport::create($values);
    }

    /**
     * Get leave report by id.
     *
     * @param  int $id
     * @return \App\Models\LeaveReport
     */
    public function get(int $id)
    {
        return LeaveReport::with('user')->find($id);
    }

    /**
     * Get leave report list by conditions.
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
        $query = LeaveReport::query();

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
            $query->where(function ($query) use ($period) {
                $query
                    ->whereYear('start_at', $period['year'])
                    ->orWhereYear('end_at', $period['year']);
            });
        }

        if (! empty($period['month'])) {
            $query->where(function ($query) use ($period) {
                $query
                    ->whereMonth('start_at', $period['month'])
                    ->orWhereMonth('end_at', $period['month']);
            });
        }

        return empty($paginate)
            ? $query->get()
            : $query->orderBy($paginate['column'], $paginate['order'])->paginate($paginate['limit']);
    }

    /**
     * Get leave report list by date.
     *
     * @param  int    $userId
     * @param  string $startAt
     * @param  string $endAt
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getListByDate(int $userId, string $startAt, string $endAt)
    {
        return LeaveReport::where('user_id', $userId)
            ->where(function ($query) use ($startAt, $endAt) {
                $query
                    ->whereBetween('start_at', [ $startAt, $endAt ])
                    ->orWhereBetween('end_at', [ $startAt, $endAt ]);
            })
            ->get();
    }

    /**
     * Get leave report by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\LeaveReport
     */
    public function getByDate(int $userId, string $date)
    {
        return LeaveReport::where('user_id', $userId)
            ->whereDate('start_at', '<=', $date)
            ->whereDate('end_at', '>=', $date)
            ->first();
    }

    /**
     * Update leave report.
     *
     * @param  int   $id
     * @param  array $values
     * @return int
     */
    public function update(int $id, array $values)
    {
        return LeaveReport::find($id)->update($values);
    }
}