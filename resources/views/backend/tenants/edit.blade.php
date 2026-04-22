<x-backend-layout>
    <section class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-stone-900">Kelola Tenant: {{ $tenant->name }}</h2>
                <p class="mt-1 text-sm text-stone-600">{{ $tenant->email }}</p>
            </div>
            <a href="{{ route('backend.tenants.index') }}" class="rounded-xl border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                Kembali ke List
            </a>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-stone-900">Informasi Dasar</h3>
                <form method="POST" action="{{ route('backend.tenants.update', $tenant) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <label class="block text-sm font-medium text-stone-700">
                        Nama
                        <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                    </label>
                    <label class="block text-sm font-medium text-stone-700">
                        Email
                        <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                    </label>
                    <label class="block text-sm font-medium text-stone-700">
                        Role
                        <select name="role" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                            @foreach($roles as $role)
                                <option value="{{ $role }}" @selected(old('role', $tenant->role) === $role)>{{ strtoupper($role) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-black">
                        Simpan Informasi Dasar
                    </button>
                </form>
            </article>

            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-stone-900">Subscription</h3>
                <form method="POST" action="{{ route('backend.tenants.subscription.update', $tenant) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm font-medium text-stone-700">
                        Plan
                        <select name="plan" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                            @foreach($plans as $plan)
                                <option value="{{ $plan }}" @selected(old('plan', $subscription->plan) === $plan)>{{ strtoupper($plan) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-stone-700">
                        Expired At
                        <input type="date" name="expired_at" value="{{ old('expired_at', optional($subscription->expired_at)->toDateString()) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                    </label>
                    <label class="block text-sm font-medium text-stone-700">
                        Bookings Consumed Total
                        <input type="number" min="0" name="bookings_consumed_total" value="{{ old('bookings_consumed_total', (int) $subscription->bookings_consumed_total) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                    </label>
                    <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-black">
                        Simpan Subscription
                    </button>
                </form>
            </article>

            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-stone-900">Suspend Tenant</h3>
                @if($tenant->is_suspended)
                    <p class="mt-2 text-sm text-rose-700">Tenant sedang disuspend sejak {{ optional($tenant->suspended_at)->format('d M Y H:i') }}.</p>
                @else
                    <p class="mt-2 text-sm text-stone-600">Tenant saat ini aktif.</p>
                @endif
                <form method="POST" action="{{ route('backend.tenants.suspend.update', $tenant) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_suspended" value="{{ $tenant->is_suspended ? '0' : '1' }}">
                    @if(! $tenant->is_suspended)
                        <label class="block text-sm font-medium text-stone-700">
                            Alasan Suspend
                            <input type="text" name="suspended_reason" value="{{ old('suspended_reason') }}" placeholder="Contoh: pelanggaran kebijakan" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                        </label>
                    @endif
                    <button type="submit" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-white {{ $tenant->is_suspended ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }}">
                        {{ $tenant->is_suspended ? 'Aktifkan Kembali Tenant' : 'Suspend Tenant' }}
                    </button>
                </form>
            </article>

            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-stone-900">Reset Password</h3>
                <p class="mt-2 text-sm text-stone-600">Kosongkan password untuk generate password acak otomatis.</p>
                <form method="POST" action="{{ route('backend.tenants.password-reset', $tenant) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm font-medium text-stone-700">
                        Password Baru (opsional)
                        <input type="password" name="password" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                    </label>
                    <label class="block text-sm font-medium text-stone-700">
                        Konfirmasi Password Baru
                        <input type="password" name="password_confirmation" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                    </label>
                    <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-black">
                        Reset Password
                    </button>
                </form>
            </article>
        </div>

        <article class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-rose-700">Zona Berbahaya</h3>
            <p class="mt-1 text-sm text-rose-700">Hapus tenant akan menghapus seluruh data tenant secara permanen.</p>
            <form method="POST" action="{{ route('backend.tenants.destroy', $tenant) }}" class="mt-4">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    onclick="return confirm('Yakin ingin menghapus tenant ini beserta seluruh datanya?')"
                    class="rounded-xl border border-rose-300 bg-white px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100"
                >
                    Hapus Tenant Permanen
                </button>
            </form>
        </article>
    </section>
</x-backend-layout>
