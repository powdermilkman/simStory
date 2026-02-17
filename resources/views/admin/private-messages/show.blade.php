<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Message: {{ $privateMessage->subject }}</h2>
            <a href="{{ route('admin.private-messages.edit', $privateMessage) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow overflow-hidden max-w-4xl">
        <!-- Message header -->
        <div class="bg-gray-50 px-6 py-4 border-b">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">
                        From: <a href="{{ route('admin.characters.show', $privateMessage->sender) }}" class="text-blue-600 hover:underline font-medium">{{ $privateMessage->sender->display_name }}</a>
                    </p>
                    <p class="text-sm text-gray-500">
                        To:
                        @if($privateMessage->is_inbox_message)
                            <span class="text-purple-600 font-medium">Reader Inbox</span>
                        @elseif($privateMessage->recipient)
                            <a href="{{ route('admin.characters.show', $privateMessage->recipient) }}" class="text-blue-600 hover:underline font-medium">{{ $privateMessage->recipient->display_name }}</a>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ $privateMessage->fake_sent_at?->format('M d, Y \a\t g:i A') ?? 'Date not set' }}</p>
                    @if($privateMessage->is_read)
                        <span class="text-xs text-gray-500">Read</span>
                    @else
                        <span class="text-xs text-blue-600 font-medium">Unread</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Message content -->
        <div class="px-6 py-6">
            <div class="prose max-w-none">
                {!! nl2br(e($privateMessage->content)) !!}
            </div>
        </div>

        @if($privateMessage->phase)
            <div class="px-6 py-4 bg-purple-50 border-t">
                <p class="text-sm text-purple-700">
                    <span class="font-medium">Visible in phase:</span>
                    {{ $privateMessage->phase->name }}
                </p>
            </div>
        @endif
    </div>
</x-admin-layout>
