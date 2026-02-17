<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Posts</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thread</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.posts.show', $post) }}" class="text-blue-600 hover:underline">
                                {{ Str::limit(strip_tags($post->content), 60) }}
                            </a>
                            @if($post->phase_id)
                                <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Phase-gated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <a href="{{ route('admin.threads.show', $post->thread) }}" class="hover:underline">
                                {{ Str::limit($post->thread->title, 30) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->author->display_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->fake_created_at?->format('M d, Y') ?? 'Not set' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No posts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</x-admin-layout>
