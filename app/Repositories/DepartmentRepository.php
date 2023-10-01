<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository
{
    /**
     * Get department by id.
     *
     * @param  int $id
     * @return Department
     */
    public function get(int $id)
    {
        return Department::with('manager')->find($id);
    }

    /**
     * Get department list.
     *
     * @return Collection
     */
    public function getList()
    {
        return Department::with('manager')->get();
    }

    /**
     * Create department.
     *
     * @param  array $values
     * @return Department
     */
    public function create(array $values)
    {
        $values['parent_id'] = 0;

        return Department::create($values);
    }

    /**
     * Update department.
     *
     * @param  int   $id
     * @param  array $values
     * @return int
     */
    public function update(int $id, array $values)
    {
        return Department::find($id)->update($values);
    }
}