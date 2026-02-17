{{-- Category Edit --}}
<template x-if="editingNode?.type === 'category'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" x-model="editingNode.name" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea x-model="editingNode.description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
            <input type="number" x-model="editingNode.sort_order" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="pt-2 flex gap-2">
            <button @click="updateNode()" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save</button>
            <button @click="deleteNode()" class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete</button>
        </div>
        <div class="text-xs text-gray-500 pt-2">
            <a :href="'{{ route('admin.categories.index') }}/' + editingNode.id + '/edit'" target="_blank" class="text-blue-600 hover:underline">Open in full editor →</a>
        </div>
    </div>
</template>

{{-- Thread Edit --}}
<template x-if="editingNode?.type === 'thread'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" x-model="editingNode.title" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select x-model="editingNode.category_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <template x-for="cat in categories" :key="cat.id">
                    <option :value="cat.id" x-text="cat.name"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
            <select x-model="editingNode.author_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <template x-for="char in characters" :key="char.id">
                    <option :value="char.id" x-text="char.display_name || char.username"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="datetime-local" x-model="editingNode.fake_created_at" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="flex gap-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="editingNode.is_pinned" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Pinned</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="editingNode.is_locked" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Locked</span>
            </label>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Visible in Phase</label>
            <select x-model="editingNode.phase_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Always visible</option>
                <template x-for="phase in phases" :key="phase.id">
                    <option :value="phase.id" x-text="phase.name"></option>
                </template>
            </select>
        </div>
        <div class="pt-2 flex gap-2">
            <button @click="updateNode()" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save</button>
            <button @click="deleteNode()" class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete</button>
        </div>
        <div class="text-xs text-gray-500 pt-2">
            <a :href="'{{ route('admin.threads.index') }}/' + editingNode.id" target="_blank" class="text-blue-600 hover:underline">View thread →</a>
        </div>
    </div>
</template>

{{-- Post Edit --}}
<template x-if="editingNode?.type === 'post'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Thread</label>
            <p class="text-sm text-gray-600" x-text="editingNode.thread_title"></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
            <select x-model="editingNode.author_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <template x-for="char in characters" :key="char.id">
                    <option :value="char.id" x-text="char.display_name || char.username"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea x-model="editingNode.content" rows="6" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="datetime-local" x-model="editingNode.fake_created_at" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Visible in Phase</label>
            <select x-model="editingNode.phase_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Always visible</option>
                <template x-for="phase in phases" :key="phase.id">
                    <option :value="phase.id" x-text="phase.name"></option>
                </template>
            </select>
        </div>
        <div x-show="editingNode.has_choice" class="p-2 bg-purple-50 rounded text-sm text-purple-700">
            This post has an attached choice
        </div>
        <div class="pt-2 flex gap-2">
            <button @click="updateNode()" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save</button>
            <button @click="deleteNode()" class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete</button>
        </div>
        <div class="text-xs text-gray-500 pt-2">
            <a :href="'{{ route('admin.posts.index') }}/' + editingNode.id" target="_blank" class="text-blue-600 hover:underline">View post →</a>
        </div>
    </div>
</template>

{{-- Choice Edit --}}
<template x-if="editingNode?.type === 'choice'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prompt</label>
            <input type="text" x-model="editingNode.prompt_text" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select x-model="editingNode.type" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="choice">Choice (branching)</option>
                <option value="poll">Poll (results)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Identifier</label>
            <input type="text" x-model="editingNode.identifier" readonly class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea x-model="editingNode.description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div class="pt-2 flex gap-2">
            <button @click="updateNode()" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save</button>
            <button @click="deleteNode()" class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete</button>
        </div>
        <div class="text-xs text-gray-500 pt-2" x-show="editingNode.trigger_post_id">
            <a :href="'{{ route('admin.posts.index') }}/' + editingNode.trigger_post_id + '/edit'" target="_blank" class="text-blue-600 hover:underline">Edit parent post →</a>
        </div>
    </div>
</template>

{{-- Choice Option Edit --}}
<template x-if="editingNode?.type === 'choice_option'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
            <input type="text" x-model="editingNode.label" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea x-model="editingNode.description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div class="pt-2 flex gap-2">
            <button @click="updateNode()" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save</button>
            <button @click="deleteNode()" class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete</button>
        </div>
    </div>
</template>

{{-- Trigger Edit --}}
<template x-if="editingNode?.type === 'trigger'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" x-model="editingNode.name" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Identifier</label>
            <input type="text" x-model="editingNode.identifier" readonly class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea x-model="editingNode.description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div class="p-2 bg-amber-50 rounded text-sm text-amber-700">
            <span x-text="editingNode.conditions_count || 0"></span> condition(s)
        </div>
        <div class="pt-2 flex gap-2">
            <button @click="updateNode()" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save</button>
            <button @click="deleteNode()" class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete</button>
        </div>
        <div class="text-xs text-gray-500 pt-2">
            <a :href="'{{ route('admin.triggers.index') }}/' + editingNode.id" target="_blank" class="text-blue-600 hover:underline">Manage conditions →</a>
        </div>
    </div>
</template>

{{-- Message Edit --}}
<template x-if="editingNode?.type === 'message'">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input type="text" x-model="editingNode.subject" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sender</label>
            <select x-model="editingNode.sender_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <template x-for="char in characters" :key="char.id">
                    <option :value="char.id" x-text="char.display_name || char.username"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
            <select x-model="editingNode.recipient_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <template x-for="char in characters" :key="char.id">
                    <option :value="char.id" x-text="char.display_name || char.username"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea x-model="editingNode.content" rows="4" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date Sent</label>
            <input type="datetime-local" x-model="editingNode.fake_sent_at" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Visible in Phase</label>
            <select x-model="editingNode.phase_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Always visible</option>
                <template x-for="phase in phases" :key="phase.id">
                    <option :value="phase.id" x-text="phase.name"></option>
                </template>
            </select>
        </div>
        <div class="pt-2 flex gap-2">
            <button @click="updateNode()" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save</button>
            <button @click="deleteNode()" class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete</button>
        </div>
        <div class="text-xs text-gray-500 pt-2">
            <a :href="'{{ route('admin.private-messages.index') }}/' + editingNode.id" target="_blank" class="text-blue-600 hover:underline">View message →</a>
        </div>
    </div>
</template>
