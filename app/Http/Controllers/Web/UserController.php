<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepository;
    protected $leaveReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserRepository $userRepository
     * @param  \App\Services\LeaveReportService $leaveReportService
     * @return void
     */
    public function __construct(
        UserRepository     $userRepository,
        LeaveReportService $leaveReportService
    ) {
        $this->userRepository     = $userRepository;
        $this->leaveReportService = $leaveReportService;
    }

    /**
     * Show user profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user  = $this->userRepository->get(Auth::id());
        $leave = $this->leaveReportService->getPaidLeave(Auth::id());

        return view('web.user.detail')->with([
            'user'  => $user,
            'leave' => (object) $leave,
        ]);
    }
}