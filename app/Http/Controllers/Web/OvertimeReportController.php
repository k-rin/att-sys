<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\OvertimeReportRequest;
use App\Repositories\UserRepository;
use App\Services\OvertimeReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeReportController extends Controller
{
    protected $overtimeReportService;
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * \App\Services\OvertimeReportService $overtimeReportService
     * \App\Repositories\UserRepository    $userRepository
     * @return void
     */
    public function __construct(
        OvertimeReportService $overtimeReportService,
        UserRepository        $userRepository
    ) {
        $this->overtimeReportService = $overtimeReportService;
        $this->userRepository        = $userRepository;
    }

    /**
     * Show user overtime report.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $report = $this->overtimeReportService->get($id);

        $this->authorize('view', $report->user);

        return response()->json($report, 200);
    }

    /**
     * Show user overtime report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);
        $reports = $this->overtimeReportService->getList(
            [ 'id' => Auth::id() ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );
        $user = $this->userRepository->get(Auth::id());

        return view('web.overtime-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }

    /**
     * Create overtime report page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('web.overtime-report.create');
    }

    /**
     * Store overtime report.
     *
     * @param  \App\Http\Requests\OvertimeReportRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OvertimeReportRequest $request)
    {
        $validated = $request->validated();

        if ($error = $this->overtimeReportService->create(Auth::id(), $validated)) {
            return response()->json($error, 403);
        }

        return response()->json([], 201);
    }

    /**
     * Update overtime report.
     *
     * @param \App\Http\Requests\OvertimeReportRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(OvertimeReportRequest $request, int $id)
    {
        $report = $this->overtimeReportService->get($id);

        $this->authorize('view', $report->user);

        $validated = $request->validated();

        if ($error = $this->overtimeReportService->update($id, $validated)) {
            return response()->json($error, 403);
        }

        return response()->json([], 201);
    }
}