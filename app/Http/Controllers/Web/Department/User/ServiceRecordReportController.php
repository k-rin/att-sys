<?php

namespace App\Http\Controllers\Web\Department\User;

use App\Http\Controllers\Controller;
use App\Services\ServiceRecordReportService;
use Illuminate\Http\Request;

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
     * @param  int    $id
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, string $date)
    {
        $report = $this->serviceRecordReportService->get($id, $date);

        return response()->json($report, 200);
    }

    /**
     * Update user service record report.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int    $id
     * @param  string $date
     * @return \Illminate\Http\Response
     */
    public function update(Request $request, int $id, string $date)
    {
        $request->merge(['layer' => 1]);
        if (! $this->serviceRecordReportService->approve($id, $date, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}
