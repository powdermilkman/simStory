<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --color-bg: #0a0a0f;
                --color-surface: #14141f;
                --color-surface-hover: #1a1a28;
                --color-border: #2a2a3a;
                --color-text: #e8e8ed;
                --color-text-muted: #8888a0;
                --color-accent: #5c9ead;
                --color-accent-warm: #e0a458;
            }
            
            body {
                font-family: 'Outfit', sans-serif;
                background-color: var(--color-bg);
                color: var(--color-text);
            }
        </style>
    </head>
    <body class="antialiased min-h-screen" style="background-color: var(--color-bg);">
        <!-- Navigation -->
        <nav style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <a href="{{ route('forum.index') }}" class="text-xl font-semibold" style="color: var(--color-accent);">
                            {{ config('app.name') }}
                        </a>
                        <div class="hidden md:flex space-x-6">
                            <a href="{{ route('forum.index') }}" class="text-sm hover:opacity-80" style="color: var(--color-text-muted);">
                                Forums
                            </a>
                            <a href="{{ route('forum.messages') }}" class="text-sm hover:opacity-80 relative" style="color: var(--color-text-muted);">
                                Messages
                                @if($unreadMessageCount > 0)
                                    <span class="absolute -top-1 -right-3 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none rounded-full" style="background-color: var(--color-accent); color: var(--color-bg); min-width: 1.25rem;">
                                        {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth('reader')
                            <a href="{{ route('reader.progress') }}" class="text-sm" style="color: var(--color-text-muted);">
                                {{ Auth::guard('reader')->user()->username }}
                            </a>
                            <form method="POST" action="{{ route('reader.logout') }}">
                                @csrf
                                <button type="submit" class="text-sm hover:opacity-80" style="color: var(--color-text-muted);">
                                    Sign Out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('reader.login') }}" class="text-sm hover:opacity-80" style="color: var(--color-text-muted);">
                                Sign In
                            </a>
                            <a href="{{ route('reader.register') }}" class="text-sm px-4 py-2 rounded-lg" style="background-color: var(--color-accent); color: var(--color-bg);">
                                Join
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg px-4 py-3" style="background-color: rgba(92, 158, 173, 0.2); border: 1px solid var(--color-accent); color: var(--color-accent);">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg px-4 py-3" style="background-color: rgba(224, 164, 88, 0.2); border: 1px solid var(--color-accent-warm); color: var(--color-accent-warm);">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-8" style="border-top: 1px solid var(--color-border);">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center" style="color: var(--color-text-muted);">
                <p class="text-sm">{{ config('app.name') }} — A place to share simulation experiences</p>
            </div>
        </footer>
    </body>
</html>
