<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRecordReportRequest;
use App\Services\ServiceRecordReportService;
use Illuminate\Support\Facades\Auth;

class ServiceRecordReportController extends Controller
{
    protected $serviceRecordReportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ServiceRecordReportService $serviceRecordReportService
     * @return void
     */
    public function __construct(
        ServiceRecordReportService $serviceRecordReportService
    ) {
        $this->serviceRecordReportService = $serviceRecordReportService;
    }

    /**
     * Get user service record report.
     *
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function show(string $date)
    {
        $report = $this->serviceRecordReportService->get(Auth::id(), $date);

        return response()->json($report, 200);
    }

    /**
     * Update user service record report.
     *
     * @param string $date
     * @param \App\Http\Requests\ServiceRecordReportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceRecordReportRequest $request, string $date)
    {
        $validated = $request->validated();

        $this->serviceRecordReportService->update(Auth::id(), $date, $validated);

        return response()->json([], 201);
    }
}