<?php

namespace App\Http\Controllers;

use App\Support\LarashopApi;
use App\Support\LarashopApiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function __construct(
        private readonly LarashopApi $api,
    ) {
    }

    public function catalog(Request $request): View
    {
        $query = $this->catalogQuery($request);
        $response = $this->api->publicProducts($query);

        return view('storefront.catalog', [
            'products' => data_get($response, 'data', []),
            'catalogQuery' => $query,
            'search' => $request->string('search')->toString(),
            'activeCategory' => $request->string('category')->toString() ?: 'all',
            'activeStatus' => $request->string('status')->toString() ?: 'all',
            'activeSort' => $request->string('sort')->toString() ?: 'default',
            'categories' => data_get($response, 'meta.categories', []),
            'statuses' => data_get($response, 'meta.statuses', []),
        ]);
    }

    public function product(Request $request, string $slug): View
    {
        $payload = $this->api->publicProduct($slug);
        $product = data_get($payload, 'product', []);
        $gallery = collect($product['images'] ?? [])
            ->map(fn (array $image, int $index) => [
                'path' => $image['path'],
                'label' => $image['alt'] ?: 'Foto '.($index + 1),
            ])
            ->values()
            ->all();

        if ($gallery === [] && isset($product['image'])) {
            $gallery[] = [
                'path' => $product['image'],
                'label' => $product['name'] ?? 'Foto utama',
            ];
        }

        return view('storefront.product', [
            'catalogQuery' => $this->catalogQuery($request),
            'gallery' => $gallery,
            'product' => $product,
            'relatedProducts' => data_get($payload, 'related_products', []),
        ]);
    }

    public function cart(): View
    {
        $token = $this->customerToken();

        if ($token === null) {
            return view('storefront.cart', [
                'items' => [],
                'summary' => [
                    'selected_product_count' => 0,
                    'selected_total_value' => 0,
                    'selected_total' => 'Rp0',
                ],
            ]);
        }

        try {
            $payload = $this->api->customerCart($token);
        } catch (LarashopApiException $exception) {
            return view('storefront.cart', [
                'items' => [],
                'summary' => [
                    'selected_product_count' => 0,
                    'selected_total_value' => 0,
                    'selected_total' => 'Rp0',
                ],
            ])->with('error', $exception->getMessage());
        }

        return view('storefront.cart', [
            'items' => data_get($payload, 'items', []),
            'summary' => data_get($payload, 'summary', [
                'selected_product_count' => 0,
                'selected_total_value' => 0,
                'selected_total' => 'Rp0',
            ]),
        ]);
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk menambahkan produk ke keranjang.');
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'product_variant_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'redirect_to' => ['nullable', 'string'],
        ]);

        try {
            $this->api->addCustomerCartItem($token, [
                'product_id' => (int) $validated['product_id'],
                'product_variant_id' => (int) $validated['product_variant_id'],
                'quantity' => (int) ($validated['quantity'] ?? 1),
                'selected' => ($validated['redirect_to'] ?? '') === 'checkout',
            ]);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return back()
                ->withInput()
                ->withErrors($exception->errors !== [] ? $exception->errors : ['product_variant_id' => $exception->getMessage()]);
        }

        if (($validated['redirect_to'] ?? '') === 'checkout') {
            return redirect()->route('checkout')->with('success', 'Varian produk siap diproses ke checkout.');
        }

        return redirect()->route('cart')->with('success', 'Produk berhasil masuk ke keranjang.');
    }

    public function updateCartItem(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return response()->json([
                'message' => 'Silakan login dulu untuk memperbarui keranjang.',
            ], 401);
        }

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'selected' => ['nullable', 'boolean'],
        ]);

        try {
            $payload = $this->api->updateCustomerCartItem($token, $id, $validated);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return response()->json([
                    'message' => 'Session customer tidak valid. Silakan login ulang.',
                ], $exception->status);
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors,
            ], $exception->status >= 400 ? $exception->status : 422);
        }

        return response()->json([
            'data' => $payload,
        ]);
    }

    public function destroyCartItem(int $id): JsonResponse|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return response()->json([
                'message' => 'Silakan login dulu untuk menghapus item keranjang.',
            ], 401);
        }

        try {
            $this->api->deleteCustomerCartItem($token, $id);
            $cart = $this->api->customerCart($token);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return response()->json([
                    'message' => 'Session customer tidak valid. Silakan login ulang.',
                ], $exception->status);
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors,
            ], $exception->status >= 400 ? $exception->status : 422);
        }

        return response()->json([
            'data' => [
                'summary' => data_get($cart, 'summary', [
                    'selected_product_count' => 0,
                    'selected_total_value' => 0,
                    'selected_total' => 'Rp0',
                ]),
            ],
        ]);
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk lanjut checkout.');
        }

        try {
            $payload = $this->resolveCheckoutPayload($request, $token);
        } catch (LarashopApiException $exception) {
            return $this->handleCustomerAccessException($exception);
        }

        return view('storefront.checkout', [
            'address' => data_get($payload, 'address', []),
            'addresses' => data_get($payload, 'addresses', []),
            'shippingOptions' => data_get($payload, 'shipping_options', []),
            'paymentSummary' => data_get($payload, 'payment_summary', []),
            'shipmentOrigin' => data_get($payload, 'shipment_origin'),
            'items' => data_get($payload, 'items', []),
        ]);
    }

    public function checkoutData(Request $request): JsonResponse|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return response()->json([
                'message' => 'Silakan login dulu untuk lanjut checkout.',
            ], 401);
        }

        try {
            $payload = $this->resolveCheckoutPayload($request, $token);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return response()->json([
                    'message' => 'Session customer tidak valid. Silakan login ulang.',
                ], $exception->status);
            }

            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status >= 400 ? $exception->status : 500);
        }

        return response()->json([
            'data' => [
                'address' => data_get($payload, 'address', []),
                'addresses' => data_get($payload, 'addresses', []),
                'shipping_options' => data_get($payload, 'shipping_options', []),
                'payment_summary' => data_get($payload, 'payment_summary', []),
                'shipment_origin' => data_get($payload, 'shipment_origin'),
            ],
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk membuat pesanan.');
        }

        $validated = $request->validate([
            'address_id' => ['required', 'integer'],
            'shipping_option_id' => ['required', 'string', 'max:100'],
            'use_unique_code_balance' => ['nullable', 'boolean'],
        ]);

        try {
            $order = $this->api->createCustomerOrder($token, [
                'address_id' => (int) $validated['address_id'],
                'shipping_option_id' => $validated['shipping_option_id'],
                'use_unique_code_balance' => $request->boolean('use_unique_code_balance'),
            ]);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return redirect()
                ->route('checkout', $request->only(['address_id', 'use_unique_code_balance']))
                ->withInput()
                ->withErrors($exception->errors !== [] ? $exception->errors : ['checkout' => $exception->getMessage()]);
        }

        return redirect()
            ->route('customer.orders.show', $order['code'])
            ->with('success', "Pesanan {$order['code']} berhasil dibuat.");
    }

    public function login(): View
    {
        return view('storefront.auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $payload = $this->api->login($validated['login'], $validated['password']);
        } catch (LarashopApiException $exception) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors($exception->errors !== [] ? $exception->errors : ['login' => $exception->getMessage()]);
        }

        if (data_get($payload, 'user.role') !== 'customer') {
            if (! empty($payload['token'])) {
                try {
                    $this->api->logout($payload['token']);
                } catch (LarashopApiException) {
                    // Ignore token revoke failure for non-customer login on storefront.
                }
            }

            return back()
                ->withInput($request->except('password'))
                ->withErrors(['login' => 'Akun ini bukan akun customer. Gunakan panel admin untuk login admin.']);
        }

        $this->storeCustomerSession($payload);

        return redirect()->route('home')->with('success', 'Login customer berhasil.');
    }

    public function register(): View
    {
        return view('storefront.auth.register');
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Laravel tidak menyertakan password_confirmation di $validated; BE juga
        // pakai rule `confirmed`, jadi konfirmasinya harus ikut diteruskan.
        $validated['password_confirmation'] = (string) $request->input('password_confirmation');

        try {
            $payload = $this->api->register($validated);
        } catch (LarashopApiException $exception) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors($exception->errors !== [] ? $exception->errors : ['username' => $exception->getMessage()]);
        }

        $this->storeCustomerSession($payload);

        return redirect()->route('home')->with('success', 'Akun customer berhasil dibuat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token !== null) {
            try {
                $this->api->logout($token);
            } catch (LarashopApiException) {
                // Clear local session even if backend token is already invalid.
            }
        }

        $request->session()->forget(['customer.token', 'customer.user']);

        return redirect()->route('home')->with('success', 'Session customer sudah keluar.');
    }

    public function profile(): View|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk membuka profil.');
        }

        try {
            $customer = $this->api->customerProfile($token);
        } catch (LarashopApiException $exception) {
            return $this->handleCustomerAccessException($exception);
        }

        return view('storefront.profile', [
            'customer' => $customer,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk memperbarui profil.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
        ];

        if (($validated['password'] ?? '') !== '') {
            $payload['password'] = $validated['password'];
            $payload['password_confirmation'] = $request->input('password_confirmation');
        }

        try {
            $customer = $this->api->updateCustomerProfile($token, $payload);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        session(['customer.user' => $customer]);

        return redirect()->route('profile')->with('success', 'Profil customer berhasil diperbarui.');
    }

    public function addresses(): View|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk membuka alamat.');
        }

        try {
            $addresses = $this->api->customerAddresses($token);
        } catch (LarashopApiException $exception) {
            return $this->handleCustomerAccessException($exception);
        }

        return view('storefront.addresses', [
            'addresses' => $addresses,
            'editingAddress' => collect($addresses)->firstWhere('id', (int) request('edit')),
        ]);
    }

    public function searchAddressDestinations(Request $request): JsonResponse|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return response()->json([
                'message' => 'Silakan login dulu.',
            ], 401);
        }

        $validated = $request->validate([
            'search' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        try {
            $destinations = $this->api->searchCustomerDestinations($token, $validated['search']);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return response()->json([
                    'message' => 'Session customer tidak valid.',
                ], $exception->status);
            }

            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status >= 400 ? $exception->status : 500);
        }

        return response()->json([
            'data' => $destinations,
        ]);
    }

    public function addressDetail(int $id): JsonResponse|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return response()->json([
                'message' => 'Silakan login dulu.',
            ], 401);
        }

        try {
            $addresses = $this->api->customerAddresses($token);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return response()->json([
                    'message' => 'Session customer tidak valid.',
                ], $exception->status);
            }

            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status >= 400 ? $exception->status : 500);
        }

        $address = collect($addresses)->firstWhere('id', $id);

        if ($address === null) {
            return response()->json([
                'message' => 'Alamat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $address,
        ]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk menambah alamat.');
        }

        $payload = $this->validateAddressPayload($request);

        try {
            $this->api->createCustomerAddress($token, $payload);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return back()
                ->withInput()
                ->withErrors($exception->errors !== [] ? $exception->errors : ['label' => $exception->getMessage()]);
        }

        return redirect()->route('addresses')->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    public function saveAddress(Request $request): JsonResponse|RedirectResponse
    {
        $token = $this->customerToken();

        Log::info('storefront.addresses.save.request', [
            'mode' => $request->input('_address_mode', 'create'),
            'address_id' => $request->input('_address_id'),
            'expects_json' => $request->expectsJson(),
            'payload' => $request->except(['_token']),
        ]);

        if ($token === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Silakan login dulu untuk menyimpan alamat.',
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Silakan login dulu untuk menyimpan alamat.');
        }

        $payload = $this->validateAddressPayload($request);
        $mode = $request->input('_address_mode', 'create');
        $addressId = (int) $request->input('_address_id');

        try {
            if ($mode === 'edit' && $addressId > 0) {
                $this->api->updateCustomerAddress($token, $addressId, $payload);

                Log::info('storefront.addresses.save.success', [
                    'mode' => 'edit',
                    'address_id' => $addressId,
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Alamat berhasil diperbarui.',
                    ]);
                }

                return redirect()->route('addresses')->with('success', 'Alamat berhasil diperbarui.');
            }

            $this->api->createCustomerAddress($token, $payload);

            Log::info('storefront.addresses.save.success', [
                'mode' => 'create',
            ]);
        } catch (LarashopApiException $exception) {
            Log::warning('storefront.addresses.save.failed', [
                'mode' => $mode,
                'address_id' => $addressId,
                'status' => $exception->status,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors,
            ]);

            if (in_array($exception->status, [401, 403], true)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Session customer tidak valid atau akun ini bukan customer. Silakan login ulang dengan akun customer.',
                    ], $exception->status);
                }

                return $this->handleCustomerAccessException($exception);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors,
                ], $exception->status >= 400 ? $exception->status : 422);
            }

            return back()
                ->withInput()
                ->withErrors($exception->errors !== [] ? $exception->errors : ['label' => $exception->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Alamat baru berhasil ditambahkan.',
            ], 201);
        }

        return redirect()->route('addresses')->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, int $id): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk mengubah alamat.');
        }

        $payload = $this->validateAddressPayload($request);

        try {
            $this->api->updateCustomerAddress($token, $id, $payload);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return back()
                ->withInput()
                ->withErrors($exception->errors !== [] ? $exception->errors : ['label' => $exception->getMessage()]);
        }

        return redirect()->route('addresses')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function makePrimaryAddress(Request $request, int $id): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk memilih alamat utama.');
        }

        try {
            $addresses = $this->api->customerAddresses($token);
            $address = collect($addresses)->firstWhere('id', $id);

            if ($address === null) {
                return redirect()->route('addresses')->with('error', 'Alamat tidak ditemukan.');
            }

            $this->api->updateCustomerAddress($token, $id, [
                'label' => $address['label'],
                'destination_id' => $address['destination_id'] ?? null,
                'recipient_name' => $address['name'],
                'recipient_phone' => $address['phone'],
                'province' => $address['province'],
                'city' => $address['city'],
                'district' => $address['district'],
                'subdistrict' => $address['subdistrict'],
                'postal_code' => $address['postal_code'],
                'address_line' => $address['address_line'],
                'note' => $address['note'],
                'is_primary' => true,
            ]);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return redirect()->route('addresses')->with('error', $exception->getMessage());
        }

        return redirect()->route('addresses')->with('success', 'Alamat utama berhasil diperbarui.');
    }

    public function destroyAddress(int $id): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk menghapus alamat.');
        }

        try {
            $this->api->deleteCustomerAddress($token, $id);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return redirect()->route('addresses')->with('error', $exception->getMessage());
        }

        return redirect()->route('addresses')->with('success', 'Alamat berhasil dihapus.');
    }

    public function orders(): View|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk membuka pesanan.');
        }

        try {
            $orders = $this->api->customerOrders($token);
        } catch (LarashopApiException $exception) {
            return $this->handleCustomerAccessException($exception);
        }

        $allOrders = collect($orders);
        $orderCounts = [
            'all' => $allOrders->count(),
            'pending_payment' => $allOrders->where('status', 'pending_payment')->count(),
            'paid' => $allOrders->where('status', 'paid')->count(),
            'processing' => $allOrders->where('status', 'processing')->count(),
            'shipped' => $allOrders->where('status', 'shipped')->count(),
            'completed' => $allOrders->where('status', 'completed')->count(),
            'cancelled' => $allOrders->where('status', 'cancelled')->count(),
        ];

        $tabs = [
            ['key' => 'all', 'label' => 'Semua', 'count' => $orderCounts['all']],
            ['key' => 'pending_payment', 'label' => 'Belum bayar', 'count' => $orderCounts['pending_payment']],
            ['key' => 'paid', 'label' => 'Dibayar', 'count' => $orderCounts['paid']],
            ['key' => 'processing', 'label' => 'Diproses', 'count' => $orderCounts['processing']],
            ['key' => 'shipped', 'label' => 'Dikirim', 'count' => $orderCounts['shipped']],
            ['key' => 'completed', 'label' => 'Selesai', 'count' => $orderCounts['completed']],
            ['key' => 'cancelled', 'label' => 'Dibatalkan', 'count' => $orderCounts['cancelled']],
        ];
        $activeStatus = request()->string('status')->toString() ?: 'all';

        if (! in_array($activeStatus, collect($tabs)->pluck('key')->all(), true)) {
            $activeStatus = 'all';
        }

        if ($activeStatus !== 'all') {
            $orders = $allOrders
                ->filter(fn (array $order): bool => ($order['status'] ?? null) === $activeStatus)
                ->values()
                ->all();
        }

        return view('storefront.orders', [
            'orders' => $orders,
            'orderTabs' => $tabs,
            'activeOrderStatus' => $activeStatus,
            'statuses' => [
                ['label' => 'pending_payment', 'description' => 'Order dibuat dan menunggu pembayaran.'],
                ['label' => 'paid', 'description' => 'Pembayaran sudah tervalidasi admin.'],
                ['label' => 'processing', 'description' => 'Order sedang disiapkan sebelum masuk proses kirim.'],
                ['label' => 'shipped', 'description' => 'Pesanan sudah dikirim dan memiliki resi.'],
                ['label' => 'completed', 'description' => 'Pesanan sudah selesai diterima customer.'],
                ['label' => 'cancelled', 'description' => 'Pesanan dihentikan dan tidak akan lanjut ke proses pembayaran atau pengiriman.'],
            ],
        ]);
    }

    public function orderDetail(string $code): View|RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk membuka detail pesanan.');
        }

        try {
            $order = $this->api->customerOrder($token, $code);
        } catch (LarashopApiException $exception) {
            return $this->handleCustomerAccessException($exception);
        }

        // Pembayaran via QRIS terkonfirmasi otomatis (tanpa validasi manual admin),
        // jadi label/notes timeline disesuaikan biar tak membingungkan pelanggan.
        $isQris = ($order['payment']['method'] ?? '') === 'QRIS';

        return view('storefront.order-detail', [
            'order' => $order,
            'timeline' => [
                ['label' => 'Order dibuat', 'note' => 'Pesanan berhasil dibuat di sistem.', 'active' => true],
                ['label' => 'Menunggu pembayaran', 'note' => $isQris ? 'Bayar dengan scan QRIS.' : 'Customer melakukan transfer manual.', 'active' => in_array($order['status'], ['pending_payment', 'paid', 'processing', 'shipped', 'completed'], true)],
                ['label' => $isQris ? 'Pembayaran terkonfirmasi' : 'Validasi admin', 'note' => $isQris ? 'Pembayaran QRIS otomatis terkonfirmasi, tanpa cek manual.' : 'Admin akan mengecek mutasi dan nominal unik.', 'active' => in_array($order['status'], ['paid', 'processing', 'shipped', 'completed'], true)],
                ['label' => 'Proses pengiriman', 'note' => 'Shipment dibuat setelah pembayaran tervalidasi.', 'active' => in_array($order['status'], ['processing', 'shipped', 'completed'], true)],
                ['label' => 'Pesanan diterima', 'note' => 'Customer sudah menerima barang dan order dinyatakan selesai.', 'active' => ($order['status'] ?? null) === 'completed'],
                ['label' => 'Pesanan dibatalkan', 'note' => 'Order dihentikan dan tidak akan diproses lebih lanjut.', 'active' => ($order['status'] ?? null) === 'cancelled', 'tone' => 'cancelled'],
            ],
        ]);
    }

    public function orderQris(string $code): JsonResponse
    {
        $token = $this->customerToken();
        if ($token === null) {
            return response()->json(['message' => 'Sesi habis, silakan login ulang.'], 401);
        }

        try {
            $data = $this->api->customerGenerateQris($token, $code);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal membuat QRIS: '.$e->getMessage()], 422);
        }

        return response()->json(['data' => $data]);
    }

    public function orderQrisStatus(string $code): JsonResponse
    {
        $token = $this->customerToken();
        if ($token === null) {
            return response()->json(['message' => 'unauthorized'], 401);
        }

        try {
            $data = $this->api->customerQrisStatus($token, $code);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['data' => $data]);
    }

    public function cancelOrder(string $code): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk membatalkan pesanan.');
        }

        try {
            $this->api->cancelCustomerOrder($token, $code);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return redirect()
                ->route('customer.orders.show', $code)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('customer.orders.show', $code)
            ->with('success', "Pesanan {$code} berhasil dibatalkan.");
    }

    public function completeOrder(string $code): RedirectResponse
    {
        $token = $this->customerToken();

        if ($token === null) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk menyelesaikan pesanan.');
        }

        try {
            $this->api->completeCustomerOrder($token, $code);
        } catch (LarashopApiException $exception) {
            if (in_array($exception->status, [401, 403], true)) {
                return $this->handleCustomerAccessException($exception);
            }

            return redirect()
                ->route('customer.orders.show', $code)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('customer.orders.show', $code)
            ->with('success', "Pesanan {$code} ditandai sudah diterima.");
    }

    protected function catalogQuery(Request $request): array
    {
        return collect($request->only(['search', 'category', 'status', 'sort']))
            ->filter(function (?string $value, string $key): bool {
                if ($value === null || $value === '') {
                    return false;
                }

                return ! (($key === 'category' || $key === 'status') && $value === 'all')
                    && ! ($key === 'sort' && $value === 'default');
            })
            ->all();
    }

    protected function customerToken(): ?string
    {
        return session('customer.token');
    }

    protected function resolveCheckoutPayload(Request $request, string $token): array
    {
        $selectedAddressId = $request->integer('address_id');
        $useUniqueCodeBalance = $request->boolean('use_unique_code_balance');

        return $this->api->checkout(
            $token,
            $selectedAddressId > 0 ? $selectedAddressId : null,
            $useUniqueCodeBalance
        );
    }

    protected function validateAddressPayload(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'is_primary' => ['sometimes', 'boolean'],
            'destination_id' => ['nullable', 'integer'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'subdistrict' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'address_line' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
    }

    protected function storeCustomerSession(array $payload): void
    {
        session([
            'customer.token' => $payload['token'],
            'customer.user' => $payload['user'],
        ]);
    }

    protected function handleCustomerAccessException(LarashopApiException $exception): RedirectResponse
    {
        if (in_array($exception->status, [401, 403], true)) {
            session()->forget(['customer.token', 'customer.user']);

            return redirect()
                ->route('login')
                ->with('error', 'Session customer tidak valid atau akun ini bukan customer. Silakan login ulang dengan akun customer.');
        }

        throw $exception;
    }
}
