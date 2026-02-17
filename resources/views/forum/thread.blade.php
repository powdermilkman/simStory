<x-forum-layout>
    <x-slot name="title">{{ $thread->title }} - {{ config('app.name') }}</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 text-sm" style="color: var(--color-text-muted);">
        <a href="{{ route('forum.index') }}" class="hover:opacity-80" style="color: var(--color-accent);">Forums</a>
        <span class="mx-2">›</span>
        <a href="{{ route('forum.category', $category) }}" class="hover:opacity-80" style="color: var(--color-accent);">{{ $category->name }}</a>
        <span class="mx-2">›</span>
        <span>{{ Str::limit($thread->title, 40) }}</span>
    </div>

    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            @if($thread->is_pinned)
                <span class="text-xs px-2 py-0.5 rounded" style="background-color: var(--color-accent); color: var(--color-bg);">Pinned</span>
            @endif
            @if($thread->is_locked)
                <span class="text-xs px-2 py-0.5 rounded" style="background-color: var(--color-border); color: var(--color-text-muted);">Locked</span>
            @endif
        </div>
        <h1 class="text-2xl font-medium" style="color: var(--color-text);">{{ $thread->title }}</h1>
    </div>

    <!-- Posts -->
    <div class="space-y-4">
        @foreach($posts as $post)
            <div class="rounded-lg overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                <div class="flex flex-col md:flex-row">
                    <!-- Author sidebar -->
                    <div class="md:w-48 p-4 flex md:flex-col items-center md:items-center gap-4 md:gap-2" style="background-color: rgba(0,0,0,0.2); border-bottom: 1px solid var(--color-border); md:border-bottom: none; md:border-right: 1px solid var(--color-border);">
                        @if($post->author->avatar_path)
                            <img src="{{ Storage::url($post->author->avatar_path) }}" alt="{{ $post->author->display_name }}" class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full flex items-center justify-center text-xl font-bold" style="background-color: var(--color-border); color: var(--color-text-muted);">
                                {{ substr($post->author->display_name, 0, 1) }}
                            </div>
                        @endif
                        <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                            <div style="font-weight: 500; width: 100%; text-align: center;">
                                <a href="{{ route('forum.profile', $post->author) }}" class="hover:opacity-80" style="color: var(--color-accent);">
                                    {{ $post->author->display_name }}
                                </a>
                            </div>
                            @if($post->author->role)
                                <div style="margin-top: 0.25rem; width: 100%; text-align: center;">
                                    <span style="display: inline-block; padding: 0.125rem 0.5rem; font-size: 0.75rem; font-weight: 500; border-radius: 0.25rem; background-color: {{ $post->author->role->color }}; color: {{ $post->author->role->text_color }};">
                                        {{ $post->author->role->name }}
                                    </span>
                                </div>
                            @endif
                            @if($post->author->is_official)
                                <div style="margin-top: 0.25rem; width: 100%; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; font-size: 0.75rem; font-weight: 500; border-radius: 0.25rem; background-color: var(--color-accent); color: var(--color-bg);">
                                        ✓ Official
                                    </span>
                                </div>
                            @endif
                            <p style="width: 100%; text-align: center; font-size: 0.75rem; margin-top: 0.25rem; color: var(--color-text-muted);">{{ $post->author->post_count }} posts</p>

                            <!-- Bytes Rating -->
                            @if($post->author->show_bytes)
                                @php $effectiveBytes = $post->author->getEffectiveBytes($reader ?? null); @endphp
                                <div class="mt-2">
                                    <p class="text-xs mb-1" style="color: var(--color-text-muted);">Bytes</p>
                                    <div class="flex gap-0.5 justify-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="w-5 h-5 rounded flex items-center justify-center text-xs"
                                                 style="background-color: {{ $i <= $effectiveBytes ? 'var(--color-accent)' : 'var(--color-border)' }}; color: {{ $i <= $effectiveBytes ? 'var(--color-bg)' : 'var(--color-text-muted)' }};">
                                                ★
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Post content -->
                    <div class="flex-1 p-5">
                        <div class="flex justify-between items-center mb-4 pb-3" style="border-bottom: 1px solid var(--color-border);">
                            <span class="text-sm" style="color: var(--color-text-muted);">
                                {{ $post->fake_created_at?->format('F j, Y \a\t g:i A') ?? 'Unknown date' }}
                                @if($post->fake_edited_at)
                                    <span style="color: var(--color-text-muted);">(edited)</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="prose prose-invert max-w-none" style="color: var(--color-text);">
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
                                            <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="max-w-full rounded-lg" style="border: 1px solid var(--color-border);">
                                        </a>
                                        @if($attachment->caption)
                                            <figcaption class="mt-2 text-sm text-center" style="color: var(--color-text-muted);">{{ $attachment->caption }}</figcaption>
                                        @endif
                                    </figure>
                                @endforeach
                            </div>
                        @endif

                        <!-- Gallery Images -->
                        @if($galleryAttachments->count() > 0)
                            <div class="mt-4 pt-4" style="border-top: 1px solid var(--color-border);">
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($galleryAttachments->sortBy('sort_order') as $attachment)
                                        <a href="{{ $attachment->url }}" target="_blank" class="block group relative aspect-square overflow-hidden rounded-lg" style="border: 1px solid var(--color-border);">
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
                            <div class="mt-4 pt-4" style="border-top: 1px solid var(--color-border);">
                                <p class="text-xs mb-2" style="color: var(--color-text-muted);">Attachments:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($fileAttachments->sortBy('sort_order') as $attachment)
                                        @if($attachment->isImage())
                                            <a href="{{ $attachment->url }}" target="_blank" class="block">
                                                <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="h-24 rounded" style="border: 1px solid var(--color-border);">
                                            </a>
                                        @else
                                            <a href="{{ $attachment->url }}" target="_blank" class="flex items-center gap-2 text-sm px-3 py-2 rounded transition-opacity hover:opacity-80" style="background-color: var(--color-border); color: var(--color-text-muted);">
                                                <span>{{ $attachment->getFileIcon() }}</span>
                                                <span>{{ $attachment->original_filename }}</span>
                                                <span class="text-xs opacity-70">({{ $attachment->human_file_size }})</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Reactions -->
                        @php 
                            $reactionCounts = $post->reactionCounts(); 
                            $reader = Auth::guard('reader')->user();
                            $readerReactions = $reader 
                                ? \App\Models\Reaction::where('post_id', $post->id)->where('reader_id', $reader->id)->pluck('type')->toArray() 
                                : [];
                        @endphp
                        <div class="mt-4 flex flex-wrap gap-2" x-data="{ reacting: false }">
                            @if($reader)
                                {{-- Interactive reactions for logged-in readers --}}
                                @foreach(\App\Models\Reaction::TYPES as $type => $emoji)
                                    @php $count = $reactionCounts[$type] ?? 0; $hasReacted = in_array($type, $readerReactions); @endphp
                                    <button 
                                        type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded text-sm transition-all duration-200 hover:scale-105"
                                        style="background-color: {{ $hasReacted ? 'var(--color-accent)' : 'var(--color-border)' }}; 
                                               color: {{ $hasReacted ? 'var(--color-bg)' : 'inherit' }};"
                                        @click="if(reacting) return; reacting = true;
                                            fetch('{{ route('posts.react', $post) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ type: '{{ $type }}' })
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                if(data.reacted) {
                                                    $el.style.backgroundColor = 'var(--color-accent)';
                                                    $el.style.color = 'var(--color-bg)';
                                                } else {
                                                    $el.style.backgroundColor = 'var(--color-border)';
                                                    $el.style.color = 'inherit';
                                                }
                                                const countEl = $el.querySelector('.reaction-count');
                                                const newCount = data.counts['{{ $type }}'] || 0;
                                                countEl.textContent = newCount || '';
                                            })
                                            .finally(() => reacting = false);"
                                        title="React with {{ $type }}">
                                        {{ $emoji }}
                                        <span class="reaction-count" style="color: {{ $hasReacted ? 'var(--color-bg)' : 'var(--color-text-muted)' }}; opacity: 0.8;">{{ $count ?: '' }}</span>
                                    </button>
                                @endforeach
                            @else
                                {{-- Static reactions for guests --}}
                                @foreach($reactionCounts as $type => $count)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-sm" style="background-color: var(--color-border);">
                                        {{ \App\Models\Reaction::TYPES[$type] ?? $type }}
                                        <span style="color: var(--color-text-muted);">{{ $count }}</span>
                                    </span>
                                @endforeach
                                @if(count($reactionCounts) === 0)
                                    <span class="text-sm" style="color: var(--color-text-muted);">
                                        <a href="{{ route('reader.login') }}" style="color: var(--color-accent);">Sign in</a> to react
                                    </span>
                                @endif
                            @endif
                        </div>

                        <!-- Report Button -->
                        @if($reader)
                            <div class="mt-4 flex justify-end" x-data="{ showReportModal: false }">
                                <button
                                    type="button"
                                    @click="showReportModal = true"
                                    class="text-xs px-2 py-1 rounded transition-opacity hover:opacity-80"
                                    style="color: var(--color-text-muted);">
                                    Report
                                </button>

                                <!-- Report Modal -->
                                <div x-show="showReportModal"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                     style="background-color: rgba(0,0,0,0.5);"
                                     @click.self="showReportModal = false">
                                    <div class="w-full max-w-md rounded-lg p-6" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                                        <h3 class="text-lg font-medium mb-4" style="color: var(--color-text);">Report Post</h3>
                                        <form method="POST" action="{{ route('posts.report', $post) }}">
                                            @csrf
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium mb-2" style="color: var(--color-text-muted);">Reason</label>
                                                <select name="reason" required class="w-full rounded px-3 py-2" style="background-color: var(--color-bg); border: 1px solid var(--color-border); color: var(--color-text);">
                                                    <option value="">Select a reason...</option>
                                                    @foreach(\App\Models\PostReport::REASONS as $value => $label)
                                                        <option value="{{ $value }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium mb-2" style="color: var(--color-text-muted);">Additional details (optional)</label>
                                                <textarea name="details" rows="3" maxlength="1000" class="w-full rounded px-3 py-2" style="background-color: var(--color-bg); border: 1px solid var(--color-border); color: var(--color-text);" placeholder="Provide more context..."></textarea>
                                            </div>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="showReportModal = false" class="px-4 py-2 rounded" style="color: var(--color-text-muted);">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="px-4 py-2 rounded" style="background-color: var(--color-accent); color: var(--color-bg);">
                                                    Submit Report
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Signature -->
                        @php $effectiveSignature = $post->author->getEffectiveSignature($reader ?? null); @endphp
                        @if($effectiveSignature)
                            <div class="mt-6 pt-4 text-sm italic" style="border-top: 1px solid var(--color-border); color: var(--color-text-muted);">
                                {{ $effectiveSignature }}
                            </div>
                        @endif

                        <!-- Choice/Poll Point -->
                        @if($post->choice)
                            @php
                                $reader = Auth::guard('reader')->user();
                                $hasChosen = $reader ? $reader->hasCompletedChoice($post->choice) : false;
                                $chosenOption = $reader ? $reader->getChosenOptionForChoice($post->choice) : null;
                                $isPoll = $post->choice->type === 'poll';
                            @endphp
                            
                            <div class="mt-6 p-5 rounded-lg" style="background: linear-gradient(135deg, rgba(92, 158, 173, 0.1) 0%, rgba(224, 164, 88, 0.1) 100%); border: 1px solid var(--color-accent);">
                                <p class="font-medium mb-3" style="color: var(--color-accent);">
                                    @if($isPoll)
                                        📊 
                                    @endif
                                    {{ $post->choice->prompt_text }}
                                </p>
                                
                                @if($isPoll)
                                    {{-- Poll Display --}}
                                    @if($hasChosen)
                                        <p class="text-sm mb-3" style="color: var(--color-text-muted);">
                                            You voted: <span style="color: var(--color-accent-warm);">{{ $chosenOption->label }}</span>
                                        </p>
                                    @endif
                                    
                                    @if($hasChosen || !$reader)
                                        {{-- Show results based on chosen option --}}
                                        @php
                                            // Get the result votes from the chosen option, or first option for guests
                                            $resultSource = $chosenOption ?? $post->choice->options->first();
                                            $totalVotes = $resultSource ? $resultSource->getResultTotalVotes() : 0;
                                        @endphp
                                        <div class="space-y-3">
                                            @foreach($post->choice->options as $optionIndex => $option)
                                                @php
                                                    $voteCount = $resultSource ? $resultSource->getResultVoteFor($optionIndex) : 0;
                                                    $percentage = $resultSource ? $resultSource->getResultPercentageFor($optionIndex) : 0;
                                                @endphp
                                                <div>
                                                    <div class="flex justify-between text-sm mb-1">
                                                        <span style="color: var(--color-text);">
                                                            {{ $option->label }}
                                                            @if($chosenOption && $chosenOption->id === $option->id)
                                                                <span style="color: var(--color-accent);">✓</span>
                                                            @endif
                                                        </span>
                                                        <span style="color: var(--color-text-muted);">{{ number_format($voteCount) }} ({{ $percentage }}%)</span>
                                                    </div>
                                                    <div class="w-full rounded-full overflow-hidden" style="background-color: rgba(255,255,255,0.15); height: 12px;">
                                                        <div class="rounded-full" style="width: {{ $percentage }}%; height: 12px; background-color: {{ $chosenOption && $chosenOption->id === $option->id ? '#f97316' : '#14b8a6' }};"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-xs mt-3" style="color: var(--color-text-muted);">{{ number_format($totalVotes) }} total votes</p>
                                        
                                        @if(!$reader)
                                            <p class="text-sm mt-3" style="color: var(--color-text-muted);">
                                                <a href="{{ route('reader.login') }}" style="color: var(--color-accent);">Sign in</a> to vote in this poll.
                                            </p>
                                        @endif
                                    @else
                                        {{-- Voting form --}}
                                        <form method="POST" action="{{ route('choice.make', $post->choice) }}">
                                            @csrf
                                            <div class="space-y-2">
                                                @foreach($post->choice->options as $option)
                                                    <label class="flex items-center gap-3 p-3 rounded cursor-pointer transition-all duration-200"
                                                           style="background-color: var(--color-surface); border: 1px solid var(--color-border);"
                                                           onmouseover="this.style.borderColor='var(--color-accent)'"
                                                           onmouseout="this.style.borderColor='var(--color-border)'">
                                                        <input type="radio" name="option_id" value="{{ $option->id }}" required
                                                               class="text-accent" style="accent-color: var(--color-accent);">
                                                        <span style="color: var(--color-text);">{{ $option->label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <button type="submit" class="mt-4 px-6 py-2 rounded-lg font-medium transition-all duration-200"
                                                    style="background-color: var(--color-accent); color: var(--color-bg);">
                                                Vote
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    {{-- Choice Display --}}
                                    @if($hasChosen)
                                        <p class="text-sm mb-2" style="color: var(--color-text-muted);">You chose:</p>
                                        <p class="font-medium" style="color: var(--color-accent-warm);">{{ $chosenOption->label }}</p>
                                    @elseif($reader)
                                        <form method="POST" action="{{ route('choice.make', $post->choice) }}">
                                            @csrf
                                            <div class="space-y-2">
                                                @foreach($post->choice->options as $option)
                                                    <label class="flex items-center gap-3 p-3 rounded cursor-pointer transition-all duration-200"
                                                           style="background-color: var(--color-surface); border: 1px solid var(--color-border);"
                                                           onmouseover="this.style.borderColor='var(--color-accent)'"
                                                           onmouseout="this.style.borderColor='var(--color-border)'">
                                                        <input type="radio" name="option_id" value="{{ $option->id }}" required
                                                               class="text-accent" style="accent-color: var(--color-accent);">
                                                        <div>
                                                            <span style="color: var(--color-text);">{{ $option->label }}</span>
                                                            @if($option->description)
                                                                <span class="block text-sm" style="color: var(--color-text-muted);">{{ $option->description }}</span>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <button type="submit" class="mt-4 px-6 py-2 rounded-lg font-medium transition-all duration-200"
                                                    style="background-color: var(--color-accent); color: var(--color-bg);">
                                                Make Your Choice
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-sm" style="color: var(--color-text-muted);">
                                            <a href="{{ route('reader.login') }}" style="color: var(--color-accent);">Sign in</a> to make a choice and unlock new content.
                                        </p>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-forum-layout>
