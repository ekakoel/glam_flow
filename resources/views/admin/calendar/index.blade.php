<x-app-layout>
    <style>[x-cloak]{display:none!important;}</style>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Booking Calendar</h2>
            <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 rounded-xl bg-rose-500 text-white hover:bg-rose-600 transition">
                Manage Bookings
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/95 border border-rose-100 rounded-2xl shadow-lg p-4 md:p-6">
                <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700">Confirmed</span>
                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700">Completed</span>
                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700">Canceled</span>
                </div>
                <div id="booking-calendar"></div>
            </div>
        </div>
    </div>

    <div
        x-data="calendarBookingModal()"
        x-init="window.calendarBookingModal = $data"
        x-show="isOpen"
        x-cloak
        class="fixed inset-0 z-50"
        @keydown.escape.window="close()"
    >
        <div class="absolute inset-0 bg-black/40" @click="close()"></div>
        <div class="relative max-w-xl mx-auto mt-16 md:mt-24 bg-white rounded-2xl shadow-2xl border border-rose-100 p-6">
            <h3 class="text-xl font-semibold text-stone-800">Create Booking</h3>
            <p class="text-sm text-stone-500 mt-1">Date: <span x-text="form.booking_date"></span></p>

            <template x-if="error">
                <p class="mt-3 text-sm text-red-600" x-text="error"></p>
            </template>

            <form class="mt-5 space-y-4" @submit.prevent="submitForm()">
                <div>
                    <label class="block text-sm font-medium text-stone-700">Service</label>
                    <select id="calendar_service_id" x-model="form.service_id" @change="calculateEndTime()" class="mt-1 w-full rounded-lg border-stone-300 focus:border-rose-400 focus:ring-rose-300">
                        <option value="">Select service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" data-duration="{{ $service->duration }}">{{ $service->name }} ({{ $service->duration }} min)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Customer</label>
                    <select x-model="form.customer_id" class="mt-1 w-full rounded-lg border-stone-300 focus:border-rose-400 focus:ring-rose-300">
                        <option value="">Select customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Start Time</label>
                        <input type="time" x-model="form.booking_time" @input="calculateEndTime()" class="mt-1 w-full rounded-lg border-stone-300 focus:border-rose-400 focus:ring-rose-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Estimated End</label>
                        <input type="text" x-model="endTimePreview" readonly class="mt-1 w-full rounded-lg border-stone-200 bg-stone-50 text-stone-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Location</label>
                        <input type="text" x-model="form.location" placeholder="Studio / Home service" class="mt-1 w-full rounded-lg border-stone-300 focus:border-rose-400 focus:ring-rose-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Status</label>
                        <select x-model="form.status" class="mt-1 w-full rounded-lg border-stone-300 focus:border-rose-400 focus:ring-rose-300">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Notes</label>
                    <textarea x-model="form.notes" rows="3" class="mt-1 w-full rounded-lg border-stone-300 focus:border-rose-400 focus:ring-rose-300"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="close()" class="px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-50">Cancel</button>
                    <button type="submit" :disabled="loading" class="px-6 py-2.5 rounded-xl bg-rose-500 text-white hover:bg-rose-600 disabled:opacity-50">
                        <span x-show="!loading">Save Booking</span>
                        <span x-show="loading">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="calendar-detail-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40" data-calendar-detail-close></div>
        <div class="relative max-w-xl mx-auto mt-16 md:mt-24 bg-white rounded-2xl shadow-2xl border border-rose-100 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-stone-500">Booking Detail</p>
                    <h3 id="calendar-detail-title" class="mt-1 text-xl font-semibold text-stone-900">-</h3>
                </div>
                <button type="button" class="rounded-lg px-2 py-1 text-stone-500 hover:bg-stone-100" data-calendar-detail-close>✕</button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 text-sm">
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Customer</p>
                    <p id="calendar-detail-customer" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Phone</p>
                    <p id="calendar-detail-phone" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Jumlah Orang</p>
                    <p id="calendar-detail-total-people" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Makeup / Layanan</p>
                    <p id="calendar-detail-services" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Schedule</p>
                    <p id="calendar-detail-schedule" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Status</p>
                    <p id="calendar-detail-status" class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Location</p>
                    <p id="calendar-detail-location" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Notes</p>
                    <p id="calendar-detail-notes" class="mt-1 font-medium text-stone-800">-</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5">
                <button type="button" class="px-5 py-2.5 rounded-xl bg-rose-500 text-white hover:bg-rose-600 transition" data-calendar-detail-close>
                    Close
                </button>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('calendarBookingModal', () => ({
                isOpen: false,
                loading: false,
                error: '',
                endTimePreview: '',
                form: {
                    booking_date: '',
                    booking_time: '10:00',
                    service_id: '',
                    customer_id: '',
                    location: '',
                    status: 'pending',
                    notes: '',
                },
                open(date) {
                    this.isOpen = true;
                    this.error = '';
                    this.form.booking_date = date;
                    this.calculateEndTime();
                },
                close() {
                    this.isOpen = false;
                    this.error = '';
                },
                calculateEndTime() {
                    const serviceSelect = document.getElementById('calendar_service_id');
                    const selected = serviceSelect?.selectedOptions?.[0];
                    const duration = parseInt(selected?.dataset?.duration || '0', 10);

                    if (!this.form.booking_time || Number.isNaN(duration) || duration <= 0) {
                        this.endTimePreview = '-';
                        return;
                    }

                    const [hours, minutes] = this.form.booking_time.split(':').map(Number);
                    const date = new Date(2000, 0, 1, hours, minutes + duration);
                    const hh = String(date.getHours()).padStart(2, '0');
                    const mm = String(date.getMinutes()).padStart(2, '0');
                    this.endTimePreview = `${hh}:${mm}`;
                },
                async submitForm() {
                    this.loading = true;
                    this.error = '';
                    try {
                        const response = await fetch("{{ route('admin.calendar.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify(this.form),
                        });

                        if (!response.ok) {
                            const payload = await response.json();
                            this.error = payload.message || 'Failed to create booking.';
                            return;
                        }

                        this.close();
                        localStorage.setItem('booking:lastCreatedAt', String(Date.now()));
                        window.dispatchEvent(new CustomEvent('booking-created'));
                    } finally {
                        this.loading = false;
                    }
                },
            }));
        });

        document.addEventListener('DOMContentLoaded', () => {
            const calendarEl = document.getElementById('booking-calendar');
            const detailModal = document.getElementById('calendar-detail-modal');
            const detailTitle = document.getElementById('calendar-detail-title');
            const detailCustomer = document.getElementById('calendar-detail-customer');
            const detailPhone = document.getElementById('calendar-detail-phone');
            const detailTotalPeople = document.getElementById('calendar-detail-total-people');
            const detailServices = document.getElementById('calendar-detail-services');
            const detailSchedule = document.getElementById('calendar-detail-schedule');
            const detailStatus = document.getElementById('calendar-detail-status');
            const detailLocation = document.getElementById('calendar-detail-location');
            const detailNotes = document.getElementById('calendar-detail-notes');

            const formatDateTime = (value) => {
                if (!value) {
                    return '-';
                }
                return new Intl.DateTimeFormat('id-ID', {
                    dateStyle: 'medium',
                    timeStyle: 'short',
                }).format(new Date(value));
            };

            const statusBadgeClass = (status) => {
                const map = {
                    pending: 'bg-yellow-100 text-yellow-700',
                    confirmed: 'bg-green-100 text-green-700',
                    completed: 'bg-blue-100 text-blue-700',
                    canceled: 'bg-red-100 text-red-700',
                };
                return map[status] || 'bg-stone-100 text-stone-700';
            };

            const closeDetailModal = () => {
                detailModal.classList.add('hidden');
            };

            const openDetailModal = (event) => {
                detailTitle.textContent = event.extendedProps.service_name || event.title || '-';
                detailCustomer.textContent = event.extendedProps.customer_name || '-';
                detailPhone.textContent = event.extendedProps.customer_phone || '-';
                detailTotalPeople.textContent = `${event.extendedProps.total_people || 1} orang`;
                detailServices.textContent = event.extendedProps.services_summary || event.extendedProps.service_name || '-';
                detailSchedule.textContent = `${formatDateTime(event.start)} - ${formatDateTime(event.end)}`;
                detailStatus.textContent = (event.extendedProps.status || '-').toUpperCase();
                detailStatus.className = `mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${statusBadgeClass(event.extendedProps.status)}`;
                detailLocation.textContent = event.extendedProps.location || '-';
                detailNotes.textContent = event.extendedProps.notes || '-';
                detailModal.classList.remove('hidden');
            };

            detailModal.querySelectorAll('[data-calendar-detail-close]').forEach((button) => {
                button.addEventListener('click', closeDetailModal);
            });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeDetailModal();
                }
            });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                editable: true,
                selectable: true,
                nowIndicator: true,
                eventDurationEditable: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                events: '/admin/calendar/events',
                eventDidMount(info) {
                    console.log(info.event);
                },
                eventClick(info) {
                    openDetailModal(info.event);
                },
                dateClick(info) {
                    window.calendarBookingModal?.open(info.dateStr);
                },
                async eventDrop(info) {
                    try {
                        const response = await fetch(`/admin/calendar/events/${info.event.id}/reschedule`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                start: info.event.startStr,
                            }),
                        });

                        if (!response.ok) {
                            const payload = await response.json();
                            alert(payload.message || 'Failed to reschedule booking.');
                            info.revert();
                            return;
                        }
                    } catch (error) {
                        info.revert();
                        alert('Network error while rescheduling booking.');
                    }
                },
            });

            calendar.render();

            window.addEventListener('booking-created', () => {
                calendar.refetchEvents();
            });
        });
    </script>
</x-app-layout>
