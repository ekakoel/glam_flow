<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Tautan Booking</h2>
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h3 class="text-lg font-semibold text-stone-900">Buat Form Booking Publik</h3>
                <p class="text-sm text-stone-600 mt-1">Tautan akan kedaluwarsa otomatis setelah 2 x 24 jam.</p>
                @php
                    $defaultTermsTitle = trim((string) ($tenant?->booking_terms_title ?? '')) ?: 'Syarat & Ketentuan Booking';
                    $defaultTermsContent = trim((string) ($tenant?->booking_terms_content ?? ''));
                    if ($defaultTermsContent === '') {
                        $defaultTermsContent = "1. Booking dianggap valid setelah DP diterima.\n2. Jadwal dapat diubah maksimal H-2 sebelum hari layanan.\n3. DP yang sudah dibayar tidak dapat direfund jika booking dibatalkan customer.";
                    }
                @endphp
                <form action="{{ route('admin.booking-links.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Layanan yang diizinkan</label>
                        <select name="service_ids[]" multiple size="5" class="mt-1 w-full rounded-xl border-stone-300 focus:border-rose-400 focus:ring-rose-300">
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" @selected(collect(old('service_ids', []))->contains($service->id))>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_ids') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        @error('service_ids.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700">Maksimal pengiriman (opsional)</label>
                        <input type="number" min="1" max="500" name="max_submissions" value="{{ old('max_submissions') }}" class="mt-1 w-full rounded-xl border-stone-300 focus:border-rose-400 focus:ring-rose-300">
                        @error('max_submissions') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700">Judul T&C</label>
                        <input
                            type="text"
                            name="terms_title"
                            value="{{ old('terms_title', $defaultTermsTitle) }}"
                            required
                            class="mt-1 w-full rounded-xl border-stone-300 focus:border-rose-400 focus:ring-rose-300"
                        >
                        @error('terms_title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700">Isi T&C (wajib)</label>
                        <textarea
                            name="terms_content"
                            rows="6"
                            required
                            class="mt-1 w-full rounded-xl border-stone-300 focus:border-rose-400 focus:ring-rose-300"
                            placeholder="Contoh: DP tidak dapat dikembalikan jika ada pembatalan mendadak..."
                        >{{ old('terms_content', $defaultTermsContent) }}</textarea>
                        @error('terms_content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-500 text-white hover:bg-rose-600 transition">
                        Buat
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($forms as $form)
                    <div class="bg-white rounded-2xl border border-rose-100 shadow p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase text-stone-500">Token</p>
                                <p class="text-sm font-medium text-stone-800 break-all">{{ $form->token }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $form->isAccessible() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $form->isAccessible() ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm text-stone-600">Berakhir: {{ $form->expires_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-stone-600">Jumlah pengiriman: {{ $form->submission_count }}{{ $form->max_submissions ? ' / '.$form->max_submissions : '' }}</p>
                        @php
                            $terms = $form->settings['terms'] ?? [];
                            $termsTitle = trim((string) ($terms['title'] ?? ''));
                        @endphp
                        @if($termsTitle !== '')
                            <p class="text-sm text-stone-600">
                                T&C: {{ $termsTitle }}
                            </p>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('public.booking.show', $form->token) }}" target="_blank" class="px-4 py-2 rounded-xl bg-stone-800 text-white text-sm hover:bg-black">
                                Buka
                            </a>
                                <button type="button" class="px-4 py-2 rounded-xl border border-stone-300 text-stone-700 text-sm hover:bg-stone-50"
                                onclick="navigator.clipboard.writeText('{{ route('public.booking.show', $form->token) }}').then(() => alert('Tautan berhasil disalin'))">
                                Salin
                            </button>
                            <form method="POST" action="{{ route('admin.booking-links.extend', $form) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm hover:bg-amber-600">
                                    +48 Jam
                                </button>
                            </form>
                            @if($form->is_active)
                                <form method="POST" action="{{ route('admin.booking-links.deactivate', $form) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white text-sm hover:bg-red-600">
                                        Nonaktifkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-dashed border-stone-300 p-6 text-stone-600">
                        Belum ada tautan booking. Buat tautan pertama Anda.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

