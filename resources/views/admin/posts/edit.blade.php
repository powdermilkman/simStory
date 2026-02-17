<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Post</h2>
    </x-slot>

    <div x-data="postEditor({
        postId: {{ $post->id }},
        authorId: {{ $post->author_id }},
        content: {{ json_encode($post->content) }},
        fakeCreatedAt: {{ json_encode($post->fake_created_at?->format('Y-m-d\TH:i')) }},
        fakeEditedAt: {{ json_encode($post->fake_edited_at?->format('Y-m-d\TH:i')) }},
        previewUrl: '{{ route('admin.api.preview.post') }}',
        csrfToken: '{{ csrf_token() }}'
    })" class="flex flex-col lg:flex-row gap-6">
        {{-- Editor Panel --}}
        <div class="lg:w-1/2">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('admin.posts.update', $post) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-searchable-select
                            name="author_id"
                            label="Author"
                            :endpoint="route('admin.api.search.characters')"
                            :value="old('author_id', $post->author_id)"
                            display="display_name"
                            subtitle="@username"
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">{{ old('content', $post->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <x-searchable-select
                            name="phase_id"
                            label="Visible in Phase (Optional)"
                            :endpoint="route('admin.api.search.phases')"
                            :value="old('phase_id', $post->phase_id)"
                            display="name"
                            subtitle="identifier"
                            placeholder="Always visible - no phase requirement"
                        />
                        <p class="mt-1 text-sm text-gray-500">If set, this post will only be visible to readers who have started this phase.</p>
                    </div>

                    {{-- Choice/Poll Section --}}
                    <div class="mb-6">
                        @include('admin.partials.choice-editor', ['choice' => $post->choice])
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Update Post
                        </button>
                        <a href="{{ route('admin.threads.show', $post->thread) }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>

            {{-- Attachments Section --}}
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Attachments</h3>

                {{-- Existing Attachments --}}
                @if($post->attachments->count() > 0)
                    <div class="mb-6 space-y-3">
                        @foreach($post->attachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    @if($attachment->isImage())
                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="w-16 h-16 object-cover rounded">
                                    @else
                                        <span class="text-2xl">{{ $attachment->getFileIcon() }}</span>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $attachment->original_filename }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $attachment->human_file_size }} &middot;
                                            {{ \App\Models\Attachment::DISPLAY_TYPES[$attachment->display_type] ?? 'Attachment' }}
                                            @if($attachment->caption)
                                                &middot; "{{ Str::limit($attachment->caption, 30) }}"
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ $attachment->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                    <form action="{{ route('admin.attachments.destroy', $attachment) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this attachment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 mb-6">No attachments yet.</p>
                @endif

                {{-- Upload New Attachment --}}
                <form action="{{ route('admin.attachments.store', $post) }}" method="POST" enctype="multipart/form-data" class="border-t pt-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File</label>
                            <input type="file" name="file" id="file" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <label for="display_type" class="block text-sm font-medium text-gray-700 mb-1">Display Type</label>
                            <select name="display_type" id="display_type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach(\App\Models\Attachment::DISPLAY_TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="caption" class="block text-sm font-medium text-gray-700 mb-1">Caption (Optional)</label>
                            <input type="text" name="caption" id="caption" placeholder="Image caption..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                        Upload Attachment
                    </button>
                </form>
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
                loading: true,

                init() {
                    this.updatePreview();

                    // Listen for author changes from searchable-select
                    this.$watch('authorId', () => this.updatePreview());
                },

                async updatePreview() {
                    if (!this.authorId || !this.content) {
                        this.previewHtml = '<div class="text-center py-8 text-gray-500">Enter content and select an author to see preview</div>';
                        this.loading = false;
                        return;
                    }

                    this.loading = true;

                    try {
                        const formData = new FormData();
                        formData.append('post_id', this.postId);
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

        // Handle searchable-select changes
        document.addEventListener('DOMContentLoaded', () => {
            const authorInput = document.querySelector('input[name="author_id"]');
            if (authorInput) {
                authorInput.addEventListener('change', (e) => {
                    const event = new CustomEvent('author-changed', { detail: { value: e.target.value } });
                    document.dispatchEvent(event);
                });
            }
        });
    </script>
    @endpush
</x-admin-layout>
