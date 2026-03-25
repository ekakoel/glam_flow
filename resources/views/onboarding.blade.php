<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Welcome Setup</h2>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl shadow-md border border-rose-100 p-6 md:p-8 space-y-6">
                <div>
                    <h3 class="text-3xl font-bold text-stone-900">Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="mt-2 text-stone-600 text-base">You are ready to run your MUA business. Let's complete the simple setup below.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-100">
                        <p class="text-sm text-stone-500">Current Plan</p>
                        <p class="text-2xl font-bold text-rose-700 uppercase">{{ $plan }}</p>
                        <p class="text-sm text-stone-600 mt-1">{{ $planBenefit }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-100">
                        <p class="text-sm text-stone-500">Trial Expiry Date</p>
                        <p class="text-xl font-bold text-stone-900">
                            {{ $trialExpiry?->format('d M Y') ?? 'No expiry' }}
                        </p>
                        @if(!is_null($trialDaysLeft))
                            <p class="text-sm text-amber-700 mt-1">{{ $trialDaysLeft }} day(s) remaining</p>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-semibold text-stone-900">Getting Started</h4>
                    <ul class="mt-3 space-y-2 text-stone-700">
                        <li class="flex items-center gap-3">
                            <span class="w-4 h-4 rounded border border-stone-300 {{ $servicesCount > 0 ? 'bg-emerald-400 border-emerald-400' : 'bg-white' }}"></span>
                            Add your first service
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-4 h-4 rounded border border-stone-300 {{ $bookingsCount > 0 ? 'bg-emerald-400 border-emerald-400' : 'bg-white' }}"></span>
                            Create your first booking
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-4 h-4 rounded border border-stone-300 bg-white"></span>
                            View your schedule
                        </li>
                    </ul>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="{{ route('admin.services.create') }}" class="px-6 py-3 rounded-xl bg-rose-500 text-white hover:bg-rose-600 transition">
                        Add Service
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-50 transition">
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
