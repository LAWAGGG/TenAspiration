<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            // ponytail: lempar ke fallback, bukan 403 mentah
            return redirect()->route('fallback')->with('error', 'Akses ditolak — hanya admin yang bisa masuk dashboard.');
        }

        return $next($request);
    }
}
