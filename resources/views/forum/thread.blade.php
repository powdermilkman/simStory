<x-forum-layout>
    <x-slot name="title">{{ $thread->title }} - {{ config('app.name') }}</x-slot>

    <style>
        .post-container {
            background-color: #23272b;
            border: 1px solid #343a40;
            border-radius: 6px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .post-header {
            background-color: #14181c;
            border-bottom: 1px solid #343a40;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            color: #8b949e;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .post-body {
            display: flex;
        }

        .user-sidebar {
            width: 180px;
            min-width: 180px;
            background-color: #1e2124;
            border-right: 1px solid #343a40;
            padding: 1.25rem 1rem;
            text-align: center;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--color-border), var(--color-accent));
            border-radius: 50%;
            margin: 0 auto 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .username {
            color: var(--color-accent);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .username a {
            color: inherit;
            text-decoration: none;
        }

        .username a:hover {
            text-decoration: underline;
        }

        .user-badge {
            display: inline-block;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: white;
        }

        .user-stats {
            font-size: 0.75rem;
            color: #8b949e;
            line-height: 1.6;
        }

        .user-stats .stat-value {
            color: #c9d1d9;
            font-weight: 500;
        }

        .rating-display {
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid #343a40;
        }

        .rating-label {
            font-size: 0.7rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .rating-bytes {
            display: flex;
            justify-content: center;
            gap: 3px;
        }

        .byte {
            width: 18px;
            height: 18px;
            background-color: #3a3a3a;
            border-radius: 3px;
        }

        .byte.filled {
            background-color: #f0b400;
            box-shadow: 0 0 6px rgba(240, 180, 0, 0.4);
        }

        .rating-text {
            font-size: 0.75rem;
            color: #f0b400;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .post-content {
            flex: 1;
            padding: 1.25rem 1.5rem;
            min-width: 0;
        }

        .post-content p {
            color: #c9d1d9;
            line-height: 1.7;
            margin-bottom: 1rem;
        }

        .user-signature {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px dashed #343a40;
            font-style: italic;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .post-footer {
            background-color: #14181c;
            border-top: 1px solid #343a40;
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }

        .edit-info {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .reaction-emoji-btn {
            font-size: 1.3rem;
            width: 2.1rem;
            height: 2.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: transform 0.1s ease, background 0.15s ease;
            line-height: 1;
        }

        .reaction-emoji-btn:hover {
            transform: scale(1.3);
        }

        .official-tag {
            display: inline-block;
            background-color: #3d2a10;
            color: #e8923a;
            font-size: 0.65rem;
            padding: 0.15rem 0.5rem;
            border-radius: 2px;
            margin-top: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .breadcrumb-nav {
            background-color: #23272b;
            border: 1px solid #343a40;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .breadcrumb-nav a {
            color: var(--color-accent);
            text-decoration: none;
        }

        .breadcrumb-nav a:hover {
            text-decoration: underline;
        }

        .breadcrumb-nav .separator {
            color: #6c757d;
            margin: 0 0.4rem;
        }

        .sidebar-card {
            background-color: #23272b;
            border: 1px solid #343a40;
            border-radius: 6px;
            overflow: hidden;
            position: sticky;
            top: 1rem;
        }

        .sidebar-card-header {
            background-color: #14181c;
            border-bottom: 1px solid #343a40;
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: #e6edf3;
            font-size: 0.9rem;
        }

        .trending-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #343a40;
            transition: background-color 0.15s;
        }

        .trending-item:last-child {
            border-bottom: none;
        }

        .trending-item:hover {
            background-color: #2a2e33;
        }

        .trending-title {
            color: var(--color-accent);
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.2rem;
        }

        .trending-title a {
            color: inherit;
            text-decoration: none;
        }

        .trending-title a:hover {
            text-decoration: underline;
        }

        .trending-meta {
            color: #8b949e;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .post-body {
                flex-direction: column;
            }

            .user-sidebar {
                width: 100%;
                min-width: 100%;
                border-right: none;
                border-bottom: 1px solid #343a40;
                padding: 1rem;
            }

            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .thread-layout {
                flex-direction: column;
            }

            .trending-sidebar {
                display: none !important;
            }

            .breadcrumb-nav {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start !important;
            }
        }
    </style>

    <div class="flex gap-6 thread-layout">
        <!-- Main Content -->
        <div class="flex-1 min-w-0">

            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div>
                    <a href="{{ route('forum.index') }}">Forums</a>
                    <span class="separator"><i class="bi bi-chevron-right"></i></span>
                    <a href="{{ route('forum.category', $category) }}">{{ $category->name }}</a>
                    <span class="separator"><i class="bi bi-chevron-right"></i></span>
                    <span style="color: #c9d1d9;">{{ Str::limit($thread->title, 40) }}</span>
                </div>
                <a href="{{ route('forum.category', $category) }}">Return to {{ $category->name }}</a>
            </nav>

            <!-- Thread Title & Meta -->
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-1">
                    @if($thread->is_pinned)
                        <span class="text-xs px-2 py-0.5 rounded" style="background-color: var(--color-accent); color: var(--color-bg);">Pinned</span>
                    @endif
                    @if($thread->is_locked)
                        <span class="text-xs px-2 py-0.5 rounded" style="background-color: #343a40; color: #8b949e;">Locked</span>
                    @endif
                </div>
                <h1 style="color: #e6edf3; font-size: 1.35rem; font-weight: 600; margin-bottom: 0.25rem;">{{ $thread->title }}</h1>
                <div style="color: #8b949e; font-size: 0.8rem;">
                    <i class="bi bi-chat-dots"></i> {{ $posts->count() }} {{ Str::plural('post', $posts->count()) }}
                    @if($thread->view_count)
                        <span class="mx-2">|</span>
                        <i class="bi bi-eye"></i> {{ number_format($thread->view_count) }} views
                    @endif
                </div>
            </div>

            <!-- Posts -->
            @foreach($posts as $postIndex => $post)
                @php
                    $highlightColor = $post->author->role?->post_highlight_color ?? null;
                    if ($highlightColor) {
                        $hr = hexdec(substr(ltrim($highlightColor, '#'), 0, 2));
                        $hg = hexdec(substr(ltrim($highlightColor, '#'), 2, 2));
                        $hb = hexdec(substr(ltrim($highlightColor, '#'), 4, 2));
                        $hdrBg   = "rgba($hr,$hg,$hb,0.12)";
                        $sbarBg  = "rgba($hr,$hg,$hb,0.08)";
                    }
                    $reactionCounts  = $post->reactionCounts();
                    $currentReaction = $reader
                        ? \App\Models\Reaction::where('post_id', $post->id)->where('reader_id', $reader->id)->value('type')
                        : null;
                @endphp

                <article class="post-container" @if($highlightColor) style="border-color: {{ $highlightColor }};" @endif>
                    <!-- Post Header -->
                    <div class="post-header" @if($highlightColor) style="background-color: {{ $hdrBg }}; border-bottom-color: {{ $highlightColor }};" @endif>
                        <span>
                            <i class="bi bi-clock"></i>
                            {{ $post->fake_created_at?->format('F j, Y \a\t g:i A') ?? 'Unknown date' }}
                            @if($post->fake_edited_at)
                                <span style="margin-left: 0.5rem; color: #6c757d;">(edited)</span>
                            @endif
                        </span>
                        <span>#{{ $postIndex + 1 }}</span>
                    </div>

                    <!-- Post Body -->
                    <div class="post-body">
                        <!-- User Sidebar -->
                        <aside class="user-sidebar" @if($highlightColor) style="background-color: {{ $sbarBg }}; border-right-color: {{ $highlightColor }};" @endif>
                            <div class="user-avatar" @if($highlightColor) style="background: linear-gradient(135deg, rgba({{ $hr }},{{ $hg }},{{ $hb }},0.5), {{ $highlightColor }});" @endif>
                                @if($post->author->avatar_path)
                                    <img src="{{ Storage::url($post->author->avatar_path) }}" alt="{{ $post->author->display_name }}">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                            </div>

                            <div class="username" @if($highlightColor) style="color: {{ $highlightColor }};" @endif>
                                <a href="{{ route('forum.profile', $post->author) }}">{{ $post->author->display_name }}</a>
                            </div>

                            @if($post->author->role)
                                <div class="user-badge" style="background: {{ $post->author->role->color }}; color: {{ $post->author->role->text_color }};">
                                    {{ $post->author->role->name }}
                                </div>
                            @endif

                            <div class="user-stats" style="margin-top: 0.5rem;">
                                <div><span class="stat-value">{{ number_format($post->author->post_count) }}</span> posts</div>
                            </div>

                            <!-- Bytes Rating -->
                            @if($post->author->show_bytes)
                                @php $effectiveBytes = $post->author->getEffectiveBytes($reader ?? null); @endphp
                                <div class="rating-display">
                                    <div class="rating-label">Bytes</div>
                                    <div class="rating-bytes">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="byte {{ $i <= $effectiveBytes ? 'filled' : '' }}"></div>
                                        @endfor
                                    </div>
                                    <div class="rating-text">{{ $effectiveBytes }}/5 Bytes</div>
                                </div>
                            @endif
                        </aside>

                        <!-- Post Content -->
                        <div class="post-content" @if($highlightColor) style="background-color: {{ $sbarBg }};" @endif>
                            <div style="color: var(--color-text); line-height: 1.7;">
                                {!! nl2br(e($post->content)) !!}
                            </div>

                            <!-- Inline Images -->
                            @php
                                $inlineAttachments = $post->attachments->filter(fn($a) => $a->shouldDisplayInline());
                                $galleryAttachments = $post->attachments->filter(fn($a) => $a->shouldDisplayAsGallery());
                                $fileAttachments = $post->attachments->filter(fn($a) => $a->shouldDisplayAsAttachment());
                            @endphp

                            @if($inlineAttachments->count() > 0)
                                <div class="mt-4 space-y-4">
                                    @foreach($inlineAttachments->sortBy('sort_order') as $attachment)
                                        <figure>
                                            <a href="{{ $attachment->url }}" target="_blank" class="block">
                                                <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="max-w-full rounded-lg" style="border: 1px solid #343a40;">
                                            </a>
                                            @if($attachment->caption)
                                                <figcaption class="mt-2 text-sm text-center" style="color: #8b949e;">{{ $attachment->caption }}</figcaption>
                                            @endif
                                        </figure>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Gallery Images -->
                            @if($galleryAttachments->count() > 0)
                                <div class="mt-4 pt-4" style="border-top: 1px solid #343a40;">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach($galleryAttachments->sortBy('sort_order') as $attachment)
                                            <a href="{{ $attachment->url }}" target="_blank" class="block group relative aspect-square overflow-hidden rounded-lg" style="border: 1px solid #343a40;">
                                                <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                                @if($attachment->caption)
                                                    <div class="absolute bottom-0 left-0 right-0 p-2 text-xs text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                                        {{ $attachment->caption }}
                                                    </div>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- File Attachments -->
                            @if($fileAttachments->count() > 0)
                                <div class="mt-4 pt-4" style="border-top: 1px solid #343a40;">
                                    <p class="text-xs mb-2" style="color: #8b949e;">Attachments:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($fileAttachments->sortBy('sort_order') as $attachment)
                                            @if($attachment->isImage())
                                                <a href="{{ $attachment->url }}" target="_blank" class="block">
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="h-24 rounded" style="border: 1px solid #343a40;">
                                                </a>
                                            @else
                                                <a href="{{ $attachment->url }}" target="_blank" class="flex items-center gap-2 text-sm px-3 py-2 rounded transition-opacity hover:opacity-80" style="background-color: #343a40; color: #8b949e;">
                                                    <span>{{ $attachment->getFileIcon() }}</span>
                                                    <span>{{ $attachment->original_filename }}</span>
                                                    <span class="text-xs opacity-70">({{ $attachment->human_file_size }})</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Choice/Poll -->
                            @if($post->choice)
                                @php
                                    $hasChosen = $reader ? $reader->hasCompletedChoice($post->choice) : false;
                                    $chosenOption = $reader ? $reader->getChosenOptionForChoice($post->choice) : null;
                                    $isPoll = $post->choice->type === 'poll';
                                @endphp

                                <div class="mt-6" style="border-top: 1px solid #343a40; padding-top: 1.25rem;">
                                    <p class="text-sm font-medium mb-3" style="color: #c9d1d9;">
                                        {{ $post->choice->prompt_text }}
                                    </p>

                                    @if($isPoll)
                                        @if($hasChosen || !$reader)
                                            @php
                                                $resultSource = $chosenOption ?? $post->choice->options->first();
                                                $totalVotes = $resultSource ? $resultSource->getResultTotalVotes() : 0;
                                            @endphp
                                            <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                                                @foreach($post->choice->options as $optionIndex => $option)
                                                    @php
                                                        $voteCount = $resultSource ? $resultSource->getResultVoteFor($optionIndex) : 0;
                                                        $percentage = $resultSource ? $resultSource->getResultPercentageFor($optionIndex) : 0;
                                                        $isChosen = $chosenOption && $chosenOption->id === $option->id;
                                                    @endphp
                                                    <div>
                                                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.3rem;">
                                                            <span style="color: {{ $isChosen ? '#c9d1d9' : '#8b949e' }}; font-weight: {{ $isChosen ? '500' : '400' }};">
                                                                {{ $option->label }}
                                                                @if($isChosen) <span style="color: #8b949e;">✓</span> @endif
                                                            </span>
                                                            <span style="color: #6c757d;">{{ $percentage }}%</span>
                                                        </div>
                                                        <div style="background-color: #2a2e33; border-radius: 2px; height: 6px; overflow: hidden;">
                                                            <div style="width: {{ $percentage }}%; height: 6px; border-radius: 2px; background-color: {{ $isChosen ? '#5a6370' : '#3a3f46' }};"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p style="font-size: 0.75rem; color: #6c757d; margin-top: 0.6rem;">
                                                {{ number_format($totalVotes) }} {{ Str::plural('vote', $totalVotes) }}
                                                @if($hasChosen)
                                                    · you voted: <span style="color: #8b949e;">{{ $chosenOption->label }}</span>
                                                @else
                                                    · <a href="{{ route('reader.login') }}" style="color: #8b949e;">sign in to vote</a>
                                                @endif
                                            </p>
                                        @else
                                            <form method="POST" action="{{ route('choice.make', $post->choice) }}">
                                                @csrf
                                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                                    @foreach($post->choice->options as $option)
                                                        <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.75rem; border-radius: 4px; border: 1px solid #343a40; cursor: pointer; font-size: 0.875rem; color: #c9d1d9;"
                                                               onmouseover="this.style.borderColor='#5a6370'"
                                                               onmouseout="this.style.borderColor='#343a40'">
                                                            <input type="radio" name="option_id" value="{{ $option->id }}" required style="accent-color: #8b949e;">
                                                            <span>{{ $option->label }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <button type="submit" style="margin-top: 0.75rem; padding: 0.35rem 1rem; font-size: 0.8rem; border-radius: 4px; background-color: #343a40; color: #c9d1d9; border: 1px solid #5a6370; cursor: pointer;"
                                                        onmouseover="this.style.backgroundColor='#3f4650'"
                                                        onmouseout="this.style.backgroundColor='#343a40'">Vote</button>
                                            </form>
                                        @endif
                                    @else
                                        @if($hasChosen)
                                            <p style="font-size: 0.875rem; color: #8b949e;">
                                                You chose: <span style="color: #c9d1d9; font-weight: 500;">{{ $chosenOption->label }}</span>
                                            </p>
                                        @elseif($reader)
                                            <form method="POST" action="{{ route('choice.make', $post->choice) }}">
                                                @csrf
                                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                                    @foreach($post->choice->options as $option)
                                                        <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.6rem 0.75rem; border-radius: 4px; border: 1px solid #343a40; cursor: pointer; font-size: 0.875rem; color: #c9d1d9;"
                                                               onmouseover="this.style.borderColor='#5a6370'"
                                                               onmouseout="this.style.borderColor='#343a40'">
                                                            <input type="radio" name="option_id" value="{{ $option->id }}" required style="accent-color: #8b949e; margin-top: 0.2rem; flex-shrink: 0;">
                                                            <div>
                                                                <span>{{ $option->label }}</span>
                                                                @if($option->description)
                                                                    <span style="display: block; font-size: 0.8rem; color: #6c757d; margin-top: 0.15rem;">{{ $option->description }}</span>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <button type="submit" style="margin-top: 0.75rem; padding: 0.35rem 1rem; font-size: 0.8rem; border-radius: 4px; background-color: #343a40; color: #c9d1d9; border: 1px solid #5a6370; cursor: pointer;"
                                                        onmouseover="this.style.backgroundColor='#3f4650'"
                                                        onmouseout="this.style.backgroundColor='#343a40'">Make Your Choice</button>
                                            </form>
                                        @else
                                            <p style="font-size: 0.875rem; color: #8b949e;">
                                                <a href="{{ route('reader.login') }}" style="color: #8b949e; text-decoration: underline;">Sign in</a> to make a choice and unlock new content.
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            <!-- Signature -->
                            @php $effectiveSignature = $post->author->getEffectiveSignature($reader ?? null); @endphp
                            @if($effectiveSignature)
                                <div class="user-signature">{{ $effectiveSignature }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Post Footer -->
                    <div class="post-footer" @if($highlightColor) style="border-top-color: {{ $highlightColor }}; background-color: {{ $hdrBg }};" @endif>
                        <div class="edit-info">
                            @if($post->fake_edited_at)
                                <i class="bi bi-pencil-square"></i> Last edited {{ $post->fake_edited_at->format('F j, Y \a\t g:i A') }}
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            @if($reader)
                                {{-- Reaction flyout picker --}}
                                <div style="position: relative;"
                                     x-data='{
                                         open: false,
                                         myReaction: @json($currentReaction),
                                         emojiMap: @json(\App\Models\Reaction::TYPES),
                                         submitting: false
                                     }'
                                     @click.outside="open = false">

                                    {{-- Flyout emoji pill --}}
                                    <div x-show="open"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         style="position: absolute; bottom: calc(100% + 6px); right: 0; background: #1e2124; border: 1px solid #3a3f46; border-radius: 24px; padding: 5px 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.5); z-index: 20; transform-origin: bottom right;">
                                        <div style="display: flex; flex-direction: row; gap: 2px; white-space: nowrap;">
                                        @foreach(\App\Models\Reaction::TYPES as $type => $emoji)
                                            <button type="button"
                                                    class="reaction-emoji-btn"
                                                    title="{{ ucfirst($type) }}"
                                                    :style="myReaction === '{{ $type }}' ? 'background: rgba(92,158,173,0.25);' : ''"
                                                    @click="if(submitting) return; submitting = true;
                                                        const t = '{{ $type }}';
                                                        fetch('{{ route('posts.react', $post) }}', {
                                                            method: 'POST',
                                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                            body: JSON.stringify({ type: t })
                                                        })
                                                        .then(r => r.json())
                                                        .then(data => {
                                                            myReaction = data.reacted ? t : null;
                                                            open = false;
                                                        })
                                                        .finally(() => submitting = false);">{{ $emoji }}</button>
                                        @endforeach
                                        </div>
                                    </div>

                                    {{-- Trigger button --}}
                                    <button type="button"
                                            @click="open = !open"
                                            class="text-xs px-3 py-1 rounded border transition-opacity hover:opacity-80"
                                            :style="myReaction
                                                ? 'border-color: #5c9ead; color: #5c9ead; background: rgba(92,158,173,0.08);'
                                                : 'border-color: #343a40; color: #8b949e; background: transparent;'"
                                            title="React to this post">
                                        <span x-text="myReaction ? emojiMap[myReaction] : '+ Add Reaction'"></span>
                                    </button>
                                </div>

                                {{-- Report button --}}
                                <div x-data="{ showReportModal: false }">
                                    <button
                                        type="button"
                                        @click="showReportModal = true"
                                        class="text-xs px-3 py-1 rounded border transition-opacity hover:opacity-80"
                                        style="border-color: #343a40; color: #8b949e; background: transparent;">
                                        <i class="bi bi-flag"></i> Report
                                    </button>

                                    <!-- Report Modal -->
                                    <div x-show="showReportModal"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                         style="background-color: rgba(0,0,0,0.5);"
                                         @click.self="showReportModal = false">
                                        <div class="w-full max-w-md rounded-lg p-6" style="background-color: var(--color-surface); border: 1px solid #343a40;">
                                            <h3 class="text-lg font-medium mb-4" style="color: var(--color-text);">Report Post</h3>
                                            <form method="POST" action="{{ route('posts.report', $post) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium mb-2" style="color: #8b949e;">Reason</label>
                                                    <select name="reason" required class="w-full rounded px-3 py-2" style="background-color: var(--color-bg); border: 1px solid #343a40; color: var(--color-text);">
                                                        <option value="">Select a reason...</option>
                                                        @foreach(\App\Models\PostReport::REASONS as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium mb-2" style="color: #8b949e;">Additional details (optional)</label>
                                                    <textarea name="details" rows="3" maxlength="1000" class="w-full rounded px-3 py-2" style="background-color: var(--color-bg); border: 1px solid #343a40; color: var(--color-text);" placeholder="Provide more context..."></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showReportModal = false" class="px-4 py-2 rounded" style="color: #8b949e;">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 rounded" style="background-color: var(--color-accent); color: var(--color-bg);">Submit Report</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach

        </div>

        <!-- Trending Sidebar -->
        <aside class="trending-sidebar" style="width: 240px; min-width: 240px;">
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <i class="bi bi-fire"></i> Trending Threads
                </div>
                <div>
                    @forelse($trendingThreads as $trendingThread)
                        <div class="trending-item">
                            <div class="trending-title">
                                <a href="{{ route('forum.thread', [$trendingThread->category ?? $category, $trendingThread]) }}">
                                    {{ Str::limit($trendingThread->title, 45) }}
                                </a>
                            </div>
                            <div class="trending-meta">
                                by <strong style="color: #c9d1d9;">{{ $trendingThread->author->display_name }}</strong>
                                @if($trendingThread->view_count)
                                    | <span style="color: var(--color-accent-warm);">{{ number_format($trendingThread->view_count) }} views</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="trending-item" style="color: #8b949e; font-size: 0.875rem;">No trending threads yet.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</x-forum-layout>
