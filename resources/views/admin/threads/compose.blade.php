<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thread Composer</h2>
    </x-slot>

    <div x-data="threadComposer({
        previewUrl: '{{ route('admin.api.preview.post') }}',
        csrfToken: '{{ csrf_token() }}'
    })">
        <form action="{{ route('admin.threads.compose.store') }}" method="POST" @submit="prepareSubmit()">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-semibold text-red-800 mb-2">Please fix the following errors:</h4>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Thread Settings --}}
            <div class="bg-white rounded-lg shadow p-6 mb-6 overflow-visible">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Thread Settings</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Thread Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('title') }}">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-searchable-select
                            name="author_id"
                            label="Thread Author"
                            :endpoint="route('admin.api.search.characters')"
                            :value="old('author_id')"
                            display="display_name"
                            subtitle="@username"
                            placeholder="Select thread author"
                            :required="true"
                        />
                    </div>

                    <div>
                        <label for="fake_created_at" class="block text-sm font-medium text-gray-700 mb-1">Thread Date</label>
                        <input type="datetime-local" name="fake_created_at" id="fake_created_at"
                            x-model="threadDate"
                            @change="autoIncrementDates()"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('fake_created_at') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-searchable-select
                            name="phase_id"
                            label="Visible in Phase (Optional)"
                            :endpoint="route('admin.api.search.phases')"
                            :value="old('phase_id')"
                            display="name"
                            subtitle="identifier"
                            placeholder="Always visible - no phase requirement"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_pinned" value="1"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ old('is_pinned') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Pinned</span>
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_locked" value="1"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ old('is_locked') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Locked</span>
                    </label>
                </div>
            </div>

            {{-- Posts --}}
            <div class="space-y-4">
                <template x-for="(post, index) in posts" :key="post.id">
                    <div class="bg-white rounded-lg shadow overflow-visible">
                        <div class="flex items-center justify-between px-6 py-3 bg-gray-50 border-b">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-semibold text-sm" x-text="index + 1"></span>
                                <span class="font-medium text-gray-700" x-text="index === 0 ? 'First Post' : 'Post ' + (index + 1)"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="movePost(index, -1)" x-show="index > 0"
                                    class="p-1 text-gray-400 hover:text-gray-600" title="Move up">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                </button>
                                <button type="button" @click="movePost(index, 1)" x-show="index < posts.length - 1"
                                    class="p-1 text-gray-400 hover:text-gray-600" title="Move down">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <button type="button" @click="removePost(index)" x-show="posts.length > 1"
                                    class="p-1 text-red-400 hover:text-red-600" title="Remove post">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row gap-6">
                                {{-- Post Editor --}}
                                <div class="lg:w-1/2 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Author <span class="text-red-500">*</span></label>
                                            <div x-data="searchableSelect({
                                                endpoint: '{{ route('admin.api.search.characters') }}',
                                                initialValue: post.author_id,
                                                displayField: 'display_name',
                                                subtitleField: '@username',
                                            })" class="relative" x-on:change="post.author_id = $event.detail.value; updatePostPreview(index)">
                                                <input type="hidden" :name="'posts[' + index + '][author_id]'" :value="selectedId" required>
                                                <div class="relative">
                                                    <input
                                                        type="text"
                                                        x-model="query"
                                                        @focus="open = true; fetchResults()"
                                                        @input.debounce.200ms="fetchResults()"
                                                        @keydown.arrow-down.prevent="highlightNext()"
                                                        @keydown.arrow-up.prevent="highlightPrev()"
                                                        @keydown.enter.prevent="selectHighlighted()"
                                                        @keydown.escape="open = false"
                                                        @click.away="open = false"
                                                        :placeholder="selectedLabel || 'Select author...'"
                                                        :class="{ 'text-gray-400': !selectedId && !query }"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10 text-sm"
                                                        autocomplete="off"
                                                    >
                                                </div>
                                                <div x-show="open && (results.length > 0 || loading)"
                                                    class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 max-h-48 overflow-auto"
                                                    style="display: none;">
                                                    <template x-if="loading">
                                                        <div class="px-4 py-3 text-sm text-gray-500">Loading...</div>
                                                    </template>
                                                    <ul class="py-1">
                                                        <template x-for="(item, i) in results" :key="item.id">
                                                            <li @click="select(item)" @mouseenter="highlightedIndex = i"
                                                                :class="{ 'bg-blue-50': highlightedIndex === i }"
                                                                class="px-3 py-2 cursor-pointer hover:bg-gray-50 text-sm">
                                                                <span x-text="item.display_name"></span>
                                                                <span class="text-gray-400 text-xs" x-text="'@' + item.username"></span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Post Date</label>
                                            <input type="datetime-local" :name="'posts[' + index + '][fake_created_at]'"
                                                x-model="post.fake_created_at"
                                                @change="updatePostPreview(index)"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Content <span class="text-red-500">*</span></label>
                                        <textarea :name="'posts[' + index + '][content]'" rows="8" required
                                            x-model="post.content"
                                            @input.debounce.300ms="updatePostPreview(index)"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Visible in Phase</label>
                                        <div x-data="searchableSelect({
                                            endpoint: '{{ route('admin.api.search.phases') }}',
                                            initialValue: post.phase_id,
                                            displayField: 'name',
                                            subtitleField: 'identifier',
                                        })" class="relative" x-on:change="post.phase_id = $event.detail.value">
                                            <input type="hidden" :name="'posts[' + index + '][phase_id]'" :value="selectedId">
                                            <div class="relative">
                                                <input
                                                    type="text"
                                                    :value="displayValue"
                                                    @input.debounce.200ms="query = $event.target.value; fetchResults()"
                                                    @focus="open = true; if(!query) fetchResults()"
                                                    @keydown.escape="open = false"
                                                    @click.away="open = false"
                                                    placeholder="Always visible"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pr-8"
                                                    autocomplete="off"
                                                >
                                                <button x-show="selectedId" type="button" @click.stop="clear()"
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-red-500 rounded"
                                                    title="Clear">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div x-show="open && (results.length > 0 || loading)"
                                                class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 max-h-48 overflow-auto"
                                                style="display: none;">
                                                <ul class="py-1">
                                                    <template x-for="(item, i) in results" :key="item.id">
                                                        <li @click="select(item)" class="px-3 py-2 cursor-pointer hover:bg-gray-50 text-sm">
                                                            <span x-text="item.name"></span>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Inline Choice/Poll --}}
                                    <div class="border rounded-lg mt-2">
                                        <div class="flex items-center justify-between px-3 py-2 bg-gray-50 border-b">
                                            <span class="text-sm font-medium text-gray-700">Choice / Poll</span>
                                            <button type="button"
                                                x-show="!post.hasChoice" @click="post.hasChoice = true; if(post.choiceOptions.length < 2) { post.choiceOptions = [{label:'',description:'',result_votes:{},spawned_post_id:''},{label:'',description:'',result_votes:{},spawned_post_id:''}]; }"
                                                class="text-xs text-blue-600 hover:text-blue-800">+ Add</button>
                                            <button type="button"
                                                x-show="post.hasChoice" @click="post.hasChoice = false"
                                                class="text-xs text-red-600 hover:text-red-800">Remove</button>
                                        </div>
                                        <input type="hidden" :name="'posts[' + index + '][has_choice]'" :value="post.hasChoice ? '1' : '0'">
                                        <div x-show="post.hasChoice" class="p-3 space-y-3">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Type</label>
                                                    <select :name="'posts[' + index + '][choice_type]'" x-model="post.choiceType"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                        <option value="choice">Choice</option>
                                                        <option value="poll">Poll</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Identifier</label>
                                                    <input type="text" :name="'posts[' + index + '][choice_identifier]'" x-model="post.choiceIdentifier"
                                                        placeholder="auto-generated"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Prompt Text</label>
                                                <input type="text" :name="'posts[' + index + '][choice_prompt_text]'" x-model="post.choicePromptText"
                                                    placeholder="What will you do?"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Description</label>
                                                <input type="text" :name="'posts[' + index + '][choice_description]'" x-model="post.choiceDescription"
                                                    placeholder="Internal notes (optional)"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Options</label>
                                                <template x-for="(opt, optIdx) in post.choiceOptions" :key="optIdx">
                                                    <div class="flex gap-2 mb-2">
                                                        <input type="text" :name="'posts[' + index + '][choice_options][' + optIdx + '][label]'" x-model="opt.label"
                                                            placeholder="Label" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                        <input type="text" :name="'posts[' + index + '][choice_options][' + optIdx + '][description]'" x-model="opt.description"
                                                            placeholder="Desc" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                        <button type="button" x-show="post.choiceOptions.length > 2"
                                                            @click="post.choiceOptions.splice(optIdx, 1)"
                                                            class="text-red-500 hover:text-red-700 text-sm px-1">&times;</button>
                                                    </div>
                                                </template>
                                                <button type="button"
                                                    @click="post.choiceOptions.push({label:'',description:'',result_votes:{},spawned_post_id:''})"
                                                    class="text-xs text-blue-600 hover:text-blue-800">+ Add option</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Post Preview --}}
                                <div class="lg:w-1/2">
                                    <div class="bg-gray-100 rounded-lg p-3 h-full">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-gray-500 uppercase">Preview</span>
                                            <button type="button" @click="updatePostPreview(index)" class="text-xs text-blue-600 hover:text-blue-800">
                                                Refresh
                                            </button>
                                        </div>
                                        <div x-show="post.previewLoading" class="text-center py-8 text-gray-500 text-sm">
                                            Loading...
                                        </div>
                                        <div x-show="!post.previewLoading" x-html="post.previewHtml"
                                            class="overflow-auto max-h-80 rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Add Post Button --}}
            <div class="mt-4">
                <button type="button" @click="addPost()"
                    class="flex items-center gap-2 px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors w-full justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Another Post
                </button>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-medium">
                    Create Thread with <span x-text="posts.length"></span> Post<span x-show="posts.length > 1">s</span>
                </button>
                <a href="{{ route('admin.threads.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        window.__threadComposerDefaults = {
            oldPosts: @json(old('posts', [])),
            oldThreadDate: @json(old('fake_created_at', '')),
        };

        function __makeComposerPost(i, old) {
            old = old || {};
            return {
                id: 'post_' + (i + 1),
                author_id: old.author_id || null,
                content: old.content || '',
                fake_created_at: old.fake_created_at || '',
                phase_id: old.phase_id || null,
                hasChoice: old.has_choice === '1',
                choiceType: old.choice_type || 'choice',
                choicePromptText: old.choice_prompt_text || '',
                choiceIdentifier: old.choice_identifier || '',
                choiceDescription: old.choice_description || '',
                choiceOptions: (old.choice_options && old.choice_options.length >= 2)
                    ? old.choice_options.map(o => ({ label: o.label || '', description: o.description || '', result_votes: {}, spawned_post_id: '' }))
                    : [{label:'',description:'',result_votes:{},spawned_post_id:''},{label:'',description:'',result_votes:{},spawned_post_id:''}],
                previewHtml: '<div class="text-center py-8 text-gray-500 text-sm">Enter content and select an author</div>',
                previewLoading: false,
            };
        }

        document.addEventListener('alpine:init', () => {
            const _defaults = window.__threadComposerDefaults;
            const _oldPosts = _defaults.oldPosts;
            const _initialPosts = _oldPosts.length > 0
                ? _oldPosts.map((old, i) => __makeComposerPost(i, old))
                : [__makeComposerPost(0, {})];

            Alpine.data('threadComposer', (config) => ({
                previewUrl: config.previewUrl,
                csrfToken: config.csrfToken,
                threadDate: _defaults.oldThreadDate || '',
                posts: _initialPosts,
                postIdCounter: Math.max(1, _initialPosts.length),

                addPost() {
                    this.postIdCounter++;
                    const lastPost = this.posts[this.posts.length - 1];

                    // Auto-increment date by 5 minutes if last post has a date
                    let newDate = '';
                    if (lastPost.fake_created_at) {
                        const date = new Date(lastPost.fake_created_at);
                        date.setMinutes(date.getMinutes() + 5);
                        newDate = date.toISOString().slice(0, 16);
                    } else if (this.threadDate) {
                        const date = new Date(this.threadDate);
                        date.setMinutes(date.getMinutes() + 5 * this.posts.length);
                        newDate = date.toISOString().slice(0, 16);
                    }

                    const newPost = __makeComposerPost(this.postIdCounter - 1, {});
                    newPost.id = 'post_' + this.postIdCounter;
                    newPost.fake_created_at = newDate;
                    this.posts.push(newPost);
                },

                removePost(index) {
                    if (this.posts.length > 1) {
                        this.posts.splice(index, 1);
                    }
                },

                movePost(index, direction) {
                    const newIndex = index + direction;
                    if (newIndex >= 0 && newIndex < this.posts.length) {
                        const post = this.posts.splice(index, 1)[0];
                        this.posts.splice(newIndex, 0, post);
                    }
                },

                autoIncrementDates() {
                    if (!this.threadDate) return;

                    this.posts.forEach((post, index) => {
                        const date = new Date(this.threadDate);
                        date.setMinutes(date.getMinutes() + 5 * index);
                        post.fake_created_at = date.toISOString().slice(0, 16);
                    });
                },

                async updatePostPreview(index) {
                    const post = this.posts[index];

                    if (!post.author_id || !post.content) {
                        post.previewHtml = '<div class="text-center py-8 text-gray-500 text-sm">Enter content and select an author</div>';
                        return;
                    }

                    post.previewLoading = true;

                    try {
                        const formData = new FormData();
                        formData.append('author_id', post.author_id);
                        formData.append('content', post.content);
                        if (post.fake_created_at) formData.append('fake_created_at', post.fake_created_at);

                        const response = await fetch(this.previewUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: formData,
                        });

                        if (response.ok) {
                            post.previewHtml = await response.text();
                        } else {
                            post.previewHtml = '<div class="text-center py-8 text-red-500 text-sm">Failed to load preview</div>';
                        }
                    } catch (error) {
                        console.error('Preview error:', error);
                        post.previewHtml = '<div class="text-center py-8 text-red-500 text-sm">Failed to load preview</div>';
                    } finally {
                        post.previewLoading = false;
                    }
                },

                prepareSubmit() {
                    // The form data is already bound via x-model and name attributes
                    return true;
                }
            }));
        });
    </script>
    @endpush
</x-admin-layout>
