<?php

namespace App\Http\Controllers\Web\Department;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveReportController extends Controller
{
    protected $userRepository;
    protected $leaveReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository   $userRepository
     * \App\Services\ServiceRecordService $serviceRecordService
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
     * Show user leave report.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $report = $this->leaveReportService->get($id);

        $this->authorize('view', $report->user);

        return response()->json($report);
    }

    /**
     * Show user leave report list.
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
        $reports = $this->leaveReportService->getList(
            $request->only(['name', 'alias', 'department_id']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.leave-report.list')->with([
            'reports' => $reports,
        ]);
    }

    /**
     * Update leave report.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $request->merge(['layer' => 1]);
        if (! $this->leaveReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}
