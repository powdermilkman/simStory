<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Reputation Type: {{ $reputationType->name }}</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-xl">
        <form action="{{ route('admin.reputation-types.update', $reputationType) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $reputationType->name) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">Identifier</label>
                <input type="text" name="identifier" id="identifier" value="{{ old('identifier', $reputationType->identifier) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">
                <p class="mt-1 text-xs text-gray-500">Used in code references. Leave empty to auto-generate from name.</p>
                @error('identifier')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="2"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $reputationType->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="min_value" class="block text-sm font-medium text-gray-700 mb-1">Min Value</label>
                    <input type="number" name="min_value" id="min_value" value="{{ old('min_value', $reputationType->min_value) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('min_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="max_value" class="block text-sm font-medium text-gray-700 mb-1">Max Value</label>
                    <input type="number" name="max_value" id="max_value" value="{{ old('max_value', $reputationType->max_value) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="default_value" class="block text-sm font-medium text-gray-700 mb-1">Default</label>
                    <input type="number" name="default_value" id="default_value" value="{{ old('default_value', $reputationType->default_value) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('default_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $reputationType->sort_order) }}"
                    class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_visible_to_readers" value="1" {{ old('is_visible_to_readers', $reputationType->is_visible_to_readers) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Visible to readers on character profiles</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Update Reputation Type
                </button>
                <a href="{{ route('admin.reputation-types.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
