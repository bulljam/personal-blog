<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'My Personal Blog' }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen">
        <header class="border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <h1 class="text-2xl font-bold">
                    <a href="{{ route('posts.index') }}">My Personal Blog</a>
                </h1>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 py-8">
            <div class="my-4">
                <a href="{{ route('posts.index') }}" wire:navigate class="bg-blue-500 text-white px-4 py-2 rounded my-4">Back</a>
            </div>
            {{ $slot }}
        </main>
    </div>
</body>
</html>