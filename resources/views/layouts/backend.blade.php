<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Glam Flow') }} | Backend</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 font-sans text-stone-900 antialiased">
        <header class="sticky top-0 z-40 border-b border-stone-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-rose-600">Backend</p>
                    <h1 class="text-base font-semibold text-stone-900 sm:text-lg">Super Admin Panel</h1>
                </div>
                <nav class="flex items-center gap-2 text-sm">
                    <a href="{{ route('backend.dashboard') }}" class="rounded-lg px-3 py-2 font-medium {{ request()->routeIs('backend.dashboard') ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100' }}">Dashboard</a>
                    <a href="{{ route('backend.tenants.index') }}" class="rounded-lg px-3 py-2 font-medium {{ request()->routeIs('backend.tenants.*') ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100' }}">Tenant</a>
                    <a href="{{ route('backend.plans.index') }}" class="rounded-lg px-3 py-2 font-medium {{ request()->routeIs('backend.plans.*') ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100' }}">Paket</a>
                    <a href="{{ route('backend.audit-logs.index') }}" class="rounded-lg px-3 py-2 font-medium {{ request()->routeIs('backend.audit-logs.*') ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100' }}">Audit Log</a>
                    <a href="{{ route('admin.dashboard') }}" class="hidden rounded-lg border border-stone-200 px-3 py-2 font-medium text-stone-700 hover:bg-stone-100 sm:inline-flex">Frontend</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 font-medium text-rose-700 hover:bg-rose-100">Logout</button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Ada data yang belum valid.</p>
                    <ul class="mt-1 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ $slot }}
        </main>
    </body>
</html>
