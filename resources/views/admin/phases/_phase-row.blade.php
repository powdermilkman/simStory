<div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50" style="padding-left: {{ 24 + ($level * 24) }}px">
    <div class="flex items-center gap-3">
        @if($phase->childPhases->isNotEmpty())
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        @else
            <span class="w-4"></span>
        @endif

        <div>
            <a href="{{ route('admin.phases.show', $phase) }}" class="font-medium text-gray-900 hover:text-blue-600">
                {{ $phase->name }}
            </a>
            @if($phase->description)
                <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($phase->description, 60) }}</p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">
            <span class="inline-flex items-center gap-1">
                <span class="font-medium">{{ $phase->conditions->count() }}</span> conditions
            </span>
            <span class="mx-2 text-gray-300">|</span>
            <span class="inline-flex items-center gap-1">
                <span class="font-medium">{{ $phase->actions->count() }}</span> actions
            </span>
        </div>

        @if($phase->is_active)
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                Active
            </span>
        @else
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                Inactive
            </span>
        @endif

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.phases.edit', $phase) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
        </div>
    </div>
</div>

@foreach($phase->childPhases as $child)
    @include('admin.phases._phase-row', ['phase' => $child, 'level' => $level + 1])
@endforeach
