<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CalendarRepository;
use App\Imports\CalendarImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CalendarController extends Controller
{
    protected $calendarRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\CalendarRepository $calendarRepository
     * @return void
     */
    public function __construct(
        CalendarRepository $calendarRepository
    ) {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * Get calendar list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request = $this->completeDate($request);

        $calendar = $this->calendarRepository->getList(
            $request->input('year'),
            $request->input('month')
        );

        return view('admin.calendar.list')->with([
            'calendar' => $calendar,
        ]);
    }

    /**
     * Import calendar page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.calendar.import');
    }

    /**
     * Import calendar.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Excel::import(new CalendarImport, ($request->file('filename')));

        return response()->json([], 201);
    }

    /**
     * Show calendar by date.
     *
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function show(string $date)
    {
        $day = $this->calendarRepository->get($date);

        return response()->json($day, 200);
    }

    /**
     * Update calendar by date.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $date)
    {
        $this->calendarRepository->update($date, [
            'holiday' => $request->input('holiday'),
            'note'    => $request->input('note'),
        ]);

        return response()->json([], 201);
    }
}