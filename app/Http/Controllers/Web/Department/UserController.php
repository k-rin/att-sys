<?php

namespace App\Http\Controllers\Web\Department;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $leaveReportService;
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\LeaveReportService $leaveReportService
     * @param  \App\Repositories\UserRepository $userRepository
     * @return void
     */
    public function __construct(
        LeaveReportService $leaveReportService,
        UserRepository     $userRepository
    ) {
        $this->leaveReportService = $leaveReportService;
        $this->userRepository     = $userRepository;
    }

    /**
     * Get user by id.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $user = $this->userRepository->get($id);

        $this->authorize('view', $user);

        $leave = $this->leaveReportService->getPaidLeave($id);

        return view('web.user.detail')->with([
            'user'  => $user,
            'leave' => (object) $leave,
        ]);
    }

    /**
     * Get user list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request->merge([
            'department_id' => Auth::user()->department_id
        ]);

        $users = $this->userRepository->getList(
            $request->only(['name', 'alias', 'department_id']),
            $this->getPaginate($request)
        );

        return view('web.user.list')->with([
            'users' => $users,
        ]);
    }
}