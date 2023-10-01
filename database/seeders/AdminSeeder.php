<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createUser('admin@test.com', 'password', 'Master', 0);
    }

    private function createUser(string $email, string $password, string $role, bool $locked)
    {
        $user = new Admin();
        $user->email = $email;
        $user->password = Hash::make($password, ['cost' => 4]);
        $user->role = $role;
        $user->locked = $locked;
        $user->save();
    }
}
