<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['categories'] }}</div>
            <div class="text-gray-500">Categories</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['characters'] }}</div>
            <div class="text-gray-500">Characters</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['threads'] }}</div>
            <div class="text-gray-500">Threads</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['posts'] }}</div>
            <div class="text-gray-500">Posts</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['private_messages'] }}</div>
            <div class="text-gray-500">Private Messages</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['readers'] }}</div>
            <div class="text-gray-500">Readers</div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Recent Threads -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Threads</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($recentThreads as $thread)
                    <li class="px-6 py-4">
                        <a href="{{ route('admin.threads.show', $thread) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $thread->title }}
                        </a>
                        <div class="text-sm text-gray-500">
                            by {{ $thread->author->display_name }} in {{ $thread->category->name }}
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500">No threads yet.</li>
                @endforelse
            </ul>
        </div>

        <!-- Recent Posts -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Posts</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($recentPosts as $post)
                    <li class="px-6 py-4">
                        <a href="{{ route('admin.posts.show', $post) }}" class="text-blue-600 hover:underline font-medium">
                            {{ Str::limit(strip_tags($post->content), 50) }}
                        </a>
                        <div class="text-sm text-gray-500">
                            by {{ $post->author->display_name }} in "{{ $post->thread->title }}"
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500">No posts yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                + New Category
            </a>
            <a href="{{ route('admin.characters.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                + New Character
            </a>
            <a href="{{ route('admin.threads.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                + New Thread
            </a>
            <a href="{{ route('admin.private-messages.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                + New Message
            </a>
        </div>
    </div>
</x-admin-layout>
