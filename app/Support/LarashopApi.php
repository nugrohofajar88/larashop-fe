<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LarashopApi
{
    public function publicProducts(array $query = []): array
    {
        return $this->request('GET', '/products', ['query' => $query]);
    }

    public function publicProduct(string $slug): array
    {
        return $this->request('GET', '/products/'.$slug)['data'] ?? [];
    }

    /** Info publik toko (brand, nomor WhatsApp) untuk storefront. */
    public function storeInfo(): array
    {
        return $this->request('GET', '/store-info')['data'] ?? [];
    }

    public function login(string $login, string $password, string $deviceName = 'larashop-fe'): array
    {
        return $this->request('POST', '/auth/login', [
            'json' => [
                'login' => $login,
                'password' => $password,
                'device_name' => $deviceName,
            ],
        ])['data'] ?? [];
    }

    public function register(array $payload): array
    {
        return $this->request('POST', '/auth/register', ['json' => $payload])['data'] ?? [];
    }

    public function forgotPassword(string $login): array
    {
        return $this->request('POST', '/auth/forgot-password', ['json' => ['login' => $login]]);
    }

    public function resetPassword(array $payload): array
    {
        return $this->request('POST', '/auth/reset-password', ['json' => $payload]);
    }

    public function me(string $token): array
    {
        return $this->request('GET', '/auth/me', token: $token)['data'] ?? [];
    }

    public function logout(string $token): void
    {
        $this->request('POST', '/auth/logout', token: $token);
    }

    public function logoutAdmin(): void
    {
        $token = session('admin.token');

        if (! is_string($token) || $token === '') {
            $token = Cache::get($this->adminTokenCacheKey());
        }

        if (is_string($token) && $token !== '') {
            try {
                $this->logout($token);
            } catch (LarashopApiException) {
                // Tetap lanjut bersihkan cache token lokal meski token backend sudah tidak valid.
            }
        }

        Cache::forget($this->adminTokenCacheKey());
        session()->forget(['admin.authenticated', 'admin.token', 'admin.user']);
    }

    public function customerProfile(string $token): array
    {
        return $this->request('GET', '/customer/profile', token: $token)['data'] ?? [];
    }

    public function updateCustomerProfile(string $token, array $payload): array
    {
        return $this->request('PUT', '/customer/profile', ['json' => $payload], $token)['data'] ?? [];
    }

    public function customerAddresses(string $token): array
    {
        return $this->request('GET', '/customer/addresses', token: $token)['data'] ?? [];
    }

    public function customerCart(string $token): array
    {
        return $this->request('GET', '/customer/cart', token: $token)['data'] ?? [];
    }

    public function addCustomerCartItem(string $token, array $payload): array
    {
        return $this->request('POST', '/customer/cart/items', ['json' => $payload], $token)['data'] ?? [];
    }

    public function updateCustomerCartItem(string $token, int $itemId, array $payload): array
    {
        return $this->request('PUT', '/customer/cart/items/'.$itemId, ['json' => $payload], $token)['data'] ?? [];
    }

    public function deleteCustomerCartItem(string $token, int $itemId): void
    {
        $this->request('DELETE', '/customer/cart/items/'.$itemId, token: $token);
    }

    public function searchCustomerDestinations(string $token, string $search, int $limit = 5, int $offset = 0): array
    {
        return $this->request('GET', '/customer/destinations/search', [
            'query' => [
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ], $token)['data'] ?? [];
    }

    public function createCustomerAddress(string $token, array $payload): array
    {
        return $this->request('POST', '/customer/addresses', ['json' => $payload], $token)['data'] ?? [];
    }

    public function updateCustomerAddress(string $token, int $addressId, array $payload): array
    {
        return $this->request('PUT', '/customer/addresses/'.$addressId, ['json' => $payload], $token)['data'] ?? [];
    }

    public function deleteCustomerAddress(string $token, int $addressId): void
    {
        $this->request('DELETE', '/customer/addresses/'.$addressId, token: $token);
    }

    public function customerOrders(string $token): array
    {
        return $this->request('GET', '/customer/orders', token: $token)['data'] ?? [];
    }

    public function customerOrder(string $token, string $code): array
    {
        return $this->request('GET', '/customer/orders/'.$code, token: $token)['data'] ?? [];
    }

    public function createCustomerOrder(string $token, array $payload): array
    {
        return $this->request('POST', '/customer/orders', ['json' => $payload], $token)['data'] ?? [];
    }

    public function cancelCustomerOrder(string $token, string $code): array
    {
        return $this->request('POST', '/customer/orders/'.$code.'/cancel', token: $token)['data'] ?? [];
    }

    public function completeCustomerOrder(string $token, string $code): array
    {
        return $this->request('POST', '/customer/orders/'.$code.'/complete', token: $token)['data'] ?? [];
    }

    public function customerGenerateQris(string $token, string $code): array
    {
        return $this->request('POST', '/customer/orders/'.$code.'/qris', token: $token)['data'] ?? [];
    }

    public function customerQrisStatus(string $token, string $code): array
    {
        return $this->request('GET', '/customer/orders/'.$code.'/qris/status', token: $token)['data'] ?? [];
    }

    public function checkout(string $token, ?int $addressId = null, bool $useUniqueCodeBalance = false): array
    {
        $query = [];

        if ($addressId !== null && $addressId > 0) {
            $query['address_id'] = $addressId;
        }

        if ($useUniqueCodeBalance) {
            $query['use_unique_code_balance'] = 1;
        }

        return $this->request('GET', '/checkout', [
            'query' => $query,
        ], $token)['data'] ?? [];
    }

    public function adminAccounts(array $query = []): array
    {
        return $this->requestAsAdmin('GET', '/admin/accounts', ['query' => $query])['data'] ?? [];
    }

    public function adminAccount(int $id): array
    {
        return $this->requestAsAdmin('GET', '/admin/accounts/'.$id)['data'] ?? [];
    }

    public function createAdminAccount(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/accounts', ['json' => $payload])['data'] ?? [];
    }

    public function updateAdminAccount(int $id, array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/accounts/'.$id, ['json' => $payload])['data'] ?? [];
    }

    public function deleteAdminAccount(int $id): void
    {
        $this->requestAsAdmin('DELETE', '/admin/accounts/'.$id);
    }

    public function adminPaymentAccounts(): array
    {
        return $this->requestAsAdmin('GET', '/admin/payment-accounts')['data'] ?? [];
    }

    public function createAdminPaymentAccount(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/payment-accounts', ['json' => $payload])['data'] ?? [];
    }

    public function updateAdminPaymentAccount(int $id, array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/payment-accounts/'.$id, ['json' => $payload])['data'] ?? [];
    }

    public function deleteAdminPaymentAccount(int $id): void
    {
        $this->requestAsAdmin('DELETE', '/admin/payment-accounts/'.$id);
    }

    public function adminCategories(): array
    {
        return $this->requestAsAdmin('GET', '/admin/categories')['data'] ?? [];
    }

    public function createAdminCategory(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/categories', ['json' => $payload])['data'] ?? [];
    }

    public function updateAdminCategory(int $id, array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/categories/'.$id, ['json' => $payload])['data'] ?? [];
    }

    public function deleteAdminCategory(int $id): void
    {
        $this->requestAsAdmin('DELETE', '/admin/categories/'.$id);
    }

    public function adminSettings(): array
    {
        return $this->requestAsAdmin('GET', '/admin/settings')['data'] ?? [];
    }

    public function updateAdminSettings(array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/settings', ['json' => $payload])['data'] ?? [];
    }

    public function adminProducts(array $query = []): array
    {
        return $this->requestAsAdmin('GET', '/admin/products', ['query' => $query])['data'] ?? [];
    }

    public function adminProduct(int $id): array
    {
        return $this->requestAsAdmin('GET', '/admin/products/'.$id)['data'] ?? [];
    }

    public function createAdminProduct(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/products', ['json' => $payload])['data'] ?? [];
    }

    public function updateAdminProduct(int $id, array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/products/'.$id, ['json' => $payload])['data'] ?? [];
    }

    public function deleteAdminProduct(int $id): array
    {
        return $this->requestAsAdmin('DELETE', '/admin/products/'.$id);
    }

    public function adminCustomers(array $query = []): array
    {
        return $this->requestAsAdmin('GET', '/admin/customers', ['query' => $query])['data'] ?? [];
    }

    public function adminCustomer(int $id): array
    {
        return $this->requestAsAdmin('GET', '/admin/customers/'.$id)['data'] ?? [];
    }

    public function createAdminCustomer(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/customers', ['json' => $payload])['data'] ?? [];
    }

    public function updateAdminCustomer(int $id, array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/customers/'.$id, ['json' => $payload])['data'] ?? [];
    }

    public function deleteAdminCustomer(int $id): void
    {
        $this->requestAsAdmin('DELETE', '/admin/customers/'.$id);
    }

    public function bulkDeleteAdminCustomers(array $codes): array
    {
        return $this->requestAsAdmin('POST', '/admin/customers/bulk-delete', ['json' => ['customer_codes' => $codes]])['data'] ?? [];
    }

    public function adminOrders(array $params = []): array
    {
        return $this->requestAsAdmin('GET', '/admin/orders', ['query' => $params]);
    }

    public function adminDashboard(): array
    {
        return $this->requestAsAdmin('GET', '/admin/dashboard')['data'] ?? [];
    }

    /**
     * Return utuh (data + meta) karena meta berisi total per kartu ringkasan,
     * termasuk potential_income (snapshot COD in-transit, independen dari
     * filter month/paymentMethod di parameter - selalu real-time).
     */
    public function adminAccounting(?string $month = null, string $mode = 'seller', string $paymentMethod = 'all'): array
    {
        return $this->requestAsAdmin('GET', '/admin/accounting', [
            'query' => array_filter([
                'month' => $month,
                'mode' => $mode !== 'seller' ? $mode : null,
                'payment_method' => $paymentMethod !== 'all' ? $paymentMethod : null,
            ]),
        ]);
    }

    public function adminReportTrend(string $granularity = 'day'): array
    {
        return $this->requestAsAdmin('GET', '/admin/reports/trend', ['query' => ['granularity' => $granularity]]);
    }

    public function adminReportProducts(?string $month = null): array
    {
        return $this->requestAsAdmin('GET', '/admin/reports/products', ['query' => array_filter(['month' => $month])]);
    }

    public function adminReportShipping(?string $month = null): array
    {
        return $this->requestAsAdmin('GET', '/admin/reports/shipping', ['query' => array_filter(['month' => $month])]);
    }

    public function adminReportStock(): array
    {
        return $this->requestAsAdmin('GET', '/admin/reports/stock');
    }

    public function adminReportCustomers(?string $month = null): array
    {
        return $this->requestAsAdmin('GET', '/admin/reports/customers', ['query' => array_filter(['month' => $month])]);
    }

    public function adminRajaOngkirBalance(): array
    {
        return $this->requestAsAdmin('GET', '/admin/rajaongkir-balance');
    }

    public function adminPushPublicKey(): array
    {
        return $this->requestAsAdmin('GET', '/admin/push-subscriptions/public-key');
    }

    public function storeAdminPushSubscription(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/push-subscriptions', ['json' => $payload]);
    }

    public function destroyAdminPushSubscription(string $endpoint): array
    {
        return $this->requestAsAdmin('DELETE', '/admin/push-subscriptions', ['json' => ['endpoint' => $endpoint]]);
    }

    public function storeAdminRajaOngkirTopup(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/rajaongkir-balance/topups', ['json' => $payload]);
    }

    public function destroyAdminRajaOngkirTopup(int $id): array
    {
        return $this->requestAsAdmin('DELETE', '/admin/rajaongkir-balance/topups/'.$id);
    }

    /** Sync total biaya generate QRIS dari file mutasi CSV (multipart) ke BE. */
    public function syncAdminRajaOngkirQris(string $tmpPath, string $originalName): array
    {
        $response = Http::acceptJson()
            ->baseUrl(rtrim((string) config('services.larashop_api.base_url'), '/').'/')
            ->withToken($this->adminToken())
            ->timeout(30)
            ->attach('file', (string) file_get_contents($tmpPath), $originalName)
            ->post('admin/rajaongkir-balance/sync-qris');

        return $this->decode($response);
    }

    public function adminOrder(int $id): array
    {
        return $this->requestAsAdmin('GET', '/admin/orders/'.$id)['data'] ?? [];
    }

    public function adminOrderByCode(string $code): array
    {
        return $this->requestAsAdmin('GET', '/admin/orders/by-code/'.$code)['data'] ?? [];
    }

    public function validateAdminOrderPayment(int $id): array
    {
        // Validasi pembayaran memicu auto-booking Komerce di BE (timeout 25s).
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/validate-payment', timeout: 35)['data'] ?? [];
    }

    /** Return utuh (bukan cuma 'data') karena 'message' dipakai buat bedakan berhasil/masih gagal. */
    public function retryAdminOrderBooking(int $id): array
    {
        // Coba lagi createOrder() ke Komerce untuk order yang gagal booking (timeout 25s).
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/retry-booking', timeout: 35);
    }

    public function cancelAdminOrder(int $id, ?string $reason = null): array
    {
        // Kalau order sudah di-booking, BE juga membatalkan ke Komerce (timeout 25s).
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/cancel', [
            'json' => ['reason' => $reason],
        ], 35)['data'] ?? [];
    }

    public function rejectAdminOrderCancellation(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/reject-cancellation')['data'] ?? [];
    }

    /** Cuma boleh untuk order yang belum di-booking (BE yang menegakkan). */
    public function updateAdminOrderRecipient(int $id, array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/orders/'.$id.'/recipient', ['json' => $payload])['data'] ?? [];
    }

    public function processAdminOrderShipment(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/process-shipment')['data'] ?? [];
    }

    public function scheduleAdminPickup(int $id, array $payload): array
    {
        // BE memanggil Komerce pickup/request (timeout BE 25s) - beri jeda lebih
        // longgar dari default 15s supaya FE tidak menyerah duluan.
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/schedule-pickup', ['json' => $payload], 35)['data'] ?? [];
    }

    public function scheduleAdminPickupBulk(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/schedule-pickup-bulk', ['json' => $payload], 35);
    }

    public function markAdminShippedBulk(array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/mark-shipped-bulk', ['json' => $payload]);
    }

    /** Cetak banyak label (PDF gabungan) dari BE. Biner mentah. */
    public function adminLabelsBulk(array $codes): array
    {
        // BE memanggil Komerce printLabel satu-per-satu (beberapa detik/order),
        // jadi timeout perlu lebih longgar daripada default 15s.
        $response = $this->requestRawAsAdmin('POST', '/admin/orders/print-labels-bulk', ['json' => ['order_codes' => $codes]], 120);

        return [
            'content' => $response->body(),
            'content_type' => $response->header('Content-Type') ?: 'application/pdf',
        ];
    }

    public function completeAdminOrder(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/complete')['data'] ?? [];
    }

    /**
     * Ambil label/resi (PDF) dari BE. Bukan JSON — kembalikan biner mentah.
     *
     * @return array{content:string, content_type:string}
     */
    public function adminOrderLabel(int $id): array
    {
        $response = $this->requestRawAsAdmin('GET', '/admin/orders/'.$id.'/label');

        return [
            'content' => $response->body(),
            'content_type' => $response->header('Content-Type') ?: 'application/pdf',
        ];
    }

    /**
     * Label DIY (dibuat sendiri, terpisah dari label resmi Komerce). PDF biner.
     *
     * @return array{content:string, content_type:string}
     */
    public function adminOrderLabelDiy(int $id): array
    {
        $response = $this->requestRawAsAdmin('GET', '/admin/orders/'.$id.'/label-diy');

        return [
            'content' => $response->body(),
            'content_type' => $response->header('Content-Type') ?: 'application/pdf',
        ];
    }

    public function adminShipments(): array
    {
        return $this->requestAsAdmin('GET', '/admin/shipments')['data'] ?? [];
    }

    public function adminShipmentSettings(): array
    {
        return $this->requestAsAdmin('GET', '/admin/shipment-settings')['data'] ?? [];
    }

    public function adminShipmentDestinations(string $search, int $limit = 5, int $offset = 0): array
    {
        return $this->requestAsAdmin('GET', '/admin/shipment-destinations/search', [
            'query' => [
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ])['data'] ?? [];
    }

    public function updateAdminShipmentSettings(array $payload): array
    {
        return $this->requestAsAdmin('PUT', '/admin/shipment-settings', ['json' => $payload])['data'] ?? [];
    }

    public function adminShipment(string $code): array
    {
        return $this->requestAsAdmin('GET', '/admin/shipments/'.$code)['data'] ?? [];
    }

    public function createAdminCustomerAddress(int $customerId, array $payload): array
    {
        return $this->requestAsAdmin('POST', "/admin/customers/{$customerId}/addresses", ['json' => $payload])['data'] ?? [];
    }

    public function updateAdminCustomerAddress(int $customerId, int $addressId, array $payload): array
    {
        return $this->requestAsAdmin('PUT', "/admin/customers/{$customerId}/addresses/{$addressId}", ['json' => $payload])['data'] ?? [];
    }

    public function deleteAdminCustomerAddress(int $customerId, int $addressId): void
    {
        $this->requestAsAdmin('DELETE', "/admin/customers/{$customerId}/addresses/{$addressId}");
    }

    protected function requestAsAdmin(string $method, string $uri, array $options = [], ?int $timeout = null): array
    {
        $token = $this->adminToken();

        try {
            return $this->request($method, $uri, $options, $token, $timeout);
        } catch (LarashopApiException $exception) {
            if ($exception->status !== 401) {
                throw $exception;
            }

            Cache::forget($this->adminTokenCacheKey());

            return $this->request($method, $uri, $options, $this->adminToken(), $timeout);
        }
    }

    public function adminQrisList(): array
    {
        return $this->requestAsAdmin('GET', '/admin/qrisly');
    }

    public function adminQrisActivate(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/qrisly/'.$id.'/activate');
    }

    public function adminQrisDelete(int $id): array
    {
        return $this->requestAsAdmin('DELETE', '/admin/qrisly/'.$id);
    }

    /** Upload QRIS (multipart) ke BE. */
    public function adminQrisUpload(string $tmpPath, string $originalName, string $name): array
    {
        // JANGAN pakai client() yang memaksa ->asJson() (memasang header
        // Content-Type: application/json) — itu menimpa boundary multipart sehingga
        // file & field 'name' tidak terbaca BE. Bangun request multipart sendiri.
        $response = Http::acceptJson()
            ->baseUrl(rtrim((string) config('services.larashop_api.base_url'), '/').'/')
            ->withToken($this->adminToken())
            ->timeout(30)
            ->attach('qris_image', (string) file_get_contents($tmpPath), $originalName)
            ->post('admin/qrisly/upload', ['name' => $name]);

        return $this->decode($response);
    }

    protected function requestRawAsAdmin(string $method, string $uri, array $options = [], ?int $timeout = null): Response
    {
        $response = $this->client($this->adminToken(), $timeout)->send($method, ltrim($uri, '/'), $options);

        if ($response->status() === 401) {
            Cache::forget($this->adminTokenCacheKey());
            $response = $this->client($this->adminToken(), $timeout)->send($method, ltrim($uri, '/'), $options);
        }

        if (! $response->successful()) {
            $body = $response->json();

            throw new LarashopApiException(
                $response->status(),
                data_get($body, 'message', 'Gagal mengambil data dari server.'),
                (array) data_get($body, 'errors', []),
            );
        }

        return $response;
    }

    protected function adminToken(): string
    {
        $sessionToken = session('admin.token');

        if (is_string($sessionToken) && $sessionToken !== '') {
            return $sessionToken;
        }

        return Cache::remember($this->adminTokenCacheKey(), now()->addHours(12), function (): string {
            $response = $this->request('POST', '/auth/login', [
                'json' => [
                    'login' => config('services.larashop_api.admin_login'),
                    'password' => config('services.larashop_api.admin_password'),
                    'device_name' => 'larashop-fe-admin-panel',
                ],
            ]);

            return data_get($response, 'data.token', '');
        });
    }

    protected function adminTokenCacheKey(): string
    {
        return 'larashop-fe.admin-token';
    }

    protected function request(string $method, string $uri, array $options = [], ?string $token = null, ?int $timeout = null): array
    {
        $response = $this->client($token, $timeout)->send($method, ltrim($uri, '/'), $options);

        return $this->decode($response);
    }

    protected function client(?string $token = null, ?int $timeout = null): PendingRequest
    {
        $client = Http::acceptJson()
            ->baseUrl(rtrim((string) config('services.larashop_api.base_url'), '/').'/')
            ->timeout($timeout ?? 15)
            ->asJson();

        if ($token !== null && $token !== '') {
            $client = $client->withToken($token);
        }

        return $client;
    }

    protected function decode(Response $response): array
    {
        if ($response->successful()) {
            return $response->json();
        }

        $body = $response->json();
        $message = data_get($body, 'message', 'Larashop API request failed.');
        $errors = data_get($body, 'errors', []);

        throw new LarashopApiException($response->status(), $message, is_array($errors) ? $errors : []);
    }
}
