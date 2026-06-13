@props([
    'address',
    'editable' => true,
    'showPrimaryAction' => true,
])

<article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <p class="font-body-md font-semibold text-on-surface">{{ $address['label'] }}</p>
                @if ($address['is_primary'])
                    <span class="rounded-full bg-secondary-container px-3 py-1 text-xs font-semibold text-on-secondary-container">Utama</span>
                @endif
            </div>
            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">{{ $address['name'] }} · {{ $address['phone'] }}</p>
        </div>
    </div>

    <p class="mt-4 font-body-md text-body-md leading-6 text-on-surface-variant">{{ $address['detail'] }}</p>

    @if ($editable)
        <div class="mt-4 flex flex-wrap gap-3">
            <button
                type="button"
                class="rounded-full border border-surface-container-highest px-4 py-2 font-body-sm text-sm font-medium text-on-surface-variant transition hover:border-primary hover:text-primary"
                data-address-open-edit
                data-address-id="{{ $address['id'] }}"
                data-address-destination-id="{{ $address['destination_id'] ?? '' }}"
                data-address-label="{{ $address['label'] }}"
                data-address-is-primary="{{ $address['is_primary'] ? '1' : '0' }}"
                data-address-recipient-name="{{ $address['name'] }}"
                data-address-recipient-phone="{{ $address['phone'] }}"
                data-address-province="{{ $address['province'] }}"
                data-address-city="{{ $address['city'] }}"
                data-address-district="{{ $address['district'] }}"
                data-address-subdistrict="{{ $address['subdistrict'] }}"
                data-address-postal-code="{{ $address['postal_code'] }}"
                data-address-line="{{ $address['address_line'] }}"
                data-address-note="{{ $address['note'] }}"
                data-address-destination-label="{{ $address['subdistrict'] }}, {{ $address['district'] }}, {{ $address['city'] }}, {{ $address['province'] }}, {{ $address['postal_code'] }}"
            >
                Edit
            </button>

            @if ($showPrimaryAction && ! $address['is_primary'])
                <form method="POST" action="{{ route('addresses.primary', $address['id']) }}">
                    @csrf
                    <button type="submit" class="rounded-full bg-on-background px-4 py-2 font-body-sm text-sm font-medium text-on-primary transition hover:bg-primary">Jadikan utama</button>
                </form>
            @endif

            <form method="POST" action="{{ route('addresses.destroy', $address['id']) }}" data-confirm="Hapus alamat ini?" data-confirm-ok="Ya, hapus">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full bg-error-container/50 px-4 py-2 font-body-sm text-sm font-medium text-error transition hover:bg-error-container">Hapus</button>
            </form>
        </div>
    @endif
</article>
