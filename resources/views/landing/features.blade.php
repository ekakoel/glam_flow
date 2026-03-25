<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - MUA SaaS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-b from-rose-50 via-amber-50 to-white text-stone-800">
    <main class="max-w-7xl mx-auto px-6 py-16">
        <h1 class="text-4xl font-bold text-center">Built for Modern MUA Teams</h1>
        <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h2 class="text-lg font-semibold">Multi-Tenant Isolation</h2>
                <p class="mt-2 text-stone-600">Each MUA account sees only their own services, bookings, and payments.</p>
            </div>
            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h2 class="text-lg font-semibold">Smart Booking Calendar</h2>
                <p class="mt-2 text-stone-600">Conflict detection, drag-drop scheduling, and real-time updates.</p>
            </div>
            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h2 class="text-lg font-semibold">Payments & Invoices</h2>
                <p class="mt-2 text-stone-600">Auto payment records, manual payment confirmation, invoice PDF generation.</p>
            </div>
            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h2 class="text-lg font-semibold">Plan Limits & SaaS Billing Ready</h2>
                <p class="mt-2 text-stone-600">Free/Pro/Premium plans and billing logs for upgrades and transactions.</p>
            </div>
            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h2 class="text-lg font-semibold">Notifications</h2>
                <p class="mt-2 text-stone-600">WhatsApp-ready service abstraction for booking and payment updates.</p>
            </div>
            <div class="bg-white rounded-2xl border border-rose-100 shadow p-6">
                <h2 class="text-lg font-semibold">Scalable Architecture</h2>
                <p class="mt-2 text-stone-600">Service layer + repositories + tenant scope ready for API/mobile expansion.</p>
            </div>
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-rose-500 text-white hover:bg-rose-600">Start Free Trial</a>
        </div>
    </main>
</body>
</html>
