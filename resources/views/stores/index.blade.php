@extends('layouts.admin')

@section('title', 'Stores List')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Stores List</h2>
            {{-- <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <i data-feather="plus"></i> Add Store
            </a> --}}
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-900 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Store Name</th>
                        <th class="px-6 py-4 font-semibold">Manager</th>
                        <th class="px-6 py-4 font-semibold">Address</th>
                        <th class="px-6 py-4 font-semibold">Phone</th>
                        <th class="px-6 py-4 font-semibold">Created At</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($stores as $store)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $store->name }}</td>
                            <td class="px-6 py-4">
                                @if ($store->owner)
                                    <span class="text-blue-600 font-medium">{{ $store->owner->name }}</span>
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $store->owner->email }}</span>
                                @else
                                    <span class="text-red-500 italic">No Manager</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $store->address ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $store->phone ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $store->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('stores.edit', $store->id) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stores->links() }}
        </div>
    </div>
@endsection
