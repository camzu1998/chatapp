<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Services\Auth\FacebookService;
use App\Services\Auth\GoogleService;

class EnsureSocialExist
{
    public function handle(Request $request, Closure $next)
    {
        $social = Str::ucfirst($request->route('social'));

        if(!class_exists("App\Services\Auth\\".$social.'Service')){
            return redirect('/404');
        }

        return $next($request);
    }
}
