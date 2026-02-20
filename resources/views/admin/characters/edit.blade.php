<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Character: {{ $character->display_name }}</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.characters.update', $character) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $character->username) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $character->display_name) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role_id" id="role_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- No Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $character->role_id) == $role->id ? 'selected' : '' }}
                                style="background-color: {{ $role->color }}; color: {{ $role->text_color }};">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:underline">Manage roles</a>
                    </p>
                </div>

                <div>
                    <label for="fake_join_date" class="block text-sm font-medium text-gray-700 mb-1">Join Date (Story)</label>
                    <input type="datetime-local" name="fake_join_date" id="fake_join_date" 
                        value="{{ old('fake_join_date', $character->fake_join_date?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('fake_join_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_official" value="1" {{ old('is_official', $character->is_official) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Official Account</span>
                </label>
                <p class="mt-1 text-xs text-gray-500 ml-6">Mark this character as an official/verified account</p>
            </div>

            <div class="mb-4">
                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">Avatar</label>
                @if($character->avatar_path)
                    <div class="mb-2">
                        <img src="{{ Storage::url($character->avatar_path) }}" alt="{{ $character->display_name }}" class="h-20 w-20 rounded-full">
                    </div>
                @endif
                <input type="file" name="avatar" id="avatar" accept="image/*"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('avatar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="signature" class="block text-sm font-medium text-gray-700 mb-1">Signature</label>
                <textarea name="signature" id="signature" rows="2" maxlength="500"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('signature', $character->signature) }}</textarea>
                @error('signature')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                <textarea name="bio" id="bio" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $character->bio) }}</textarea>
                @error('bio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ isAlien: {{ $character->is_alien ? 'true' : 'false' }} }" class="mb-4 p-4 bg-gray-50 rounded-lg">
                <label class="flex items-center gap-2 mb-2">
                    <input type="checkbox" name="is_alien" id="is_alien" value="1"
                           {{ old('is_alien', $character->is_alien) ? 'checked' : '' }}
                           @change="isAlien = $event.target.checked"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Alien Character</span>
                </label>
                <p class="text-xs text-gray-500 mb-3">Posts by this character will display in an alien script with a translation button.</p>
                <div x-show="isAlien">
                    <label for="alien_style" class="block text-sm font-medium text-gray-700 mb-1">Alien Script Style</label>
                    <select name="alien_style" id="alien_style"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">— select style —</option>
                        <option value="lovecrafts" {{ old('alien_style', $character->alien_style) === 'lovecrafts' ? 'selected' : '' }}>Lovecraft's Diary</option>
                        <option value="alphacode"  {{ old('alien_style', $character->alien_style) === 'alphacode'  ? 'selected' : '' }}>Alpha Code Beyond</option>
                        <option value="echolot"    {{ old('alien_style', $character->alien_style) === 'echolot'    ? 'selected' : '' }}>Echolot</option>
                    </select>
                </div>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <label class="flex items-center gap-2 mb-3">
                    <input type="checkbox" name="show_bytes" value="1" {{ old('show_bytes', $character->show_bytes) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Show Bytes Rating</span>
                </label>
                <p class="text-xs text-gray-500 mb-3">Enable to display bytes rating on forum posts</p>

                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Rating:</label>
                    <input type="range" name="bytes" id="bytes" min="0" max="5" value="{{ old('bytes', $character->bytes ?? 0) }}"
                        class="w-32" oninput="document.getElementById('bytes-display').textContent = this.value">
                    <span id="bytes-display" class="text-lg font-medium text-gray-900">{{ old('bytes', $character->bytes ?? 0) }}</span>
                    <span class="text-sm text-gray-500">/ 5</span>
                </div>
                @error('bytes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Update Character
                </button>
                <a href="{{ route('admin.characters.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
