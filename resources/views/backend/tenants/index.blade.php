<x-backend-layout>
    <section class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-stone-900">Kelola Subscriber Tenant</h2>
                <p class="mt-1 text-sm text-stone-600">Update paket tenant, masa berlaku, dan kuota booking terpakai.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                <form method="GET" action="{{ route('backend.tenants.index') }}" class="flex w-full gap-2 sm:w-auto">
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nama/email tenant"
                        class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm focus:border-stone-500 focus:outline-none focus:ring-0 sm:w-72"
                    >
                    <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Cari</button>
                </form>
                <a href="{{ route('backend.tenants.create') }}" class="rounded-xl border border-stone-300 bg-white px-4 py-2 text-center text-sm font-semibold text-stone-700 hover:bg-stone-50">
                    + Tenant Baru
                </a>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($tenants as $tenant)
                @php
                    $subscription = $tenant->subscription;
                    $currentPlan = $subscription?->plan ?? 'free';
                @endphp
                <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-semibold text-stone-900">{{ $tenant->name }}</h3>
                                @if ($tenant->is_suspended)
                                    <span class="rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700">SUSPENDED</span>
                                @endif
                            </div>
                            <p class="text-sm text-stone-600">{{ $tenant->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-stone-700">
                                <span class="rounded-lg bg-stone-100 px-2 py-1">Role: {{ $tenant->role }}</span>
                                <span class="rounded-lg bg-stone-100 px-2 py-1">Layanan: {{ $tenant->services_count }}</span>
                                <span class="rounded-lg bg-stone-100 px-2 py-1">Pelanggan: {{ $tenant->customers_count }}</span>
                                <span class="rounded-lg bg-stone-100 px-2 py-1">Booking: {{ $tenant->bookings_count }}</span>
                                <span class="rounded-lg bg-stone-100 px-2 py-1">Kuota Terpakai: {{ $subscription?->bookings_consumed_total ?? 0 }}</span>
                            </div>
                            @if ($tenant->is_suspended && $tenant->suspended_reason)
                                <p class="mt-2 text-xs text-rose-700">Alasan suspend: {{ $tenant->suspended_reason }}</p>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('backend.tenants.edit', $tenant) }}" class="rounded-xl border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                                Kelola Detail
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-6 text-sm text-stone-600">
                    Tenant tidak ditemukan.
                </div>
            @endforelse
        </div>

        <div>
            {{ $tenants->links() }}
        </div>
    </section>
</x-backend-layout>
