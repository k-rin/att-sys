<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Complete date info.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Request
     */
    protected function completeDate(\Illuminate\Http\Request $request)
    {
        $now = Carbon::now('Asia/Taipei');

        if ($request->input('year') === null) {
            $request->merge(['year' => $now->year]);
        }

        if ($request->input('month') === null) {
            $request->merge(['month' => $now->month]);
        }

        return $request;
    }

    /**
     * Get paginate info.
     *
     * @param  \Illuminate\Http\Request $request
     * @return  array
     */
    protected function getPaginate(\Illuminate\Http\Request $request)
    {
        return [
            'limit'  => $request->input('limit', 10),
            'column' => $request->input('column', 'id'),
            'order'  => $request->input('order', 'desc'),
        ];
    }
}