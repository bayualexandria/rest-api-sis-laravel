<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->status_id == 3 && auth()->user()->status_id==2) {
            return response()->json(['message' => 'Akun ini tidak memiliki hak akses', 'status' => 400], 400);
        }
        return $next($request);
    }
}
