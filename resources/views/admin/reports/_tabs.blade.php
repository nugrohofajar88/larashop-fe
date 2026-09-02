@php
    $reportTabs = [
        ['route' => 'admin.reports.trend', 'label' => 'Tren Penjualan'],
        ['route' => 'admin.reports.products', 'label' => 'Penjualan Produk'],
        ['route' => 'admin.reports.shipping', 'label' => 'Performa Ekspedisi'],
        ['route' => 'admin.reports.stock', 'label' => 'Stok'],
        ['route' => 'admin.reports.customers', 'label' => 'Pelanggan'],
    ];
@endphp

<div class="flex flex-wrap gap-2">
    @foreach ($reportTabs as $tab)
        <a href="{{ route($tab['route']) }}" class="rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs($tab['route']) ? 'bg-stone-900 text-white' : 'border border-stone-200 bg-white text-stone-600 hover:border-stone-300' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
