<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Sementara — untuk diagnosis 419 di Chrome mobile. Hapus setelah selesai.
 */
class DebugLoginRequest
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin/login')) {
            Log::info('LOGIN DEBUG', [
                'method' => $request->method(),
                'ua' => $request->userAgent(),
                'cookie_names' => array_keys($request->cookies->all()),
                'has_xsrf_cookie' => $request->cookies->has('XSRF-TOKEN'),
                'has_session_cookie' => $request->cookies->has(config('session.cookie')),
                'xsrf_header' => $request->header('X-XSRF-TOKEN') ? 'present' : 'absent',
                'token_input' => $request->filled('_token') ? 'present' : 'absent',
                'session_id_cookie_tail' => substr((string) $request->cookies->get(config('session.cookie')), -8),
            ]);
        }

        return $next($request);
    }
}
