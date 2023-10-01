<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\PasswordResetRepository;
use App\Mail\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    protected $adminRepository;
    protected $passwordResetRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\AdminRepository         $adminRepository
     * @param  \App\Repositories\PasswordResetRepository $passwordResetRepository
     * @return void
     */
    public function __construct(
        AdminRepository         $adminRepository,
        PasswordResetRepository $passwordResetRepository,
    ) {
        $this->adminRepository         = $adminRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * Send password reset url.
     *
     * @param  string $email
     * @return void
     */
    public function sendResetMail(string $email)
    {
        $reset = $this->passwordResetRepository->updateOrCreate($email);

        //$url = route('password.reset', ['token' => $reset->token]);
        $url = url("/admin/password/reset?token={$reset->token}");

        Mail::to($email)->send(new PasswordReset($url));

        return;
    }

    /**
     * Check reset token.
     *
     * @param  string $token
     * @return string
     */
    public function checkResetToken(string $token)
    {
        $error = '';
        $reset = $this->passwordResetRepository->getByToken($token);

        if ($reset->reset_at) {
            $error = 'The token has been used.';
        } elseif (Carbon::parse($reset->expired_at) < Carbon::now()) {
            $error = 'The token is expired.';
        }

        return $error;
    }

    /**
     * Reset password.
     *
     * @param  string $token
     * @param  string $password
     * @return void
     */
    public function resetPassword(string $token, string $password)
    {
        $reset = $this->passwordResetRepository->getByToken($token);
        $admin = $this->adminRepository->getByEmail($reset->email);

        DB::transaction(function() use ($reset, $admin, $password) {
            $this->adminRepository->update($admin->id, [
                'password' => Hash::make($password, ['cost' => 4]),
            ]);
            $this->passwordResetRepository->update($reset->email, [
                'reset_at' => Carbon::now(),
            ]);
        });

        return;
    }
 }