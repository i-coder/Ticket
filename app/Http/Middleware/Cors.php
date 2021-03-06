<?php

namespace App\Http\Middleware;

use Closure;
use http\Client\Response;

class Cors
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://192.168.5.129')
            ->header('Access-Control-Allow-Methods', '*')
            ->header('Access-Control-Allow-Credentials', true)
            ->header('Access-Control-Allow-Headers', 'x-csrf-token, x-requested-with, content-type, x-token-auth, authorization, x-auth-token')
            ->header('Accept', 'application/json')
            ->header('Accept', 'multipart/form-data');
    }

}
