<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Character: {{ $character->display_name }}</h2>
            <a href="{{ route('admin.characters.edit', $character) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start gap-6">
            @if($character->avatar_path)
                <img src="{{ Storage::url($character->avatar_path) }}" alt="{{ $character->display_name }}" class="h-24 w-24 rounded-full">
            @else
                <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-2xl font-bold">
                    {{ substr($character->display_name, 0, 1) }}
                </div>
            @endif
            <div class="flex-1">
                <h3 class="text-xl font-bold">{{ $character->display_name }}</h3>
                <p class="text-gray-500">{{ '@' . $character->username }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    @if($character->role)
                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded"
                              style="background-color: {{ $character->role->color }}; color: {{ $character->role->text_color }};">
                            {{ $character->role->name }}
                        </span>
                    @endif
                    @if($character->is_official)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded bg-blue-600 text-white">
                            ✓ Official
                        </span>
                    @endif
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Posts:</span>
                        <span class="font-medium">{{ $character->post_count }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Join Date:</span>
                        <span class="font-medium">{{ $character->fake_join_date?->format('M d, Y') ?? 'Not set' }}</span>
                    </div>
                </div>
                @if($character->signature)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <span class="text-gray-500 text-sm">Signature:</span>
                        <p class="italic text-gray-600">{{ $character->signature }}</p>
                    </div>
                @endif
                @if($character->bio)
                    <div class="mt-4">
                        <span class="text-gray-500 text-sm">Bio:</span>
                        <p class="text-gray-700">{{ $character->bio }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Posts</h3>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thread</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Preview</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($character->posts->take(10) as $post)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.threads.show', $post->thread) }}" class="text-blue-600 hover:underline">
                                {{ $post->thread->title }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit(strip_tags($post->content), 60) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->fake_created_at?->format('M d, Y') ?? 'Not set' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            No posts by this character yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
