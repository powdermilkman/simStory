<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Characters</h2>
            <a href="{{ route('admin.characters.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + New Character
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Character</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($characters as $character)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($character->avatar_path)
                                    <img src="{{ Storage::url($character->avatar_path) }}" alt="{{ $character->display_name }}" class="h-10 w-10 rounded-full mr-3">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-300 mr-3 flex items-center justify-center text-gray-600 font-bold">
                                        {{ substr($character->display_name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.characters.show', $character) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $character->display_name }}
                                    </a>
                                    <div class="text-sm text-gray-500">{{ '@' . $character->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-1">
                                @if($character->role)
                                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded"
                                          style="background-color: {{ $character->role->color }}; color: {{ $character->role->text_color }};">
                                        {{ $character->role->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                                @if($character->is_official)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded bg-blue-600 text-white">
                                        ✓ Official
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $character->post_count }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $character->fake_join_date?->format('M d, Y') ?? 'Not set' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.characters.edit', $character) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('admin.characters.destroy', $character) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will delete all posts by this character.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No characters found. <a href="{{ route('admin.characters.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
