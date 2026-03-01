<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ResolveAppId
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
        $appId = $request->header('appId');

        if ($appId) {
            app()->instance('current_app_id', $appId);
        }

        return $next($request);
    }
}
