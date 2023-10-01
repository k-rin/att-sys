<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Get user by id.
     *
     * @param  int $id
     * @return \App\Models\User
     */
    public function get(int $id)
    {
        return User::with(['department', 'attendanceTimes'])->find($id);
    }

    /**
     * Get user by email.
     *
     * @param  string $email
     * @return \App\Models\User
     */
    public function getByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get user list by conditions.
     *
     * @param  array $conditions
     * @param  array $paginate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList(array $conditions, array $paginate)
    {
        $query = User::query();

        foreach ($conditions as $key => $value) {
            if (empty($value)) {
                continue;
            }
            if (in_array($key, ['name', 'alias'])) {
                $query->where($key, 'like', "%{$value}%");
            } else {
                $query->where($key, $value);
            }
        }

        return empty($paginate)
            ? $query->get()
            : $query->orderBy($paginate['column'], $paginate['order'])->paginate($paginate['limit']);
    }

    /**
     * Update user.
     *
     * @param  int   $id
     * @param  array $values
     * @return int
     */
    public function update(int $id, array $values)
    {
        return User::find($id)->update($values);
    }

    /**
     * Create user.
     *
     * @param  array $values
     * @return \App\Models\User
     */
    public function create(array $values)
    {
        return User::create($values);
    }
}