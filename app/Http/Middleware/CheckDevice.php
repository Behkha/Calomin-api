<?php

namespace App\Http\Middleware;

use Closure;

class CheckDevice
{
    public function handle($request, Closure $next)
    {
        if (\Route::currentRouteName() === 'users.orders.verify') {
            return $next($request);
        }

        if ($request->header('device') === 'mobile' || $request->header('device') === 'browser' || $request->header('device') === 'panel') {
            $request->merge(['device' => $request->header('device')]);
            return $next($request);
        } else {
            return response()->json(['errors' => 'device header is not valid'], 400);
        }
    }
}
