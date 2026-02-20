{{-- Inline Choice/Poll Editor for Posts --}}
{{-- Usage: @include('admin.partials.choice-editor', ['choice' => $post->choice, 'posts' => route('admin.api.search.posts')]) --}}

@php
    $existingChoice = isset($choice) && $choice ? $choice : null;
    $existingOptions = $existingChoice
        ? $existingChoice->options->map(fn($o, $idx) => [
            'id' => $o->id,
            'label' => $o->label,
            'description' => $o->description ?? '',
            'result_votes' => $o->result_votes ?? (object)[],
            'spawned_post_id' => $o->spawned_post_id ?? '',
        ])->values()->toArray()
        : [];
@endphp

<div x-data="choiceEditor({
    hasChoice: {{ $existingChoice ? 'true' : 'false' }},
    choiceId: {{ $existingChoice ? $existingChoice->id : 'null' }},
    type: {{ json_encode(old('choice_type', $existingChoice?->type ?? 'choice')) }},
    promptText: {{ json_encode(old('choice_prompt_text', $existingChoice?->prompt_text ?? '')) }},
    identifier: {{ json_encode(old('choice_identifier', $existingChoice?->identifier ?? '')) }},
    description: {{ json_encode(old('choice_description', $existingChoice?->description ?? '')) }},
    options: {{ json_encode(old('choice_options', $existingOptions) ?: []) }},
})" class="border rounded-lg">
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
        <h4 class="font-medium text-gray-700">Choice / Poll</h4>
        <div>
            <button type="button" x-show="!enabled" @click="enable()" class="text-sm text-blue-600 hover:text-blue-800">
                + Add Choice
            </button>
            <button type="button" x-show="enabled" @click="disable()" class="text-sm text-red-600 hover:text-red-800">
                Remove Choice
            </button>
        </div>
    </div>

    <input type="hidden" name="has_choice" :value="enabled ? '1' : '0'">
    <input type="hidden" name="choice_id" :value="choiceId || ''">

    <div x-show="enabled" x-transition class="p-4 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="choice_type" x-model="type"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="choice">Choice (branching story)</option>
                    <option value="poll">Poll (show results)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Identifier</label>
                <input type="text" name="choice_identifier" x-model="identifier"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                    placeholder="auto-generated if blank">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prompt Text</label>
            <input type="text" name="choice_prompt_text" x-model="promptText"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                placeholder="What will you do?">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
            <textarea name="choice_description" x-model="description" rows="2"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                placeholder="Internal notes about this choice"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>

            <template x-for="(option, index) in options" :key="index">
                <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                    <input type="hidden" :name="'choice_options[' + index + '][id]'" :value="option.id || ''">
                    <div class="flex gap-3 mb-2">
                        <div class="flex-1">
                            <input type="text" :name="'choice_options[' + index + '][label]'" x-model="option.label"
                                placeholder="Option label"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="flex-1">
                            <input type="text" :name="'choice_options[' + index + '][description]'" x-model="option.description"
                                placeholder="Description (optional)"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <button type="button" x-show="options.length > 2" @click="removeOption(index)"
                            class="px-2 py-1 text-red-500 hover:text-red-700 text-sm">
                            &times;
                        </button>
                    </div>

                    {{-- Poll mode: result votes --}}
                    <div x-show="type === 'poll'" class="mt-2 p-2 bg-white rounded border">
                        <p class="text-xs text-gray-500 mb-1">
                            Vote counts when "<span x-text="option.label || 'this option'"></span>" is chosen:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(opt, optIdx) in options" :key="'rv_' + index + '_' + optIdx">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-500 truncate max-w-[80px]" x-text="opt.label || ('Opt ' + (optIdx + 1))"></span>
                                    <input type="number"
                                        :name="'choice_options[' + index + '][result_votes][' + optIdx + ']'"
                                        x-model.number="option.result_votes[optIdx]"
                                        min="0" placeholder="0"
                                        class="w-16 text-xs rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Choice mode: spawned post --}}
                    <div x-show="type === 'choice'" class="mt-2">
                        <label class="text-xs text-gray-500">Spawned reply post (optional)</label>
                        <div x-data="searchableSelect({
                            endpoint: '{{ route('admin.api.search.posts') }}',
                            initialValue: option.spawned_post_id,
                            displayField: 'name',
                            subtitleField: 'thread_title',
                        })" class="relative" x-on:change="option.spawned_post_id = $event.detail.value">
                            <input type="hidden" :name="'choice_options[' + index + '][spawned_post_id]'" :value="selectedId">
                            <div class="relative">
                                <input type="text"
                                    x-model="query"
                                    @focus="open = true; fetchResults()"
                                    @input.debounce.200ms="fetchResults()"
                                    @keydown.escape="open = false"
                                    @click.away="open = false"
                                    :placeholder="selectedLabel || 'No spawned post'"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pr-8"
                                    autocomplete="off">
                                <button x-show="selectedId" type="button" @click.stop="clear()"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-red-500 rounded">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="open && (results.length > 0 || loading)"
                                class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 max-h-32 overflow-auto"
                                style="display: none;">
                                <ul class="py-1">
                                    <template x-for="(item, i) in results" :key="item.id">
                                        <li @click="select(item)" class="px-3 py-2 cursor-pointer hover:bg-gray-50 text-sm">
                                            <span x-text="item.name || ('Post #' + item.id)"></span>
                                            <span class="text-gray-400 text-xs" x-text="item.thread_title ? '(' + item.thread_title + ')' : ''"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addOption()" class="text-sm text-blue-600 hover:text-blue-800">
                + Add option
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('choiceEditor', (config) => ({
        enabled: config.hasChoice,
        choiceId: config.choiceId,
        type: config.type || 'choice',
        promptText: config.promptText || '',
        identifier: config.identifier || '',
        description: config.description || '',
        options: config.options.length > 0 ? config.options : [
            { id: null, label: '', description: '', result_votes: {}, spawned_post_id: '' },
            { id: null, label: '', description: '', result_votes: {}, spawned_post_id: '' },
        ],

        init() {
            // Ensure result_votes objects exist
            this.options.forEach(opt => {
                if (!opt.result_votes || typeof opt.result_votes !== 'object') {
                    opt.result_votes = {};
                }
            });
        },

        enable() {
            this.enabled = true;
            if (this.options.length < 2) {
                this.options = [
                    { id: null, label: '', description: '', result_votes: {}, spawned_post_id: '' },
                    { id: null, label: '', description: '', result_votes: {}, spawned_post_id: '' },
                ];
            }
        },

        disable() {
            this.enabled = false;
        },

        addOption() {
            const newOpt = { id: null, label: '', description: '', result_votes: {}, spawned_post_id: '' };
            this.options.forEach((_, idx) => { newOpt.result_votes[idx] = 0; });
            newOpt.result_votes[this.options.length] = 0;
            this.options.push(newOpt);
            // Add slot for new option in existing options' result_votes
            const newIdx = this.options.length - 1;
            this.options.forEach((opt, idx) => {
                if (idx !== newIdx) { opt.result_votes[newIdx] = 0; }
            });
        },

        removeOption(index) {
            this.options.splice(index, 1);
            this.options.forEach(opt => {
                const newVotes = {};
                this.options.forEach((_, newIdx) => { newVotes[newIdx] = opt.result_votes[newIdx] || 0; });
                opt.result_votes = newVotes;
            });
        },
    }));
});
</script>
@endpush
@endonce
