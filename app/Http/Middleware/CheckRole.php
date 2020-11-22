<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next, $role)
    {

        if ($request->user() === null) {

            abort(401, 'Not enougth permission');
        }

        if (!$request->user()->hasAnyRole($role) ) {
            abort(401, 'Not enougth permission');
        }
        return $next($request);


    }
}
