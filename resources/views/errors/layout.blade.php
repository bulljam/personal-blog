<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Error') • BlogOne</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function () {
            function applyDarkMode() {
                const stored = localStorage.getItem('darkMode');
                const darkMode = stored === 'true' ||
                    (stored === null && window.matchMedia('(prefers-color-scheme: dark)').matches);

                if (darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }

            applyDarkMode();

            document.addEventListener('livewire:navigated', () => {
                applyDarkMode();
            });
        })();
    </script>
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white/90 p-8 shadow-lg shadow-gray-200/70 backdrop-blur
                    dark:border-gray-800 dark:bg-gray-900/90 dark:shadow-black/40">
            @yield('content', 'Something went wrong.')

            <div class="mt-8 flex justify-center">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700
                          hover:bg-gray-100 hover:text-blue-600
                          dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-blue-400">
                    <x-heroicon-o-arrow-left class="h-4 w-4" />
                    <span>Back to home</span>
                </a>
            </div>
        </div>
    </div>
</body>

</html>