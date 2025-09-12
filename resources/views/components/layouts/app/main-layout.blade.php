<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        {{-- Your custom header --}}
        <header class="border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    {{-- Logo/Brand --}}
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <x-app-logo-icon class="h-8 w-8" />
                            <span class="text-xl font-bold">Syllabus Generator</span>
                        </a>
                    </div>
                    
                    {{-- Navigation --}}
                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('dashboard') }}" class="text-zinc-900 dark:text-zinc-100 hover:text-zinc-600 dark:hover:text-zinc-300">Dashboard</a>
                        {{-- Add your nav items --}}
                    </nav>
                    
                    {{-- User menu --}}
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="relative">
                                <button class="flex items-center space-x-2 text-sm">
                                    <span class="h-8 w-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        {{ Auth::user()->initials() }}
                                    </span>
                                    <span>{{ Auth::user()->name }}</span>
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        {{-- Main content --}}
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        {{-- Optional footer --}}
        <footer class="border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                    © {{ date('Y') }} Syllabus Generator. All rights reserved.
                </p>
            </div>
        </footer>
    </body>
</html>