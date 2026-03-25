<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customers</h2>
            <a href="{{ route('admin.customers.create') }}" class="px-4 py-2 bg-rose-600 text-white rounded-md hover:bg-rose-700">
                Add Customer
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 rounded-md bg-emerald-100 text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instagram</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($customers as $customer)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $customer->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $customer->phone }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $customer->email ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $customer->instagram ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.customers.edit', $customer) }}" class="px-3 py-1.5 text-sm bg-amber-500 text-white rounded hover:bg-amber-600">Edit</a>
                                            <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700" onclick="return confirm('Delete this customer?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No customers yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden p-4 space-y-3">
                    @forelse ($customers as $customer)
                        <div class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 shadow-sm">
                            <p class="text-sm font-semibold text-stone-900">{{ $customer->name }}</p>
                            <p class="mt-1 text-sm text-stone-700">{{ $customer->phone }}</p>
                            <p class="text-sm text-stone-600">{{ $customer->email ?? '-' }}</p>
                            <p class="text-sm text-stone-600">{{ $customer->instagram ?? '-' }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="px-3 py-1.5 text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600">Edit</a>
                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Delete this customer?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50 p-4 text-sm text-stone-500">
                            No customers yet.
                        </div>
                    @endforelse
                </div>
                <div class="p-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
