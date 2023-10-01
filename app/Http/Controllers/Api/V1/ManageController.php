<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Get leave report list.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getReportList(Request $request)
    {
        return response()->json('Get leave report list.', 200);
    }

    /**
     * Get leave report by id.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getReport(int $id)
    {
        return response()->json('Get leave report by id', 200);
    }

    /**
     * Update leave report.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateReport(Request $request)
    {
        return response()->json('Update leave report by id', 200);
    }
}
