@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<div class="space-y-4">
    @if ($eyebrow)
        <span class="flex items-center gap-2 font-label-eyebrow text-label-eyebrow uppercase text-primary">
            <span class="h-1 w-1 rounded-full bg-primary"></span>
            {{ $eyebrow }}
        </span>
    @endif
    <h1 class="font-headline-xl text-headline-lg-mobile text-on-surface md:text-headline-xl">{{ $title }}</h1>
    @if ($description)
        <p class="max-w-2xl font-body-lg text-body-lg text-on-surface-variant">{{ $description }}</p>
    @endif
</div>
