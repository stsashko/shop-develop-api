<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
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
        if (!Auth::check())
            abort(response()->json(
                [
                    'success' => false,
                    'errors' => ['UnAuthenticated']
                ], 401));;

        $user = Auth::user();

        if($user->isAdmin())
            return $next($request);

        abort(response()->json(
            [
                'success' => false,
                'errors' => ['UnAuthenticated']
            ], 401));

    }
}
