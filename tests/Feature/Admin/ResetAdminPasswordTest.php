<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetAdminPassword;
use Carbon\Carbon;

class ResetAdminPasswordTest extends TestCase
{
    /**
     * 執行方法
     * php artisan test tests/Feature/Admin/ResetAdminPasswordTest.php
     */

    use DatabaseTransactions;

    const EMAIL = 'test@test.com';
    const PASSWORD = 'test';
    const NEW_PASSWORD = 'abc123';
    public function setUp(): void
    {
        parent::setUp();

        // admins塞入測試資料
        Admin::create([
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
            'role' => 'Master',
            'locked' => 0,
        ]);
    }
    
    // Email欄位未輸入
    public function testEmailEmpty()
    {
        $response = $this->post('/admin/reset_confirm', ['email' => '']);

        $response->assertStatus(302);
        $response->assertInvalid(['email' => 'The email field is required.']);
    }

    // Email欄位輸入不存在email
    public function testEmailFail()
    {
        $response = $this->post('/admin/reset_confirm', ['email' => 'test1@test.com']);

        $response->assertStatus(302);
        $response->assertInvalid(['email' => 'The selected email is invalid.']);
    }

    // 重設密碼確認頁
    public function testConfirmPage()
    {
        $response = $this->post('/admin/reset_confirm', ['email' => self::EMAIL]);

        $response->assertSee('是否確定重置此帳號:'.self::EMAIL.'之密碼?');
    }

    // 成功重設密碼
    public function testResetSuccess()
    {
        Mail::fake();
        $responseEmail = $this->post('/admin/reset', ['email' => self::EMAIL]);
        $responseEmail->assertSee('已將重置密碼連結寄至'.self::EMAIL);
        Mail::assertSent(ResetAdminPassword::class);

        $token = PasswordReset::where('email', self::EMAIL)->first()->token;
        $responseResetPage = $this->get('/admin/reset_password/'.$token);
        $responseResetPage->assertSee('New Password');

        $responseReset = $this->post('/admin/reset_password_update', [
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
            'token' => $token,
        ]);
        $responseReset->assertRedirectContains('admin/index');

        $this->assertDatabaseMissing('admins', [
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ]);
    }

    // 重設密碼輸入錯誤
    public function testResetPasswordFail()
    {
        Mail::fake();
        $responseEmail = $this->post('/admin/reset', ['email' => self::EMAIL]);
        Mail::assertSent(ResetAdminPassword::class);

        $token = PasswordReset::where('email', self::EMAIL)->first()->token;
        $responseReset = $this->post('/admin/reset_password_update', [
            'password' => '',
            'password_confirmation' => '',
            'token' => $token,
        ]);
        $responseReset->assertInvalid([
            'password' => 'The password field is required.',
            'password_confirmation' => 'The password confirmation field is required.',
        ]);

        $responseReset = $this->post('/admin/reset_password_update', [
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD.'123',
            'token' => $token,
        ]);
        $responseReset->assertInvalid(['password' => 'The password confirmation does not match.']);
    }

    // 重置密碼後，token失效
    public function testTokenInvalid()
    {
        Mail::fake();
        $responseEmail = $this->post('/admin/reset', ['email' => self::EMAIL]);
        Mail::assertSent(ResetAdminPassword::class);

        $token = PasswordReset::where('email', self::EMAIL)->first()->token;
        $responseReset = $this->post('/admin/reset_password_update', [
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
            'token' => $token,
        ]);
        $responseReset->assertRedirectContains('admin/index');

        $responseResetAgain = $this->get('/admin/reset_password/'.$token);
        $responseResetAgain->assertSee('此連結已無效');
    }

    // 重置密碼連結，三天後過期
    public function testUrlExpired()
    {
        Mail::fake();
        $responseEmail = $this->post('/admin/reset', ['email' => self::EMAIL]);
        Mail::assertSent(ResetAdminPassword::class);

        $passwordReset = PasswordReset::where('email', self::EMAIL)->first();
        $passwordReset->expired_at = Carbon::now()->subMinute();
        $passwordReset->save();

        $responseResetExpired = $this->get('/admin/reset_password/'.$passwordReset->token);
        $responseResetExpired->assertSee('此連結已過期');
    }
}
