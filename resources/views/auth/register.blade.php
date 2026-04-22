<x-guest-layout>
    @php
        $plan = request('plan', $selectedPlan ?? 'free');
        $planConfig = (array) ($availablePlans[$plan] ?? []);
        $planLabel = strtoupper((string) ($planConfig['name'] ?? $plan));
        $bookingLimit = $planConfig['booking_limit_total'] ?? null;
        $planLimitLabel = (string) ($bookingLimit === null
            ? 'Booking tanpa batas'
            : 'Maksimal '.(int) $bookingLimit.' booking/bulan');
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Buat Akun MUA</h1>
        <p class="mt-1 text-sm text-stone-600">Mulai kelola layanan, booking, pembayaran, dan jadwal Anda dalam satu sistem.</p>
    </div>

    <form method="POST" action="{{ route('register', ['plan' => $plan]) }}" class="js-auth-form space-y-4">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan }}">

        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
            <p>Paket terpilih: <span class="font-semibold uppercase">{{ $planLabel }}</span></p>
            <p class="mt-1 text-xs text-rose-700/90">{{ $planLimitLabel }}</p>
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama')" class="text-stone-700" />
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="mt-1 block w-full rounded-xl border-stone-300 bg-white text-stone-900 placeholder:text-stone-400 focus:border-rose-400 focus:ring-rose-300">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-stone-700" />
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                class="mt-1 block w-full rounded-xl border-stone-300 bg-white text-stone-900 placeholder:text-stone-400 focus:border-rose-400 focus:ring-rose-300">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-stone-700" />
            <div class="relative mt-1">
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    class="block w-full rounded-xl border-stone-300 bg-white text-stone-900 pr-12 focus:border-rose-400 focus:ring-rose-300">
                <button type="button" data-toggle-password="password"
                    class="absolute inset-y-0 right-0 px-4 text-xs font-semibold tracking-wide text-stone-500 hover:text-stone-700">
                    SHOW
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-stone-700" />
            <div class="relative mt-1">
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                    class="block w-full rounded-xl border-stone-300 bg-white text-stone-900 pr-12 focus:border-rose-400 focus:ring-rose-300">
                <button type="button" data-toggle-password="password_confirmation"
                    class="absolute inset-y-0 right-0 px-4 text-xs font-semibold tracking-wide text-stone-500 hover:text-stone-700">
                    SHOW
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" data-loading-target class="w-full rounded-xl bg-rose-500 py-3 text-sm font-semibold text-white shadow-md hover:bg-rose-600 transition">
            Buat Akun
        </button>

        <p class="text-center text-sm text-stone-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-rose-600 hover:text-rose-700">Masuk di sini</a>
        </p>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-toggle-password]').forEach((button) => {
                button.addEventListener('click', () => {
                    const target = document.getElementById(button.getAttribute('data-toggle-password'));
                    if (!target) return;
                    const nextType = target.type === 'password' ? 'text' : 'password';
                    target.type = nextType;
                    button.textContent = nextType === 'password' ? 'SHOW' : 'HIDE';
                });
            });

            document.querySelectorAll('.js-auth-form').forEach((form) => {
                form.addEventListener('submit', () => {
                    const submit = form.querySelector('[data-loading-target]');
                    if (!submit) return;
                    submit.disabled = true;
                    submit.textContent = 'Mendaftar...';
                    submit.classList.add('opacity-70', 'cursor-not-allowed');
                });
            });
        });
    </script>
</x-guest-layout>

