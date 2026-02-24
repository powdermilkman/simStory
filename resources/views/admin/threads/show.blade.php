<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $thread->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.threads.edit', $thread) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Edit Thread
                </a>
                <a href="{{ route('admin.threads.posts.create', $thread) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    + Add Post
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500">
            <span>Category: <a href="{{ route('admin.categories.show', $thread->category) }}" class="text-blue-600 hover:underline">{{ $thread->category->name }}</a></span>
            <span>•</span>
            <span>Author: <a href="{{ route('admin.characters.show', $thread->author) }}" class="text-blue-600 hover:underline">{{ $thread->author->display_name }}</a></span>
            <span>•</span>
            <span>Date: {{ $thread->fake_created_at?->format('M d, Y \a\t g:i A') ?? 'Not set' }}</span>
            @if($thread->is_pinned)
                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Pinned</span>
            @endif
            @if($thread->is_locked)
                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded">Locked</span>
            @endif
            @if($thread->phase)
                <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded">
                    Phase: {{ $thread->phase->name }}
                </span>
            @endif
        </div>
    </div>

    <h3 class="text-lg font-medium text-gray-900 mb-4">Posts ({{ $thread->posts->count() }})</h3>
    
    <div class="space-y-4">
        @foreach($thread->posts as $post)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Author sidebar -->
                    <div class="md:w-48 bg-gray-50 p-4 md:border-r border-b md:border-b-0">
                        <div class="text-center">
                            @if($post->author->avatar_path)
                                <img src="{{ Storage::url($post->author->avatar_path) }}" alt="{{ $post->author->display_name }}" class="h-16 w-16 rounded-full mx-auto mb-2">
                            @else
                                <div class="h-16 w-16 rounded-full bg-gray-300 mx-auto mb-2 flex items-center justify-center text-gray-600 font-bold">
                                    {{ substr($post->author->display_name, 0, 1) }}
                                </div>
                            @endif
                            <a href="{{ route('admin.characters.show', $post->author) }}" class="font-medium text-blue-600 hover:underline">
                                {{ $post->author->display_name }}
                            </a>
                            @if($post->author->role_title)
                                <p class="text-xs text-gray-500">{{ $post->author->role_title }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Post content -->
                    <div class="flex-1 p-4">
                        <div class="flex justify-between items-start mb-4">
                            <div class="text-sm text-gray-500">
                                {{ $post->fake_created_at?->format('M d, Y \a\t g:i A') ?? 'Date not set' }}
                                @if($post->fake_edited_at)
                                    <span class="text-gray-400">(edited {{ $post->fake_edited_at->format('M d, Y') }})</span>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                @if($post->phase)
                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Phase: {{ $post->phase->name }}</span>
                                @endif
                                @if($post->choice)
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Has Choice</span>
                                @endif
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="prose max-w-none">
                            {!! nl2br(e($post->content)) !!}
                        </div>

                        @if($post->author->signature)
                            <div class="mt-4 pt-4 border-t text-sm text-gray-500 italic">
                                {{ $post->author->signature }}
                            </div>
                        @endif

                        @if($post->choice)
                            <div class="mt-4 pt-4 border-t bg-blue-50 -mx-4 -mb-4 p-4">
                                <p class="font-medium text-blue-900">Choice Point: {{ $post->choice->prompt_text }}</p>
                                <div class="mt-2 space-y-1">
                                    @foreach($post->choice->options as $option)
                                        <div class="text-sm text-blue-700">• {{ $option->label }}</div>
                                    @endforeach
                                </div>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Edit Choice</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>
