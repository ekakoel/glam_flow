<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Billing & Plan</h2>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('error'))
                <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-700">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-2xl border border-rose-100 shadow-md p-6">
                <h3 class="text-lg font-semibold text-stone-900">Current Plan</h3>
                <div class="mt-4 grid md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-stone-200 bg-stone-50">
                        <p class="text-sm text-stone-500">Plan Name</p>
                        <p class="text-2xl font-bold text-stone-900 uppercase">{{ $plan }}</p>
                    </div>
                    <div class="p-4 rounded-xl border border-stone-200 bg-stone-50">
                        <p class="text-sm text-stone-500">Price</p>
                        <p class="text-2xl font-bold text-stone-900">{{ $currentPlan['price'] }}</p>
                    </div>
                    <div class="p-4 rounded-xl border border-stone-200 bg-stone-50">
                        <p class="text-sm text-stone-500">Expiry Date</p>
                        <p class="text-2xl font-bold text-stone-900">{{ $expiresAt?->format('d M Y') ?? 'No expiry' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-rose-100 shadow-md p-6">
                <h3 class="text-lg font-semibold text-stone-900">Features</h3>
                <div class="mt-4 grid md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-stone-200 bg-stone-50">
                        <p class="text-sm text-stone-500">Booking Limit</p>
                        <p class="text-lg font-semibold text-stone-900">{{ $currentPlan['booking_limit'] }}</p>
                    </div>
                    <div class="p-4 rounded-xl border border-stone-200 bg-stone-50">
                        <p class="text-sm text-stone-500">Included Features</p>
                        <ul class="mt-2 text-sm text-stone-700 space-y-1">
                            @foreach($currentPlan['features'] as $feature)
                                <li>• {{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-rose-100 shadow-md p-6">
                <h3 class="text-lg font-semibold text-stone-900">Upgrade Options</h3>
                <div class="mt-4 grid md:grid-cols-3 gap-4">
                    @foreach($plans as $key => $planData)
                        @php
                            $isCurrent = $plan === $key;
                            $isRecommended = $plan === 'free' && $key === 'pro';
                            $theme = match($planData['theme']) {
                                'amber' => 'border-amber-300 bg-amber-50',
                                'rose' => 'border-rose-300 bg-rose-50',
                                default => 'border-stone-300 bg-stone-50'
                            };
                        @endphp
                        <div class="relative p-5 rounded-xl border {{ $theme }} {{ $isCurrent ? 'ring-2 ring-stone-800' : '' }}">
                            @if($isCurrent)
                                <span class="absolute -top-2 right-3 px-2 py-1 rounded bg-stone-800 text-white text-xs">Current</span>
                            @elseif($isRecommended)
                                <span class="absolute -top-2 right-3 px-2 py-1 rounded bg-rose-600 text-white text-xs">Recommended</span>
                            @endif

                            <h4 class="text-xl font-bold text-stone-900">{{ $planData['name'] }}</h4>
                            <p class="mt-1 text-stone-700">{{ $planData['price'] }}</p>
                            <p class="mt-2 text-sm text-stone-600">{{ $planData['booking_limit'] }}</p>
                            <a href="{{ route('landing.pricing') }}" class="inline-flex mt-4 px-4 py-2 rounded-xl bg-stone-800 text-white hover:bg-black transition">
                                Upgrade Plan
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
