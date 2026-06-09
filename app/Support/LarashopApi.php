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

    public function adminOrders(): array
    {
        return $this->requestAsAdmin('GET', '/admin/orders')['data'] ?? [];
    }

    public function adminOrder(int $id): array
    {
        return $this->requestAsAdmin('GET', '/admin/orders/'.$id)['data'] ?? [];
    }

    public function validateAdminOrderPayment(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/validate-payment')['data'] ?? [];
    }

    public function cancelAdminOrder(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/cancel')['data'] ?? [];
    }

    public function processAdminOrderShipment(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/process-shipment')['data'] ?? [];
    }

    public function scheduleAdminPickup(int $id, array $payload): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/schedule-pickup', ['json' => $payload])['data'] ?? [];
    }

    public function completeAdminOrder(int $id): array
    {
        return $this->requestAsAdmin('POST', '/admin/orders/'.$id.'/complete')['data'] ?? [];
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

    protected function requestAsAdmin(string $method, string $uri, array $options = []): array
    {
        $token = $this->adminToken();

        try {
            return $this->request($method, $uri, $options, $token);
        } catch (LarashopApiException $exception) {
            if ($exception->status !== 401) {
                throw $exception;
            }

            Cache::forget($this->adminTokenCacheKey());

            return $this->request($method, $uri, $options, $this->adminToken());
        }
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

    protected function request(string $method, string $uri, array $options = [], ?string $token = null): array
    {
        $response = $this->client($token)->send($method, ltrim($uri, '/'), $options);

        return $this->decode($response);
    }

    protected function client(?string $token = null): PendingRequest
    {
        $client = Http::acceptJson()
            ->baseUrl(rtrim((string) config('services.larashop_api.base_url'), '/').'/')
            ->timeout(15)
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
