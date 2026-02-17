<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Thread</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
        <form action="{{ route('admin.threads.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" id="category_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author_id" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                    <select name="author_id" id="author_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select an author</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ old('author_id') == $character->id ? 'selected' : '' }}>
                                {{ $character->display_name }} ({{ '@' . $character->username }})
                            </option>
                        @endforeach
                    </select>
                    @error('author_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="fake_created_at" class="block text-sm font-medium text-gray-700 mb-1">Post Date (Story Timeline)</label>
                <input type="datetime-local" name="fake_created_at" id="fake_created_at" value="{{ old('fake_created_at') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('fake_created_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="first_post_content" class="block text-sm font-medium text-gray-700 mb-1">First Post Content</label>
                <textarea name="first_post_content" id="first_post_content" rows="8" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('first_post_content') }}</textarea>
                @error('first_post_content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="is_pinned" class="ml-2 text-sm text-gray-700">Pin this thread</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_locked" id="is_locked" value="1" {{ old('is_locked') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="is_locked" class="ml-2 text-sm text-gray-700">Lock this thread</label>
                </div>
            </div>

            <div class="mb-6">
                <x-searchable-select
                    name="phase_id"
                    label="Visible in Phase (Optional)"
                    :endpoint="route('admin.api.search.phases')"
                    :value="old('phase_id')"
                    display="name"
                    subtitle="identifier"
                    placeholder="Always visible - no phase requirement"
                />
                <p class="mt-1 text-sm text-gray-500">If set, this thread will only be visible to readers who have started this phase.</p>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Create Thread
                </button>
                <a href="{{ route('admin.threads.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>

        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>Note:</strong> Attachments can be added to the first post after creating the thread. Edit the post to upload files.
            </p>
        </div>
    </div>
</x-admin-layout>
