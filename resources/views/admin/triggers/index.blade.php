<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Content Triggers</h2>
            <a href="{{ route('admin.triggers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + New Trigger
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <p class="text-sm text-gray-600">
                Content triggers unlock content when readers complete specific actions. All conditions must be met for a trigger to activate.
            </p>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identifier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conditions</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($triggers as $trigger)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.triggers.show', $trigger) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $trigger->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                            {{ $trigger->identifier }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($trigger->conditions->count() > 0)
                                <span class="inline-flex items-center gap-1">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $trigger->conditions->count() }} condition{{ $trigger->conditions->count() > 1 ? 's' : '' }}
                                    </span>
                                    @if($trigger->conditions->count() > 1)
                                        <span class="text-xs text-gray-500">(all required)</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">No conditions</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.triggers.edit', $trigger) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <form action="{{ route('admin.triggers.destroy', $trigger) }}" method="POST" class="inline" onsubmit="return confirm('Delete this trigger?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No triggers found. <a href="{{ route('admin.triggers.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($triggers->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $triggers->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
