<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Category: {{ $category->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.categories.edit', $category) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Edit
                </a>
                <a href="{{ route('admin.threads.create', ['category_id' => $category->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    + New Thread
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-gray-500">Slug:</span>
                <span class="font-mono">{{ $category->slug }}</span>
            </div>
            <div>
                <span class="text-gray-500">Sort Order:</span>
                <span>{{ $category->sort_order }}</span>
            </div>
        </div>
        @if($category->description)
            <div class="mt-4">
                <span class="text-gray-500">Description:</span>
                <p class="mt-1">{{ $category->description }}</p>
            </div>
        @endif
    </div>

    <h3 class="text-lg font-medium text-gray-900 mb-4">Threads in this category</h3>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($category->threads as $thread)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.threads.show', $thread) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $thread->title }}
                            </a>
                            @if($thread->is_pinned)
                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Pinned</span>
                            @endif
                            @if($thread->is_locked)
                                <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Locked</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $thread->author->display_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $thread->fake_created_at?->format('M d, Y') ?? 'Not set' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.threads.edit', $thread) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No threads in this category yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
