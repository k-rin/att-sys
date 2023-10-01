<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\CloseAttendanceRepository;
use Illuminate\Http\Request;

class CloseAttendanceController extends Controller
{
    protected $closeAttendanceRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\CloseAttendanceRepository $closeAttendanceRepository
     * @return void
     */
    public function __construct(
        CloseAttendanceRepository $closeAttendanceRepository
    ) {
        $this->closeAttendanceRepository = $closeAttendanceRepository;
    }

    /**
     * Update user close attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function  update(Request $request, int $id)
    {
        $this->closeAttendanceRepository->updateOrCreate(
            $id, $request->only([ 'year', 'month', 'locked' ])
        );

        return response()->json([], 201);
    }
}