<?php

namespace App\Providers;

use App\Support\LarashopApi;
use App\Support\LarashopApiException;
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
        });
    }
}
