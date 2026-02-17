<x-forum-layout>
    <x-slot name="title">Your Progress - {{ config('app.name') }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-medium mb-2" style="color: var(--color-text);">Your Journey</h1>
            <p style="color: var(--color-text-muted);">Track your choices and progress through the story.</p>
        </div>

        <div class="rounded-lg p-6 mb-8" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full flex items-center justify-center text-2xl font-bold" style="background-color: var(--color-accent); color: var(--color-bg);">
                    {{ substr($reader->username, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-medium" style="color: var(--color-text);">{{ $reader->username }}</h2>
                    <p class="text-sm" style="color: var(--color-text-muted);">{{ $reader->email }}</p>
                    <p class="text-sm mt-1" style="color: var(--color-text-muted);">
                        Joined {{ $reader->created_at->format('F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <h2 class="text-lg font-medium mb-4" style="color: var(--color-text);">Choices Made</h2>
        
        @if($progress->count() > 0)
            <div class="space-y-4 mb-8">
                @foreach($progress as $item)
                    <div class="rounded-lg p-4" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                        <p class="text-sm mb-1" style="color: var(--color-text-muted);">{{ $item->choiceOption->choice->prompt_text }}</p>
                        <p class="font-medium" style="color: var(--color-accent-warm);">{{ $item->choiceOption->label }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-text-muted);">Chosen {{ $item->chosen_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-lg p-8 text-center mb-8" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                <p style="color: var(--color-text-muted);">You haven't made any choices yet.</p>
                <p class="text-sm mt-2" style="color: var(--color-text-muted);">Explore the forums to find choice points in the story.</p>
            </div>
        @endif

        <div class="rounded-lg p-6" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <h3 class="font-medium mb-2" style="color: var(--color-text);">Start Over?</h3>
            <p class="text-sm mb-4" style="color: var(--color-text-muted);">
                Reset your progress to experience the story from a different perspective.
            </p>
            <form method="POST" action="{{ route('reader.progress.reset') }}" onsubmit="return confirm('Are you sure you want to reset your progress? This cannot be undone.')">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg text-sm transition-all duration-200"
                    style="background-color: var(--color-border); color: var(--color-text-muted);">
                    Reset Progress
                </button>
            </form>
        </div>
    </div>
</x-forum-layout>
