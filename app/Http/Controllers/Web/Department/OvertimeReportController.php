<?php

namespace App\Http\Controllers\Web\Department;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\OvertimeReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeReportController extends Controller
{
    protected $userRepository;
    protected $overtimeReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository    $userRepository
     * \App\Services\OvertimeReportService $overtimeReportService
     * @return void
     */
    public function __construct(
        UserRepository        $userRepository,
        OvertimeReportService $overtimeReportService
    ) {
        $this->userRepository        = $userRepository;
        $this->overtimeReportService = $overtimeReportService;
    }

    /**
     * Show user overtime report.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->overtimeReportService->get($id);

        $this->authorize('view', $report->user);

        return response()->json($report, 200);
    }

    /**
     * Show user overtime report list.
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
        $reports = $this->overtimeReportService->getList(
            $request->only(['name', 'alias', 'department_id']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.overtime-report.list')->with([
            'reports' => $reports,
        ]);
    }

    /**
     * Update overtime report.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $request->merge(['layer' => 1]);
        if (! $this->overtimeReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}
