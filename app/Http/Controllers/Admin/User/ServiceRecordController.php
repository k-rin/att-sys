<?php

namespace App\Http\Controllers\Admin\User;

use App\Enums\LeaveType;
use App\Exports\ServiceRecordExport;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\LeaveReportService;
use App\Services\ServiceRecordService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ServiceRecordController extends Controller
{
    protected $userRepository;
    protected $leaveReportService;
    protected $serviceRecordService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserRepository   $userRepository
     * @param  \App\Services\LeaveReportService   $leaveReportService
     * @param  \App\Services\ServiceRecordService $serviceRecordService
     * @return void
     */
    public function __construct(
        UserRepository       $userRepository,
        LeaveReportService   $leaveReportService,
        ServiceRecordService $serviceRecordService
    ) {
        $this->userRepository       = $userRepository;
        $this->leaveReportService   = $leaveReportService;
        $this->serviceRecordService = $serviceRecordService;
    }

    /**
     * Get user service record list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $request = $this->completeDate($request);
        $records = $this->serviceRecordService->getList(
            $id, $request->input('year'), $request->input('month'),
        );
        $leave = $this->leaveReportService->getLeaveSummary(
            $id, $request->input('year'), $request->input('month'),
        );
        $records['summary']['sick_leave'] = $leave[LeaveType::SickLeave];
        $records['summary']['prenatal_care_leave'] = $leave[LeaveType::PrenatalCareLeave];

        $user = $this->userRepository->get($id);

        return view('admin.service-record.list')->with([
            'records' => $records['records'],
            'closed'  => $records['closed'],
            'summary' => (object) $records['summary'],
            'user'    => $user,
        ]);
    }

    /**
     * Get service record.
     *
     * @param  int    $id
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, string $date)
    {
        $record = $this->serviceRecordService->get($id, $date);

        return response()->json($record);
    }

    /**
     * Update service record.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int    $id
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id, string $date)
    {
        $this->serviceRecordService->updateOrCreate(
            $id, $date, $request->only('start_at', 'end_at')
        );

        return response()->json([], 201);
    }

    /**
     * Export user service record list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function export(Request $request, int $id)
    {
        $request = $this->completeDate($request);
        $year    = $request->input('year');
        $month   = $request->input('month');
        $user    = $this->userRepository->get($id);
        $file    = '%s_%s%02s_勤怠記録.xlsx';

        return Excel::download(new ServiceRecordExport(
            $id,
            $year,
            $month,
            $this->userRepository,
            $this->leaveReportService,
            $this->serviceRecordService
        ), sprintf($file, $user->name, $year, $month));
    }
}