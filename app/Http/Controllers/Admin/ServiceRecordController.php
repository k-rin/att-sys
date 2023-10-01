<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ServiceRecordImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ServiceRecordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Import service record page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.service-record.import');
    }

    /**
     * Import service record.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Excel::import(new ServiceRecordImport, ($request->file('filename')));

        return response()->json([], 201);
    }
}