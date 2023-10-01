<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveReportRequest;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use Illuminate\Http\Request;

class LeaveReportController extends Controller
{
    protected $userRepository;
    protected $leaveReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository   $userRepository
     * \App\Services\ServiceRecordService $serviceRecordService
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
     * Get user leave report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $request = $this->completeDate($request);
        $user    = $this->userRepository->get($id);
        $reports = $this->leaveReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.leave-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }

    /**
     * Store leave report.
     *
     * @param  \App\Http\Requests\LeaveReportRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveReportRequest $request, int $id)
    {
        $validated = $request->validated();

        $user = $this->userRepository->get($id);

        $error = $this->leaveReportService->create($id, array_merge([
            'status' => $user->is_manager ? 1111 : 11,
        ], $validated));

        if (empty($error)) {
            return response()->json([], 201);
        } else {
            return response()->json($error, 400);
        }
    }
}