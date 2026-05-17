<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('admin.authenticated', false)) {
            return redirect()->route('admin.login')->with('error', 'Silakan login dulu untuk membuka panel admin.');
        }

        return $next($request);
    }
}
