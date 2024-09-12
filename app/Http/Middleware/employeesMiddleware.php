<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class employeesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
     public function handle(Request $request, Closure $next,$guard=null): Response
    {
         if(Auth::guard('employees')->guest()){
            if($request->ajax() || $request->wantsJson()){
                return response('Unauthorized.', 401);
            }
            else{
                return redirect(url('api/login'));
            }
        }

        return $next($request);
    }
}
