<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Repositories\AttendanceTimeRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $attendanceTimeRepository;
    protected $departmentRepository;
    protected $userRepository;
    protected $leaveReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\AttendanceTimeRepository $attendanceTimeRepository
     * @param  \App\Repositories\DepartmentRepository     $departmentRepository
     * @param  \App\Repositories\UserRepository           $userRepository
     * @param  \App\Services\LeaveReportService           $leaveReportService
     * @return void
     */
    public function __construct(
        AttendanceTimeRepository $attendanceTimeRepository,
        DepartmentRepository     $departmentRepository,
        UserRepository           $userRepository,
        LeaveReportService       $leaveReportService
    ) {
        $this->attendanceTimeRepository = $attendanceTimeRepository;
        $this->departmentRepository     = $departmentRepository;
        $this->userRepository           = $userRepository;
        $this->leaveReportService       = $leaveReportService;
    }

    /**
     * Get user by id.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $user  = $this->userRepository->get($id);
        $leave = $this->leaveReportService->getPaidLeave($id);

        return view('admin.user.detail')->with([
            'user'  => $user,
            'leave' => (object) $leave,
        ]);
    }

    /**
     * Get user list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->getList(
            $request->only(['name', 'alias', 'department_id']),
            $this->getPaginate($request)
        );

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.user.list')->with([
            'users' => $users
        ]);
    }

    /**
     * Edit user page.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $user        = $this->userRepository->get($id);
        $departments = $this->departmentRepository->getList();

        return view('admin.user.edit')->with([
            'user'        => $user,
            'departments' => $departments,
        ]);
    }

    /**
     * Update user by id.
     *
     * @param  \App\Http\Requests\UserRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, int $id)
    {
        $validated = $request->validated();

        $this->userRepository->update($id, $validated);

        return redirect("admin/users/{$id}");
    }

    /**
     * Create user page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = $this->departmentRepository->getList();

        return view('admin.user.create')->with([
            'departments' => $departments,
        ]);
    }

    /**
     * Create user.
     *
     * @param  \App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        $user = DB::transaction(function() use ($validated) {
            $user = $this->userRepository->create($validated);
            $this->attendanceTimeRepository->create($user);

            return $user;
        });

        return redirect("admin/users/{$user->id}");
    }
}