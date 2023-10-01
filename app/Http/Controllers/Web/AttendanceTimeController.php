<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AttendanceTimeController extends Controller
{
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserRepository $userRepository
     * @return void
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * Show user attendance time.
     *
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function show(string $date)
    {
        $user = $this->userRepository->get(Auth::id());
        $time = $user->getAttendanceTime($date);

        return response()->json([
            'start_time' => $time->start_time,
            'end_time'   => $time->end_time,
        ]);
    }
}