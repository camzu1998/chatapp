<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminRoomGuard
{
    public function handle(Request $request, Closure $next)
    {

        $roomAdmin = $request->user()->adminRoom()->where('id', $request->route('room_id'))->first();

        if(empty($roomAdmin))
        {
            if($request->expectsJson())
            {
                return response()->json([
                    'status' => 2
                ]);
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
