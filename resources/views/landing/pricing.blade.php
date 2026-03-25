<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - MUA SaaS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-b from-rose-50 via-amber-50 to-white text-stone-800">
    <main class="max-w-7xl mx-auto px-6 py-16">
        <h1 class="text-4xl font-bold text-center">Simple Pricing</h1>
        <p class="mt-3 text-center text-stone-600">Start free, upgrade as your MUA business grows.</p>
        <div class="mt-10 grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-rose-100 p-6 shadow">
                <h2 class="text-xl font-semibold">Free</h2>
                <p class="mt-2 text-stone-600">Max 10 bookings/month</p>
                <p class="mt-4 text-3xl font-bold">Rp 0</p>
                <a href="{{ route('register', ['plan' => 'free']) }}" class="inline-flex mt-5 px-4 py-2 rounded-xl bg-rose-500 text-white hover:bg-rose-600">
                    Choose Free
                </a>
            </div>
            <div class="bg-white rounded-2xl border border-rose-300 p-6 shadow-xl">
                <h2 class="text-xl font-semibold">Pro</h2>
                <p class="mt-2 text-stone-600">Unlimited bookings</p>
                <p class="mt-4 text-3xl font-bold">Rp 199K</p>
                <a href="{{ route('register', ['plan' => 'pro']) }}" class="inline-flex mt-5 px-4 py-2 rounded-xl bg-rose-500 text-white hover:bg-rose-600">
                    Choose Pro
                </a>
            </div>
            <div class="bg-white rounded-2xl border border-rose-100 p-6 shadow">
                <h2 class="text-xl font-semibold">Premium</h2>
                <p class="mt-2 text-stone-600">Priority support + advanced analytics</p>
                <p class="mt-4 text-3xl font-bold">Rp 399K</p>
                <a href="{{ route('register', ['plan' => 'premium']) }}" class="inline-flex mt-5 px-4 py-2 rounded-xl bg-rose-500 text-white hover:bg-rose-600">
                    Choose Premium
                </a>
            </div>
        </div>
        <div class="mt-10 text-center text-sm text-stone-600">All plans start with a 7-day trial in current onboarding flow.</div>
    </main>
</body>
</html>
