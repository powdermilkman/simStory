{{-- Category Create --}}
<template x-if="createType === 'category'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" x-model="createForm.name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea x-model="createForm.description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div class="pt-2 flex gap-2 justify-end">
            <button @click="createModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
            <button @click="createNode()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Create Category</button>
        </div>
    </div>
</template>

{{-- Thread Create --}}
<template x-if="createType === 'thread'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" x-model="createForm.title" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select x-model="createForm.category_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                <select x-model="createForm.author_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <template x-for="char in characters" :key="char.id">
                        <option :value="char.id" x-text="char.display_name || char.username"></option>
                    </template>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Post Content</label>
            <textarea x-model="createForm.first_post_content" rows="4" placeholder="Write the first post content..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div class="pt-2 flex gap-2 justify-end">
            <button @click="createModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
            <button @click="createNode()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Create Thread</button>
        </div>
    </div>
</template>

{{-- Post Create --}}
<template x-if="createType === 'post'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Thread</label>
            <select x-model="createForm.thread_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select a thread...</option>
                <template x-for="category in treeData" :key="category.id">
                    <optgroup :label="category.name">
                        <template x-for="thread in category.threads" :key="thread.id">
                            <option :value="thread.id" x-text="thread.title"></option>
                        </template>
                    </optgroup>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
            <select x-model="createForm.author_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <template x-for="char in characters" :key="char.id">
                    <option :value="char.id" x-text="char.display_name || char.username"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea x-model="createForm.content" rows="4" placeholder="Write the post content..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div class="pt-2 flex gap-2 justify-end">
            <button @click="createModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
            <button @click="createNode()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Create Post</button>
        </div>
    </div>
</template>

{{-- Choice Create --}}
<template x-if="createType === 'choice'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prompt Text</label>
            <input type="text" x-model="createForm.prompt_text" required placeholder="What will you do?" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select x-model="createForm.type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="choice">Choice (branching story)</option>
                <option value="poll">Poll (show results)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attach to Post (optional)</label>
            <select x-model="createForm.trigger_post_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Not attached to a post</option>
                <template x-for="category in treeData" :key="category.id">
                    <optgroup :label="category.name">
                        <template x-for="thread in category.threads" :key="thread.id">
                            <template x-for="post in thread.posts" :key="post.id">
                                <option :value="post.id" x-text="thread.title + ' - Post #' + post.id"></option>
                            </template>
                        </template>
                    </optgroup>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
            <div class="space-y-2">
                <template x-for="(option, index) in createForm.options" :key="index">
                    <div class="flex gap-2">
                        <input type="text" x-model="option.label" :placeholder="'Option ' + (index + 1)" class="flex-1 rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="button" @click="removeChoiceOption(index)" x-show="createForm.options.length > 2" class="px-2 text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
            <button type="button" @click="addChoiceOption()" class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add option</button>
        </div>
        <div class="pt-2 flex gap-2 justify-end">
            <button @click="createModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
            <button @click="createNode()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Create Choice</button>
        </div>
    </div>
</template>

{{-- Trigger Create --}}
<template x-if="createType === 'trigger'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" x-model="createForm.name" required placeholder="e.g., Read Introduction" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea x-model="createForm.description" rows="2" placeholder="What does this trigger do?" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <p class="text-xs text-gray-500">Note: Add conditions to this trigger after creating it using the full trigger editor.</p>
        <div class="pt-2 flex gap-2 justify-end">
            <button @click="createModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
            <button @click="createNode()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Create Trigger</button>
        </div>
    </div>
</template>

{{-- Message Create --}}
<template x-if="createType === 'message'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input type="text" x-model="createForm.subject" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                <select x-model="createForm.sender_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <template x-for="char in characters" :key="char.id">
                        <option :value="char.id" x-text="char.display_name || char.username"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <select x-model="createForm.recipient_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <template x-for="char in characters" :key="char.id">
                        <option :value="char.id" x-text="char.display_name || char.username"></option>
                    </template>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea x-model="createForm.content" rows="4" placeholder="Message content..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div class="pt-2 flex gap-2 justify-end">
            <button @click="createModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
            <button @click="createNode()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Create Message</button>
        </div>
    </div>
</template>
