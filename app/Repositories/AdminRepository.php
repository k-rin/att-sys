<?php

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository
{
    /**
     * Create admin.
     *
     * @param  array $values
     * @return \App\Models\Admin
     */
    public function create(array $values)
    {
        return Admin::create($values);
    }

    /**
     * Get admin by id.
     *
     * @param  int $id
     * @return \App\Models\Admin
     */
    public function get(int $id)
    {
        return Admin::find($id);
    }

    /**
     * Get admin list.
     *
     * @param  array $paginate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList(array $paginate)
    {
        return Admin::orderBy($paginate['column'], $paginate['order'])
            ->paginate($paginate['limit']);
    }

    /**
     * Update admin by id.
     *
     * @param  int   $id
     * @param  array $values
     * @return \App\Models\Admin
     */
    public function update(int $id, array $values)
    {
        return Admin::find($id)->update($values);
    }

    /**
     * Get admin by email.
     *
     * @param  string $email
     * @return \App\Models\Admin
     */
    public function getByEmail(string $email)
    {
        return Admin::where('email', $email)->first();
    }
}