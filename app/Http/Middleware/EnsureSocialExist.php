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
        switch ($request->route('social')) {
            case 'google':
            case 'facebook':
                return $next($request);
                break;
            default:
                return redirect('/404');
        }


//        Todo: I think this solution is better but don't know why this doesn't work
//        $social = Str::ucfirst($request->route('social'));
//        $classNameWithNamespace = "App\\Services\\Auth\\".$social.'Service';
//
//        if(!class_exists($classNameWithNamespace, false)){
//            dd($classNameWithNamespace);
//            return redirect('/404');
//        }
//
//        return $next($request);
    }
}
