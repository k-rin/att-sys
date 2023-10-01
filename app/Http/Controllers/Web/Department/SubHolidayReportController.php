<?php

namespace App\Http\Controllers\Web\Department;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\SubHolidayReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubHolidayReportController extends Controller
{
    protected $userRepository;
    protected $subHolidayReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository      $userRepository
     * \App\Services\SubHolidayReportService $subHolidayReportService
     * @return void
     */
    public function __construct(
        UserRepository          $userRepository,
        SubHolidayReportService $subHolidayReportService
    ) {
        $this->userRepository          = $userRepository;
        $this->subHolidayReportService = $subHolidayReportService;
    }

    /**
     * Show use sub holiday report.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->subHolidayReportService->get($id);

        $this->authorize('view', $report->user);

        return response()->json($report, 200);
    }

    /**
     * Show user sub holiday report list.
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
        $reports = $this->subHolidayReportService->getList(
            $request->only(['name', 'alias', 'department_id']),
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.sub-holiday-report.list')->with([
            'reports' => $reports,
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
        $request->merge(['layer' => 1]);
        if (! $this->subHolidayReportService->approve($id, $request->all())) {
            return response()->json([], 403);
        }

        return response()->json([], 201);
    }
}