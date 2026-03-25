<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Payments</h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl shadow border border-rose-100 overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Service</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-stone-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-rose-50/30">
                                    <td class="px-4 py-3 text-sm text-stone-800">{{ $payment->booking->customer->name }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-800">{{ $payment->booking->service->name }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-stone-900">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $statusClass = match($payment->status) {
                                                'paid' => 'bg-emerald-100 text-emerald-700',
                                                'failed' => 'bg-red-100 text-red-700',
                                                default => 'bg-yellow-100 text-yellow-700'
                                            };
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if($payment->status !== 'paid')
                                                <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm hover:bg-emerald-600">
                                                        Mark as Paid
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="failed">
                                                <input type="hidden" name="payment_method" value="{{ $payment->payment_method }}">
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white text-sm hover:bg-red-600">
                                                    Mark Failed
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-stone-500">No payment records yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden p-4 space-y-3">
                    @forelse($payments as $payment)
                        @php
                            $statusClass = match($payment->status) {
                                'paid' => 'bg-emerald-100 text-emerald-700',
                                'failed' => 'bg-red-100 text-red-700',
                                default => 'bg-yellow-100 text-yellow-700'
                            };
                        @endphp
                        <div class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 shadow-sm">
                            <p class="text-sm font-semibold text-stone-900">{{ $payment->booking->customer->name }}</p>
                            <p class="text-sm text-stone-700 mt-1">{{ $payment->booking->service->name }}</p>
                            <p class="text-sm font-semibold text-stone-900 mt-1">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                            <p class="text-sm text-stone-600 mt-1">Method: {{ ucfirst($payment->payment_method) }}</p>
                            <p class="text-sm mt-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($payment->status !== 'paid')
                                    <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm hover:bg-emerald-600">
                                            Mark as Paid
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="failed">
                                    <input type="hidden" name="payment_method" value="{{ $payment->payment_method }}">
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white text-sm hover:bg-red-600">
                                        Mark Failed
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50 p-4 text-sm text-stone-500">
                            No payment records yet.
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
