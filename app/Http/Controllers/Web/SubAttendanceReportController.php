<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubAttendanceReportRequest;
use App\Repositories\UserRepository;
use App\Services\SubAttendanceReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubAttendanceReportController extends Controller
{
    protected $subAttendanceReportService;
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\SubAttendanceReportService $subAttendanceReportService
     * @param  \App\Repositories\UserRepository         $userRepository
     * @return void
     */
    public function __construct(
        SubAttendanceReportService $subAttendanceReportService,
        UserRepository             $userRepository
    ) {
        $this->subAttendanceReportService = $subAttendanceReportService;
        $this->userRepository             = $userRepository;
    }

    /**
     * Get user sub attendance report.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->subAttendanceReportService->get($id);

        $this->authorize('view', $report->user);

        return response()->json($report, 200);
    }

    /**
     * Show user sub attendance report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);
        $reports = $this->subAttendanceReportService->getList(
            [ 'id' => Auth::id() ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );
        $user = $this->userRepository->get(Auth::id());

        return view('web.sub-attendance-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }

    /**
     * Create sub attendance report page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('web.sub-attendance-report.create');
    }

    /**
     * Store sub attendance report.
     *
     * @param  \App\Http\Requests\SubAttendanceReportRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SubAttendanceReportRequest $request)
    {
        $validated = $request->validated();

        if ($error = $this->subAttendanceReportService->create(Auth::id(), $validated)) {
            return response()->json($error, 403);
        }

        return response()->json([], 201);
    }

    /**
     * Update sub attendance report.
     *
     * @param  \App\Http\Requests\SubAttendanceReportRquest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubAttendanceReportRequest $request, int $id)
    {
        $report = $this->subAttendanceReportService->get($id);

        $this->authorize('view', $report->user);

        $validated = $request->validated();

        if ($error = $this->subAttendanceReportService->update($id, $validated)) {
            return response()->json($error, 403);
        }

        return response()->json([], 201);
    }

    /**
     * Get uncompensated sub attendance report.
     *
     * @return \Illuminate\Http\Response
     */
    public function uncompensated()
    {
        $reports = $this->subAttendanceReportService->getUncompensatedReport(Auth::id());

        return response()->json($reports, 200);
    }
}