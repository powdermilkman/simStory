{{--
    Post preview partial for admin editor.
    Renders a post with forum styling in an isolated container.
--}}
<style>
    .forum-preview {
        --color-bg: #1a1d21;
        --color-surface: #23272b;
        --color-surface-hover: #2a2e33;
        --color-border: #343a40;
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

    .forum-preview .post-container {
        background-color: #23272b;
        border: 1px solid #343a40;
        border-radius: 6px;
        overflow: hidden;
    }

    .forum-preview .post-header {
        background-color: #14181c;
        border-bottom: 1px solid #343a40;
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        color: #8b949e;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .forum-preview .post-body {
        display: flex;
    }

    .forum-preview .user-sidebar {
        width: 180px;
        min-width: 180px;
        background-color: #1e2124;
        border-right: 1px solid #343a40;
        padding: 1.25rem 1rem;
        text-align: center;
    }

    .forum-preview .user-avatar {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #343a40, #5c9ead);
        border-radius: 50%;
        margin: 0 auto 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        overflow: hidden;
    }

    .forum-preview .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .forum-preview .username {
        color: #5c9ead;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .forum-preview .user-badge {
        display: inline-block;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 3px;
        margin-bottom: 0.75rem;
        font-weight: 500;
    }

    .forum-preview .user-stats {
        font-size: 0.75rem;
        color: #8b949e;
        line-height: 1.6;
    }

    .forum-preview .user-stats .stat-value {
        color: #c9d1d9;
        font-weight: 500;
    }

    .forum-preview .rating-display {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #343a40;
    }

    .forum-preview .rating-label {
        font-size: 0.7rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .forum-preview .rating-bytes {
        display: flex;
        justify-content: center;
        gap: 3px;
    }

    .forum-preview .byte {
        width: 18px;
        height: 18px;
        background-color: #3a3a3a;
        border-radius: 3px;
    }

    .forum-preview .byte.filled {
        background-color: #f0b400;
        box-shadow: 0 0 6px rgba(240, 180, 0, 0.4);
    }

    .forum-preview .rating-text {
        font-size: 0.75rem;
        color: #f0b400;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    .forum-preview .post-content {
        flex: 1;
        padding: 1.25rem 1.5rem;
        min-width: 0;
        color: #c9d1d9;
        line-height: 1.7;
    }

    .forum-preview .user-signature {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px dashed #343a40;
        font-style: italic;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .forum-preview .post-footer {
        background-color: #14181c;
        border-top: 1px solid #343a40;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        color: #6c757d;
        min-height: 2rem;
    }
</style>

<div class="forum-preview">
    @include('forum.partials.post-card', [
        'post' => $post,
        'preview' => true,
        'previewContent' => $previewContent,
    ])
</div>
