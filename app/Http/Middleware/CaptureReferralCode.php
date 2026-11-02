<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tangkap ?ref=KODE di URL manapun & simpan ke session, supaya kode referral
 * reseller tetap "nempel" walau pengunjung belum login - diikat permanen ke
 * reseller terkait begitu dia login/daftar (lihat
 * StorefrontController::storeCustomerSession).
 */
class CaptureReferralCode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('features.reseller')) {
            return $next($request);
        }

        $ref = trim((string) $request->query('ref', ''));

        if ($ref !== '' && session('customer.token') === null) {
            session(['pending_referral_code' => $ref]);
        }

        return $next($request);
    }
}
