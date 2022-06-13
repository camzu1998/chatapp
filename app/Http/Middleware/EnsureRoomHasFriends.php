<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRoomHasFriends
{
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->add_friend)) {
            return response()->json([
                'status' => 1,
                'msg'    => 'Please add some friends to room'
            ]);
        }

        return $next($request);
    }
}
