<x-forum-layout>
    <x-slot name="title">{{ $thread->title }} - {{ config('app.name') }}</x-slot>

    <style>
        @font-face {
            font-family: 'AlienLovecrafts';
            src: url('/fonts/lovecrafts-diary.regular.ttf') format('truetype');
        }
        @font-face {
            font-family: 'AlienAlphacode';
            src: url('/fonts/alphacode-beyond.regular.ttf') format('truetype');
        }
        @font-face {
            font-family: 'AlienEcholot';
            src: url('/fonts/echolot.regular.ttf') format('truetype');
        }
        @font-face {
            font-family: 'AlienLomtrian';
            src: url('/fonts/Lomtrian.ttf') format('truetype');
        }
        @font-face {
            font-family: 'AlienStray';
            src: url('/fonts/stray.ttf') format('truetype');
        }

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
            display: block;
            width: fit-content;
            margin: 0 auto 0.4rem;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
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

        .spoiler {
            display: inline-block;
            vertical-align: baseline;
            position: relative;
            cursor: pointer;
            padding: 0 2px;
            border-radius: 6px;
            clip-path: inset(-1px round 7px);
            box-shadow: inset 0 0 0 1px var(--color-border, #343a40);
            background: rgba(0, 0, 0, 0.45);
            transition: background 0.4s ease;
        }
        .spoiler * {
            filter: blur(5px);
            transition: filter 0.4s ease;
        }
        .spoiler:not(.revealed) * {
            user-select: none;
        }
        .spoiler::before {
            content: "Spoiler";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 0.75em;
            font-style: normal;
            letter-spacing: 0.05em;
            white-space: nowrap;
            color: var(--color-text);
            pointer-events: none;
            user-select: none;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        .spoiler.revealed {
            background: transparent;
        }
        .spoiler.revealed * {
            filter: blur(0);
        }
        .spoiler.revealed::before {
            opacity: 0;
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
            @php
            $alienFonts = [
                'lovecrafts' => ['family' => 'AlienLovecrafts', 'size' => '0.8em',  'spacing' => '0.05em'],
                'alphacode'  => ['family' => 'AlienAlphacode',  'size' => '1.0em',  'spacing' => '0.08em'],
                'echolot'    => ['family' => 'AlienEcholot',     'size' => '1.4em', 'spacing' => '0.06em'],
                'lomtrian'   => ['family' => 'AlienLomtrian',    'size' => '1.4em',  'spacing' => '0.06em'],
                'stray'      => ['family' => 'AlienStray',       'size' => '0.7em',  'spacing' => '0.05em'],
            ];
            @endphp
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
                    $isAlien    = $post->author->is_alien ?? false;
                    $alienStyle = $post->author->alien_style ?? 'lovecrafts';
                    $alienFont  = $alienFonts[$alienStyle] ?? $alienFonts['lovecrafts'];
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

                            @if($post->author->is_official)
                                <div class="user-badge" style="background: var(--color-accent); color: var(--color-bg);">
                                    ✓ Official
                                </div>
                            @endif

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
                            @if($isAlien)
                                <div class="alien-content"
                                     data-style="{{ $alienStyle }}"
                                     data-post-id="{{ $post->id }}"
                                     data-original="{{ $post->content }}"
                                     style="color: var(--color-text); line-height: 1.9;
                                            font-family: '{{ $alienFont['family'] }}', monospace;
                                            font-size: {{ $alienFont['size'] }};">
                                    <noscript>{!! nl2br(e(preg_replace(['/\[spoiler\](.*?)\[\/spoiler\]/is', '/<en>(.*?)<\/en>/is'], ['$1', '$1'], $post->content))) !!}</noscript>
                                </div>
                            @else
                                <div style="color: var(--color-text); line-height: 1.7;">
                                    {!! preg_replace('/\[spoiler\](.*?)\[\/spoiler\]/is', '<span class="spoiler" title="Click to reveal spoiler" onclick="this.classList.toggle(\'revealed\')">$1</span>', nl2br(e($post->content))) !!}
                                </div>
                            @endif

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
                                            <a href="{{ $attachment->url }}" class="lightbox-trigger block" data-caption="{{ $attachment->caption }}">
                                                <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="max-w-full rounded-lg" style="border: 1px solid #343a40; cursor: zoom-in;">
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
                                            <a href="{{ $attachment->url }}" class="lightbox-trigger block group relative aspect-square overflow-hidden rounded-lg" data-caption="{{ $attachment->caption }}" style="border: 1px solid #343a40; cursor: zoom-in;">
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
                                                <a href="{{ $attachment->url }}" class="lightbox-trigger block" data-caption="{{ $attachment->original_filename }}">
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="h-24 rounded" style="border: 1px solid #343a40; cursor: zoom-in;">
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

                            <!-- Universal Translator -->
                            @if($isAlien)
                                <div class="mt-4">
                                    <button type="button"
                                            class="alien-translate-btn text-xs px-3 py-1 rounded-full border transition-all"
                                            style="border-color: var(--color-accent); color: var(--color-accent); background: transparent; cursor: pointer;"
                                            data-post-id="{{ $post->id }}">
                                        Universal Translator
                                    </button>
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
                            @if(!$reader)
                                <a href="{{ route('reader.login') }}" class="text-xs" style="color: #6c757d;">Log in to react or report</a>
                            @else
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

    <!-- Lightbox -->
    <div x-data="{ open: false, src: '', caption: '' }"
         x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @lightbox-open.window="open = true; src = $event.detail.src; caption = $event.detail.caption"
         @keydown.escape.window="open = false"
         @click.self="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.88);">
        <div class="relative flex flex-col items-center max-w-full max-h-full">
            <button @click="open = false"
                    class="absolute -top-10 right-0 text-2xl leading-none transition-opacity hover:opacity-70"
                    style="color: #c9d1d9;">&times;</button>
            <img :src="src" class="max-w-full rounded-lg object-contain" style="max-height: 85vh; border: 1px solid #343a40;">
            <p x-show="caption" x-text="caption" class="mt-3 text-sm text-center" style="color: #8b949e;"></p>
        </div>
    </div>
</x-forum-layout>

<script>
(function () {
    // Standard letters — alien fonts remap these to their own glyphs
    const ALIEN_POOL = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';

    // Capture normal body font once so translated spans can switch to it
    const BODY_FONT = window.getComputedStyle(document.body).fontFamily;

    const FONT_MAP = {
        lovecrafts: "'AlienLovecrafts', monospace",
        alphacode:  "'AlienAlphacode', monospace",
        echolot:    "'AlienEcholot', monospace",
        lomtrian:   "'AlienLomtrian', monospace",
        stray:      "'AlienStray', monospace",
    };
    const SPACING_MAP = { lovecrafts: '0.05em', alphacode: '0.08em', echolot: '0.06em', lomtrian: '0.06em', stray: '0.05em' };
    const SIZE_MAP    = { lovecrafts: '1.1em',  alphacode: '1.0em',  echolot: '1.05em', lomtrian: '1.0em',  stray: '1.0em'  };

    // Seeded LCG — stable alien words on every page load
    function rng(seed) {
        let s = seed >>> 0;
        return () => { s = Math.imul(s, 1664525) + 1013904223 >>> 0; return s / 0x100000000; };
    }
    function wordSeed(postId, idx) {
        return ((postId * 31337) + (idx * 1234567)) & 0x7fffffff;
    }

    // Convert one English word to alien letters with variable length (80–180%)
    function alienWord(token, seed) {
        const r = rng(seed);
        const len = Math.max(1, Math.round(token.length * (0.8 + r() * 1.0)));
        return Array.from({ length: len }, () => ALIEN_POOL[Math.floor(r() * ALIEN_POOL.length)]).join('');
    }

    // Append words from a plain-text chunk as alien word spans + body-font space spans.
    function appendAlienWords(frag, text, postId, letterSpacing, wordIdxRef) {
        text.split('\n').forEach((line, li) => {
            if (li > 0) frag.appendChild(document.createElement('br'));
            const words = line.split(' ');
            words.forEach((word, wi) => {
                if (word) {
                    const seed = wordSeed(postId, wordIdxRef.i++);
                    const span = document.createElement('span');
                    span.dataset.english     = word;
                    span.dataset.alien       = alienWord(word, seed);
                    span.textContent         = span.dataset.alien;
                    span.style.letterSpacing = letterSpacing;
                    frag.appendChild(span);
                }
                if (wi < words.length - 1) {
                    const sp = document.createElement('span');
                    sp.textContent         = ' ';
                    sp.style.fontFamily    = BODY_FONT;
                    sp.style.letterSpacing = '0';
                    frag.appendChild(sp);
                }
            });
        });
    }

    // Append words from an <en>…</en> segment — always shown in body font,
    // no data-english/data-alien attributes so the translator ignores them.
    function appendEnglishWords(frag, text) {
        text.split('\n').forEach((line, li) => {
            if (li > 0) frag.appendChild(document.createElement('br'));
            const words = line.split(' ');
            words.forEach((word, wi) => {
                if (word) {
                    const span = document.createElement('span');
                    span.textContent         = word;
                    span.style.fontFamily    = BODY_FONT;
                    span.style.fontSize      = '1rem';
                    span.style.letterSpacing = 'normal';
                    frag.appendChild(span);
                }
                if (wi < words.length - 1) {
                    const sp = document.createElement('span');
                    sp.textContent         = ' ';
                    sp.style.fontFamily    = BODY_FONT;
                    sp.style.fontSize      = '1rem';
                    sp.style.letterSpacing = '0';
                    frag.appendChild(sp);
                }
            });
        });
    }

    // Process a text segment that may contain <en>…</en> tags but no [spoiler] tags.
    function processMixedContent(frag, text, postId, letterSpacing, wordIdxRef) {
        const parts = text.split(/(<en>[\s\S]*?<\/en>)/i);
        parts.forEach(part => {
            const enMatch = part.match(/^<en>([\s\S]*?)<\/en>$/i);
            if (enMatch) {
                appendEnglishWords(frag, enMatch[1]);
            } else if (part) {
                appendAlienWords(frag, part, postId, letterSpacing, wordIdxRef);
            }
        });
    }

    // Build alien DOM: handles [spoiler]…[/spoiler] and <en>…</en> tags.
    // Alien segments get scrambled glyphs; <en> segments stay in body font;
    // [spoiler] segments are hidden until clicked.
    function buildAlienContent(container, postId, style, originalText) {
        const letterSpacing = SPACING_MAP[style] || '0.05em';
        const frag          = document.createDocumentFragment();
        const wordIdxRef    = { i: 0 }; // mutable counter shared across segments

        // Split on [spoiler]…[/spoiler] first, preserving delimiters
        const parts = originalText.split(/(\[spoiler\][\s\S]*?\[\/spoiler\])/i);
        parts.forEach(part => {
            const spoilerMatch = part.match(/^\[spoiler\]([\s\S]*?)\[\/spoiler\]$/i);
            if (spoilerMatch) {
                const wrapper = document.createElement('span');
                wrapper.className = 'spoiler';
                wrapper.title     = 'Click to reveal spoiler';
                wrapper.addEventListener('click', () => wrapper.classList.toggle('revealed'));
                const innerFrag = document.createDocumentFragment();
                processMixedContent(innerFrag, spoilerMatch[1], postId, letterSpacing, wordIdxRef);
                wrapper.appendChild(innerFrag);
                frag.appendChild(wrapper);
            } else if (part) {
                processMixedContent(frag, part, postId, letterSpacing, wordIdxRef);
            }
        });

        container.innerHTML = '';
        container.appendChild(frag);
    }

    const sleep = ms => new Promise(r => setTimeout(r, ms));

    // Scramble a span through random noise then snap to the English target,
    // switching to the normal body font so the result is actually readable
    async function scrambleSpan(span, target) {
        const r = rng(Date.now() & 0xffffff);
        for (let i = 8; i > 0; i--) {
            const len = Math.max(1, target.length + Math.round((r() - 0.5) * 2));
            span.textContent = Array.from({ length: len }, () => ALIEN_POOL[Math.floor(r() * ALIEN_POOL.length)]).join('');
            await sleep(28);
        }
        span.textContent = target;
        span.style.fontFamily    = BODY_FONT;
        span.style.letterSpacing = 'normal';
        span.style.fontSize      = '1rem';
    }

    // Group word spans into sentences, splitting after words that end a sentence
    function groupIntoSentences(spans) {
        const sentences = [];
        let current = [];
        for (const span of spans) {
            current.push(span);
            if (/[.!?]$/.test(span.dataset.english || '')) {
                sentences.push(current);
                current = [];
            }
        }
        if (current.length) sentences.push(current);
        return sentences;
    }

    async function handleTranslate(btn, container, style) {
        if (btn.dataset.animating === 'true') return;
        const translating = btn.dataset.translated !== 'true';

        if (!translating) {
            // Revert to alien: restore container font and per-span alien styles
            const spacing = SPACING_MAP[style] || '0.05em';
            container.style.fontFamily = FONT_MAP[style] || FONT_MAP.lovecrafts;
            container.querySelectorAll('span[data-alien]').forEach(s => {
                s.textContent         = s.dataset.alien;
                s.style.fontFamily    = '';
                s.style.letterSpacing = spacing;
                s.style.fontSize      = '';
            });
            btn.textContent = 'Universal Translator';
            btn.dataset.translated = 'false';
            return;
        }

        btn.dataset.animating = 'true';
        btn.textContent = 'Translating\u2026';
        btn.disabled = true;

        const wordSpans = Array.from(container.querySelectorAll('span[data-english]'));
        const sentences = groupIntoSentences(wordSpans);
        for (const sentence of sentences) {
            await Promise.all(sentence.map(span => scrambleSpan(span, span.dataset.english)));
            await sleep(120);
        }

        // Reset container font so space text nodes render in the body font
        container.style.fontFamily = BODY_FONT;

        btn.textContent = 'Show Original';
        btn.dataset.translated = 'true';
        btn.dataset.animating  = 'false';
        btn.disabled = false;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.alien-content').forEach(container => {
            buildAlienContent(
                container,
                parseInt(container.dataset.postId, 10),
                container.dataset.style || 'lovecrafts',
                container.dataset.original || ''
            );
        });

        document.querySelectorAll('.alien-translate-btn').forEach(btn => {
            const postId    = btn.dataset.postId;
            const container = document.querySelector(`.alien-content[data-post-id="${postId}"]`);
            if (!container) return;
            const style = container.dataset.style || 'lovecrafts';
            btn.addEventListener('click', () => handleTranslate(btn, container, style));
        });

        document.querySelectorAll('a.lightbox-trigger').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                window.dispatchEvent(new CustomEvent('lightbox-open', {
                    detail: { src: link.href, caption: link.dataset.caption || '' }
                }));
            });
        });
    });
})();
</script>
