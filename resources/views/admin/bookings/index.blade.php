<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Bookings</h2>
            <a href="{{ route('admin.bookings.create') }}" class="px-5 py-2.5 bg-rose-500 text-white rounded-xl hover:bg-rose-600">
                Add Booking
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-100 text-emerald-700 border border-emerald-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow rounded-2xl border border-rose-100 overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($bookings as $booking)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $booking->customer->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $booking->service->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $booking->booking_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ substr((string) $booking->booking_time, 0, 5) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ substr((string) $booking->end_time, 0, 5) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="px-3 py-1.5 text-sm bg-stone-600 text-white rounded-lg hover:bg-stone-700">Detail</a>
                                            <a href="{{ route('admin.bookings.invoice', $booking) }}" class="px-3 py-1.5 text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600">Invoice</a>
                                            <form method="POST" action="{{ route('admin.bookings.pay-now', $booking) }}">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 text-sm bg-rose-500 text-white rounded-lg hover:bg-rose-600">Pay Now</button>
                                            </form>
                                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="px-3 py-1.5 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600">Edit</a>
                                            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Delete this booking?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">No bookings yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden p-4 space-y-3">
                    @forelse ($bookings as $booking)
                        <div class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 shadow-sm">
                            <p class="text-sm font-semibold text-stone-900">{{ $booking->customer->name }}</p>
                            <p class="text-sm text-stone-700 mt-1">{{ $booking->service->name }}</p>
                            <p class="text-sm text-stone-600 mt-1">
                                {{ $booking->booking_date?->format('d M Y') }} • {{ substr((string) $booking->booking_time, 0, 5) }} - {{ substr((string) $booking->end_time, 0, 5) }}
                            </p>
                            <p class="text-sm mt-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="px-3 py-1.5 text-sm bg-stone-600 text-white rounded-lg hover:bg-stone-700">Detail</a>
                                <a href="{{ route('admin.bookings.invoice', $booking) }}" class="px-3 py-1.5 text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600">Invoice</a>
                                <form method="POST" action="{{ route('admin.bookings.pay-now', $booking) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-sm bg-rose-500 text-white rounded-lg hover:bg-rose-600">Pay Now</button>
                                </form>
                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="px-3 py-1.5 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600">Edit</a>
                                <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Delete this booking?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50 p-4 text-sm text-stone-500">
                            No bookings yet.
                        </div>
                    @endforelse
                </div>
                <div class="p-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
