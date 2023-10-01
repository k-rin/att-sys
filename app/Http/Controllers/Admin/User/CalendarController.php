<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\UserCalendarRepository;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected $userRepository;
    protected $userCalendarRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserCalendarRepository $calendarRepository
     * @return void
     */
    public function __construct(
        UserRepository         $userRepository,
        UserCalendarRepository $userCalendarRepository
    ) {
        $this->userRepository         = $userRepository;
        $this->userCalendarRepository = $userCalendarRepository;
    }

    /**
     * Get user calendar list.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $id)
    {
        $user = $this->userRepository->get($id);

        $request  = $this->completeDate($request);
        $calendar = $this->userCalendarRepository->getList(
            $id,
            $request->input('year'),
            $request->input('month')
        );

        return view('admin.calendar.list')->with([
            'user'     => $user,
            'calendar' => $calendar,
        ]);
    }

    /**
     * Create user calendar.
     *
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(int $id)
    {
        $this->userCalendarRepository->create($id);

        return redirect("/admin/users/{$id}/calendar");
    }

    /**
     * Show user calendar by date.
     *
     * @param  int    $id
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, string $date)
    {
        $day  = $this->userCalendarRepository->get($id, $date);

        return response()->json($day, 200);
    }

    /**
     * Update calendar by date.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int    $id
     * @param  string $date
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id, string $date)
    {
        $this->userCalendarRepository->update($id, $date, [
            'holiday' => $request->input('holiday'),
            'note'    => $request->input('note'),
        ]);

        return response()->json([], 201);
    }
}