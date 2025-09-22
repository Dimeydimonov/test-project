<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Необходима аутентификация.'
                ], 401);
            }
            
            return redirect()->route('login');
        }

        if (!Auth::user()->hasRole('admin')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Недостаточно прав для доступа к этому разделу.'
                ], 403);
            }
            
            return redirect()->route('home')->with('error', 'Недостаточно прав для доступа к этому разделу.');
        }

        return $next($request);
    }
}
