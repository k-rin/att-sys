<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdminService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $adminService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\AdminService $adminService
     * @return void
     */
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Login page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.login');
    }

    /**
     * Login.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('admin/users');
        }

        return redirect('admin');
    }

    /**
     * Logout.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('admin');
    }

    /**
     * Send password reset url. (axios api)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'email' => 'bail|required|email|exists:admins',
        ]);

        $this->adminService->sendResetMail($validated['email']);

        return response()->json([], 200);
    }

    /**
     * Password reset page.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $error = $this->adminService->checkResetToken($validated['token']);

        return view('admin.password_reset')->with([
            'error' => $error,
            'token' => $validated['token'],
        ]);
    }

    /**
     * Update admin password. (axios api)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required',
            'token'    => 'required',
        ]);

        $token    = $validated['token'];
        $password = $validated['password'];

        if ($error = $this->adminService->checkResetToken($token)) {
             return response()->json([], 400);
        }

        $this->adminService->resetPassword($token, $password);

        return response()->json([], 201);
    }
}