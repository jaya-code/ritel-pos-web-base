@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
        <a href="{{ route('categories.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
            <i data-feather="plus" class="w-4 h-4"></i> Add Category
        </a>
    </div>

    <!-- Stats/Filter placeholder -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="p-3 bg-purple-100 rounded-lg text-purple-600">
                <i data-feather="grid" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Categories</p>
                <h3 class="text-xl font-bold text-gray-800">{{ $categories->total() }}</h3>
            </div>
        </div>

        <!-- Search Form -->
        <div class="md:col-span-2">
            <form action="{{ route('categories.index') }}" method="GET" class="flex gap-2 items-end h-full">
                <div class="w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-3 px-4">
                </div>
                @if (request('search'))
                    <a href="{{ route('categories.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-3 rounded-lg flex items-center justify-center mb-[1px]">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                @endif
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-sm transition-colors mb-[1px]">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-900 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Description</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4">{{ Str::limit($category->description, 50) }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('categories.edit', $category->id) }}"
                                        class="text-blue-600 hover:text-blue-800 p-1">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this category?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
