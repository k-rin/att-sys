<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PasswordResetRepository
{
    /**
     * Get password reset info by token.
     *
     * @param  string $tokrn
     * @return \App\Models\PasswordReset
     */
    public function getByToken(string $token)
    {
        return PasswordReset::where('token', $token)->firstOrFail();
    }

    /**
     * Create or update password reset info.
     *
     * @param  string $email
     * @return \App\Models\PasswordReset
     */
    public function updateOrCreate(string $email)
    {
        return PasswordReset::updateOrCreate([
                'email' => $email,
            ], [
                'token'      => Str::uuid(),
                'reset_at'   => null,
                'expired_at' => Carbon::now()->addDays(3),
            ]);
    }

    /**
     * Update password reset info.
     *
     * @param  string $email
     * @param  array  $values
     * @return int
     */
    public function update(string $email, array $values)
    {
        return PasswordReset::where('email', $email)->update($values);
    }
}