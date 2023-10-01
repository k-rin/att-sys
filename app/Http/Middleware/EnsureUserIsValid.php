<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->locked) {
            if ($request->is('admin/*')) {
                Auth::guard('admin')->logout();
                $to = '/admin';
            } else {
                Auth::guard('web')->logout();
                $to = '/';
            }
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect($to);
        }

        return $next($request);
    }
}