<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-stone-800 leading-tight">Tenant Management</h2>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-rose-50 via-amber-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-rose-100 shadow overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Tenant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Services</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Customers</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Bookings</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($tenants as $tenant)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-stone-800">{{ $tenant->name }} ({{ $tenant->email }})</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">{{ $tenant->role }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">{{ $tenant->subscription?->plan ?? 'free' }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">{{ $tenant->services_count }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">{{ $tenant->customers_count }}</td>
                                    <td class="px-4 py-3 text-sm text-stone-700">{{ $tenant->bookings_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-stone-500">No tenants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden p-4 space-y-3">
                    @forelse($tenants as $tenant)
                        <div class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 shadow-sm">
                            <p class="text-sm font-semibold text-stone-900">{{ $tenant->name }}</p>
                            <p class="text-sm text-stone-600">{{ $tenant->email }}</p>
                            <div class="mt-2 grid grid-cols-2 gap-2 text-sm text-stone-700">
                                <p>Role: {{ $tenant->role }}</p>
                                <p>Plan: {{ $tenant->subscription?->plan ?? 'free' }}</p>
                                <p>Services: {{ $tenant->services_count }}</p>
                                <p>Customers: {{ $tenant->customers_count }}</p>
                                <p>Bookings: {{ $tenant->bookings_count }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50 p-4 text-sm text-stone-500">
                            No tenants found.
                        </div>
                    @endforelse
                </div>
                <div class="p-4">{{ $tenants->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
