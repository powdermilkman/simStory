<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Threads</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.threads.create') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm">
                    + Quick Thread
                </a>
                <a href="{{ route('admin.threads.compose') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                    + Compose Thread
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($threads as $thread)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.threads.show', $thread) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $thread->title }}
                            </a>
                            <div class="flex gap-1 mt-1">
                                @if($thread->is_pinned)
                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Pinned</span>
                                @endif
                                @if($thread->is_locked)
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded">Locked</span>
                                @endif
                                @if($thread->phase_id)
                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Phase-gated</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $thread->category->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $thread->author->display_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $thread->fake_created_at?->format('M d, Y') ?? 'Not set' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.threads.edit', $thread) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('admin.threads.destroy', $thread) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No threads found. <a href="{{ route('admin.threads.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card List --}}
    <div class="md:hidden space-y-3">
        @forelse($threads as $thread)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="mb-2">
                    <a href="{{ route('admin.threads.show', $thread) }}" class="font-medium text-blue-600 hover:underline">
                        {{ $thread->title }}
                    </a>
                    <div class="flex gap-1 mt-1 flex-wrap">
                        @if($thread->is_pinned)
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Pinned</span>
                        @endif
                        @if($thread->is_locked)
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded">Locked</span>
                        @endif
                        @if($thread->phase_id)
                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Phase-gated</span>
                        @endif
                    </div>
                </div>
                <div class="text-sm text-gray-500 space-y-0.5">
                    <div>{{ $thread->category->name }} · {{ $thread->author->display_name }}</div>
                    <div>{{ $thread->fake_created_at?->format('M d, Y') ?? 'No date' }}</div>
                </div>
                <div class="flex gap-4 mt-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.threads.edit', $thread) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form action="{{ route('admin.threads.destroy', $thread) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                No threads found. <a href="{{ route('admin.threads.create') }}" class="text-blue-600 hover:underline">Create one</a>.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $threads->links() }}
    </div>
</x-admin-layout>
