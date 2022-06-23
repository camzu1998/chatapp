<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class  AdminRoomGuard
{
    public function handle(Request $request, Closure $next)
    {
        if(!empty($request->route('type')) && $request->route('type') === 'user') {
            return $next($request);
        }

        $room_id = $request->route('type_id') ?: $request->route('room_id');

        $roomAdmin = $request->user()->adminRoom()->find($room_id);

        if (empty($roomAdmin)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 2
                ], 403);
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
