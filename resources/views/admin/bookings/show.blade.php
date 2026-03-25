<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Booking Detail</h2>
            <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h3 class="text-lg font-semibold text-stone-900">Booking Information</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200">
                        <p class="text-stone-500">Customer</p>
                        <p class="mt-1 font-semibold text-stone-800">{{ $booking->customer->name }}</p>
                        <p class="text-stone-600">{{ $booking->customer->phone }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200">
                        <p class="text-stone-500">Services</p>
                        @if($booking->bookingItems->isNotEmpty())
                            <div class="mt-1 space-y-1">
                                @foreach($booking->bookingItems as $item)
                                    <p class="font-semibold text-stone-800">
                                        {{ $item->service->name }} x {{ $item->people_count }} person
                                        <span class="font-normal text-stone-600">
                                            (Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }})
                                        </span>
                                    </p>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-1 font-semibold text-stone-800">{{ $booking->service->name }}</p>
                            <p class="text-stone-600">Rp {{ number_format((float) $booking->service->price, 0, ',', '.') }}</p>
                        @endif
                        <p class="mt-2 text-stone-600">Total people: {{ $booking->total_people ?? 1 }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200">
                        <p class="text-stone-500">Schedule</p>
                        <p class="mt-1 font-semibold text-stone-800">{{ $booking->booking_date?->format('d M Y') }}</p>
                        <p class="text-stone-600">{{ substr((string) $booking->booking_time, 0, 5) }} - {{ substr((string) $booking->end_time, 0, 5) }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200">
                        <p class="text-stone-500">Payment</p>
                        <p class="mt-1 font-semibold text-stone-800">{{ ucfirst($booking->payment?->status ?? 'pending') }}</p>
                        <p class="text-stone-600">{{ ucfirst($booking->payment?->payment_method ?? 'manual') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.bookings.invoice', $booking) }}" class="px-6 py-2.5 rounded-xl bg-amber-500 text-white hover:bg-amber-600">
                        Download Invoice
                    </a>
                    <form method="POST" action="{{ route('admin.bookings.pay-now', $booking) }}">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-500 text-white hover:bg-rose-600">
                            Pay Now
                        </button>
                    </form>
                    <a href="{{ route('admin.payments.index') }}" class="px-6 py-2.5 rounded-xl bg-emerald-500 text-white hover:bg-emerald-600">
                        Open Payments
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
