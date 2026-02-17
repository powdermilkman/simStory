<x-forum-layout>
    <x-slot name="title">{{ $character->display_name }} - {{ config('app.name') }}</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 text-sm" style="color: var(--color-text-muted);">
        <a href="{{ route('forum.index') }}" class="hover:opacity-80" style="color: var(--color-accent);">Forums</a>
        <span class="mx-2">›</span>
        <span>Member Profile</span>
    </div>

    <!-- Profile Card -->
    <div class="rounded-lg p-8 mb-8" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        <div class="flex items-start gap-6">
            @if($character->avatar_path)
                <img src="{{ Storage::url($character->avatar_path) }}" alt="{{ $character->display_name }}" class="h-24 w-24 rounded-full object-cover" style="border: 2px solid var(--color-accent);">
            @else
                <div class="h-24 w-24 rounded-full flex items-center justify-center text-3xl font-bold" style="background-color: var(--color-border); color: var(--color-text-muted); border: 2px solid var(--color-accent);">
                    {{ substr($character->display_name, 0, 1) }}
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-2xl font-medium mb-1" style="color: var(--color-text);">{{ $character->display_name }}</h1>
                <p class="text-sm mb-2" style="color: var(--color-text-muted);">{{ '@' . $character->username }}</p>
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($character->role)
                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded"
                              style="background-color: {{ $character->role->color }}; color: {{ $character->role->text_color }};">
                            {{ $character->role->name }}
                        </span>
                    @endif
                    @if($character->is_official)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded"
                              style="background-color: var(--color-accent); color: var(--color-bg);">
                            ✓ Official
                        </span>
                    @endif
                </div>
                
                <div class="flex gap-6 text-sm" style="color: var(--color-text-muted);">
                    <div>
                        <span class="font-medium" style="color: var(--color-text);">{{ $character->post_count }}</span>
                        posts
                    </div>
                    <div>
                        Joined <span class="font-medium" style="color: var(--color-text);">{{ $character->fake_join_date?->format('F Y') ?? 'Unknown' }}</span>
                    </div>
                </div>

                @php
                    $effectiveBio = $character->getEffectiveBio($reader ?? null);
                    $effectiveSignature = $character->getEffectiveSignature($reader ?? null);
                @endphp

                @if($effectiveBio)
                    <div class="mt-4 pt-4" style="border-top: 1px solid var(--color-border);">
                        <p style="color: var(--color-text);">{{ $effectiveBio }}</p>
                    </div>
                @endif

                @if($effectiveSignature)
                    <div class="mt-4 pt-4 italic text-sm" style="border-top: 1px solid var(--color-border); color: var(--color-text-muted);">
                        "{{ $effectiveSignature }}"
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    <h2 class="text-xl font-medium mb-4" style="color: var(--color-text);">Recent Posts</h2>
    <div class="rounded-lg overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        @forelse($posts as $post)
            <a href="{{ route('forum.thread', [$post->thread->category, $post->thread]) }}" 
               class="block p-5 transition-all duration-200"
               style="border-bottom: 1px solid var(--color-border);"
               onmouseover="this.style.backgroundColor='var(--color-surface-hover)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <p class="text-sm mb-2" style="color: var(--color-text-muted);">
                    In <span style="color: var(--color-accent);">{{ $post->thread->title }}</span>
                    · {{ $post->fake_created_at?->diffForHumans() ?? 'Unknown date' }}
                </p>
                <p style="color: var(--color-text);">{{ Str::limit(strip_tags($post->content), 150) }}</p>
            </a>
        @empty
            <div class="p-8 text-center" style="color: var(--color-text-muted);">
                No posts yet.
            </div>
        @endforelse
    </div>
</x-forum-layout>
