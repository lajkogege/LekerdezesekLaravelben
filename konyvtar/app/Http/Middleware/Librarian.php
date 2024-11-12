<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Auth osztály importálása

class Librarian
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !(Auth::user()->role === 2)||(Auth::user()->role<2)) { // ez az útvonal megengedi, hogy az admin és a könyvtáros is hozzáférjen, ezt az Admin.php-ból másoltam
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $next($request); // folytatódhat a kérés
    }
}
