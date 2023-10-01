<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\ServiceRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $serviceRecordService;
    protected $userRepository;

    public function __construct(
        ServiceRecordService    $serviceRecordService,
        UserRepository          $userRepository,
    ) {
        $this->serviceRecordService = $serviceRecordService;
        $this->userRepository       = $userRepository;
    }

    public function get()
    {
        $user = $this->userRepository->get(Auth::id());

        return response()->json($user, 200);
    }

    public function getRecordList(Request $request)
    {
        $request = $this->completeDate($request);
        $records = $this->serviceRecordService->getList(
            Auth::id(),
            $request->input('year'),
            $request->input('month')
        );

        return response()->json([
            'records' => $records['records'],
            'summary' => $records['summary']
        ], 200);
    }
}