<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class hodMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
     
    // public function handle(Request $request, Closure $next,$guard=null): Response
    // {
    //     if(Auth::guard('hods')->guest()){
    //         if($request->ajax() || $request->wantsJson()){
    //             return response('Unauthorized.', 401);
    //         }
    //         else{
    //             return redirect(url('api/login'));
    //         }
    //     }

    //     return $next($request);
    // }
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Check if the user is not authenticated in the 'hods' guard
        if (Auth::guard('hod')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            } else {
                return redirect()->route('hod.login'); // Redirect to HOD login route
            }
        }

        return $next($request);
    }
}
