<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reader: {{ $reader->username }}
            </h2>
            <a href="{{ route('admin.readers.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Readers
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Reader Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Account Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Username</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $reader->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $reader->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Joined</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $reader->created_at->format('F j, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Active</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $reader->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>

                <div class="mt-6 pt-4 border-t">
                    <form action="{{ route('admin.readers.destroy', $reader) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Are you sure you want to delete this reader? This will also delete all their progress.')">
                            Delete Reader
                        </button>
                    </form>
                </div>
            </div>

            <!-- Choices Made -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Choices Made ({{ $reader->progress->count() }})</h3>
                @if($reader->progress->isEmpty())
                    <p class="text-gray-500 text-sm">No choices made yet.</p>
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($reader->progress as $progress)
                            <div class="p-3 rounded bg-gray-50">
                                <p class="text-sm font-medium text-gray-900">{{ $progress->choiceOption?->label ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    From: {{ $progress->choiceOption?->choice?->title ?? 'Unknown choice' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $progress->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Actions & Reactions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Activity</h3>

                <!-- Actions -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Content Viewed ({{ $reader->actions->count() }})</h4>
                    @if($reader->actions->isEmpty())
                        <p class="text-gray-500 text-sm">No content viewed yet.</p>
                    @else
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($reader->actions->take(10) as $action)
                                @php
                                    if (in_array($action->action_type, ['view_post', 'react_post', 'report_post'])) {
                                        $actionUrl = route('admin.posts.show', $action->target_id);
                                        $actionLabel = ucfirst($action->target_type) . ' #' . $action->target_id;
                                    } elseif ($action->action_type === 'view_thread') {
                                        $linkedThread = $actionThreads->get($action->target_id);
                                        $actionUrl = $linkedThread ? route('admin.threads.show', $linkedThread) : null;
                                        $actionLabel = $linkedThread ? $linkedThread->title : 'Thread #' . $action->target_id;
                                    } else {
                                        $actionUrl = null;
                                        $actionLabel = ucfirst($action->target_type) . ' #' . $action->target_id;
                                    }
                                @endphp
                                <div class="text-sm p-2 bg-gray-50 rounded">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($action->action_type === 'view_post') bg-blue-100 text-blue-800
                                        @elseif($action->action_type === 'view_thread') bg-green-100 text-green-800
                                        @else bg-pink-100 text-pink-800
                                        @endif">
                                        {{ str_replace('_', ' ', $action->action_type) }}
                                    </span>
                                    @if($actionUrl)
                                        <a href="{{ $actionUrl }}" class="text-gray-700 hover:text-blue-600 hover:underline ml-1">{{ $actionLabel }}</a>
                                    @else
                                        <span class="text-gray-500 ml-1">{{ $actionLabel }}</span>
                                    @endif
                                    <span class="text-gray-400 text-xs block mt-1">{{ $action->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                            @if($reader->actions->count() > 10)
                                <p class="text-xs text-gray-500 mt-2">...and {{ $reader->actions->count() - 10 }} more</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Reactions -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Reactions ({{ $reactions->count() }})</h4>
                    @if($reactions->isEmpty())
                        <p class="text-gray-500 text-sm">No reactions yet.</p>
                    @else
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($reactions->take(10) as $reaction)
                                <div class="text-sm p-2 bg-gray-50 rounded flex items-center gap-2">
                                    <span class="text-lg">{{ \App\Models\Reaction::TYPES[$reaction->type] ?? '👍' }}</span>
                                    <div>
                                        <a href="{{ route('admin.posts.show', $reaction->post_id) }}" class="text-gray-700 hover:text-blue-600 hover:underline">
                                            {{ $reaction->post?->thread?->title ?? 'Unknown thread' }}
                                        </a>
                                        <span class="text-gray-400 text-xs block">{{ $reaction->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                            @if($reactions->count() > 10)
                                <p class="text-xs text-gray-500 mt-2">...and {{ $reactions->count() - 10 }} more</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Story Progress -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Story Progress ({{ $reader->phaseProgress->count() }} phases tracked)</h3>

            @if($reader->phaseProgress->isEmpty())
                <p class="text-gray-500 text-sm">No phase progress recorded yet.</p>
            @else
                @php
                    $completedProgress = $reader->phaseProgress->where('status', 'completed')->sortByDesc('completed_at');
                    $inProgressProgress = $reader->phaseProgress->where('status', 'in_progress')->sortBy('started_at');
                    $notStartedProgress = $reader->phaseProgress->where('status', 'not_started');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @if($completedProgress->isNotEmpty())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                                Completed ({{ $completedProgress->count() }})
                            </h4>
                            <ul class="space-y-2">
                                @foreach($completedProgress as $pp)
                                    <li class="flex items-center justify-between p-2 bg-green-50 rounded-lg border border-green-100">
                                        <a href="{{ route('admin.phases.show', $pp->phase) }}" class="text-sm font-medium text-green-800 hover:underline">
                                            {{ $pp->phase->name }}
                                        </a>
                                        <span class="text-xs text-green-600 ml-2 flex-shrink-0">
                                            {{ $pp->completed_at?->format('M j, Y') ?? '—' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($inProgressProgress->isNotEmpty())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                                In Progress ({{ $inProgressProgress->count() }})
                            </h4>
                            <ul class="space-y-2">
                                @foreach($inProgressProgress as $pp)
                                    <li class="flex items-center justify-between p-2 bg-blue-50 rounded-lg border border-blue-100">
                                        <a href="{{ route('admin.phases.show', $pp->phase) }}" class="text-sm font-medium text-blue-800 hover:underline">
                                            {{ $pp->phase->name }}
                                        </a>
                                        <span class="text-xs text-blue-600 ml-2 flex-shrink-0">
                                            since {{ $pp->started_at?->format('M j, Y') ?? '—' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($notStartedProgress->isNotEmpty())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                                Not Started ({{ $notStartedProgress->count() }})
                            </h4>
                            <ul class="space-y-2">
                                @foreach($notStartedProgress as $pp)
                                    <li class="p-2 bg-gray-50 rounded-lg border border-gray-100">
                                        <a href="{{ route('admin.phases.show', $pp->phase) }}" class="text-sm text-gray-600 hover:underline">
                                            {{ $pp->phase->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
