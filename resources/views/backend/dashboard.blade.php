<x-backend-layout>
    <section class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-900">Ringkasan Sistem</h2>
            <p class="mt-1 text-sm text-stone-600">Panel ini hanya untuk super admin dan tidak ditampilkan di navigasi frontend tenant.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-stone-500">Total Tenant</p>
                <p class="mt-2 text-3xl font-bold text-stone-900">{{ number_format($totalTenants) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 shadow-sm">
                <p class="text-sm font-medium text-emerald-700">Subscriber Aktif (Pro/Premium)</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($activeSubscribers) }}</p>
            </div>
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-stone-500">Subscriber Free</p>
                <p class="mt-2 text-3xl font-bold text-stone-900">{{ number_format($freeSubscribers) }}</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-stone-900">Distribusi Plan Subscriber</h3>
                <div class="mt-4 space-y-3">
                    @forelse($planBreakdown as $row)
                        <div class="flex items-center justify-between rounded-xl border border-stone-200 px-4 py-3">
                            <span class="text-sm font-medium uppercase text-stone-700">{{ $row->plan }}</span>
                            <span class="text-sm font-semibold text-stone-900">{{ number_format((int) $row->total) }} tenant</span>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Belum ada data subscriber.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-stone-900">Quick Actions</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('backend.tenants.index') }}" class="block rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700 hover:bg-stone-100">
                        Kelola Subscriber Tenant
                    </a>
                    <a href="{{ route('backend.plans.index') }}" class="block rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700 hover:bg-stone-100">
                        Kelola Paket Harga
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-backend-layout>

