<x-forum-layout>
    <x-slot name="title">Messages - {{ config('app.name') }}</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 text-sm" style="color: var(--color-text-muted);">
        <a href="{{ route('forum.index') }}" class="hover:opacity-80" style="color: var(--color-accent);">Forums</a>
        <span class="mx-2">›</span>
        <span>Messages</span>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-medium mb-2" style="color: var(--color-text);">Messages</h1>
        <p class="mb-4" style="color: var(--color-text-muted);">Private messages sent to you.</p>

        @if($inboxMessages)
            <div class="rounded-lg overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                @forelse($inboxMessages as $message)
                    <a href="{{ route('forum.message', $message) }}"
                       class="block p-5 transition-all duration-200"
                       style="border-bottom: 1px solid var(--color-border);"
                       onmouseover="this.style.backgroundColor='var(--color-surface-hover)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium" style="color: var(--color-text);">
                                @if(!$message->is_read)
                                    <span class="inline-block w-2 h-2 rounded-full mr-2" style="background-color: var(--color-accent);"></span>
                                @endif
                                {{ $message->subject }}
                            </h3>
                            <span class="text-sm" style="color: var(--color-text-muted);">{{ $message->fake_sent_at?->diffForHumans() ?? 'Unknown date' }}</span>
                        </div>
                        <p class="text-sm" style="color: var(--color-text-muted);">
                            From <span style="color: var(--color-accent);">{{ $message->sender->display_name }}</span>
                            @if($message->is_read)
                                <span class="ml-2 text-xs" style="color: var(--color-text-muted);">· Read</span>
                            @else
                                <span class="ml-2 text-xs font-semibold" style="color: var(--color-accent);">· Unread</span>
                            @endif
                        </p>
                    </a>
                @empty
                    <div class="p-8 text-center" style="color: var(--color-text-muted);">
                        Your inbox is empty.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $inboxMessages->links('pagination::tailwind', ['pageName' => 'inbox_page']) }}
            </div>
        @else
            <div class="rounded-lg p-8 text-center" style="background-color: var(--color-surface); border: 1px solid var(--color-border); color: var(--color-text-muted);">
                <a href="{{ route('reader.login') }}" style="color: var(--color-accent);">Sign in</a> to view your messages.
            </div>
        @endif
    </div>
</x-forum-layout>
