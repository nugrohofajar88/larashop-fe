<?php

namespace App\Providers;

use App\Support\LarashopApi;
use App\Support\LarashopApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inject the customer cart item count into the storefront layout so the
        // navigation can render a live badge. Resilient: any API/session issue
        // simply yields a zero badge instead of breaking the page render.
        View::composer('components.layouts.customer', function ($view): void {
            $count = 0;
            $token = session('customer.token');

            if (is_string($token) && $token !== '') {
                try {
                    $cart = app(LarashopApi::class)->customerCart($token);
                    $count = count(data_get($cart, 'items', []));
                } catch (LarashopApiException) {
                    $count = 0;
                }
            }

            $view->with('cartCount', $count);

            // Nomor WhatsApp toko untuk tombol melayang. Di-cache 30 menit supaya tak
            // memanggil API tiap render; gagal API = tombol tak muncul (bukan error).
            $storeWhatsapp = Cache::remember('storefront.store_whatsapp', now()->addMinutes(30), function (): string {
                try {
                    return (string) (app(LarashopApi::class)->storeInfo()['whatsapp'] ?? '');
                } catch (\Throwable) {
                    return '';
                }
            });

            $view->with('storeWhatsapp', $storeWhatsapp);
        });
    }
}
