<section>
    <header>
        <h3 class="text-xl font-semibold text-stone-900">Informasi Akun & Studio</h3>
        <p class="mt-1 text-sm leading-6 text-stone-600">
            Lengkapi informasi tenant agar alur booking, onboarding, dan komunikasi pelanggan tetap konsisten.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="name" class="text-sm font-semibold text-stone-700">Nama Lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="field-control touch-target">
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>
            <div>
                <label for="email" class="text-sm font-semibold text-stone-700">Email Login</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="field-control touch-target">
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="mt-2 text-xs text-amber-700">
                        Email belum terverifikasi.
                        <button form="send-verification" class="font-semibold underline underline-offset-2 hover:text-amber-800">
                            Kirim ulang verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-xs font-semibold text-emerald-700">
                            Link verifikasi baru telah dikirim.
                        </p>
                    @endif
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
            <p class="text-sm font-semibold text-stone-800">Informasi Studio</p>
            <p class="mt-1 text-xs leading-5 text-stone-600">
                Data studio digunakan pada booking publik saat pelanggan memilih lokasi layanan di studio.
            </p>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label for="studio_name" class="text-sm font-semibold text-stone-700">Nama Studio</label>
                    <input id="studio_name" name="studio_name" type="text" value="{{ old('studio_name', $user->studio_name) }}" class="field-control touch-target">
                    <x-input-error class="mt-2" :messages="$errors->get('studio_name')" />
                </div>
                <div>
                    <label for="studio_location" class="text-sm font-semibold text-stone-700">Alamat Studio</label>
                    <input id="studio_location" name="studio_location" type="text" value="{{ old('studio_location', $user->studio_location) }}" class="field-control touch-target">
                    <x-input-error class="mt-2" :messages="$errors->get('studio_location')" />
                </div>
                <div class="md:col-span-2">
                    <label for="studio_maps_link" class="text-sm font-semibold text-stone-700">Link Google Maps Studio (opsional)</label>
                    <input id="studio_maps_link" name="studio_maps_link" type="text" value="{{ old('studio_maps_link', $user->studio_maps_link) }}" class="field-control touch-target" placeholder="https://maps.google.com/...">
                    <x-input-error class="mt-2" :messages="$errors->get('studio_maps_link')" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-sm font-semibold text-stone-800">Logo</p>
            <p class="mt-1 text-xs leading-5 text-stone-600">
                Logo ini dipakai untuk foto profil tenant dan header invoice PDF.
            </p>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start">
                <div class="h-20 w-20 overflow-hidden rounded-2xl border border-stone-200 bg-stone-50">
                    @if($user->logoUrl())
                        <img src="{{ $user->logoUrl() }}" alt="Logo" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-stone-400">
                            {{ strtoupper(substr((string) $user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <label for="logo" class="text-sm font-semibold text-stone-700">Upload Logo (JPG, PNG, WEBP, maks 2MB)</label>
                    <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-1 block w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-stone-700 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-stone-700 hover:file:bg-stone-200">
                    <x-input-error class="mt-2" :messages="$errors->get('logo')" />

                    <input type="hidden" name="remove_logo" value="0">
                    <label for="remove_logo" class="mt-3 inline-flex items-center gap-2 text-xs font-medium text-stone-600">
                        <input id="remove_logo" name="remove_logo" type="checkbox" value="1" class="rounded border-stone-300 text-rose-600 focus:ring-rose-400" @checked(old('remove_logo', false))>
                        Hapus logo saat simpan
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
            <p class="text-sm font-semibold text-stone-800">Informasi Pembayaran</p>
            <p class="mt-1 text-xs leading-5 text-stone-600">
                Simpan lebih dari satu rekening agar customer punya opsi pembayaran.
            </p>
            @php
                $oldAccounts = old('payment_accounts');
                $existingAccounts = $user->paymentAccounts ?? collect();
                $accounts = is_array($oldAccounts)
                    ? collect($oldAccounts)->values()
                    : $existingAccounts->map(fn($account) => [
                        'bank_name' => $account->bank_name,
                        'account_number' => $account->account_number,
                        'account_name' => $account->account_name,
                        'contact' => $account->contact,
                        'notes' => $account->notes,
                    ])->values();

                $minRows = max(3, $accounts->count() > 0 ? $accounts->count() : 0);
                $totalRows = min(5, $minRows);
                if ($accounts->count() < $totalRows) {
                    $accounts = $accounts->concat(collect(range(1, $totalRows - $accounts->count()))->map(fn () => [
                        'bank_name' => '',
                        'account_number' => '',
                        'account_name' => '',
                        'contact' => '',
                        'notes' => '',
                    ]));
                }

                $primaryIndex = old('primary_account_index');
                if ($primaryIndex === null) {
                    $primaryIndex = $existingAccounts->search(fn ($item) => (bool) ($item->is_primary ?? false));
                    $primaryIndex = $primaryIndex === false ? 0 : $primaryIndex;
                }
            @endphp

            <div class="mt-4 space-y-3">
                @foreach($accounts as $index => $account)
                    <div class="rounded-xl border border-stone-200 bg-white p-3">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Rekening {{ $index + 1 }}</p>
                            <label class="inline-flex items-center gap-2 text-xs font-medium text-stone-600">
                                <input
                                    type="radio"
                                    name="primary_account_index"
                                    value="{{ $index }}"
                                    class="border-stone-300 text-rose-600 focus:ring-rose-400"
                                    @checked((string) $primaryIndex === (string) $index)
                                >
                                Utama
                            </label>
                        </div>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-stone-700">Nama Bank</label>
                                <input type="text" name="payment_accounts[{{ $index }}][bank_name]" value="{{ $account['bank_name'] ?? '' }}" class="field-control touch-target" placeholder="Contoh: BCA">
                                <x-input-error class="mt-1" :messages="$errors->get('payment_accounts.'.$index.'.bank_name')" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-stone-700">No. Rekening</label>
                                <input type="text" name="payment_accounts[{{ $index }}][account_number]" value="{{ $account['account_number'] ?? '' }}" class="field-control touch-target" placeholder="Contoh: 1234567890">
                                <x-input-error class="mt-1" :messages="$errors->get('payment_accounts.'.$index.'.account_number')" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-stone-700">Nama Pemilik Rekening</label>
                                <input type="text" name="payment_accounts[{{ $index }}][account_name]" value="{{ $account['account_name'] ?? '' }}" class="field-control touch-target" placeholder="Contoh: Yunni Makeup Studio">
                                <x-input-error class="mt-1" :messages="$errors->get('payment_accounts.'.$index.'.account_name')" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-stone-700">Kontak Konfirmasi</label>
                                <input type="text" name="payment_accounts[{{ $index }}][contact]" value="{{ $account['contact'] ?? '' }}" class="field-control touch-target" placeholder="Contoh: +62 812-xxxx-xxxx">
                                <x-input-error class="mt-1" :messages="$errors->get('payment_accounts.'.$index.'.contact')" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-stone-700">Catatan Rekening (opsional)</label>
                                <input type="text" name="payment_accounts[{{ $index }}][notes]" value="{{ $account['notes'] ?? '' }}" class="field-control touch-target" placeholder="Contoh: Prioritas untuk transfer antarbank">
                                <x-input-error class="mt-1" :messages="$errors->get('payment_accounts.'.$index.'.notes')" />
                            </div>
                        </div>
                    </div>
                @endforeach
                <x-input-error class="mt-1" :messages="$errors->get('payment_accounts')" />
                <x-input-error class="mt-1" :messages="$errors->get('primary_account_index')" />
            </div>

            <div class="mt-4">
                <label for="payment_instructions" class="text-sm font-semibold text-stone-700">Instruksi Pembayaran Tambahan</label>
                <textarea id="payment_instructions" name="payment_instructions" rows="3" class="field-control touch-target">{{ old('payment_instructions', $user->payment_instructions) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('payment_instructions')" />
            </div>
        </div>

        <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4">
            <input type="hidden" name="notify_tomorrow_booking" value="0">
            <label for="notify_tomorrow_booking" class="flex items-start gap-3">
                <input
                    id="notify_tomorrow_booking"
                    name="notify_tomorrow_booking"
                    type="checkbox"
                    value="1"
                    class="mt-1 rounded border-stone-300 text-rose-600 focus:ring-rose-400"
                    @checked(old('notify_tomorrow_booking', $user->notify_tomorrow_booking ?? true))
                >
                <span>
                    <span class="block text-sm font-semibold text-stone-800">Notifikasi booking besok</span>
                    <span class="mt-1 block text-xs leading-5 text-stone-600">
                        Menampilkan badge kalender dan mengaktifkan reminder otomatis H-1 via email/WhatsApp.
                    </span>
                </span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('notify_tomorrow_booking')" />
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <button type="submit" class="touch-target rounded-xl bg-rose-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-600">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2400)"
                    class="text-sm font-medium text-emerald-700"
                >
                    Profil berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>
