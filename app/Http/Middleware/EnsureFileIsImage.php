<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFileIsImage
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasFile('input_profile')) {
            $file = $request->input_profile;
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Invalid file input'
            ], 400);
        }

        if(!in_array($file->extension(), config('profiles.extensions'))) {
            return response()->json([
                'status' => false,
                'msg' => 'Invalid file input - File is not image'
            ], 400);
        }

        return $next($request);
    }
}
