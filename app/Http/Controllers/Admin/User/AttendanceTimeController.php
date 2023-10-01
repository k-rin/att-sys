<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\AttendanceTimeRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class AttendanceTimeController extends Controller
{
    protected $attendanceTimeRepository;
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\AttendanceTimeRepository $attendanceTimeRepository
     * @param  \App\Repositories\UserRepository           $userRepository
     * @return void
     */
    public function __construct(
        AttendanceTimeRepository $attendanceTimeRepository,
        UserRepository           $userRepository
    ) {
        $this->attendanceTimeRepository = $attendanceTimeRepository;
        $this->userRepository           = $userRepository;
    }

    /**
     * Get user attendance time list.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(int $id)
    {
        $times = $this->attendanceTimeRepository->getList($id);
        $user  = $this->userRepository->get($id);

        return view('admin.attendance-time.list')->with([
            'times' => $times,
            'user'  => $user,
        ]);
    }

    /**
     * Show user attendance time.
     *
     * @param  int    $id
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, string $date)
    {
        $user = $this->userRepository->get($id);
        $time = $user->getAttendanceTime($date);

        return response()->json([
            'start_time' => $time->start_time,
            'end_time'   => $time->end_time,
        ]);
    }

    /**
     * Create user attendance time.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, int $id)
    {
        $this->attendanceTimeRepository->updateOrCreate(
            $id,
            $request->only(['date', 'start_time', 'end_time']),
        );

        return redirect("/admin/users/{$id}/attendance-times");
    }
}