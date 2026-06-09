<x-layouts.customer title="Sobat Akar Tani Kimia | Alamat">
    @php
        $openOnLoad = $errors->any();
        $formMode = old('_address_mode', 'create');
        $formAddressId = old('_address_id', '');
    @endphp

    <div class="flex flex-col gap-10 md:flex-row">
        <x-customer.account-nav />

        <section class="flex-1">
            <h1 class="mb-8 font-headline-lg text-headline-lg text-on-surface">Alamat</h1>

            <div
                class="space-y-6"
                data-customer-address-manager
                data-search-url="{{ route('addresses.destination-search') }}"
                data-save-action="{{ route('addresses.save') }}"
                data-detail-template="{{ route('addresses.detail', ['id' => '__ID__']) }}"
                data-open-on-load="{{ $openOnLoad ? 'true' : 'false' }}"
                data-initial-mode="{{ $formMode }}"
                data-initial-address-id="{{ $formAddressId }}"
            >
                @if ($errors->any())
                    <div class="rounded-2xl border border-error-container bg-error-container/40 px-5 py-4 font-body-sm text-body-sm text-on-error-container">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex flex-col gap-3 rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface">Alamat saya</h2>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Tentukan alamat utama yang dipakai otomatis saat checkout.</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-primary px-5 py-3 font-body-md font-bold text-on-primary transition hover:bg-secondary"
                        data-address-open-create
                    >
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Tambah alamat
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach ($addresses as $address)
                        <x-customer.address-card :address="$address" />
                    @endforeach
                </div>

                <x-customer.address-modal-form :form-mode="$formMode" :form-address-id="$formAddressId" />
            </div>
        </section>
    </div>
</x-layouts.customer>
