{{--
    Post card partial for rendering a single post in forum style.
    Used by: forum thread view, admin preview

    Required variables:
    - $post: Post model with author relationship loaded
    - $preview: boolean - if true, uses passed $previewContent instead of $post->content
    - $previewContent: string - content to display when $preview is true
--}}

@php
    $preview = $preview ?? false;
    $previewContent = $previewContent ?? $post->content;
    $displayContent = $preview ? $previewContent : $post->content;
@endphp

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
                <div style="font-weight: 500; width: 100%; text-align: center; color: var(--color-accent);">
                    {{ $post->author->display_name }}
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
                    @php $effectiveBytes = $post->author->bytes ?? 0; @endphp
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
                {!! nl2br(e($displayContent)) !!}
            </div>

            <!-- Attachments -->
            @if($post->relationLoaded('attachments') && $post->attachments->count() > 0)
                @php
                    $inlineAttachments = $post->attachments->filter(fn($a) => $a->shouldDisplayInline());
                    $galleryAttachments = $post->attachments->filter(fn($a) => $a->shouldDisplayAsGallery());
                    $fileAttachments = $post->attachments->filter(fn($a) => $a->shouldDisplayAsAttachment());
                @endphp

                <!-- Inline Images -->
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
            @endif

            <!-- Signature -->
            @php $signature = $post->author->signature ?? null; @endphp
            @if($signature)
                <div class="mt-6 pt-4 text-sm italic" style="border-top: 1px solid var(--color-border); color: var(--color-text-muted);">
                    {{ $signature }}
                </div>
            @endif
        </div>
    </div>
</div>
