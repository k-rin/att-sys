<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\SubAttendanceReportService;
use Illuminate\Http\Request;

class SubAttendanceReportController extends Controller
{
    protected $userRepository;
    protected $subAttendanceReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository         $userRepository
     * \App\Services\subAttendanceReportService $subAttendanceReportService
     * @return void
     */
    public function __construct(
        UserRepository             $userRepository,
        SubAttendanceReportService $subAttendanceReportService
    ) {
        $this->userRepository             = $userRepository;
        $this->subAttendanceReportService = $subAttendanceReportService;
    }

    /**
     * Get user sub attendance report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $request = $this->completeDate($request);
        $user    = $this->userRepository->get($id);
        $reports = $this->subAttendanceReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.sub-attendance-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }
}