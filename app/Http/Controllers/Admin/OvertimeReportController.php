<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OvertimeReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeReportController extends Controller
{
    protected $overtimeReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\OvertimeReportService $overtimeReportService
     * @return void
     */
    public function __construct(
        OvertimeReportService $overtimeReportService
    ) {
        $this->overtimeReportService = $overtimeReportService;
    }

    /**
     * Get overtime report by id.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->overtimeReportService->get($id);

        return response()->json($report, 200);
    }

    /**
     * Get overtime report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);
        $reports = $this->overtimeReportService->getList(
            $request->only(['name', 'alias']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.overtime-report.list')->with([
            'reports' => $reports
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
        if ($request->input('layer') != Auth::user()->approval_layer ||
            ! $this->overtimeReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}
