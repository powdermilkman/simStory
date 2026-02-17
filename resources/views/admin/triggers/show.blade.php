<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trigger: {{ $trigger->name }}</h2>
            <a href="{{ route('admin.triggers.edit', $trigger) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <span class="text-gray-500">Identifier:</span>
                <span class="font-mono">{{ $trigger->identifier }}</span>
            </div>
            @if($trigger->getRawOriginal('description'))
                <div class="col-span-2">
                    <span class="text-gray-500">Description:</span>
                    <p class="mt-1">{{ $trigger->getRawOriginal('description') }}</p>
                </div>
            @endif
        </div>

        <h3 class="text-lg font-medium text-gray-900 mb-4">
            Conditions 
            <span class="text-sm font-normal text-gray-500">
                ({{ $trigger->conditions->count() }} total - all must be met)
            </span>
        </h3>

        @if($trigger->conditions->isNotEmpty())
            <div class="space-y-3">
                @foreach($trigger->conditions as $index => $condition)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1">
                            <span class="px-2 py-1 text-xs font-medium rounded bg-gray-200 text-gray-700">
                                {{ \App\Models\TriggerCondition::TYPES[$condition->type] ?? $condition->type }}
                            </span>
                            <span class="ml-2 text-gray-700">
                                @if($condition->type === 'choice')
                                    "{{ $condition->choiceOption?->label ?? 'Unknown' }}"
                                    <span class="text-gray-500">(from: {{ $condition->choiceOption?->choice?->prompt_text ?? 'Unknown' }})</span>
                                @elseif($condition->type === 'view_thread')
                                    @php $thread = \App\Models\Thread::find($condition->target_id); @endphp
                                    Thread: "{{ $thread?->title ?? 'Unknown' }}" (#{{ $condition->target_id }})
                                @else
                                    @php $post = \App\Models\Post::with('thread')->find($condition->target_id); @endphp
                                    Post #{{ $condition->target_id }}
                                    @if($post?->thread)
                                        <span class="text-gray-500">(in: {{ $post->thread->title }})</span>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No conditions configured.</p>
        @endif
    </div>


    <div class="mt-6">
        <form action="{{ route('admin.triggers.destroy', $trigger) }}" method="POST" class="inline" onsubmit="return confirm('Delete this trigger? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                Delete Trigger
            </button>
        </form>
    </div>
</x-admin-layout>
