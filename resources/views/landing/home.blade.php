<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUA SaaS Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-b from-rose-50 via-amber-50 to-white text-stone-800">
    <header class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <a href="{{ route('landing.home') }}" class="font-bold text-xl text-rose-700">MUA SaaS</a>
        <nav class="flex items-center gap-4 text-sm">
            <a href="{{ route('landing.features') }}" class="hover:text-rose-600">Features</a>
            <a href="{{ route('landing.pricing') }}" class="hover:text-rose-600">Pricing</a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-rose-500 text-white">Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-rose-500 text-white">Start Free Trial</a>
            @endauth
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <p class="inline-flex px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-sm">SaaS for Makeup Artists</p>
                <h1 class="mt-5 text-4xl md:text-5xl font-bold leading-tight">Run your MUA business in one beautiful workspace.</h1>
                <p class="mt-4 text-stone-600 text-lg">Manage bookings, payments, reports, and client communication with full tenant data isolation.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-rose-500 text-white hover:bg-rose-600">Start Free Trial</a>
                    <a href="{{ route('landing.pricing') }}" class="px-6 py-3 rounded-xl border border-stone-300 hover:bg-white">See Pricing</a>
                </div>
            </div>
            <div class="bg-white rounded-3xl border border-rose-100 shadow-xl p-6">
                <p class="text-sm text-stone-500">SaaS Highlights</p>
                <ul class="mt-4 space-y-3 text-sm">
                    <li class="p-3 rounded-xl bg-stone-50">Tenant-safe data architecture</li>
                    <li class="p-3 rounded-xl bg-stone-50">Calendar + conflict-free booking</li>
                    <li class="p-3 rounded-xl bg-stone-50">Payment and invoice automation</li>
                    <li class="p-3 rounded-xl bg-stone-50">Subscription-ready plans</li>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
