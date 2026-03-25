<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Booking</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('admin.bookings.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Customer</label>
                            <select name="customer_id" class="mt-1 w-full rounded-md border-gray-300">
                                <option value="">Select customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                        {{ $customer->name }} ({{ $customer->phone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-700">Services & People</label>
                            <button type="button" id="add-service-row" class="px-3 py-1.5 text-sm rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200">
                                + Add Service
                            </button>
                        </div>
                        @error('service_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('services') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <div id="service-rows" class="mt-3 space-y-3"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="booking_date" value="{{ old('booking_date') }}" class="mt-1 w-full rounded-md border-gray-300">
                            @error('booking_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" id="booking_time" name="booking_time" value="{{ old('booking_time') }}" class="mt-1 w-full rounded-md border-gray-300">
                            @error('booking_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estimated End Time</label>
                        <input type="text" id="estimated_end_time" readonly class="mt-1 w-full rounded-md border-gray-200 bg-gray-50 text-gray-700" value="-">
                        <p class="mt-2 text-sm text-gray-600">Total people: <span id="total_people_preview">0</span></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" name="location" value="{{ old('location') }}" class="mt-1 w-full rounded-md border-gray-300">
                            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 w-full rounded-md border-gray-300">
                                @foreach (['pending', 'confirmed', 'completed', 'canceled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', 'pending') === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border-gray-300">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-md hover:bg-rose-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $servicesCatalog = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'duration' => (int) $service->duration,
            ];
        })->values();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const serviceRows = document.getElementById('service-rows');
            const addServiceRowButton = document.getElementById('add-service-row');
            const timeInput = document.getElementById('booking_time');
            const endInput = document.getElementById('estimated_end_time');
            const totalPeoplePreview = document.getElementById('total_people_preview');

            const servicesCatalog = @json($servicesCatalog);

            const oldRows = @json(old('services', []));
            const fallbackServiceId = @json(old('service_id'));
            const renderServiceOptions = (selectedId = '') => {
                const options = ['<option value="">Select service</option>'];
                servicesCatalog.forEach((service) => {
                    const selected = String(selectedId) === String(service.id) ? 'selected' : '';
                    options.push(
                        `<option value="${service.id}" data-duration="${service.duration}" data-price="${service.price}" ${selected}>${service.name} - Rp ${new Intl.NumberFormat('id-ID').format(service.price)}</option>`
                    );
                });
                return options.join('');
            };

            let rowIndex = 0;

            const addServiceRow = (serviceId = '', peopleCount = 1) => {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-1 md:grid-cols-12 gap-3 p-3 rounded-xl border border-rose-100 bg-rose-50/40';
                row.dataset.rowIndex = String(rowIndex);
                row.innerHTML = `
                    <div class="md:col-span-7">
                        <label class="block text-xs font-medium text-gray-600">Service</label>
                        <select name="services[${rowIndex}][service_id]" class="service-select mt-1 w-full rounded-md border-gray-300">
                            ${renderServiceOptions(serviceId)}
                        </select>
                        <p class="service-error mt-1 text-sm text-red-600"></p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600">People</label>
                        <input type="number" min="1" name="services[${rowIndex}][people_count]" value="${peopleCount}" class="people-count mt-1 w-full rounded-md border-gray-300" />
                        <p class="people-error mt-1 text-sm text-red-600"></p>
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="button" class="remove-service-row w-full px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">Remove</button>
                    </div>
                `;

                serviceRows.appendChild(row);
                rowIndex += 1;
                updateEstimate();
            };

            const updateEstimate = () => {
                const timeValue = timeInput?.value;
                let totalDuration = 0;
                let totalPeople = 0;

                document.querySelectorAll('#service-rows [data-row-index]').forEach((row) => {
                    const select = row.querySelector('.service-select');
                    const peopleInput = row.querySelector('.people-count');
                    const selected = select?.selectedOptions?.[0];
                    const baseDuration = parseInt(selected?.dataset?.duration || '0', 10);
                    const people = Math.max(1, parseInt(peopleInput?.value || '1', 10));
                    if (baseDuration > 0) {
                        totalDuration += baseDuration * people;
                    }
                    totalPeople += people;
                });

                totalPeoplePreview.textContent = String(totalPeople);

                if (!timeValue || Number.isNaN(totalDuration) || totalDuration <= 0) {
                    endInput.value = '-';
                    return;
                }

                const [hours, minutes] = timeValue.split(':').map(Number);
                const date = new Date(2000, 0, 1, hours, minutes + totalDuration);
                endInput.value = `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
            };

            addServiceRowButton?.addEventListener('click', () => addServiceRow('', 1));
            serviceRows?.addEventListener('change', (event) => {
                if (event.target.classList.contains('service-select')) {
                    updateEstimate();
                }
            });
            serviceRows?.addEventListener('input', (event) => {
                if (event.target.classList.contains('people-count')) {
                    updateEstimate();
                }
            });
            serviceRows?.addEventListener('click', (event) => {
                const target = event.target;
                if (target.classList.contains('remove-service-row')) {
                    target.closest('[data-row-index]')?.remove();
                    updateEstimate();
                }
            });
            timeInput?.addEventListener('input', updateEstimate);

            if (Array.isArray(oldRows) && oldRows.length > 0) {
                oldRows.forEach((row) => addServiceRow(row.service_id || '', row.people_count || 1));
            } else if (fallbackServiceId) {
                addServiceRow(fallbackServiceId, 1);
            } else {
                addServiceRow('', 1);
            }

            updateEstimate();
        });
    </script>
</x-app-layout>
