{{--
    Post preview partial for admin editor.
    Renders a post with forum styling in an isolated container.
--}}
<style>
    .forum-preview {
        --color-bg: #0a0a0f;
        --color-surface: #14141f;
        --color-surface-hover: #1a1a28;
        --color-border: #2a2a3a;
        --color-text: #e8e8ed;
        --color-text-muted: #8888a0;
        --color-accent: #5c9ead;
        --color-accent-warm: #e0a458;
        font-family: 'Outfit', system-ui, sans-serif;
        background-color: var(--color-bg);
        color: var(--color-text);
        padding: 1rem;
        border-radius: 0.5rem;
    }
</style>

<div class="forum-preview">
    @include('forum.partials.post-card', [
        'post' => $post,
        'preview' => true,
        'previewContent' => $previewContent,
    ])
</div>
