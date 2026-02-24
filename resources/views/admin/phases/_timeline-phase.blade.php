@php
    $completed = $phase->readerProgress->where('status', 'completed')->count();
    $inProgress = $phase->readerProgress->where('status', 'in_progress')->count();
    $total = $phase->readerProgress->count();
@endphp

<div class="relative pl-{{ $level > 0 ? '8' : '0' }}">
    {{-- Connector line for child phases --}}
    @if($level > 0)
        <div class="absolute left-3 top-0 bottom-0 w-px bg-gray-200"></div>
        <div class="absolute left-3 top-6 w-5 h-px bg-gray-200"></div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm {{ $level > 0 ? 'ml-2' : '' }} {{ $phase->is_active ? '' : 'opacity-60' }}">
        {{-- Phase header --}}
        <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $phase->is_active ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $loop->iteration }}
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('admin.phases.show', $phase) }}"
                           class="font-semibold text-gray-900 hover:text-blue-600">
                            {{ $phase->name }}
                        </a>
                        @if($phase->is_active)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Active</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-medium">Inactive</span>
                        @endif
                        @if($phase->requires_all_sibling_phases)
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Requires previous</span>
                        @endif
                    </div>
                    @if($phase->description)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $phase->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex-shrink-0 text-right">
                <div class="text-sm font-medium text-gray-700">{{ $completed }} completed</div>
                @if($inProgress > 0)
                    <div class="text-xs text-amber-600">{{ $inProgress }} in progress</div>
                @endif
                @if($total > 0)
                    <div class="text-xs text-gray-400 mt-1">{{ $total }} total readers</div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            {{-- Conditions --}}
            <div class="px-5 py-4">
                <div class="flex items-center gap-1.5 mb-3">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Triggered when</span>
                </div>
                @if($phase->conditions->isEmpty())
                    <p class="text-sm text-gray-400 italic">No conditions — triggers immediately</p>
                @else
                    <ul class="space-y-1.5">
                        @foreach($phase->conditions as $condition)
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="flex-shrink-0 mt-0.5 w-4 h-4 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-xs text-blue-600 font-bold">{{ $loop->iteration }}</span>
                                <span>{{ $condition->getDescription() }}</span>
                            </li>
                        @endforeach
                        @if($phase->conditions->count() > 1)
                            <li class="text-xs text-gray-400 italic mt-1">All conditions must be met</li>
                        @endif
                    </ul>
                @endif
            </div>

            {{-- Actions --}}
            <div class="px-5 py-4">
                <div class="flex items-center gap-1.5 mb-3">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">On completion</span>
                </div>
                @if($phase->actions->isEmpty())
                    <p class="text-sm text-gray-400 italic">No actions</p>
                @else
                    <ul class="space-y-1.5">
                        @foreach($phase->actions as $action)
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="flex-shrink-0 mt-0.5 w-4 h-4 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-xs text-emerald-600 font-bold">{{ $loop->iteration }}</span>
                                <span>{{ $action->getDescription() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Gated content --}}
        @php
            $hasGated = $phase->gatedThreads->isNotEmpty() || $phase->gatedPosts->isNotEmpty() || $phase->gatedMessages->isNotEmpty();
        @endphp
        @if($hasGated)
            <div class="px-5 py-4 border-t border-gray-100">
                <div class="flex items-center gap-1.5 mb-3">
                    <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Visible once this phase is complete</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($phase->gatedThreads as $thread)
                        <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-800 border border-violet-200 px-2 py-1 rounded">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            Thread: {{ $thread->title }}
                        </span>
                    @endforeach
                    @foreach($phase->gatedPosts as $post)
                        <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-800 border border-violet-200 px-2 py-1 rounded">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Post #{{ $post->id }}{{ $post->thread ? ' in "' . $post->thread->title . '"' : '' }}
                        </span>
                    @endforeach
                    @foreach($phase->gatedMessages as $message)
                        <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-800 border border-violet-200 px-2 py-1 rounded">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Message: "{{ $message->subject }}"{{ $message->sender ? ' from ' . $message->sender->display_name : '' }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Edit link --}}
        <div class="px-5 py-2 bg-gray-50 border-t border-gray-100 flex justify-end">
            <a href="{{ route('admin.phases.edit', $phase) }}" class="text-xs text-indigo-600 hover:text-indigo-900">Edit phase</a>
        </div>
    </div>

    {{-- Child phases --}}
    @if($phase->childPhases->isNotEmpty())
        <div class="mt-3 space-y-3 pl-6 border-l-2 border-blue-100 ml-4">
            @foreach($phase->childPhases->sortBy('sort_order') as $child)
                @include('admin.phases._timeline-phase', ['phase' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
