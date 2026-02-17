<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Content Trigger</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
        <form action="{{ route('admin.triggers.store') }}" method="POST" x-data="triggerForm()">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Trigger Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="e.g., Read Introduction Thread"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">Identifier (Optional)</label>
                <input type="text" name="identifier" id="identifier" value="{{ old('identifier') }}"
                    placeholder="Auto-generated if empty"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono">
                <p class="mt-1 text-xs text-gray-500">Unique code for programmatic reference</p>
                @error('identifier')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                <textarea name="description" id="description" rows="2"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Conditions <span class="text-gray-500 font-normal">(ALL must be met)</span>
                </label>
                
                <template x-for="(condition, index) in conditions" :key="index">
                    <div class="mb-3 p-4 bg-gray-50 rounded-lg border">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-sm font-medium text-gray-600">Condition <span x-text="index + 1"></span></span>
                            <button type="button" x-show="conditions.length > 1" @click="removeCondition(index)"
                                class="text-red-600 hover:text-red-800 text-sm">
                                Remove
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                                <select :name="'conditions[' + index + '][type]'" x-model="condition.type" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select type...</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Target ID for view/react types --}}
                            <div x-show="condition.type && condition.type !== 'choice'">
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    <span x-show="condition.type === 'view_thread'">Thread</span>
                                    <span x-show="condition.type !== 'view_thread'">Post</span>
                                </label>
                                <select :name="'conditions[' + index + '][target_id]'" x-model="condition.target_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select target...</option>
                                    <template x-if="condition.type === 'view_thread'">
                                        <template x-for="thread in threads" :key="thread.id">
                                            <option :value="thread.id" x-text="thread.title"></option>
                                        </template>
                                    </template>
                                    <template x-if="condition.type !== 'view_thread' && condition.type !== 'choice'">
                                        <template x-for="post in posts" :key="post.id">
                                            <option :value="post.id" x-text="post.thread_title + ' - Post #' + post.id"></option>
                                        </template>
                                    </template>
                                </select>
                            </div>

                            {{-- Choice option for choice type --}}
                            <div x-show="condition.type === 'choice'">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Choice Option</label>
                                <select :name="'conditions[' + index + '][choice_option_id]'" x-model="condition.choice_option_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select option...</option>
                                    <template x-for="option in choiceOptions" :key="option.id">
                                        <option :value="option.id" x-text="option.label + ' (from: ' + option.choice_title + ')'"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addCondition()"
                    class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                    + Add another condition
                </button>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Create Trigger
                </button>
                <a href="{{ route('admin.triggers.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>

    @php
        $threadsData = $threads->map(function($t) {
            return ['id' => $t->id, 'title' => $t->title];
        })->toArray();
        
        $postsData = $posts->map(function($p) {
            return ['id' => $p->id, 'thread_title' => $p->thread?->title ?? 'Unknown'];
        })->toArray();
        
        $choiceOptionsData = $choiceOptions->map(function($o) {
            return ['id' => $o->id, 'label' => $o->label, 'choice_title' => $o->choice?->prompt_text ?? 'Unknown'];
        })->toArray();
    @endphp

    <script>
        function triggerForm() {
            return {
                conditions: [{ type: '', target_id: '', choice_option_id: '' }],
                threads: @json($threadsData),
                posts: @json($postsData),
                choiceOptions: @json($choiceOptionsData),
                
                addCondition() {
                    this.conditions.push({ type: '', target_id: '', choice_option_id: '' });
                },
                removeCondition(index) {
                    this.conditions.splice(index, 1);
                }
            }
        }
    </script>
</x-admin-layout>
