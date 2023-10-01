<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SubHolidayReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubHolidayReportController extends Controller
{
    protected $subHolidayReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\SubHolidayReportService $subHolidayReportService
     * @return void
     */
    public function __construct(
        SubHolidayReportService $subHolidayReportService
    ) {
        $this->subHolidayReportService = $subHolidayReportService;
    }

    /**
     * Get sub holiday report by id.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->subHolidayReportService->get($id);

        return response()->json($report, 200);
    }

    /**
     * Get sub holiday report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);
        $reports = $this->subHolidayReportService->getList(
            $request->only(['name', 'alias']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.sub-holiday-report.list')->with([
            'reports' => $reports
        ]);
    }

    /**
     * Update sub holiday report.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        if ($request->input('layer') != Auth::user()->approval_layer ||
            ! $this->subHolidayReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}