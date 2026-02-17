<x-forum-layout>
    <x-slot name="title">Sign In - {{ config('app.name') }}</x-slot>

    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-medium mb-2" style="color: var(--color-text);">Welcome back</h1>
            <p style="color: var(--color-text-muted);">Sign in to track your progress and make choices.</p>
        </div>

        <div class="rounded-lg p-8" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form method="POST" action="{{ route('reader.login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-1" style="color: var(--color-text-muted);">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
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

                <div class="flex items-center mb-6">
                    <input type="checkbox" name="remember" id="remember"
                        class="rounded" style="accent-color: var(--color-accent);">
                    <label for="remember" class="ml-2 text-sm" style="color: var(--color-text-muted);">Remember me</label>
                </div>

                <button type="submit" class="w-full py-3 rounded-lg font-medium transition-all duration-200"
                    style="background-color: var(--color-accent); color: var(--color-bg);">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-sm" style="color: var(--color-text-muted);">
                Don't have an account?
                <a href="{{ route('reader.register') }}" style="color: var(--color-accent);">Join us</a>
            </div>
        </div>
    </div>
</x-forum-layout>
