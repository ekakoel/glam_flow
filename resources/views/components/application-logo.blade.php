@php
    $logoOwner = auth()->user();
    $dynamicLogo = $logoOwner?->logoUrl();
    $logoSrc = $dynamicLogo ?: asset('images/logo/pavicon.png');
@endphp

<img
    src="{{ $logoSrc }}"
    alt="{{ config('app.name', 'Glam Flow') }} Logo"
    {{ $attributes->merge(['class' => 'h-9 w-auto object-contain']) }}
>
