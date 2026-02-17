<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Private Message</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
        <form action="{{ route('admin.private-messages.update', $privateMessage) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="sender_id" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <select name="sender_id" id="sender_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ old('sender_id', $privateMessage->sender_id) == $character->id ? 'selected' : '' }}>
                                {{ $character->display_name }} ({{ '@' . $character->username }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="recipient_id" class="block text-sm font-medium text-gray-700 mb-1">To (Optional for inbox messages)</label>
                    <select name="recipient_id" id="recipient_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select character recipient (or leave blank for inbox)</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ old('recipient_id', $privateMessage->recipient_id) == $character->id ? 'selected' : '' }}>
                                {{ $character->display_name }} ({{ '@' . $character->username }})
                            </option>
                        @endforeach
                    </select>
                    @error('recipient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject', $privateMessage->subject) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="fake_sent_at" class="block text-sm font-medium text-gray-700 mb-1">Sent Date</label>
                    <input type="datetime-local" name="fake_sent_at" id="fake_sent_at" 
                        value="{{ old('fake_sent_at', $privateMessage->fake_sent_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-end gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_read" id="is_read" value="1" {{ old('is_read', $privateMessage->is_read) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Mark as read</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_inbox_message" id="is_inbox_message" value="1" {{ old('is_inbox_message', $privateMessage->is_inbox_message) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Show in reader inbox</span>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea name="content" id="content" rows="8" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $privateMessage->content) }}</textarea>
            </div>

            <div class="mb-6">
                <x-searchable-select
                    name="phase_id"
                    label="Visible in Phase (Optional)"
                    :endpoint="route('admin.api.search.phases')"
                    :value="old('phase_id', $privateMessage->phase_id)"
                    display="name"
                    subtitle="identifier"
                    placeholder="Always visible - no phase requirement"
                />
                <p class="mt-1 text-sm text-gray-500">If set, this message will only be visible to readers who have started this phase.</p>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Update Message
                </button>
                <a href="{{ route('admin.private-messages.show', $privateMessage) }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>

</x-admin-layout>
