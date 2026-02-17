<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($thread)
                Add Post to: {{ $thread->title }}
            @else
                Create Post
            @endif
        </h2>
    </x-slot>

    <div x-data="postEditor({
        postId: null,
        authorId: {{ old('author_id') ?? 'null' }},
        content: {{ json_encode(old('content', '')) }},
        fakeCreatedAt: {{ json_encode(old('fake_created_at', '')) }},
        fakeEditedAt: {{ json_encode(old('fake_edited_at', '')) }},
        previewUrl: '{{ route('admin.api.preview.post') }}',
        csrfToken: '{{ csrf_token() }}'
    })" class="flex flex-col lg:flex-row gap-6">
        {{-- Editor Panel --}}
        <div class="lg:w-1/2">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ $thread ? route('admin.threads.posts.store', $thread) : route('admin.posts.store') }}" method="POST">
                    @csrf

                    @unless($thread)
                        <div class="mb-4">
                            <x-searchable-select
                                name="thread_id"
                                label="Thread"
                                :endpoint="route('admin.api.search.threads')"
                                :value="old('thread_id')"
                                display="title"
                                subtitle="category"
                                placeholder="Select a thread"
                                :required="true"
                            />
                        </div>
                    @endunless

                    <div class="mb-4">
                        <x-searchable-select
                            name="author_id"
                            label="Author"
                            :endpoint="route('admin.api.search.characters')"
                            :value="old('author_id')"
                            display="display_name"
                            subtitle="@username"
                            placeholder="Select an author"
                            :required="true"
                            x-on:change="authorId = $event.detail.value; updatePreview()"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="fake_created_at" class="block text-sm font-medium text-gray-700 mb-1">Post Date</label>
                            <input type="datetime-local" name="fake_created_at" id="fake_created_at"
                                x-model="fakeCreatedAt"
                                @change="updatePreview()"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="fake_edited_at" class="block text-sm font-medium text-gray-700 mb-1">Edited Date (Optional)</label>
                            <input type="datetime-local" name="fake_edited_at" id="fake_edited_at"
                                x-model="fakeEditedAt"
                                @change="updatePreview()"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea name="content" id="content" rows="12" required
                            x-model="content"
                            @input.debounce.300ms="updatePreview()"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        <p class="mt-1 text-sm text-gray-500">If set, this post will only be visible to readers who have started this phase.</p>
                    </div>

                    {{-- Choice/Poll Section --}}
                    <div class="mb-6">
                        @include('admin.partials.choice-editor', ['choice' => null])
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Create Post
                        </button>
                        <a href="{{ $thread ? route('admin.threads.show', $thread) : route('admin.posts.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <strong>Note:</strong> Attachments can be added after creating the post. Edit the post to upload files.
                    </p>
                </div>
            </div>
        </div>

        {{-- Preview Panel --}}
        <div class="lg:w-1/2">
            <div class="bg-white rounded-lg shadow p-4 sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Forum Preview</h3>
                    <button @click="updatePreview()" class="text-sm text-blue-600 hover:text-blue-800">
                        Refresh
                    </button>
                </div>
                <div x-show="loading" class="text-center py-8 text-gray-500">
                    Loading preview...
                </div>
                <div x-show="!loading" x-html="previewHtml" class="overflow-auto max-h-[calc(100vh-200px)]"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('postEditor', (config) => ({
                postId: config.postId,
                authorId: config.authorId,
                content: config.content || '',
                fakeCreatedAt: config.fakeCreatedAt || '',
                fakeEditedAt: config.fakeEditedAt || '',
                previewUrl: config.previewUrl,
                csrfToken: config.csrfToken,
                previewHtml: '',
                loading: false,

                init() {
                    if (this.authorId && this.content) {
                        this.updatePreview();
                    } else {
                        this.previewHtml = '<div class="text-center py-8 text-gray-500">Enter content and select an author to see preview</div>';
                    }
                },

                async updatePreview() {
                    if (!this.authorId || !this.content) {
                        this.previewHtml = '<div class="text-center py-8 text-gray-500">Enter content and select an author to see preview</div>';
                        return;
                    }

                    this.loading = true;

                    try {
                        const formData = new FormData();
                        if (this.postId) formData.append('post_id', this.postId);
                        formData.append('author_id', this.authorId);
                        formData.append('content', this.content);
                        if (this.fakeCreatedAt) formData.append('fake_created_at', this.fakeCreatedAt);
                        if (this.fakeEditedAt) formData.append('fake_edited_at', this.fakeEditedAt);

                        const response = await fetch(this.previewUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: formData,
                        });

                        if (response.ok) {
                            this.previewHtml = await response.text();
                        } else {
                            this.previewHtml = '<div class="text-center py-8 text-red-500">Failed to load preview</div>';
                        }
                    } catch (error) {
                        console.error('Preview error:', error);
                        this.previewHtml = '<div class="text-center py-8 text-red-500">Failed to load preview</div>';
                    } finally {
                        this.loading = false;
                    }
                },
            }));
        });
    </script>
    @endpush
</x-admin-layout>
