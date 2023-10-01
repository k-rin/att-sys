<?php

namespace App\Repositories;

use App\Models\OvertimeReport;

class OvertimeReportRepository
{
    /**
     * Create overtime report.
     *
     * @param  array $values
     * @return \App\Models\OvertimeReport
     */
    public function create(array $values)
    {
        return OvertimeReport::create($values);
    }

    /**
     * Get overtime report.
     *
     * @param  int $id
     * @return \App\Models\OvertimeReport
     */
    public function get(int $id)
    {
        return OvertimeReport::with('user')->find($id);
    }

    /**
     * Get overtime report by date.
     *
     * @param  int    $userId
     * @param  string $date
     * @return \App\Models\OvertimeReport
     */
    public function getByDate(int $userId, string $date)
    {
        return OvertimeReport::with('user')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Get overtime report list by conditions.
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
        $query = OvertimeReport::query();

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
     * Update overtime report.
     *
     * @param  int   $id
     * @param  array $values
     * @return int
     */
    public function update(int $id, array $values)
    {
        return OvertimeReport::find($id)->update($values);
    }
}