<x-forum-layout>
    <x-slot name="title">{{ config('app.name') }} - Simulation Discussion Forums</x-slot>

    <style>
        .category-card {
            background-color: #23272b;
            border: 1px solid #343a40;
            border-radius: 6px;
            overflow: hidden;
            text-decoration: none;
            display: block;
            transition: background-color 0.15s;
        }

        .category-card:hover {
            background-color: #2a2e33;
        }

        .category-card-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
        }

        .category-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-accent);
            margin-bottom: 0.25rem;
        }

        .category-description {
            font-size: 0.85rem;
            color: #8b949e;
        }

        .category-stat {
            text-align: right;
            min-width: 60px;
        }

        .category-stat-number {
            font-size: 1.5rem;
            font-weight: 300;
            color: #e6edf3;
            line-height: 1;
        }

        .category-stat-label {
            font-size: 0.75rem;
            color: #8b949e;
        }

        .new-badge {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.4rem;
            border-radius: 3px;
            background-color: var(--color-accent);
            color: var(--color-bg);
            vertical-align: middle;
            margin-left: 0.4rem;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
    </style>

    <div class="mb-6" style="border-bottom: 1px solid #343a40; padding-bottom: 1rem;">
        <h1 style="font-size: 1.35rem; font-weight: 600; color: #e6edf3; margin-bottom: 0.25rem;">Forum Index</h1>
        <p style="color: #8b949e; font-size: 0.875rem;">Share your experiences, discuss simulations, connect with fellow enthusiasts.</p>
    </div>

    <div class="space-y-2">
        @foreach($categories as $category)
            @php
                $newInfo = $categoryNewCounts[$category->id] ?? null;
                $hasNew = $newInfo && ($newInfo['new_threads'] > 0 || $newInfo['threads_with_new_posts'] > 0);
            @endphp
            <a href="{{ route('forum.category', $category) }}" class="category-card">
                <div class="category-card-body">
                    <div class="flex-1">
                        <div class="category-name">
                            {{ $category->name }}
                            @if($hasNew)
                                <span class="new-badge">NEW</span>
                            @endif
                        </div>
                        @if($category->description)
                            <div class="category-description">{{ $category->description }}</div>
                        @endif
                        @if($hasNew)
                            <div style="font-size: 0.75rem; color: var(--color-accent); margin-top: 0.25rem;">
                                @if($newInfo['new_threads'] > 0)
                                    {{ $newInfo['new_threads'] }} new thread{{ $newInfo['new_threads'] > 1 ? 's' : '' }}
                                @endif
                                @if($newInfo['new_threads'] > 0 && $newInfo['threads_with_new_posts'] > 0)
                                    ·
                                @endif
                                @if($newInfo['threads_with_new_posts'] > 0)
                                    {{ $newInfo['threads_with_new_posts'] }} thread{{ $newInfo['threads_with_new_posts'] > 1 ? 's' : '' }} with new posts
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="category-stat">
                        <div class="category-stat-number">{{ $category->threads_count }}</div>
                        <div class="category-stat-label">threads</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if($categories->isEmpty())
        <div style="text-align: center; padding: 3rem; background-color: #23272b; border: 1px solid #343a40; border-radius: 6px; color: #8b949e;">
            No forums available yet. Check back soon!
        </div>
    @endif
</x-forum-layout>
