<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Role: {{ $role->name }}</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-xl">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST"
              x-data="{
                  color: '{{ old('color', $role->color) }}',
                  name: '{{ old('name', $role->name) }}',
                  enableHighlight: {{ old('enable_highlight', $role->post_highlight_color ? 'true' : 'false') }},
                  highlightColor: '{{ old('post_highlight_color', $role->post_highlight_color ?? '#e8923a') }}'
              }">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                <input type="text" name="name" id="name" x-model="name" value="{{ old('name', $role->name) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Badge Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" id="color" x-model="color" value="{{ old('color', $role->color) }}"
                        class="w-12 h-10 rounded border-gray-300 cursor-pointer">
                    <input type="text" x-model="color"
                        class="w-28 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                        pattern="^#[a-fA-F0-9]{6}$">
                </div>
                @error('color')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $role->sort_order) }}"
                    class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <label class="flex items-center gap-3 cursor-pointer mb-3">
                    <input type="checkbox" name="enable_highlight" value="1" x-model="enableHighlight"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Highlight posts from this role</span>
                </label>
                <div x-show="enableHighlight" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Post Highlight Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="post_highlight_color" x-model="highlightColor"
                            class="w-12 h-10 rounded border-gray-300 cursor-pointer">
                        <input type="text" x-model="highlightColor"
                            class="w-28 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                            pattern="^#[a-fA-F0-9]{6}$">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Posts from characters with this role will be tinted with this color on the forum thread view.</p>
                </div>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-2">Badge Preview</label>
                <span class="inline-block px-2 py-1 text-xs font-medium rounded"
                      :style="{ backgroundColor: color, color: getLuminance(color) > 0.5 ? '#000' : '#fff' }"
                      x-text="name || 'Role Name'"></span>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Update Role
                </button>
                <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        function getLuminance(hex) {
            hex = hex.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16) / 255;
            const g = parseInt(hex.substr(2, 2), 16) / 255;
            const b = parseInt(hex.substr(4, 2), 16) / 255;
            return 0.299 * r + 0.587 * g + 0.114 * b;
        }
    </script>
</x-admin-layout>
