@props([
    'name',
    'label' => null,
    'endpoint',
    'value' => null,
    'display' => 'name',
    'subtitle' => null,
    'placeholder' => 'Type to search...',
    'required' => false,
])

<div x-data="searchableSelect({
    endpoint: '{{ $endpoint }}',
    initialValue: {{ $value ? $value : 'null' }},
    displayField: '{{ $display }}',
    subtitleField: {!! $subtitle ? "'" . $subtitle . "'" : 'null' !!},
})" class="relative">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <input type="hidden" name="{{ $name }}" :value="selectedId" {{ $required ? 'required' : '' }}>

    <div class="relative">
        <input
            type="text"
            :value="displayValue"
            @input.debounce.200ms="query = $event.target.value; fetchResults()"
            @focus="open = true; if(!query) fetchResults()"
            @keydown.arrow-down.prevent="highlightNext()"
            @keydown.arrow-up.prevent="highlightPrev()"
            @keydown.enter.prevent="selectHighlighted()"
            @keydown.escape="open = false"
            @click.away="open = false"
            placeholder="{{ $placeholder }}"
            :class="{ 'text-gray-400 italic': !selectedId && !query }"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-16"
            autocomplete="off"
        >
        <div class="absolute inset-y-0 right-0 flex items-center gap-1 pr-2">
            <button x-show="selectedId" x-cloak type="button" @click.stop="clear()"
                class="p-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors"
                title="Clear selection">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <svg x-show="!selectedId" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <div
        x-show="open && (results.length > 0 || loading)"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-auto"
        style="display: none;"
    >
        <template x-if="loading">
            <div class="px-4 py-3 text-sm text-gray-500">Loading...</div>
        </template>

        <template x-if="!loading && results.length === 0 && query">
            <div class="px-4 py-3 text-sm text-gray-500">No results found</div>
        </template>

        <ul class="py-1">
            <template x-for="(item, index) in results" :key="item.id">
                <li
                    @click="select(item)"
                    @mouseenter="highlightedIndex = index"
                    :class="{
                        'bg-blue-50': highlightedIndex === index,
                        'text-gray-900': highlightedIndex !== index
                    }"
                    class="px-4 py-2 cursor-pointer hover:bg-gray-50"
                >
                    <div class="flex items-center gap-3">
                        <template x-if="item.avatar_url">
                            <img :src="item.avatar_url" class="w-8 h-8 rounded-full object-cover">
                        </template>
                        <template x-if="!item.avatar_url && item.initial">
                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-medium text-gray-600" x-text="item.initial"></div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate" x-text="getDisplay(item)"></div>
                            <template x-if="getSubtitle(item)">
                                <div class="text-xs text-gray-500 truncate" x-text="getSubtitle(item)"></div>
                            </template>
                        </div>
                    </div>
                </li>
            </template>
        </ul>
    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('searchableSelect', (config) => ({
        endpoint: config.endpoint,
        displayField: config.displayField,
        subtitleField: config.subtitleField,
        selectedId: config.initialValue,
        selectedLabel: '',
        query: '',
        results: [],
        open: false,
        loading: false,
        highlightedIndex: 0,

        get displayValue() {
            // Show query when typing, otherwise show selected label
            return this.query || this.selectedLabel || '';
        },

        async init() {
            if (this.selectedId) {
                await this.fetchInitial();
            }
        },

        async fetchInitial() {
            try {
                const response = await fetch(`${this.endpoint}?id=${this.selectedId}`);
                const data = await response.json();
                if (data.results && data.results.length > 0) {
                    this.selectedLabel = this.getDisplay(data.results[0]);
                }
            } catch (e) {
                console.error('Failed to fetch initial value:', e);
            }
        },

        async fetchResults() {
            this.loading = true;
            this.highlightedIndex = 0;
            try {
                const params = new URLSearchParams({ q: this.query, limit: 20 });
                const response = await fetch(`${this.endpoint}?${params}`);
                const data = await response.json();
                this.results = data.results || [];
            } catch (e) {
                console.error('Search failed:', e);
                this.results = [];
            }
            this.loading = false;
        },

        select(item) {
            this.selectedId = item.id;
            this.selectedLabel = this.getDisplay(item);
            this.query = '';
            this.open = false;
            this.$dispatch('change', { id: item.id, value: item.id, item: item });
        },

        clear() {
            this.selectedId = null;
            this.selectedLabel = '';
            this.query = '';
            this.results = [];
            this.$dispatch('change', { id: null, value: null, item: null });
        },

        getDisplay(item) {
            return item[this.displayField] || item.name || item.title || `#${item.id}`;
        },

        getSubtitle(item) {
            if (!this.subtitleField) return null;
            if (this.subtitleField.startsWith('@')) {
                return '@' + item[this.subtitleField.substring(1)];
            }
            return item[this.subtitleField];
        },

        highlightNext() {
            if (this.highlightedIndex < this.results.length - 1) {
                this.highlightedIndex++;
            }
        },

        highlightPrev() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
            }
        },

        selectHighlighted() {
            if (this.results[this.highlightedIndex]) {
                this.select(this.results[this.highlightedIndex]);
            }
        }
    }));
});
</script>
@endpush
@endonce
