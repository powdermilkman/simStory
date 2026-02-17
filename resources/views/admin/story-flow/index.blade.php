<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Story Flow Editor</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('forum.index') }}" target="_blank" class="text-sm text-gray-600 hover:text-gray-900">
                    Preview Forum →
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="storyFlowEditor()" x-init="init()" 
         @keydown.escape.window="handleEscape()"
         @keydown.delete.window="handleDelete()"
         @keydown.f.window.prevent="currentView === 'graph' && fitGraph()"
         class="flex flex-col" style="height: calc(100vh - 200px); min-height: 600px;">
        {{-- Toolbar --}}
        <div class="bg-white border-b px-4 py-2 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                {{-- View Switcher --}}
                <div class="flex rounded-lg border overflow-hidden">
                    <button @click="currentView = 'graph'" 
                            :class="currentView === 'graph' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            class="px-3 py-1.5 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                        Graph
                    </button>
                    <button @click="currentView = 'timeline'" 
                            :class="currentView === 'timeline' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            class="px-3 py-1.5 text-sm font-medium transition-colors border-l">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Timeline
                    </button>
                </div>

                <div class="h-6 w-px bg-gray-300 mx-2"></div>

                {{-- Create Buttons --}}
                <div class="flex items-center gap-1">
                    <button @click="openCreateModal('category')" class="px-2 py-1 text-xs font-medium rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200" title="New Category">
                        + Category
                    </button>
                    <button @click="openCreateModal('thread')" class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-700 hover:bg-blue-200" title="New Thread">
                        + Thread
                    </button>
                    <button @click="openCreateModal('post')" class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-700 hover:bg-green-200" title="New Post">
                        + Post
                    </button>
                    <button @click="openCreateModal('choice')" class="px-2 py-1 text-xs font-medium rounded bg-purple-100 text-purple-700 hover:bg-purple-200" title="New Choice">
                        + Choice
                    </button>
                    <button @click="openCreateModal('trigger')" class="px-2 py-1 text-xs font-medium rounded bg-amber-100 text-amber-700 hover:bg-amber-200" title="New Trigger">
                        + Trigger
                    </button>
                    <button @click="openCreateModal('message')" class="px-2 py-1 text-xs font-medium rounded bg-pink-100 text-pink-700 hover:bg-pink-200" title="New Message">
                        + Message
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Graph Controls --}}
                <template x-if="currentView === 'graph'">
                    <div class="flex items-center gap-2">
                        <button @click="fitGraph()" class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200" title="Fit to screen">
                            Fit
                        </button>
                        <button @click="togglePhysics()" :class="physicsEnabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'" class="px-2 py-1 text-xs font-medium rounded hover:opacity-80" title="Toggle physics simulation">
                            Physics
                        </button>
                        <button @click="toggleConnectMode()" :class="connectMode ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700'" class="px-2 py-1 text-xs font-medium rounded hover:opacity-80" title="Connect nodes">
                            🔗 Connect
                        </button>
                    </div>
                </template>
                
                <button @click="refreshData()" class="px-3 py-1.5 text-sm font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1 flex overflow-hidden" style="min-height: 0;">
            {{-- Sidebar - Tree View --}}
            <div class="w-64 bg-white border-r overflow-y-auto flex-shrink-0" x-show="showSidebar">
                <div class="p-3 border-b bg-gray-50 flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Content Tree</span>
                    <button @click="showSidebar = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-2">
                    <template x-for="category in treeData" :key="category.id">
                        <div class="mb-2">
                            <div @click="selectNode('category_' + category.id)" 
                                 :class="selectedNode === 'category_' + category.id ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100'"
                                 class="flex items-center gap-2 px-2 py-1.5 rounded cursor-pointer">
                                <button @click.stop="category.expanded = !category.expanded" class="w-4 h-4 flex items-center justify-center">
                                    <svg :class="category.expanded ? 'rotate-90' : ''" class="w-3 h-3 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span class="text-sm font-medium truncate" x-text="category.name"></span>
                            </div>
                            <div x-show="category.expanded" class="ml-4 mt-1 space-y-1">
                                <template x-for="thread in category.threads" :key="thread.id">
                                    <div>
                                        <div @click="selectNode('thread_' + thread.id)"
                                             :class="selectedNode === 'thread_' + thread.id ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100'"
                                             class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer">
                                            <button @click.stop="thread.expanded = !thread.expanded" class="w-4 h-4 flex items-center justify-center">
                                                <svg :class="thread.expanded ? 'rotate-90' : ''" class="w-3 h-3 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <span class="text-xs truncate" x-text="thread.title"></span>
                                        </div>
                                        <div x-show="thread.expanded" class="ml-6 mt-1 space-y-0.5">
                                            <template x-for="post in thread.posts" :key="post.id">
                                                <div @click="selectNode('post_' + post.id)"
                                                     :class="selectedNode === 'post_' + post.id ? 'bg-blue-100 text-blue-800' : 'text-gray-500 hover:bg-gray-100'"
                                                     class="flex items-center gap-2 px-2 py-0.5 rounded cursor-pointer">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    <span class="text-xs truncate" x-text="'Post #' + post.id"></span>
                                                    <span x-show="post.has_choice" class="text-purple-500 text-xs">⚡</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Triggers Section --}}
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex items-center gap-2 px-2 py-1 text-sm font-medium text-gray-700">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Triggers
                        </div>
                        <div class="ml-4 mt-1 space-y-0.5">
                            <template x-for="trigger in triggers" :key="trigger.id">
                                <div @click="selectNode('trigger_' + trigger.id)"
                                     :class="selectedNode === 'trigger_' + trigger.id ? 'bg-blue-100 text-blue-800' : 'text-gray-500 hover:bg-gray-100'"
                                     class="flex items-center gap-2 px-2 py-0.5 rounded cursor-pointer">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span class="text-xs truncate" x-text="trigger.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Choices Section --}}
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex items-center gap-2 px-2 py-1 text-sm font-medium text-gray-700">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            Choices
                        </div>
                        <div class="ml-4 mt-1 space-y-0.5">
                            <template x-for="choice in choices" :key="choice.id">
                                <div @click="selectNode('choice_' + choice.id)"
                                     :class="selectedNode === 'choice_' + choice.id ? 'bg-blue-100 text-blue-800' : 'text-gray-500 hover:bg-gray-100'"
                                     class="flex items-center gap-2 px-2 py-0.5 rounded cursor-pointer">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                    <span class="text-xs truncate" x-text="choice.prompt_text"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Toggle (when hidden) --}}
            <button x-show="!showSidebar" @click="showSidebar = true" 
                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white border border-l-0 rounded-r px-1 py-4 text-gray-400 hover:text-gray-600 shadow z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Main Canvas --}}
            <div class="flex-1 relative bg-gray-100" style="min-height: 500px;">
                {{-- Graph View --}}
                <div x-show="currentView === 'graph'" class="absolute inset-0" style="height: 100%;">
                    <div id="graph-container" style="width: 100%; height: 100%;"></div>
                </div>

                {{-- Timeline View --}}
                <div x-show="currentView === 'timeline'" class="absolute inset-0 overflow-auto p-4">
                    <div id="timeline-container" class="min-w-full">
                        <div class="relative">
                            {{-- Timeline line --}}
                            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                            
                            {{-- Timeline items --}}
                            <div class="space-y-4">
                                <template x-for="item in timelineData" :key="item.id">
                                    <div @click="selectNode(item.id)" 
                                         :class="selectedNode === item.id ? 'ring-2 ring-blue-500' : ''"
                                         class="relative flex items-start gap-4 pl-4 cursor-pointer">
                                        {{-- Timeline dot --}}
                                        <div class="absolute left-6 w-4 h-4 rounded-full border-2 border-white shadow"
                                             :class="{
                                                 'bg-green-500': item.type === 'post',
                                                 'bg-pink-500': item.type === 'message'
                                             }"></div>
                                        
                                        {{-- Content card --}}
                                        <div class="ml-8 flex-1 bg-white rounded-lg shadow p-3 hover:shadow-md transition-shadow">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-medium px-2 py-0.5 rounded"
                                                      :class="{
                                                          'bg-green-100 text-green-700': item.type === 'post',
                                                          'bg-pink-100 text-pink-700': item.type === 'message'
                                                      }"
                                                      x-text="item.type"></span>
                                                <span class="text-xs text-gray-500" x-text="item.date"></span>
                                            </div>
                                            <h4 class="font-medium text-gray-900 text-sm" x-text="item.title"></h4>
                                            <p class="text-xs text-gray-500" x-text="item.subtitle"></p>
                                            <p x-show="item.author" class="text-xs text-gray-400 mt-1">
                                                by <span x-text="item.author"></span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Loading indicator --}}
                <div x-show="loading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-20">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
            </div>

            {{-- Quick Edit Panel --}}
            <div x-show="editPanelOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-80 bg-white border-l shadow-lg overflow-y-auto flex-shrink-0">
                <div class="p-4 border-b bg-gray-50 flex justify-between items-center sticky top-0">
                    <h3 class="font-medium text-gray-900">
                        <span x-text="editingNode?.type ? editingNode.type.charAt(0).toUpperCase() + editingNode.type.slice(1) : ''"></span> Details
                    </h3>
                    <button @click="closeEditPanel()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-4" x-show="editingNode">
                    @include('admin.story-flow.partials.edit-panel')
                </div>
            </div>
        </div>

        {{-- Create Modal --}}
        <div x-show="createModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
             @click.self="createModalOpen = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4" @click.stop>
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-medium text-gray-900">
                        Create New <span x-text="createType ? createType.charAt(0).toUpperCase() + createType.slice(1) : ''"></span>
                    </h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    @include('admin.story-flow.partials.create-modal')
                </div>
            </div>
        </div>

        {{-- Edge Type Modal --}}
        <div x-show="edgeModalOpen" 
             x-transition
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
             @click.self="edgeModalOpen = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4" @click.stop>
                <div class="p-4 border-b">
                    <h3 class="font-medium text-gray-900">Create Connection</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        From: <span class="font-mono text-xs" x-text="pendingEdge?.from"></span><br>
                        To: <span class="font-mono text-xs" x-text="pendingEdge?.to"></span>
                    </p>
                </div>
                <div class="p-4 space-y-2">
                    <p class="text-sm text-gray-600 mb-3">Select connection type:</p>
                    <button @click="createEdge('unlocks')" class="w-full px-4 py-2 text-left text-sm rounded bg-amber-50 text-amber-700 hover:bg-amber-100">
                        🔓 Unlocks - Source unlocks target content
                    </button>
                    <button @click="createEdge('activates')" class="w-full px-4 py-2 text-left text-sm rounded bg-orange-50 text-orange-700 hover:bg-orange-100">
                        ⚡ Activates - Source activates target trigger
                    </button>
                    <button @click="createEdge('has_choice')" class="w-full px-4 py-2 text-left text-sm rounded bg-purple-50 text-purple-700 hover:bg-purple-100">
                        🔀 Has Choice - Post has this choice
                    </button>
                    <button @click="createEdge('spawns')" class="w-full px-4 py-2 text-left text-sm rounded bg-green-50 text-green-700 hover:bg-green-100">
                        ➕ Spawns - Choice option spawns post
                    </button>
                    <button @click="createEdge('contains')" class="w-full px-4 py-2 text-left text-sm rounded bg-blue-50 text-blue-700 hover:bg-blue-100">
                        📁 Contains - Move item to container
                    </button>
                </div>
                <div class="p-4 border-t flex justify-end">
                    <button @click="edgeModalOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                </div>
            </div>
        </div>

        {{-- Connect Mode Indicator --}}
        <div x-show="connectMode" class="absolute top-20 left-1/2 -translate-x-1/2 bg-orange-500 text-white px-4 py-2 rounded-lg shadow-lg z-20 text-sm font-medium">
            🔗 Connect Mode: Click a node, then click another to connect them
            <button @click="toggleConnectMode()" class="ml-2 underline">Cancel</button>
        </div>

        {{-- Legend --}}
        <div class="absolute bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 text-xs z-10" x-show="currentView === 'graph'" x-data="{ showHelp: false }">
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium text-gray-700">Legend</span>
                <button @click="showHelp = !showHelp" class="text-gray-400 hover:text-gray-600" title="Keyboard shortcuts">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-indigo-500"></span> Category
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-blue-500"></span> Thread
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-green-500"></span> Post
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-purple-500"></span> Choice
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-amber-500"></span> Trigger
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-pink-500"></span> Message
                </div>
            </div>
            
            {{-- Keyboard Shortcuts --}}
            <div x-show="showHelp" x-transition class="mt-3 pt-3 border-t border-gray-200">
                <div class="font-medium text-gray-700 mb-2">Shortcuts</div>
                <div class="space-y-1 text-gray-600">
                    <div><kbd class="px-1 bg-gray-100 rounded">Click</kbd> Select node</div>
                    <div><kbd class="px-1 bg-gray-100 rounded">Double-click</kbd> Edit node</div>
                    <div><kbd class="px-1 bg-gray-100 rounded">F</kbd> Fit to screen</div>
                    <div><kbd class="px-1 bg-gray-100 rounded">Esc</kbd> Close/Cancel</div>
                    <div><kbd class="px-1 bg-gray-100 rounded">Del</kbd> Delete selected</div>
                    <div class="pt-1 text-gray-500">Scroll to zoom, drag to pan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- vis.js CDN --}}
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    
    <script>
        function storyFlowEditor() {
            return {
                // State
                currentView: 'graph',
                loading: true,
                showSidebar: true,
                selectedNode: null,
                editPanelOpen: false,
                editingNode: null,
                createModalOpen: false,
                createType: null,
                createForm: {},
                physicsEnabled: true,
                connectMode: false,
                connectFromNode: null,
                edgeModalOpen: false,
                pendingEdge: null,

                // Data
                graphData: { nodes: [], edges: [] },
                timelineData: [],
                treeData: [],
                triggers: [],
                choices: [],
                phases: @json($phases),
                characters: @json($characters),
                categories: @json($categories),

                // vis.js network
                network: null,
                nodes: null,
                edges: null,

                async init() {
                    await this.loadData();
                    this.initGraph();
                    this.loading = false;
                },

                handleEscape() {
                    if (this.edgeModalOpen) {
                        this.edgeModalOpen = false;
                        this.pendingEdge = null;
                    } else if (this.createModalOpen) {
                        this.createModalOpen = false;
                    } else if (this.connectMode) {
                        this.connectMode = false;
                        this.connectFromNode = null;
                    } else if (this.editPanelOpen) {
                        this.closeEditPanel();
                    }
                },

                handleDelete() {
                    // Only delete if edit panel is open and we're not in an input field
                    if (this.editPanelOpen && this.editingNode && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                        this.deleteNode();
                    }
                },

                async loadData() {
                    try {
                        const [graphRes, timelineRes] = await Promise.all([
                            fetch('{{ route("admin.story-flow.data") }}'),
                            fetch('{{ route("admin.story-flow.timeline") }}')
                        ]);
                        this.graphData = await graphRes.json();
                        this.timelineData = await timelineRes.json();
                        this.buildTreeData();
                    } catch (error) {
                        console.error('Failed to load data:', error);
                    }
                },

                buildTreeData() {
                    // Build hierarchical tree from graph data
                    const categories = {};
                    const threads = {};
                    const posts = {};

                    this.graphData.nodes.forEach(node => {
                        if (node.type === 'category') {
                            categories[node.data.id] = { ...node.data, threads: [], expanded: true };
                        } else if (node.type === 'thread') {
                            threads[node.data.id] = { ...node.data, posts: [], expanded: false };
                        } else if (node.type === 'post') {
                            posts[node.data.id] = node.data;
                        } else if (node.type === 'trigger') {
                            this.triggers.push(node.data);
                        } else if (node.type === 'choice') {
                            this.choices.push(node.data);
                        }
                    });

                    // Link posts to threads
                    Object.values(posts).forEach(post => {
                        if (threads[post.thread_id]) {
                            threads[post.thread_id].posts.push(post);
                        }
                    });

                    // Link threads to categories
                    Object.values(threads).forEach(thread => {
                        if (categories[thread.category_id]) {
                            categories[thread.category_id].threads.push(thread);
                        }
                    });

                    this.treeData = Object.values(categories);
                },

                initGraph() {
                    const container = document.getElementById('graph-container');
                    if (!container) return;

                    // Define node colors by type
                    const colors = {
                        category: { background: '#6366f1', border: '#4f46e5' },
                        thread: { background: '#3b82f6', border: '#2563eb' },
                        post: { background: '#22c55e', border: '#16a34a' },
                        choice: { background: '#a855f7', border: '#9333ea' },
                        choice_option: { background: '#c084fc', border: '#a855f7' },
                        trigger: { background: '#f59e0b', border: '#d97706' },
                        message: { background: '#ec4899', border: '#db2777' },
                    };

                    // Transform nodes for vis.js
                    const visNodes = this.graphData.nodes.map(node => ({
                        id: node.id,
                        label: node.label,
                        title: `${node.type}: ${node.label}`,
                        color: colors[node.type] || { background: '#6b7280', border: '#4b5563' },
                        font: { color: '#fff', size: 12 },
                        shape: node.type === 'choice' || node.type === 'choice_option' ? 'diamond' : 
                               node.type === 'trigger' ? 'triangle' : 
                               node.type === 'message' ? 'dot' : 'box',
                        size: node.type === 'category' ? 30 : node.type === 'thread' ? 25 : 20,
                        data: node.data,
                        nodeType: node.type,
                    }));

                    // Transform edges for vis.js
                    const visEdges = this.graphData.edges.map((edge, i) => ({
                        id: i,
                        from: edge.from,
                        to: edge.to,
                        arrows: edge.type === 'contains' ? '' : 'to',
                        dashes: edge.dashes || false,
                        color: { color: edge.color || '#9ca3af', highlight: edge.color || '#6b7280' },
                        title: edge.type,
                        edgeType: edge.type,
                    }));

                    this.nodes = new vis.DataSet(visNodes);
                    this.edges = new vis.DataSet(visEdges);

                    const data = { nodes: this.nodes, edges: this.edges };
                    const options = {
                        physics: {
                            enabled: this.physicsEnabled,
                            solver: 'forceAtlas2Based',
                            forceAtlas2Based: {
                                gravitationalConstant: -50,
                                centralGravity: 0.01,
                                springLength: 150,
                                springConstant: 0.08,
                            },
                            stabilization: { iterations: 100 },
                        },
                        interaction: {
                            hover: true,
                            selectConnectedEdges: true,
                            multiselect: true,
                        },
                        manipulation: {
                            enabled: false,
                        },
                        layout: {
                            improvedLayout: true,
                        },
                    };

                    this.network = new vis.Network(container, data, options);

                    // Event handlers
                    this.network.on('click', (params) => {
                        if (params.nodes.length > 0) {
                            const nodeId = params.nodes[0];
                            
                            if (this.connectMode) {
                                // Connect mode logic
                                if (!this.connectFromNode) {
                                    this.connectFromNode = nodeId;
                                    this.network.selectNodes([nodeId]);
                                } else if (this.connectFromNode !== nodeId) {
                                    // Second node clicked, show edge type modal
                                    this.pendingEdge = {
                                        from: this.connectFromNode,
                                        to: nodeId
                                    };
                                    this.edgeModalOpen = true;
                                }
                            } else {
                                this.selectNode(nodeId);
                            }
                        } else {
                            this.selectedNode = null;
                            this.closeEditPanel();
                            if (this.connectMode) {
                                this.connectFromNode = null;
                            }
                        }
                    });

                    this.network.on('doubleClick', (params) => {
                        if (params.nodes.length > 0 && !this.connectMode) {
                            this.openEditPanel(params.nodes[0]);
                        }
                    });
                },

                selectNode(nodeId) {
                    this.selectedNode = nodeId;
                    if (this.network) {
                        this.network.selectNodes([nodeId]);
                    }
                    
                    // Find node data
                    const node = this.graphData.nodes.find(n => n.id === nodeId);
                    if (node) {
                        this.editingNode = { ...node.data, type: node.type, nodeId: nodeId };
                        this.editPanelOpen = true;
                    }
                },

                openEditPanel(nodeId) {
                    this.selectNode(nodeId);
                },

                closeEditPanel() {
                    this.editPanelOpen = false;
                },

                openCreateModal(type) {
                    this.createType = type;
                    this.createForm = this.getDefaultFormData(type);
                    this.createModalOpen = true;
                },

                getDefaultFormData(type) {
                    switch (type) {
                        case 'category':
                            return { name: '', description: '' };
                        case 'thread':
                            return { title: '', category_id: this.categories[0]?.id || '', author_id: this.characters[0]?.id || '', first_post_content: '' };
                        case 'post':
                            return { thread_id: '', author_id: this.characters[0]?.id || '', content: '' };
                        case 'choice':
                            return { prompt_text: '', type: 'choice', trigger_post_id: '', options: [{ label: 'Option 1' }, { label: 'Option 2' }] };
                        case 'trigger':
                            return { name: '', description: '' };
                        case 'message':
                            return { subject: '', content: '', sender_id: this.characters[0]?.id || '', recipient_id: this.characters[1]?.id || '' };
                        default:
                            return {};
                    }
                },

                async createNode() {
                    try {
                        const response = await fetch('{{ route("admin.story-flow.node.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ type: this.createType, ...this.createForm }),
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.createModalOpen = false;
                            await this.refreshData();
                        } else {
                            alert('Failed to create: ' + (result.error || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Create failed:', error);
                        alert('Failed to create node');
                    }
                },

                async updateNode() {
                    if (!this.editingNode) return;
                    
                    const [type, id] = this.editingNode.nodeId.split('_');
                    try {
                        const response = await fetch(`{{ url('admin/story-flow/node') }}/${type}/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify(this.editingNode),
                        });
                        const result = await response.json();
                        if (result.success) {
                            await this.refreshData();
                        } else {
                            alert('Failed to update');
                        }
                    } catch (error) {
                        console.error('Update failed:', error);
                        alert('Failed to update node');
                    }
                },

                async deleteNode() {
                    if (!this.editingNode) return;
                    if (!confirm('Are you sure you want to delete this item?')) return;
                    
                    const [type, id] = this.editingNode.nodeId.split('_');
                    try {
                        const response = await fetch(`{{ url('admin/story-flow/node') }}/${type}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.closeEditPanel();
                            await this.refreshData();
                        } else {
                            alert('Failed to delete');
                        }
                    } catch (error) {
                        console.error('Delete failed:', error);
                        alert('Failed to delete node');
                    }
                },

                async refreshData() {
                    this.loading = true;
                    this.triggers = [];
                    this.choices = [];
                    await this.loadData();
                    
                    // Update vis.js
                    if (this.network && this.nodes && this.edges) {
                        const colors = {
                            category: { background: '#6366f1', border: '#4f46e5' },
                            thread: { background: '#3b82f6', border: '#2563eb' },
                            post: { background: '#22c55e', border: '#16a34a' },
                            choice: { background: '#a855f7', border: '#9333ea' },
                            choice_option: { background: '#c084fc', border: '#a855f7' },
                            trigger: { background: '#f59e0b', border: '#d97706' },
                            message: { background: '#ec4899', border: '#db2777' },
                        };

                        const visNodes = this.graphData.nodes.map(node => ({
                            id: node.id,
                            label: node.label,
                            title: `${node.type}: ${node.label}`,
                            color: colors[node.type] || { background: '#6b7280', border: '#4b5563' },
                            font: { color: '#fff', size: 12 },
                            shape: node.type === 'choice' || node.type === 'choice_option' ? 'diamond' : 
                                   node.type === 'trigger' ? 'triangle' : 
                                   node.type === 'message' ? 'dot' : 'box',
                            size: node.type === 'category' ? 30 : node.type === 'thread' ? 25 : 20,
                            data: node.data,
                            nodeType: node.type,
                        }));

                        const visEdges = this.graphData.edges.map((edge, i) => ({
                            id: i,
                            from: edge.from,
                            to: edge.to,
                            arrows: edge.type === 'contains' ? '' : 'to',
                            dashes: edge.dashes || false,
                            color: { color: edge.color || '#9ca3af', highlight: edge.color || '#6b7280' },
                            title: edge.type,
                            edgeType: edge.type,
                        }));

                        this.nodes.clear();
                        this.edges.clear();
                        this.nodes.add(visNodes);
                        this.edges.add(visEdges);
                    }
                    
                    this.loading = false;
                },

                fitGraph() {
                    if (this.network) {
                        this.network.fit({ animation: true });
                    }
                },

                togglePhysics() {
                    this.physicsEnabled = !this.physicsEnabled;
                    if (this.network) {
                        this.network.setOptions({ physics: { enabled: this.physicsEnabled } });
                    }
                },

                toggleConnectMode() {
                    this.connectMode = !this.connectMode;
                    this.connectFromNode = null;
                    if (this.connectMode) {
                        this.closeEditPanel();
                    }
                },

                async createEdge(edgeType) {
                    if (!this.pendingEdge) return;

                    const [fromType, fromId] = this.pendingEdge.from.split('_');
                    const fromIdNum = fromType === 'choice' && this.pendingEdge.from.includes('option') 
                        ? parseInt(this.pendingEdge.from.split('_').pop())
                        : parseInt(fromId);
                    
                    const toParts = this.pendingEdge.to.split('_');
                    const toType = toParts[0] === 'choice' && toParts.length > 2 ? 'choice_option' : toParts[0];
                    const toId = parseInt(toParts[toParts.length - 1]);

                    // Determine actual from type
                    const actualFromType = this.pendingEdge.from.includes('choice_option') ? 'choice_option' : fromType;

                    try {
                        const response = await fetch('{{ route("admin.story-flow.edge.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                from_type: actualFromType,
                                from_id: fromIdNum,
                                to_type: toType,
                                to_id: toId,
                                edge_type: edgeType,
                            }),
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.edgeModalOpen = false;
                            this.pendingEdge = null;
                            this.connectFromNode = null;
                            await this.refreshData();
                        } else {
                            alert('Failed to create connection');
                        }
                    } catch (error) {
                        console.error('Edge creation failed:', error);
                        alert('Failed to create connection');
                    }
                },

                async deleteEdge(fromNodeId, toNodeId, edgeType) {
                    const [fromType, fromId] = fromNodeId.split('_');
                    const [toType, toId] = toNodeId.split('_');

                    try {
                        const response = await fetch('{{ route("admin.story-flow.edge.destroy") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                from_type: fromType,
                                from_id: parseInt(fromId),
                                to_type: toType,
                                to_id: parseInt(toId),
                                edge_type: edgeType,
                            }),
                        });
                        const result = await response.json();
                        if (result.success) {
                            await this.refreshData();
                        }
                    } catch (error) {
                        console.error('Edge deletion failed:', error);
                    }
                },

                addChoiceOption() {
                    this.createForm.options.push({ label: `Option ${this.createForm.options.length + 1}` });
                },

                removeChoiceOption(index) {
                    if (this.createForm.options.length > 2) {
                        this.createForm.options.splice(index, 1);
                    }
                },
            };
        }
    </script>
</x-admin-layout>
