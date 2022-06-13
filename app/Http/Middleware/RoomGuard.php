<?php

namespace App\Http\Middleware;

use App\Models\RoomMember;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomGuard
{
    public function handle(Request $request, Closure $next)
    {
        $roomMember = $request->user()->roomMember()->roomID($request->route('room_id'))->first();

        if (empty($roomMember) || $roomMember->status !== 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 2,
                    'msg'    => __('app.user_inst_in_room')
                ]);
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
