<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
            <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + New Category
            </a>
        </div>
    </x-slot>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Threads</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.categories.show', $category) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $category->name }}
                            </a>
                            <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->threads_count }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($category->description, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No categories found. <a href="{{ route('admin.categories.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card List --}}
    <div class="md:hidden space-y-3">
        @forelse($categories as $category)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-start justify-between mb-1">
                    <div>
                        <a href="{{ route('admin.categories.show', $category) }}" class="font-medium text-blue-600 hover:underline">
                            {{ $category->name }}
                        </a>
                        <div class="text-xs text-gray-400">{{ $category->slug }}</div>
                    </div>
                    <span class="text-xs text-gray-500 ml-2 flex-shrink-0">Order: {{ $category->sort_order }}</span>
                </div>
                <div class="text-sm text-gray-500 space-y-0.5">
                    <div>{{ $category->threads_count }} threads</div>
                    @if($category->description)
                        <div>{{ Str::limit($category->description, 60) }}</div>
                    @endif
                </div>
                <div class="flex gap-4 mt-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                No categories found. <a href="{{ route('admin.categories.create') }}" class="text-blue-600 hover:underline">Create one</a>.
            </div>
        @endforelse
    </div>
</x-admin-layout>
