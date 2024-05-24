<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class CheckLanguage
{
    public function handle(Request $request, Closure $next)
    {
        config(['app.locale' => Session::get('language', config('app.locale'))]);
        return $next($request);
    }
}
