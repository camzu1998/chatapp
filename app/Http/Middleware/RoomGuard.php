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
        $room_id = null;
        if( !empty( $request->route('room_id') ) ) {
            $room_id = $request->route('room_id');
        } else if ( !empty( $request->route('room') ) ) {
            $room_id = $request->route('room');
        }
        $roomMember = $request->user()->roomMember()->roomID($room_id)->first();

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
