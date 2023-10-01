<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubHolidayReportRequest;
use App\Repositories\UserRepository;
use App\Services\SubHolidayReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubHolidayReportController extends Controller
{
    protected $subHolidayReportService;
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\SubHolidayReportService $subHolidayReportService
     * @param  \App\Repositories\UserRepository      $userRepository
     * @return void
     */
    public function __construct(
        SubHolidayReportService $subHolidayReportService,
        UserRepository          $userRepository
    ) {
        $this->subHolidayReportService = $subHolidayReportService;
        $this->userRepository          = $userRepository;
    }

    /**
     * Get user sub holiday report.
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
        $request = $this->completeDate($request);
        $reports = $this->subHolidayReportService->getList(
            [ 'id' => Auth::id() ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );
        $user = $this->userRepository->get(Auth::id());

        return view('web.sub-holiday-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }

    /**
     * Create sub holiday report page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('web.sub-holiday-report.create');
    }

    /**
     * Store sub holiday report.
     *
     * @param  \App\Http\Requests\SubHolidayReportRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SubHolidayReportRequest $request)
    {
        $validated = $request->validated();

        if ($error = $this->subHolidayReportService->create(Auth::id(), $validated)) {
            return response()->json($error, 403);
        }

        return response()->json([], 201);
    }

    /**
     * Update sub holiday report.
     *
     * @param  \App\Http\Requests\SubHolidayReportRquest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubHolidayReportRequest $request, int $id)
    {
        $report = $this->subHolidayReportService->get($id);

        $this->authorize('view', $report->user);

        $validated = $request->validated();

        if ($error = $this->subHolidayReportService->update($id, $validated)) {
            return response()->json($error, 403);
        }

        return response()->json([], 201);
    }
}