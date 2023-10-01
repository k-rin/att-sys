<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\LeaveType;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveReportRequest;
use App\Services\LeaveReportService;
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
     * Create leave report.
     *
     * @param  \App\Http\Requests\LeaveReportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveReportRequest $request)
    {
        $validated = $request->validated();

        $report = $this->leaveReportService->create(Auth::id(), $validated);

        return response()->json($report, 201);
    }

    /**
     * Get leave report by id.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function get(int $id)
    {
        $report = $this->leaveReportService->get(Auth::id(), $id);

        return response()->json($report, 200);
    }

    /**
     * Get leave report list.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request)
    {
        $request = $this->completeDate($request);

        $reports = $this->leaveReportService->getList(
            [ 'id' => Auth::id() ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return response()->json($reports, 200);
    }

    /**
     * Update leave report.
     *
     * @param  \App\Http\Requests\LeaveReportRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveReportRequest $request, int $id)
    {
        $validated = $request->validated();

        $report = $this->leaveReportService->update(Auth::id(), $id, $validated);

        if ($report) {
            return response()->json($report, 201);
        } else {
            return response(['error' => 'Report Not Found'], 404);
        }
    }

    /**
     * Get leave type.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTypes()
    {
        return response(LeaveType::getData(), 200);
    }
}
