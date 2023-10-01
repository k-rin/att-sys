<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * Login page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('web.login');
    }

    /**
     * Login.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google callback.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback()
    {
        $email = Socialite::driver('google')->user()->getEmail();

        if ($user = $this->userRepository->getByEmail($email)) {
           Auth::login($user);

           return redirect('/profile');
        }

        return view('web.login')->with([
            'error' => 'Invalid Google Account.',
        ]);
    }


    /**
     * Logout.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}