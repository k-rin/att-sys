<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\OvertimeReportService;
use Illuminate\Http\Request;

class OvertimeReportController extends Controller
{
    protected $userRepository;
    protected $overtimeReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository    $userRepository
     * \App\Services\overtimeReportService $overtimeReportService
     * @return void
     */
    public function __construct(
        UserRepository        $userRepository,
        OvertimeReportService $overtimeReportService
    ) {
        $this->userRepository        = $userRepository;
        $this->overtimeReportService = $overtimeReportService;
    }

    /**
     * Get user overtime report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $request = $this->completeDate($request);
        $user    = $this->userRepository->get($id);
        $reports = $this->overtimeReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.overtime-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }
}
