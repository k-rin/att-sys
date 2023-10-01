<?php

namespace App\Http\Controllers\Web\Department\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\SubHolidayReportService;
use Illuminate\Http\Request;

class SubHolidayReportController extends Controller
{
    protected $userRepository;
    protected $subHolidayReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository          $userRepository
     * \App\Repositories\SubHolidayReportService $subAttendanceReportService
     * @return void
     */
    public function __construct(
        UserRepository          $userRepository,
        SubHolidayReportService $subHolidayReportService
    ) {
        $this->userRepository          = $userRepository;
        $this->subHolidayReportService = $subHolidayReportService;
    }

    /**
     *  Show user sub holiday report list.
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
        $reports = $this->subHolidayReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.sub-holiday-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }
}