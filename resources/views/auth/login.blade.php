<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900 sm:text-3xl">Selamat Datang</h1>
        <p class="mt-1 text-sm leading-6 text-stone-600">Masuk untuk mengelola booking, pelanggan, dan jadwal makeup Anda dengan lebih cepat.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="js-auth-form space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-stone-700" />
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email" placeholder="nama@email.com"
                class="field-control touch-target">
            <p class="mt-1 text-xs text-stone-500">Gunakan email yang terdaftar pada akun Anda.</p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-stone-700" />
            <div class="relative mt-1">
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    class="field-control touch-target pr-14">
                <button type="button" data-toggle-password="password"
                    class="absolute inset-y-0 right-0 px-4 text-xs font-semibold tracking-wide text-stone-500 hover:text-stone-700">
                    TAMPILKAN
                </button>
            </div>
            <p id="caps-lock-warning" class="mt-1 hidden text-xs font-medium text-amber-700">Caps Lock aktif.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-stone-300 text-rose-600 focus:ring-rose-400">
                <span class="text-sm text-stone-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-rose-600 hover:text-rose-700" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" data-loading-target class="w-full rounded-xl bg-rose-500 py-3.5 text-sm font-semibold text-white shadow-md hover:bg-rose-600 transition">
            Masuk
        </button>

        <p class="text-center text-sm text-stone-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-rose-600 hover:text-rose-700">Daftar sekarang</a>
        </p>
    </form>

    <div class="mt-5 rounded-xl border border-rose-100 bg-rose-50/60 p-3 text-xs text-stone-600">
        Tip: gunakan email aktif agar update booking dan pembayaran lebih mudah dipantau.
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const capsLockWarning = document.getElementById('caps-lock-warning');

            if (passwordInput && capsLockWarning) {
                const updateCapsLockState = (event) => {
                    const isOn = event.getModifierState && event.getModifierState('CapsLock');
                    capsLockWarning.classList.toggle('hidden', !isOn);
                };
                passwordInput.addEventListener('keydown', updateCapsLockState);
                passwordInput.addEventListener('keyup', updateCapsLockState);
                passwordInput.addEventListener('blur', () => capsLockWarning.classList.add('hidden'));
            }

            document.querySelectorAll('[data-toggle-password]').forEach((button) => {
                button.addEventListener('click', () => {
                    const target = document.getElementById(button.getAttribute('data-toggle-password'));
                    if (!target) return;
                    const nextType = target.type === 'password' ? 'text' : 'password';
                    target.type = nextType;
                    button.textContent = nextType === 'password' ? 'TAMPILKAN' : 'SEMBUNYIKAN';
                });
            });

            document.querySelectorAll('.js-auth-form').forEach((form) => {
                form.addEventListener('submit', () => {
                    const submit = form.querySelector('[data-loading-target]');
                    if (!submit) return;
                    submit.disabled = true;
                    submit.textContent = 'Masuk...';
                    submit.classList.add('opacity-70', 'cursor-not-allowed');
                });
            });
        });
    </script>
</x-guest-layout>
