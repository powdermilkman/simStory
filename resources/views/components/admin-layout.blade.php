<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Bootstrap Icons (used by forum post preview) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Admin Navigation -->
            <nav class="bg-gray-800 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center space-x-8">
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">
                                {{ config('app.name') }} Admin
                            </a>
                            <div class="hidden md:flex space-x-4">
                                {{-- Story Flow disabled for now
                                <a href="{{ route('admin.story-flow.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.story-flow.*') ? 'bg-blue-600' : '' }}">
                                    Story Flow
                                </a>
                                --}}
                                <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-900' : '' }}">
                                    Categories
                                </a>
                                <a href="{{ route('admin.roles.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.roles.*') ? 'bg-gray-900' : '' }}">
                                    Roles
                                </a>
                                <a href="{{ route('admin.characters.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.characters.*') ? 'bg-gray-900' : '' }}">
                                    Characters
                                </a>
                                <a href="{{ route('admin.threads.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.threads.*') ? 'bg-gray-900' : '' }}">
                                    Threads
                                </a>
                                <a href="{{ route('admin.triggers.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.triggers.*') ? 'bg-gray-900' : '' }}">
                                    Triggers
                                </a>
                                <a href="{{ route('admin.phases.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.phases.*') ? 'bg-gray-900' : '' }}">
                                    Phases
                                </a>
                                <a href="{{ route('admin.private-messages.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.private-messages.*') ? 'bg-gray-900' : '' }}">
                                    Messages
                                </a>
                                <a href="{{ route('admin.readers.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.readers.*') ? 'bg-gray-900' : '' }}">
                                    Readers
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('forum.index') }}" class="text-sm text-gray-300 hover:text-white">
                                View Forum →
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-300 hover:text-white">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
