<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Phase: {{ $phase->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.phases.edit', $phase) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Edit Phase
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Details</h3>

                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Identifier</dt>
                        <dd class="mt-1">
                            <code class="text-sm text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $phase->identifier }}</code>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($phase->is_active)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Parent Phase</dt>
                        <dd class="mt-1 text-gray-900">
                            @if($phase->parentPhase)
                                <a href="{{ route('admin.phases.show', $phase->parentPhase) }}" class="text-blue-600 hover:underline">
                                    {{ $phase->parentPhase->name }}
                                </a>
                            @else
                                <span class="text-gray-500">None (Root Phase)</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                        <dd class="mt-1 text-gray-900">{{ $phase->sort_order }}</dd>
                    </div>
                    @if($phase->description)
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-gray-900">{{ $phase->description }}</dd>
                        </div>
                    @endif
                </dl>

                @if($phase->requires_all_sibling_phases)
                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            This phase requires all previous sibling phases to be completed before it can start.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Conditions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Conditions ({{ $phase->conditions->count() }})</h3>

                @if($phase->conditions->isEmpty())
                    <p class="text-gray-500">No conditions. This phase will complete immediately when it starts.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($phase->conditions as $condition)
                            <li class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-medium">
                                    {{ $loop->iteration }}
                                </span>
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-gray-200 text-gray-800 mr-2">
                                        {{ \App\Models\PhaseCondition::TYPES[$condition->type] ?? $condition->type }}
                                    </span>
                                    <span class="text-gray-700">{{ $condition->getDescription() }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-4 text-sm text-gray-500">All conditions must be met (AND logic).</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions ({{ $phase->actions->count() }})</h3>

                @if($phase->actions->isEmpty())
                    <p class="text-gray-500">No actions. Nothing will happen when this phase completes.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($phase->actions as $action)
                            <li class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <span class="flex-shrink-0 w-6 h-6 bg-green-100 text-green-800 rounded-full flex items-center justify-center text-xs font-medium">
                                    {{ $loop->iteration }}
                                </span>
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-green-200 text-green-800 mr-2">
                                        {{ \App\Models\PhaseAction::TYPES[$action->type] ?? $action->type }}
                                    </span>
                                    <span class="text-gray-700">{{ $action->getDescription() }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-4 text-sm text-gray-500">Actions execute in order when the phase completes.</p>
                @endif
            </div>

            <!-- Child Phases -->
            @if($phase->childPhases->isNotEmpty())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Child Phases ({{ $phase->childPhases->count() }})</h3>
                    <ul class="divide-y divide-gray-200">
                        @foreach($phase->childPhases as $child)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('admin.phases.show', $child) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $child->name }}
                                    </a>
                                    <p class="text-sm text-gray-500">{{ $child->conditions->count() }} conditions, {{ $child->actions->count() }} actions</p>
                                </div>
                                @if($child->is_active)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reader Progress</h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Completed</span>
                        <span class="text-lg font-semibold text-green-600">{{ $completedCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">In Progress</span>
                        <span class="text-lg font-semibold text-blue-600">{{ $inProgressCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Total Tracked</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $phase->readerProgress->count() }}</span>
                    </div>
                </div>
            </div>

            @if($phase->readerProgress->isNotEmpty())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Progress</h3>
                    <ul class="space-y-3">
                        @foreach($phase->readerProgress->sortByDesc('updated_at')->take(10) as $progress)
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-gray-900">{{ $progress->reader->username }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded
                                    @if($progress->status === 'completed') bg-green-100 text-green-800
                                    @elseif($progress->status === 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $progress->status)) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.phases.edit', $phase) }}" class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">
                        Edit Phase
                    </a>
                    <form action="{{ route('admin.phases.destroy', $phase) }}" method="POST" onsubmit="return confirm('Delete this phase? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="block w-full text-center bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200">
                            Delete Phase
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
