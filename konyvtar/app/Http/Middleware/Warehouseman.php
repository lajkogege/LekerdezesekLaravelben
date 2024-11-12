<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Auth osztály importálása

class Warehouseman
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !(Auth::user()->role === 2)||(Auth::user()->role===0)){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request); //folytatódhat a kérés
    }
}
