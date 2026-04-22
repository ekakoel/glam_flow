<x-backend-layout>
    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-semibold text-stone-900">Kelola Paket Harga</h2>
            <p class="mt-1 text-sm text-stone-600">Perubahan di sini akan meng-override `config/plans.php` pada runtime aplikasi.</p>
        </div>

        <div class="grid gap-4">
            @foreach ($plans as $plan)
                @php
                    $key = $plan['key'];
                    $effective = $plan['effective'];
                    $override = $plan['override'];
                    $flagKeys = array_keys((array) ($effective['feature_flags'] ?? []));
                    $featuresText = is_array($effective['features'] ?? null) ? implode("\n", $effective['features']) : '';
                @endphp
                <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-lg font-semibold text-stone-900">{{ strtoupper($key) }}</h3>
                        <div class="inline-flex items-center gap-2">
                            @if ($override)
                                <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Override Aktif</span>
                            @else
                                <span class="rounded-full border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs font-semibold text-stone-600">Default Config</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <form method="POST" action="{{ route('backend.plans.update', $key) }}" class="contents">
                        @csrf
                        @method('PUT')
                        <label class="text-xs font-medium text-stone-600">
                            Nama Paket
                            <input type="text" name="name" value="{{ old('name', $effective['name'] ?? '') }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                        </label>
                        <label class="text-xs font-medium text-stone-600">
                            Harga
                            <input type="text" name="price" value="{{ old('price', $effective['price'] ?? '') }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                        </label>
                        <label class="text-xs font-medium text-stone-600">
                            Siklus Billing
                            <input type="text" name="billing_cycle" value="{{ old('billing_cycle', $effective['billing_cycle'] ?? '') }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                        </label>
                        <label class="text-xs font-medium text-stone-600">
                            Batas Booking Total (kosong = tanpa batas)
                            <input type="number" min="0" name="booking_limit_total" value="{{ old('booking_limit_total', $effective['booking_limit_total']) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">
                        </label>
                        <label class="text-xs font-medium text-stone-600 md:col-span-2">
                            Benefit Ringkas
                            <textarea name="benefit" rows="2" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">{{ old('benefit', $effective['benefit'] ?? '') }}</textarea>
                        </label>
                        <label class="text-xs font-medium text-stone-600 md:col-span-2">
                            Fitur (1 baris = 1 fitur)
                            <textarea name="features_text" rows="4" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm">{{ old('features_text', $featuresText) }}</textarea>
                        </label>
                        <div class="md:col-span-2">
                            <p class="text-xs font-medium text-stone-600">Feature Flags</p>
                            <div class="mt-2 flex flex-wrap gap-3">
                                @foreach ($flagKeys as $flagKey)
                                    <label class="inline-flex items-center gap-2 rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-medium text-stone-700">
                                        <input
                                            type="checkbox"
                                            name="feature_flags[{{ $flagKey }}]"
                                            value="1"
                                            @checked((bool) old("feature_flags.$flagKey", $effective['feature_flags'][$flagKey] ?? false))
                                            class="rounded border-stone-300 text-stone-900 focus:ring-stone-500"
                                        >
                                        {{ $flagKey }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="md:col-span-2 flex flex-wrap gap-2 pt-1">
                            <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-black">
                                Simpan Override
                            </button>
                        </form>
                            <form method="POST" action="{{ route('backend.plans.reset', $key) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                                    Reset ke Default
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-backend-layout>
