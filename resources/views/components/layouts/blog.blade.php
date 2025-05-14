<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'My Personal Blog' }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <h1 class="text-xl sm:text-2xl font-bold">
                        <a href="{{ route('posts.index') }}" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            My Personal Blog
                        </a>
                    </h1>
                    <nav class="flex items-center gap-4">
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                                    <span class="hidden sm:inline">Logout</span>
                                </button>
                            </form>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                                <span>Login</span>
                            </a>
                            <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg transition-colors">
                                <x-heroicon-o-user-plus class="w-4 h-4" />
                                <span>Register</span>
                            </a>
                        @endguest
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-1 max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>
    </div>
</body>

</html>