<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-stone-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $alertClass = 'bg-yellow-50 border-yellow-200 text-yellow-800';
                $alertText = strtoupper($plan) . ' PLAN';
                if ($plan !== 'free') {
                    $alertClass = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                    $alertText = strtoupper($plan) . ' PLAN - Active';
                } elseif (!is_null($trialDaysLeft)) {
                    $alertText = strtoupper($plan) . ' PLAN - ' . $trialDaysLeft . ' day(s) remaining';
                }
                if (!is_null($trialDaysLeft) && $trialDaysLeft < 3) {
                    $alertClass = 'bg-red-50 border-red-200 text-red-700';
                    $alertText = 'Your trial ends in ' . $trialDaysLeft . ' day(s)';
                }
            @endphp
            <div class="p-5 rounded-2xl border {{ $alertClass }} shadow-md">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold">{{ $alertText }}</p>
                        <p class="text-sm mt-1">Expiry: {{ $trialExpiry?->format('d M Y') ?? 'No expiry' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('billing.index') }}" class="px-5 py-2.5 rounded-xl bg-stone-800 text-white hover:bg-black transition">Upgrade Plan</a>
                        <a href="{{ route('admin.services.create') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 bg-white text-stone-700 hover:bg-stone-50 transition">Add Service</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow border border-rose-100">
                    <p class="text-sm text-stone-500">Total Bookings</p>
                    <p class="mt-2 text-3xl font-bold text-stone-900">{{ $totalBookings }}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow border border-rose-100">
                    <p class="text-sm text-stone-500">Total Customers</p>
                    <p class="mt-2 text-3xl font-bold text-stone-900">{{ $totalCustomers }}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow border border-rose-100">
                    <p class="text-sm text-stone-500">Total Revenue</p>
                    <p class="mt-2 text-3xl font-bold text-stone-900">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow border border-rose-100">
                <h3 class="text-lg font-semibold text-stone-900">Quick Actions</h3>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('admin.services.index') }}" class="px-5 py-2.5 bg-rose-500 text-white rounded-xl hover:bg-rose-600">
                        Manage Services
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 bg-stone-700 text-white rounded-xl hover:bg-stone-800">
                        Manage Customers
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600">
                        Manage Bookings
                    </a>
                    <a href="{{ route('admin.calendar.index') }}" class="px-5 py-2.5 bg-amber-500 text-white rounded-xl hover:bg-amber-600">
                        Open Calendar
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="px-5 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600">
                        Manage Payments
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="px-5 py-2.5 bg-stone-700 text-white rounded-xl hover:bg-stone-800">
                        View Reports
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow border border-rose-100">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-stone-900">Calendar Widget</h3>
                        <span class="text-xs px-3 py-1 rounded-full bg-rose-100 text-rose-700">
                            Auto update setiap 20 detik
                        </span>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                        <span class="px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                        <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700">Confirmed</span>
                    </div>
                    <div class="mt-4" id="dashboard-mini-calendar"></div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow border border-rose-100">
                    <h3 class="text-lg font-semibold text-stone-900">Upcoming Bookings</h3>
                    <div class="mt-4 space-y-3">
                        @forelse($upcomingBookings as $booking)
                            <div class="p-3 rounded-xl border border-stone-200 bg-stone-50">
                                <p class="font-medium text-stone-800">{{ $booking->service->name }} - {{ $booking->customer->name }}</p>
                                <p class="text-sm text-stone-600">
                                    {{ $booking->booking_date?->format('d M Y') }} at {{ substr((string) $booking->booking_time, 0, 5) }}
                                </p>
                                <p class="text-sm text-stone-600 mt-1">
                                    {{ (int) ($booking->total_people ?? 1) }} orang • {{ $booking->location ?: 'Lokasi belum diisi' }}
                                </p>
                            </div>
                        @empty
                            <div class="p-4 rounded-xl border border-dashed border-stone-300 bg-stone-50">
                                <p class="text-sm text-stone-600">No bookings yet.</p>
                                <a href="{{ route('admin.bookings.create') }}" class="inline-flex mt-2 text-sm font-medium text-rose-600 hover:text-rose-700">
                                    Create your first booking
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($servicesCount === 0)
                <div class="p-5 rounded-2xl border border-dashed border-rose-200 bg-white shadow-sm">
                    <p class="text-stone-700">Add your first service to start receiving bookings.</p>
                    <a href="{{ route('admin.services.create') }}" class="inline-flex mt-3 px-5 py-2.5 rounded-xl bg-rose-500 text-white hover:bg-rose-600 transition">
                        Add your first service
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div id="booking-detail-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40" data-modal-close></div>
        <div class="relative mx-auto mt-16 w-[92%] max-w-lg rounded-2xl border border-rose-100 bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-stone-500">Booking Detail</p>
                    <h3 id="modal-title" class="mt-1 text-xl font-semibold text-stone-900">-</h3>
                </div>
                <button type="button" class="rounded-lg px-2 py-1 text-stone-500 hover:bg-stone-100" data-modal-close>✕</button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 text-sm">
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Customer</p>
                    <p id="modal-customer" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Phone</p>
                    <p id="modal-phone" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Jumlah Orang</p>
                    <p id="modal-total-people" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Makeup / Layanan</p>
                    <p id="modal-services" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Schedule</p>
                    <p id="modal-schedule" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Status</p>
                    <p id="modal-status" class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Location</p>
                    <p id="modal-location" class="mt-1 font-medium text-stone-800">-</p>
                </div>
                <div class="rounded-xl bg-stone-50 p-3">
                    <p class="text-stone-500">Notes</p>
                    <p id="modal-notes" class="mt-1 font-medium text-stone-800">-</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="rounded-xl bg-rose-500 px-5 py-2.5 text-white hover:bg-rose-600 transition" data-modal-close>
                    Close
                </button>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('dashboard-mini-calendar');
            if (!el) {
                return;
            }

            const showToast = (message, variant = 'success') => {
                const toast = document.createElement('div');
                const classes = variant === 'warning'
                    ? 'bg-amber-100 border-amber-200 text-amber-800'
                    : 'bg-emerald-100 border-emerald-200 text-emerald-800';
                toast.className = `fixed bottom-5 right-5 z-50 px-4 py-3 rounded-xl border shadow-md text-sm ${classes}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition');
                    setTimeout(() => toast.remove(), 250);
                }, 2500);
            };

            const detailModal = document.getElementById('booking-detail-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalCustomer = document.getElementById('modal-customer');
            const modalPhone = document.getElementById('modal-phone');
            const modalTotalPeople = document.getElementById('modal-total-people');
            const modalServices = document.getElementById('modal-services');
            const modalSchedule = document.getElementById('modal-schedule');
            const modalStatus = document.getElementById('modal-status');
            const modalLocation = document.getElementById('modal-location');
            const modalNotes = document.getElementById('modal-notes');

            const formatDateTime = (value) => {
                if (!value) {
                    return '-';
                }
                const date = new Date(value);
                return new Intl.DateTimeFormat('id-ID', {
                    dateStyle: 'medium',
                    timeStyle: 'short',
                }).format(date);
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

            const openDetailModal = (event) => {
                modalTitle.textContent = event.extendedProps.service_name || event.title || '-';
                modalCustomer.textContent = event.extendedProps.customer_name || '-';
                modalPhone.textContent = event.extendedProps.customer_phone || '-';
                modalTotalPeople.textContent = `${event.extendedProps.total_people || 1} orang`;
                modalServices.textContent = event.extendedProps.services_summary || event.extendedProps.service_name || '-';
                modalSchedule.textContent = `${formatDateTime(event.start)} - ${formatDateTime(event.end)}`;
                modalStatus.textContent = (event.extendedProps.status || '-').toUpperCase();
                modalStatus.className = `mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${statusBadgeClass(event.extendedProps.status)}`;
                modalLocation.textContent = event.extendedProps.location || '-';
                modalNotes.textContent = event.extendedProps.notes || '-';
                detailModal.classList.remove('hidden');
            };

            const closeDetailModal = () => {
                detailModal.classList.add('hidden');
            };

            detailModal.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', closeDetailModal);
            });
            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeDetailModal();
                }
            });

            const knownEventIds = new Set();

            const miniCalendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                height: 430,
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: '',
                },
                events: '/admin/calendar/events',
                eventDidMount(info) {
                    info.el.title = `${info.event.title} (${info.event.extendedProps.status || 'booking'})`;
                },
                eventClick(info) {
                    openDetailModal(info.event);
                },
                eventsSet(events) {
                    let hasNewEvent = false;
                    events.forEach((event) => {
                        const eventId = String(event.id ?? event.startStr + event.title);
                        if (!knownEventIds.has(eventId)) {
                            if (knownEventIds.size > 0) {
                                hasNewEvent = true;
                            }
                            knownEventIds.add(eventId);
                        }
                    });

                    if (hasNewEvent) {
                        showToast('Booking baru muncul di calendar widget.');
                    }
                },
            });

            miniCalendar.render();

            setInterval(() => {
                miniCalendar.refetchEvents();
            }, 20000);

            window.addEventListener('focus', () => {
                miniCalendar.refetchEvents();
            });

            window.addEventListener('storage', (event) => {
                if (event.key === 'booking:lastCreatedAt') {
                    miniCalendar.refetchEvents();
                    showToast('Booking baru terdeteksi, kalender diperbarui.');
                }
            });
        });
    </script>
</x-app-layout>
