<?php

namespace App\Http\Controllers\Admin;

use App\Services\LeaveReportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveReportController extends Controller
{
    protected $leaveReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\LeaveReportService $leaveReportService
     * @return void
     */
    public function __construct(
        LeaveReportService $leaveReportService
    ) {
        $this->leaveReportService = $leaveReportService;
    }

    /**
     * Get leave report by id.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $report = $this->leaveReportService->get($id);

        return response()->json($report, 200);
    }

    /**
     * Get leave report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);
        $reports = $this->leaveReportService->getList(
            $request->only(['name', 'alias']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.leave-report.list')->with([
            'reports' => $reports
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
        if ($request->input('layer') != Auth::user()->approval_layer ||
            ! $this->leaveReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}
