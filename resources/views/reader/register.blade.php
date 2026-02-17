<x-forum-layout>
    <x-slot name="title">Join - {{ config('app.name') }}</x-slot>

    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-medium mb-2" style="color: var(--color-text);">Join the community</h1>
            <p style="color: var(--color-text-muted);">Create an account to save your progress and unlock content.</p>
        </div>

        <div class="rounded-lg p-8" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form method="POST" action="{{ route('reader.register') }}">
                @csrf

                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium mb-1" style="color: var(--color-text-muted);">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                        class="w-full rounded-lg px-4 py-3 text-white"
                        style="background-color: var(--color-bg); border: 1px solid var(--color-border);">
                    @error('username')
                        <p class="mt-1 text-sm" style="color: var(--color-accent-warm);">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-1" style="color: var(--color-text-muted);">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg px-4 py-3 text-white"
                        style="background-color: var(--color-bg); border: 1px solid var(--color-border);">
                    @error('email')
                        <p class="mt-1 text-sm" style="color: var(--color-accent-warm);">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-1" style="color: var(--color-text-muted);">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full rounded-lg px-4 py-3 text-white"
                        style="background-color: var(--color-bg); border: 1px solid var(--color-border);">
                    @error('password')
                        <p class="mt-1 text-sm" style="color: var(--color-accent-warm);">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color: var(--color-text-muted);">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full rounded-lg px-4 py-3 text-white"
                        style="background-color: var(--color-bg); border: 1px solid var(--color-border);">
                </div>

                <button type="submit" class="w-full py-3 rounded-lg font-medium transition-all duration-200"
                    style="background-color: var(--color-accent); color: var(--color-bg);">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center text-sm" style="color: var(--color-text-muted);">
                Already have an account?
                <a href="{{ route('reader.login') }}" style="color: var(--color-accent);">Sign in</a>
            </div>
        </div>
    </div>
</x-forum-layout>
