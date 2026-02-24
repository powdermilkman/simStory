<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Phase Timeline</h2>
                <p class="text-sm text-gray-500 mt-0.5">Story progression overview — conditions, actions, and reader progress</p>
            </div>
            <a href="{{ route('admin.phases.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Phases
            </a>
        </div>
    </x-slot>

    @if($phases->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            No phases configured yet.
            <a href="{{ route('admin.phases.create') }}" class="text-blue-600 hover:underline">Create one</a>.
        </div>
    @else
        <div class="space-y-4">
            @foreach($phases as $phase)
                @include('admin.phases._timeline-phase', ['phase' => $phase, 'level' => 0])
            @endforeach
        </div>
    @endif
</x-admin-layout>
