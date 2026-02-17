<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Post Details</h2>
            <a href="{{ route('admin.posts.edit', $post) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="text-sm text-gray-500 mb-4">
            <span>Thread: <a href="{{ route('admin.threads.show', $post->thread) }}" class="text-blue-600 hover:underline">{{ $post->thread->title }}</a></span>
            <span class="mx-2">•</span>
            <span>Author: <a href="{{ route('admin.characters.show', $post->author) }}" class="text-blue-600 hover:underline">{{ $post->author->display_name }}</a></span>
            <span class="mx-2">•</span>
            <span>Date: {{ $post->fake_created_at?->format('M d, Y \a\t g:i A') ?? 'Not set' }}</span>
        </div>

        <div class="prose max-w-none">
            {!! nl2br(e($post->content)) !!}
        </div>

        @if($post->phase)
            <div class="mt-4 pt-4 border-t">
                <span class="text-sm text-gray-500">Visible in phase:</span>
                <span class="text-purple-600">{{ $post->phase->name }}</span>
            </div>
        @endif

        @if($post->choice)
            <div class="mt-4 pt-4 border-t bg-blue-50 p-4 rounded">
                <p class="font-medium text-blue-900">This post triggers a choice:</p>
                <p class="text-blue-800 mt-1">{{ $post->choice->prompt_text }}</p>
                <div class="mt-2 space-y-1">
                    @foreach($post->choice->options as $option)
                        <div class="text-sm text-blue-700">• {{ $option->label }}</div>
                    @endforeach
                </div>
                <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Edit Choice</a>
            </div>
        @endif
    </div>

    @if($post->attachments->count() > 0)
        <h3 class="text-lg font-medium text-gray-900 mb-4">Attachments</h3>
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-4 gap-4">
                @foreach($post->attachments as $attachment)
                    <div class="border rounded p-2">
                        @if($attachment->isImage())
                            <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="w-full h-32 object-cover rounded">
                        @else
                            <div class="h-32 flex items-center justify-center bg-gray-100 rounded">
                                <span class="text-gray-500">{{ $attachment->original_filename }}</span>
                            </div>
                        @endif
                        <p class="text-xs text-gray-500 mt-1 truncate">{{ $attachment->original_filename }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-admin-layout>
