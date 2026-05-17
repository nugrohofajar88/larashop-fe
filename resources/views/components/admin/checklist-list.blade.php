@props([
    'items' => [],
])

<div {{ $attributes->merge(['class' => 'space-y-3 text-sm text-stone-700']) }}>
    @foreach ($items as $item)
        <div class="rounded-2xl bg-stone-50 px-4 py-4">{{ $item }}</div>
    @endforeach
</div>
