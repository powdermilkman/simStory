<x-forum-layout>
    <x-slot name="title">Messages - {{ config('app.name') }}</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 text-sm" style="color: var(--color-text-muted);">
        <a href="{{ route('forum.index') }}" class="hover:opacity-80" style="color: var(--color-accent);">Forums</a>
        <span class="mx-2">›</span>
        <span>Messages</span>
    </div>

    @if($inboxMessages)
        <!-- Personal Inbox Section -->
        <div class="mb-8">
            <h1 class="text-2xl font-medium mb-2" style="color: var(--color-text);">Your Inbox</h1>
            <p class="mb-4" style="color: var(--color-text-muted);">Private messages sent to you.</p>

            <div class="rounded-lg overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                @forelse($inboxMessages as $message)
                    <a href="{{ route('forum.message', $message) }}"
                       class="block p-5 transition-all duration-200 {{ !$message->is_read ? 'font-bold' : '' }}"
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
        </div>
    @endif

    <!-- Archived Messages Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-medium mb-2" style="color: var(--color-text);">Messages Archive</h1>
        <p class="mb-4" style="color: var(--color-text-muted);">Archived private conversations shared with the community.</p>

        <div class="rounded-lg overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            @forelse($archivedMessages as $message)
                <a href="{{ route('forum.message', $message) }}"
                   class="block p-5 transition-all duration-200"
                   style="border-bottom: 1px solid var(--color-border);"
                   onmouseover="this.style.backgroundColor='var(--color-surface-hover)'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-medium" style="color: var(--color-text);">{{ $message->subject }}</h3>
                        <span class="text-sm" style="color: var(--color-text-muted);">{{ $message->fake_sent_at?->diffForHumans() ?? 'Unknown date' }}</span>
                    </div>
                    <p class="text-sm" style="color: var(--color-text-muted);">
                        From <span style="color: var(--color-accent);">{{ $message->sender->display_name }}</span>
                        to <span style="color: var(--color-accent);">{{ $message->recipient->display_name }}</span>
                    </p>
                </a>
            @empty
                <div class="p-8 text-center" style="color: var(--color-text-muted);">
                    No messages in the archive yet.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $archivedMessages->links() }}
        </div>
    </div>
</x-forum-layout>
