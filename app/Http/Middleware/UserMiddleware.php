<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role==0) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->role==1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin access not allowed for user routes.');
        }

        return redirect()->route('login')
            ->with('error', 'Please login to access this page.');
    }
}