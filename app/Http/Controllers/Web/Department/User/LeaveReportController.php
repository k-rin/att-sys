<?php

namespace App\Http\Controllers\Web\Department\User;

use App\Http\Controllers\Controller;
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
     *  Show user leave report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $user = $this->userRepository->get($id);

        $this->authorize('view', $user);

        $request = $this->completeDate($request);
        $reports = $this->leaveReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.leave-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }
}
