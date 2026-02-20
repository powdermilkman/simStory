{{--
    Post card partial for rendering a single post in forum style.
    Used by: admin preview

    Required variables:
    - $post: Post model with author relationship loaded
    - $preview: boolean - if true, uses passed $previewContent instead of $post->content
    - $previewContent: string - content to display when $preview is true
--}}

@php
    $preview = $preview ?? false;
    $displayContent = $preview ? ($previewContent ?? $post->content) : $post->content;

    $highlightColor = $post->author->role?->post_highlight_color ?? null;
    if ($highlightColor) {
        $hr = hexdec(substr(ltrim($highlightColor, '#'), 0, 2));
        $hg = hexdec(substr(ltrim($highlightColor, '#'), 2, 2));
        $hb = hexdec(substr(ltrim($highlightColor, '#'), 4, 2));
        $hdrBg  = "rgba($hr,$hg,$hb,0.12)";
        $sbarBg = "rgba($hr,$hg,$hb,0.08)";
    }
@endphp

<div class="post-container" @if($highlightColor) style="border-color: {{ $highlightColor }};" @endif>
    {{-- Post Header --}}
    <div class="post-header" @if($highlightColor) style="background-color: {{ $hdrBg }}; border-bottom-color: {{ $highlightColor }};" @endif>
        <span>
            {{ $post->fake_created_at?->format('F j, Y \a\t g:i A') ?? 'Unknown date' }}
            @if($post->fake_edited_at)
                <span style="margin-left: 0.5rem; color: #6c757d;">(edited)</span>
            @endif
        </span>
        <span>#1</span>
    </div>

    {{-- Post Body --}}
    <div class="post-body">
        {{-- User Sidebar --}}
        <aside class="user-sidebar" @if($highlightColor) style="background-color: {{ $sbarBg }}; border-right-color: {{ $highlightColor }};" @endif>
            <div class="user-avatar" @if($highlightColor) style="background: linear-gradient(135deg, rgba({{ $hr }},{{ $hg }},{{ $hb }},0.5), {{ $highlightColor }});" @endif>
                @if($post->author->avatar_path)
                    <img src="{{ Storage::url($post->author->avatar_path) }}" alt="{{ $post->author->display_name }}">
                @else
                    <span style="font-size: 2rem; font-weight: 600; color: white;">{{ substr($post->author->display_name, 0, 1) }}</span>
                @endif
            </div>

            <div class="username" @if($highlightColor) style="color: {{ $highlightColor }};" @endif>
                {{ $post->author->display_name }}
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

            {{-- Bytes Rating --}}
            @if($post->author->show_bytes)
                @php $effectiveBytes = $post->author->bytes ?? 0; @endphp
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

        {{-- Post Content --}}
        <div class="post-content" @if($highlightColor) style="background-color: {{ $sbarBg }};" @endif>
            <div style="color: #c9d1d9; line-height: 1.7;">
                {!! nl2br(e($displayContent)) !!}
            </div>

            {{-- Attachments --}}
            @if($post->relationLoaded('attachments') && $post->attachments->count() > 0)
                @php
                    $inlineAttachments  = $post->attachments->filter(fn($a) => $a->shouldDisplayInline());
                    $galleryAttachments = $post->attachments->filter(fn($a) => $a->shouldDisplayAsGallery());
                    $fileAttachments    = $post->attachments->filter(fn($a) => $a->shouldDisplayAsAttachment());
                @endphp

                @if($inlineAttachments->count() > 0)
                    <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($inlineAttachments->sortBy('sort_order') as $attachment)
                            <figure style="margin: 0;">
                                <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" style="max-width: 100%; border-radius: 6px; border: 1px solid #343a40;">
                                @if($attachment->caption)
                                    <figcaption style="margin-top: 0.5rem; font-size: 0.85rem; text-align: center; color: #8b949e;">{{ $attachment->caption }}</figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                @endif

                @if($galleryAttachments->count() > 0)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #343a40; display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                        @foreach($galleryAttachments->sortBy('sort_order') as $attachment)
                            <div style="aspect-ratio: 1; overflow: hidden; border-radius: 6px; border: 1px solid #343a40;">
                                <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($fileAttachments->count() > 0)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #343a40;">
                        <p style="font-size: 0.75rem; color: #8b949e; margin-bottom: 0.5rem;">Attachments:</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            @foreach($fileAttachments->sortBy('sort_order') as $attachment)
                                @if($attachment->isImage())
                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" style="height: 6rem; border-radius: 4px; border: 1px solid #343a40;">
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; padding: 0.4rem 0.75rem; border-radius: 4px; background-color: #343a40; color: #8b949e;">
                                        {{ $attachment->getFileIcon() }} {{ $attachment->original_filename }}
                                        <span style="opacity: 0.6; font-size: 0.75rem;">({{ $attachment->human_file_size }})</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            {{-- Choice/Poll --}}
            @if($post->relationLoaded('choice') && $post->choice)
                @php $isPoll = $post->choice->type === 'poll'; @endphp
                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #343a40;">
                    <p style="font-size: 0.875rem; font-weight: 500; color: #c9d1d9; margin-bottom: 0.75rem;">
                        {{ $post->choice->prompt_text }}
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        @foreach($post->choice->options as $option)
                            <div style="padding: 0.5rem 0.75rem; border-radius: 4px; border: 1px solid #343a40; font-size: 0.875rem; color: #8b949e;">
                                {{ $option->label }}
                                @if(!$isPoll && $option->description)
                                    <span style="display: block; font-size: 0.8rem; color: #6c757d; margin-top: 0.1rem;">{{ $option->description }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p style="font-size: 0.75rem; color: #6c757d; margin-top: 0.5rem;">
                        {{ $isPoll ? 'Poll' : 'Choice' }} · preview only
                    </p>
                </div>
            @endif

            {{-- Signature --}}
            @php $signature = $post->author->signature ?? null; @endphp
            @if($signature)
                <div class="user-signature">{{ $signature }}</div>
            @endif
        </div>
    </div>

    {{-- Post Footer --}}
    <div class="post-footer" @if($highlightColor) style="border-top-color: {{ $highlightColor }}; background-color: {{ $hdrBg }};" @endif>
        @if($post->fake_edited_at)
            Last edited {{ $post->fake_edited_at->format('F j, Y \a\t g:i A') }}
        @endif
    </div>
</div>
