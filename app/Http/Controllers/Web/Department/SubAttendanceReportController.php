<?php

namespace App\Http\Controllers\Web\Department;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\SubAttendanceReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubAttendanceReportController extends Controller
{
    protected $userRepository;
    protected $subAttendanceReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository         $userRepository
     * \App\Services\SubAttendanceReportService $subAttendanceReportService
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
     * Show use sub attendance report.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->subAttendanceReportService->get($id);

        $this->authorize('view', $report->user);

        return response()->json($report, 200);
    }

    /**
     * Show user sub attendance report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request->merge([
            'department_id' => Auth::user()->department_id
        ]);

        $request = $this->completeDate($request);
        $reports = $this->subAttendanceReportService->getList(
            $request->only(['name', 'alias', 'department_id']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.sub-attendance-report.list')->with([
            'reports' => $reports,
        ]);
    }

    /**
     * Update sub attendance report.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $request->merge(['layer' => 1]);
        if (! $this->subAttendanceReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}