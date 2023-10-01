<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SubAttendanceReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubAttendanceReportController extends Controller
{
    protected $subAttendanceReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\SubAttendanceReportService $subAttendanceReportService
     * @return void
     */
    public function __construct(
        SubAttendanceReportService $subAttendanceReportService
    ) {
        $this->subAttendanceReportService = $subAttendanceReportService;
    }

    /**
     * Get sub attendance report by id.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->subAttendanceReportService->get($id);

        return response()->json($report, 200);
    }

    /**
     * Get sub attendance report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);
        $reports = $this->subAttendanceReportService->getList(
            $request->only(['name', 'alias']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.sub-attendance-report.list')->with([
            'reports' => $reports
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
        if ($request->input('layer') != Auth::user()->approval_layer ||
            ! $this->subAttendanceReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}