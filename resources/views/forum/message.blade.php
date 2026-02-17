<x-forum-layout>
    <x-slot name="title">{{ $message->subject }} - {{ config('app.name') }}</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 text-sm" style="color: var(--color-text-muted);">
        <a href="{{ route('forum.index') }}" class="hover:opacity-80" style="color: var(--color-accent);">Forums</a>
        <span class="mx-2">›</span>
        <a href="{{ route('forum.messages') }}" class="hover:opacity-80" style="color: var(--color-accent);">Messages</a>
        <span class="mx-2">›</span>
        <span>{{ Str::limit($message->subject, 40) }}</span>
    </div>

    <div class="rounded-lg overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        <!-- Header -->
        <div class="p-6" style="background-color: rgba(0,0,0,0.2); border-bottom: 1px solid var(--color-border);">
            <h1 class="text-xl font-medium mb-4" style="color: var(--color-text);">{{ $message->subject }}</h1>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        @if($message->sender->avatar_path)
                            <img src="{{ Storage::url($message->sender->avatar_path) }}" alt="{{ $message->sender->display_name }}" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full flex items-center justify-center font-bold" style="background-color: var(--color-border); color: var(--color-text-muted);">
                                {{ substr($message->sender->display_name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <span class="text-sm" style="color: var(--color-text-muted);">From:</span>
                            <a href="{{ route('forum.profile', $message->sender) }}" class="font-medium hover:opacity-80" style="color: var(--color-accent);">
                                {{ $message->sender->display_name }}
                            </a>
                        </div>
                    </div>
                    @if(!$message->is_inbox_message)
                        <span style="color: var(--color-text-muted);">→</span>
                        <div class="flex items-center gap-2">
                            @if($message->recipient && $message->recipient->avatar_path)
                                <img src="{{ Storage::url($message->recipient->avatar_path) }}" alt="{{ $message->recipient->display_name }}" class="h-10 w-10 rounded-full object-cover">
                            @elseif($message->recipient)
                                <div class="h-10 w-10 rounded-full flex items-center justify-center font-bold" style="background-color: var(--color-border); color: var(--color-text-muted);">
                                    {{ substr($message->recipient->display_name, 0, 1) }}
                                </div>
                            @endif
                            @if($message->recipient)
                                <div>
                                    <span class="text-sm" style="color: var(--color-text-muted);">To:</span>
                                    <a href="{{ route('forum.profile', $message->recipient) }}" class="font-medium hover:opacity-80" style="color: var(--color-accent);">
                                        {{ $message->recipient->display_name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <span class="text-sm" style="color: var(--color-text-muted);">
                    {{ $message->fake_sent_at?->format('F j, Y \a\t g:i A') ?? 'Unknown date' }}
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="prose prose-invert max-w-none" style="color: var(--color-text);">
                {!! nl2br(e($message->content)) !!}
            </div>
        </div>
    </div>
</x-forum-layout>
