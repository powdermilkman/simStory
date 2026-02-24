<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Private Messages</h2>
            <a href="{{ route('admin.private-messages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + New Message
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From → To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($messages as $message)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.private-messages.show', $message) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $message->subject }}
                            </a>
                            @if($message->phase)
                                <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Phase: {{ $message->phase->name }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $message->sender->display_name }} →
                            @if($message->is_inbox_message)
                                <span class="text-purple-600 font-medium">Reader Inbox</span>
                            @else
                                {{ $message->recipient->display_name ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $message->fake_sent_at?->format('M d, Y') ?? 'Not set' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($message->is_read)
                                <span class="text-gray-500">Read</span>
                            @else
                                <span class="text-blue-600 font-medium">Unread</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.private-messages.edit', $message) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('admin.private-messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Delete this message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No private messages found. <a href="{{ route('admin.private-messages.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $messages->links() }}
    </div>
</x-admin-layout>
