<?php

namespace App\Http\Controllers\Web\Department\User;

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
     * \App\Repositories\UserRepository        $userRepository
     * \App\Repositories\OvertimeReportService $overtimeReportService
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
     *  Show user overtime report list.
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
        $reports = $this->overtimeReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('web.overtime-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }
}
