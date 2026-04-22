<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Pembayaran</h2>
            @if(!empty($bookingId))
                <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 rounded-xl border border-stone-300 text-sm text-stone-700 hover:bg-stone-50">
                    Semua
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200">{{ session('success') }}</div>
            @endif
            @if ($errors->has('payment'))
                <div class="p-4 rounded-xl bg-red-100 text-red-700 border border-red-200">{{ $errors->first('payment') }}</div>
            @endif
            @if(!empty($bookingId))
                <div class="p-4 rounded-xl bg-rose-100 text-rose-800 border border-rose-200 text-sm">
                    Menampilkan pembayaran untuk booking #{{ $bookingId }}.
                </div>
            @endif
            <div class="p-3 rounded-xl bg-stone-100 text-stone-700 border border-stone-200 text-xs">
                Nominal <span class="font-semibold">Diskon</span> dan <span class="font-semibold">DP</span> bisa diinput manual sesuai kesepakatan dengan customer.
            </div>

            <div class="bg-white rounded-2xl shadow border border-rose-100 overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Booking</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Pelanggan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Layanan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Diskon</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">DP</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Terbayar</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Sisa</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-stone-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($payments as $payment)
                                @php
                                    $total = (float) $payment->amount;
                                    $rawSubtotal = (float) $payment->booking->bookingItems->sum('subtotal');
                                    $discount = max(0, (float) $payment->discount_amount);
                                    $subtotal = $rawSubtotal > 0 ? $rawSubtotal : ($total + $discount);
                                    if ($subtotal <= 0) {
                                        $subtotal = $total;
                                    }
                                    $dp = (float) $payment->dp_amount;
                                    $paid = (float) $payment->paid_amount;
                                    $remaining = max(0, $total - $paid);
                                    $isDpPaid = $payment->isDpPaid();
                                    $isSettled = $payment->isSettled();
                                    $canSettle = $isDpPaid
                                        && ! $isSettled
                                        && $payment->booking->status !== \App\Models\Booking::STATUS_CANCELED
                                        && ! $payment->booking->hasServicePassed();
                                    $canCancelBooking = ! in_array($payment->booking->status, [\App\Models\Booking::STATUS_CANCELED, \App\Models\Booking::STATUS_COMPLETED], true)
                                        && ! $payment->booking->hasServicePassed();
                                    $statusLabel = $isSettled
                                        ? 'Lunas'
                                        : ($isDpPaid ? 'DP Lunas, Menunggu Pelunasan' : 'Menunggu DP');
                                    $statusClass = $isSettled
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : ($isDpPaid ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700');
                                @endphp
                                <tr class="hover:bg-rose-50/30">
                                    <td class="px-4 py-3 text-sm text-stone-700">#{{ $payment->booking_id }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-800">{{ $payment->booking->customer->name }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-800">{{ $payment->booking->service->name }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-rose-700">Rp {{ number_format($discount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-stone-900">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">Rp {{ number_format($dp, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold {{ $remaining > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                        <p class="mt-1 text-xs text-stone-500">Metode: {{ ucfirst($payment->payment_method) }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.bookings.show', $payment->booking_id) }}" class="px-4 py-2 rounded-xl border border-stone-300 text-stone-700 text-sm hover:bg-stone-50">
                                                Detail
                                            </a>
                                            @if(! $isSettled && $payment->booking->status !== \App\Models\Booking::STATUS_CANCELED && ! $payment->booking->hasServicePassed())
                                                <form method="POST" action="{{ route('admin.payments.update-pricing', $payment) }}" class="inline-flex items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input
                                                        type="number"
                                                        name="discount_amount"
                                                        min="0"
                                                        max="{{ (int) max(0, floor($subtotal)) }}"
                                                        step="1000"
                                                        value="{{ (int) round($discount) }}"
                                                        class="w-24 rounded-lg border-stone-300 px-2 py-1.5 text-xs text-stone-700 focus:border-rose-400 focus:ring-rose-300"
                                                        aria-label="Nominal Diskon"
                                                    >
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-stone-700 text-white text-sm hover:bg-stone-800">
                                                        Diskon
                                                    </button>
                                                </form>
                                            @endif
                                            @if(! $isDpPaid)
                                                <form method="POST" action="{{ route('admin.payments.mark-dp-paid', $payment) }}" class="inline-flex items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input
                                                        type="number"
                                                        name="dp_amount"
                                                        min="1"
                                                        max="{{ (int) max(1, floor($total)) }}"
                                                        step="1000"
                                                        value="{{ (int) round($dp) }}"
                                                        class="w-24 rounded-lg border-stone-300 px-2 py-1.5 text-xs text-stone-700 focus:border-rose-400 focus:ring-rose-300"
                                                        aria-label="Nominal DP"
                                                    >
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm hover:bg-amber-600">
                                                        Bayar DP
                                                    </button>
                                                </form>
                                            @endif
                                            @if($isSettled)
                                                <span class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 text-sm font-semibold">
                                                    Sudah Lunas
                                                </span>
                                            @elseif($canSettle)
                                                <form method="POST" action="{{ route('admin.payments.mark-settled', $payment) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm hover:bg-emerald-600">
                                                        Pelunasan
                                                    </button>
                                                </form>
                                            @else
                                                <span class="px-4 py-2 rounded-xl bg-stone-100 text-stone-500 text-xs font-semibold">
                                                    Tidak aktif
                                                </span>
                                            @endif
                                            @if($canCancelBooking)
                                                <form method="POST" action="{{ route('admin.payments.cancel-booking', $payment) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white text-sm hover:bg-red-600">
                                                        Batal
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" disabled class="px-4 py-2 rounded-xl bg-stone-100 text-stone-400 text-sm cursor-not-allowed">
                                                    Batal
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada data pembayaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden p-4 space-y-3">
                    @forelse($payments as $payment)
                        @php
                            $total = (float) $payment->amount;
                            $rawSubtotal = (float) $payment->booking->bookingItems->sum('subtotal');
                            $discount = max(0, (float) $payment->discount_amount);
                            $subtotal = $rawSubtotal > 0 ? $rawSubtotal : ($total + $discount);
                            if ($subtotal <= 0) {
                                $subtotal = $total;
                            }
                            $dp = (float) $payment->dp_amount;
                            $paid = (float) $payment->paid_amount;
                            $remaining = max(0, $total - $paid);
                            $isDpPaid = $payment->isDpPaid();
                            $isSettled = $payment->isSettled();
                            $canSettle = $isDpPaid
                                && ! $isSettled
                                && $payment->booking->status !== \App\Models\Booking::STATUS_CANCELED
                                && ! $payment->booking->hasServicePassed();
                            $canCancelBooking = ! in_array($payment->booking->status, [\App\Models\Booking::STATUS_CANCELED, \App\Models\Booking::STATUS_COMPLETED], true)
                                && ! $payment->booking->hasServicePassed();
                            $statusLabel = $isSettled
                                ? 'Lunas'
                                : ($isDpPaid ? 'DP Lunas, Menunggu Pelunasan' : 'Menunggu DP');
                            $statusClass = $isSettled
                                ? 'bg-emerald-100 text-emerald-700'
                                : ($isDpPaid ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700');
                        @endphp
                        <div class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 shadow-sm">
                            <p class="text-sm font-semibold text-stone-900">Booking #{{ $payment->booking_id }} - {{ $payment->booking->customer->name }}</p>
                            <p class="text-sm text-stone-700 mt-1">{{ $payment->booking->service->name }}</p>
                            <p class="text-sm text-stone-600 mt-2">Subtotal: Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                            <p class="text-sm text-rose-700">Diskon: Rp {{ number_format($discount, 0, ',', '.') }}</p>
                            <p class="text-sm text-stone-600 mt-2">Total: Rp {{ number_format($total, 0, ',', '.') }}</p>
                            <p class="text-sm text-stone-600">DP: Rp {{ number_format($dp, 0, ',', '.') }}</p>
                            <p class="text-sm text-stone-600">Terbayar: Rp {{ number_format($paid, 0, ',', '.') }}</p>
                            <p class="text-sm font-semibold {{ $remaining > 0 ? 'text-rose-700' : 'text-emerald-700' }}">Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                            <p class="text-sm text-stone-600 mt-1">Metode: {{ ucfirst($payment->payment_method) }}</p>
                            <p class="text-sm mt-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('admin.bookings.show', $payment->booking_id) }}" class="px-4 py-2 rounded-xl border border-stone-300 text-stone-700 text-sm hover:bg-stone-50">
                                    Detail
                                </a>
                                @if(! $isSettled && $payment->booking->status !== \App\Models\Booking::STATUS_CANCELED && ! $payment->booking->hasServicePassed())
                                    <form method="POST" action="{{ route('admin.payments.update-pricing', $payment) }}" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input
                                            type="number"
                                            name="discount_amount"
                                            min="0"
                                            max="{{ (int) max(0, floor($subtotal)) }}"
                                            step="1000"
                                            value="{{ (int) round($discount) }}"
                                            class="w-24 rounded-lg border-stone-300 px-2 py-1.5 text-xs text-stone-700 focus:border-rose-400 focus:ring-rose-300"
                                            aria-label="Nominal Diskon"
                                        >
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-stone-700 text-white text-sm hover:bg-stone-800">
                                            Diskon
                                        </button>
                                    </form>
                                @endif
                                @if(! $isDpPaid)
                                    <form method="POST" action="{{ route('admin.payments.mark-dp-paid', $payment) }}" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input
                                            type="number"
                                            name="dp_amount"
                                            min="1"
                                            max="{{ (int) max(1, floor($total)) }}"
                                            step="1000"
                                            value="{{ (int) round($dp) }}"
                                            class="w-24 rounded-lg border-stone-300 px-2 py-1.5 text-xs text-stone-700 focus:border-rose-400 focus:ring-rose-300"
                                            aria-label="Nominal DP"
                                        >
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm hover:bg-amber-600">
                                            Bayar DP
                                        </button>
                                    </form>
                                @endif
                                @if($isSettled)
                                    <span class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 text-sm font-semibold">
                                        Sudah Lunas
                                    </span>
                                @elseif($canSettle)
                                    <form method="POST" action="{{ route('admin.payments.mark-settled', $payment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm hover:bg-emerald-600">
                                            Pelunasan
                                        </button>
                                    </form>
                                @else
                                    <span class="px-4 py-2 rounded-xl bg-stone-100 text-stone-500 text-xs font-semibold">
                                        Tidak aktif
                                    </span>
                                @endif
                                @if($canCancelBooking)
                                    <form method="POST" action="{{ route('admin.payments.cancel-booking', $payment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white text-sm hover:bg-red-600">
                                            Batal
                                        </button>
                                    </form>
                                @else
                                    <button type="button" disabled class="px-4 py-2 rounded-xl bg-stone-100 text-stone-400 text-sm cursor-not-allowed">
                                        Batal
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50 p-4 text-sm text-stone-500">
                            Belum ada data pembayaran.
                        </div>
                    @endforelse
                </div>

                <div class="p-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

