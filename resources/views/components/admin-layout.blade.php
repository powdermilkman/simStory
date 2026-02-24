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
        <div class="min-h-screen bg-gray-100" x-data="{ drawerOpen: false }">

            <!-- Mobile Backdrop -->
            <div x-show="drawerOpen" x-cloak @click="drawerOpen = false"
                 class="fixed inset-0 bg-black/50 z-40 md:hidden"
                 x-transition:enter="transition-opacity duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <!-- Mobile Slide-Out Drawer -->
            <div x-show="drawerOpen" x-cloak
                 x-transition:enter="transition transform duration-200"
                 x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition transform duration-150"
                 x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 w-64 bg-gray-800 z-50 md:hidden flex flex-col">

                <!-- Drawer Header -->
                <div class="flex items-center justify-between px-4 h-16 border-b border-gray-700">
                    <span class="text-white font-bold">{{ config('app.name') }} Admin</span>
                    <button @click="drawerOpen = false" class="text-gray-400 hover:text-white p-1 rounded">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Drawer Nav Links -->
                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('admin.categories.index') }}" @click="drawerOpen = false"
                       class="block px-3 py-2 rounded text-sm hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.categories.*') ? 'bg-gray-900 text-white' : 'text-gray-300' }}">
                        Categories
                    </a>
                    <a href="{{ route('admin.roles.index') }}" @click="drawerOpen = false"
                       class="block px-3 py-2 rounded text-sm hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.roles.*') ? 'bg-gray-900 text-white' : 'text-gray-300' }}">
                        Roles
                    </a>
                    <a href="{{ route('admin.characters.index') }}" @click="drawerOpen = false"
                       class="block px-3 py-2 rounded text-sm hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.characters.*') ? 'bg-gray-900 text-white' : 'text-gray-300' }}">
                        Characters
                    </a>
                    <a href="{{ route('admin.threads.index') }}" @click="drawerOpen = false"
                       class="block px-3 py-2 rounded text-sm hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.threads.*') ? 'bg-gray-900 text-white' : 'text-gray-300' }}">
                        Threads
                    </a>
                    <a href="{{ route('admin.phases.index') }}" @click="drawerOpen = false"
                       class="block px-3 py-2 rounded text-sm hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.phases.*') ? 'bg-gray-900 text-white' : 'text-gray-300' }}">
                        Phases
                    </a>
                    <a href="{{ route('admin.readers.index') }}" @click="drawerOpen = false"
                       class="block px-3 py-2 rounded text-sm hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.readers.*') ? 'bg-gray-900 text-white' : 'text-gray-300' }}">
                        Readers
                    </a>
                    <a href="{{ route('admin.users.index') }}" @click="drawerOpen = false"
                       class="block px-3 py-2 rounded text-sm hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'text-gray-300' }}">
                        Users
                    </a>
                </nav>

                <!-- Drawer Footer -->
                <div class="px-4 py-4 border-t border-gray-700 space-y-2">
                    <a href="{{ route('forum.index') }}" class="block text-sm text-gray-300 hover:text-white">View Forum →</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-300 hover:text-white">Logout</button>
                    </form>
                </div>
            </div>

            <!-- Admin Navigation -->
            <nav class="bg-gray-800 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center space-x-4">
                            <!-- Hamburger (mobile only) -->
                            <button @click="drawerOpen = true" class="md:hidden p-2 rounded text-gray-300 hover:text-white hover:bg-gray-700" aria-label="Open menu">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

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
                                <a href="{{ route('admin.phases.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.phases.*') ? 'bg-gray-900' : '' }}">
                                    Phases
                                </a>
                                <a href="{{ route('admin.readers.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.readers.*') ? 'bg-gray-900' : '' }}">
                                    Readers
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-900' : '' }}">
                                    Users
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('forum.index') }}" class="text-sm text-gray-300 hover:text-white hidden md:inline">
                                View Forum →
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-300 hover:text-white hidden md:inline">
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
