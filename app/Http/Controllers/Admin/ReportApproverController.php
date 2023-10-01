<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ReportApproverRepository;
use Illuminate\Http\Request;

class ReportApproverController extends Controller
{
    protected $reportApproverRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\ReportApproverRepository $reportApproverRepository
     * @return void
     */
    public function __construct(
        ReportApproverRepository $reportApproverRepository
    ) {
        $this->reportApproverRepository = $reportApproverRepository;
    }

    /**
     * Get report approver list.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $approvers = $this->reportApproverRepository->getList();

        return view('admin.report-approver.list')->with([
            'approvers' => $approvers,
        ]);
    }

    /**
     * Update report approver.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function update(Request $request, int $id)
    {
        $this->reportApproverRepository->update($id, $request->only('admin_id'));

        return response()->json([], 201);
    }
}