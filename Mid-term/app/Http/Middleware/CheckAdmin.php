<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CheckAdmin
{

    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->roles[0]->name == 'AD') {
            return $next($request);
        }else if(Auth::user()->roles[0]->name == 'ST'){
            return Redirect::route('student.index');
        }else{
            return Redirect::back();
        }
    }
}
