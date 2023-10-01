<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveReportRequest;
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
     * \App\Repositories\UserRepository $userRepository
     * \App\Services\LeaveReportService $leaveReportService
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
     * @param  Reque\Illuminate\Http\Requestst $request
     * @param  int $id
     * @return \Illminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $report = $this->leaveReportService->get($id);

        $this->authorize('view', $report->user);

        return response()->json($report, 200);
    }

    /**
     * Show user leave report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);
        $reports = $this->leaveReportService->getList(
            [ 'id' => Auth::id() ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.leave-report.list')->with([
            'reports' => $reports,
            'user'    => Auth::user(),
        ]);
    }

    /**
     * Create leave report page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('web.leave-report.create');
    }

    /**
     * Store leave report.
     *
     * @param  \App\Http\Requests\LeaveReportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveReportRequest $request)
    {
        $validated = $request->validated();

        $error = $this->leaveReportService->create(Auth::id(), $validated);

        if (empty($error)) {
            return response()->json([], 201);
        } else {
            return response()->json($error, 400);
        }
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

        $report = $this->leaveReportService->get($id);

        $this->authorize('view', $report->user);

        $error = $this->leaveReportService->update($id, $validated);

        if (empty($error)) {
            return response()->json([], 201);
        } else {
            return response()->json($error, 400);
        }
    }
}