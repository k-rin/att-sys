<?php

namespace App\Http\Controllers\Web\Department\User;

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
     * \App\Repositories\UserRepository             $userRepository
     * \App\Repositories\SubAttendanceReportService $subAttendanceReportService
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
     *  Show user sub attendance report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $user = $this->userRepository->get($id);

        $this->authorize('view', $user);

        $request = $this->completeDate($request);
        $reports = $this->subAttendanceReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.sub-attendance-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }
}