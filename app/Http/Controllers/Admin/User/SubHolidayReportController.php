<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\SubHolidayReportService;
use Illuminate\Http\Request;

class SubHolidayReportController extends Controller
{
    protected $userRepository;
    protected $subHolidayReportService;

    /**
     * Create a new controller instance.
     *
     * \App\Repositories\UserRepository      $userRepository
     * \App\Services\subHolidayReportService $subHolidayReportService
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
     * Get user sub holiday report list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $request = $this->completeDate($request);
        $user    = $this->userRepository->get($id);
        $reports = $this->subHolidayReportService->getList(
            [ 'id' => $id ],
            $request->only(['year', 'month']),
            $this->getPaginate($request)
        );

        return view('admin.sub-holiday-report.list')->with([
            'reports' => $reports,
            'user'    => $user,
        ]);
    }
}