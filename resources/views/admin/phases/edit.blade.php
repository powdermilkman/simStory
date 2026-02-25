<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Phase: {{ $phase->name }}</h2>
    </x-slot>

    <form action="{{ route('admin.phases.update', $phase) }}" method="POST" x-data="phaseForm()">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $phase->name) }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">Identifier</label>
                            <input type="text" name="identifier" id="identifier" value="{{ old('identifier', $phase->identifier) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">
                            @error('identifier')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="2"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $phase->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="parent_phase_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Phase</label>
                            <select name="parent_phase_id" id="parent_phase_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">None (Root Phase)</option>
                                @foreach($parentPhases as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_phase_id', $phase->parent_phase_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $phase->sort_order) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4 flex gap-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $phase->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="requires_all_sibling_phases" value="1" {{ old('requires_all_sibling_phases', $phase->requires_all_sibling_phases) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Requires all previous sibling phases</span>
                        </label>
                    </div>
                </div>

                <!-- Conditions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Conditions</h3>
                        <button type="button" @click="addCondition()" class="text-sm text-blue-600 hover:text-blue-800">
                            + Add Condition
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">All conditions must be met for the phase to complete.</p>

                    <div class="space-y-4">
                        <template x-for="(condition, index) in conditions" :key="condition.id">
                            <div class="border border-gray-200 rounded-lg p-4 relative">
                                <button type="button" @click="removeCondition(condition.id)"
                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                        <select :name="'conditions[' + index + '][type]'" x-model="condition.type"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select type...</option>
                                            <option value="view_post">Post Viewed</option>
                                            <option value="react_post">Post Reacted To</option>
                                            <option value="report_post">Post Reported</option>
                                            <option value="view_thread">Thread Viewed</option>
                                            <option value="all_posts_in_thread">All Posts in Thread Viewed</option>
                                            <option value="choice">Choice Made</option>
                                            <option value="phase_complete">Other Phase Complete</option>
                                        </select>
                                    </div>

                                    <div x-show="condition.type === 'choice'">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Choice Option</label>
                                        <select :name="'conditions[' + index + '][choice_option_id]'" x-model="condition.choice_option_id"
                                            x-bind:disabled="condition.type !== 'choice'"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select option...</option>
                                            @foreach($choiceOptions as $option)
                                                <option value="{{ $option->id }}">{{ $option->choice?->prompt_text }}: {{ $option->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div x-show="condition.type === 'view_post' || condition.type === 'react_post' || condition.type === 'report_post'">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Post</label>
                                        <input type="hidden" :name="'conditions[' + index + '][target_type]'" value="post" x-bind:disabled="!['view_post', 'react_post', 'report_post'].includes(condition.type)">
                                        <select :name="'conditions[' + index + '][target_id]'" x-model="condition.target_id"
                                            x-bind:disabled="!['view_post', 'react_post', 'report_post'].includes(condition.type)"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select post...</option>
                                            @foreach($posts as $post)
                                                <option value="{{ $post->id }}">#{{ $post->id }} - {{ Str::limit(strip_tags($post->content), 50) }} ({{ $post->thread->title }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div x-show="condition.type === 'view_thread' || condition.type === 'all_posts_in_thread'">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Thread</label>
                                        <input type="hidden" :name="'conditions[' + index + '][target_type]'" value="thread" x-bind:disabled="!['view_thread', 'all_posts_in_thread'].includes(condition.type)">
                                        <select :name="'conditions[' + index + '][target_id]'" x-model="condition.target_id"
                                            x-bind:disabled="!['view_thread', 'all_posts_in_thread'].includes(condition.type)"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select thread...</option>
                                            @foreach($threads as $thread)
                                                <option value="{{ $thread->id }}">{{ $thread->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div x-show="condition.type === 'phase_complete'">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phase</label>
                                        <input type="hidden" :name="'conditions[' + index + '][target_type]'" value="phase" x-bind:disabled="condition.type !== 'phase_complete'">
                                        <select :name="'conditions[' + index + '][target_id]'" x-model="condition.target_id"
                                            x-bind:disabled="condition.type !== 'phase_complete'"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select phase...</option>
                                            @foreach($allPhases as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="conditions.length === 0" class="text-center py-8 text-gray-500">
                        No conditions added. Click "+ Add Condition" to add one.
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                        <button type="button" @click="addAction()" class="text-sm text-blue-600 hover:text-blue-800">
                            + Add Action
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Actions execute in order when all conditions are met.</p>

                    <div class="space-y-4">
                        <template x-for="(action, index) in actions" :key="action.id">
                            <div class="border border-gray-200 rounded-lg p-4 relative">
                                <button type="button" @click="removeAction(action.id)"
                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                                        <select :name="'actions[' + index + '][type]'" x-model="action.type"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select type...</option>
                                            <option value="send_message">Send Message to Reader</option>
                                            <option value="modify_character">Modify Character</option>
                                            <option value="trigger_phase">Trigger Another Phase</option>
                                        </select>
                                    </div>

                                    <!-- Send Message Fields -->
                                    <div x-show="action.type === 'send_message'" class="space-y-3 bg-blue-50 rounded-lg p-4">
                                        <p class="text-xs text-blue-700 font-medium uppercase tracking-wide">Message Details</p>
                                        <input type="hidden" :name="'actions[' + index + '][action_data][existing_private_message_id]'" :value="action.existing_private_message_id" x-bind:disabled="action.type !== 'send_message'">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                                <input type="text" :name="'actions[' + index + '][action_data][subject]'" x-model="action.msg_subject"
                                                    x-bind:disabled="action.type !== 'send_message'"
                                                    placeholder="Message subject..."
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">From Character</label>
                                                <select :name="'actions[' + index + '][action_data][sender_id]'" x-model="action.msg_sender_id"
                                                    x-bind:disabled="action.type !== 'send_message'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                    <option value="">Select sender...</option>
                                                    @foreach($characters as $character)
                                                        <option value="{{ $character->id }}">{{ $character->display_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Message Content</label>
                                            <textarea :name="'actions[' + index + '][action_data][content]'" x-model="action.msg_content"
                                                x-bind:disabled="action.type !== 'send_message'"
                                                rows="4"
                                                placeholder="Write the message here..."
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Fake Sent Date <span class="text-gray-400 font-normal">(optional)</span></label>
                                            <input type="datetime-local" :name="'actions[' + index + '][action_data][fake_sent_at]'" x-model="action.msg_fake_sent_at"
                                                x-bind:disabled="action.type !== 'send_message'"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </div>
                                    </div>

                                    <!-- Modify Character Fields -->
                                    <div x-show="action.type === 'modify_character'" class="space-y-3 bg-purple-50 rounded-lg p-4">
                                        <p class="text-xs text-purple-700 font-medium uppercase tracking-wide">Character Modification</p>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Character</label>
                                                <input type="hidden" :name="'actions[' + index + '][target_type]'" value="character" x-bind:disabled="action.type !== 'modify_character'">
                                                <select :name="'actions[' + index + '][target_id]'" x-model="action.target_id"
                                                    x-bind:disabled="action.type !== 'modify_character'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                    <option value="">Select character...</option>
                                                    @foreach($characters as $character)
                                                        <option value="{{ $character->id }}">{{ $character->display_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Field</label>
                                                <select :name="'actions[' + index + '][action_data][field]'" x-model="action.field"
                                                    x-bind:disabled="action.type !== 'modify_character'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                    <option value="">Select field...</option>
                                                    <option value="bytes">Bytes (0-5)</option>
                                                    <option value="role_title">Role Title</option>
                                                    <option value="signature">Signature</option>
                                                    <option value="bio">Bio</option>
                                                    <option value="is_official">Is Official</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div x-show="action.field && action.field !== 'bytes'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                            <input type="text" :name="'actions[' + index + '][action_data][value]'" x-model="action.value"
                                                x-bind:disabled="action.type !== 'modify_character'"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </div>
                                        <div x-show="action.field === 'bytes'" class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Operation</label>
                                                <select :name="'actions[' + index + '][action_data][bytes_operation]'" x-model="action.bytes_operation"
                                                    x-bind:disabled="action.type !== 'modify_character'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                    <option value="set">Set to value</option>
                                                    <option value="add">Add/Subtract</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <span x-show="action.bytes_operation !== 'add'">Value (0–5)</span>
                                                    <span x-show="action.bytes_operation === 'add'">Amount (−5 to +5)</span>
                                                </label>
                                                <input type="number" :name="'actions[' + index + '][action_data][value]'" x-model="action.value"
                                                    :min="action.bytes_operation === 'add' ? -5 : 0" max="5"
                                                    x-bind:disabled="action.type !== 'modify_character'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trigger Phase Fields -->
                                    <div x-show="action.type === 'trigger_phase'" class="bg-yellow-50 rounded-lg p-4">
                                        <p class="text-xs text-yellow-700 font-medium uppercase tracking-wide mb-3">Phase to Force-Complete</p>
                                        <select :name="'actions[' + index + '][action_data][phase_id]'" x-model="action.phase_id"
                                            x-bind:disabled="action.type !== 'trigger_phase'"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Select phase...</option>
                                            @foreach($allPhases as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="actions.length === 0" class="text-center py-8 text-gray-500">
                        No actions added. Click "+ Add Action" to add one.
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Update Phase
                        </button>
                        <a href="{{ route('admin.phases.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </div>

                <div class="bg-yellow-50 rounded-lg p-6">
                    <h4 class="font-medium text-yellow-900 mb-2">Warning</h4>
                    <p class="text-sm text-yellow-800">
                        Changing conditions or actions may affect readers who are already in progress on this phase.
                    </p>
                </div>
            </div>
        </div>
    </form>

    @php
        $conditionsData = $phase->conditions->map(function($c) {
            return [
                'type' => $c->type,
                'target_type' => $c->target_type,
                'target_id' => (string) $c->target_id,
                'choice_option_id' => (string) $c->choice_option_id,
            ];
        })->values();

        $actionsData = $phase->actions->map(function($a) {
            $data = [
                'type' => $a->type,
                'target_type' => $a->target_type ?? '',
                'target_id' => (string) ($a->target_id ?? ''),
                'field' => $a->action_data['field'] ?? '',
                'value' => $a->action_data['value'] ?? '',
                'bytes_operation' => $a->action_data['bytes_operation'] ?? 'set',
                'phase_id' => (string) ($a->action_data['phase_id'] ?? ''),
                'existing_private_message_id' => '',
                'msg_subject' => '',
                'msg_sender_id' => '',
                'msg_content' => '',
                'msg_fake_sent_at' => '',
            ];

            if ($a->type === 'send_message' && !empty($a->action_data['private_message_id'])) {
                $msg = $linkedMessages->get($a->action_data['private_message_id']);
                if ($msg) {
                    $data['existing_private_message_id'] = (string) $msg->id;
                    $data['msg_subject'] = $msg->subject ?? '';
                    $data['msg_sender_id'] = (string) ($msg->sender_id ?? '');
                    $data['msg_content'] = $msg->content ?? '';
                    $data['msg_fake_sent_at'] = $msg->fake_sent_at?->format('Y-m-d\TH:i') ?? '';
                }
            }

            return $data;
        })->values();
    @endphp

    <script>
        function phaseForm() {
            let conditionId = 0;
            let actionId = 0;
            const existingConditions = @json($conditionsData);
            const existingActions = @json($actionsData);

            return {
                conditions: existingConditions.map(c => ({ ...c, id: ++conditionId })),
                actions: existingActions.map(a => ({ ...a, id: ++actionId })),
                conditionId: conditionId,
                actionId: actionId,
                addCondition() {
                    this.conditions.push({
                        id: ++this.conditionId,
                        type: '',
                        target_type: '',
                        target_id: '',
                        choice_option_id: ''
                    });
                },
                removeCondition(id) {
                    this.conditions = this.conditions.filter(c => c.id !== id);
                },
                addAction() {
                    this.actions.push({
                        id: ++this.actionId,
                        type: '',
                        target_type: '',
                        target_id: '',
                        field: '',
                        value: '',
                        bytes_operation: 'set',
                        phase_id: '',
                        existing_private_message_id: '',
                        msg_subject: '',
                        msg_sender_id: '',
                        msg_content: '',
                        msg_fake_sent_at: ''
                    });
                },
                removeAction(id) {
                    this.actions = this.actions.filter(a => a.id !== id);
                }
            }
        }
    </script>
</x-admin-layout>
