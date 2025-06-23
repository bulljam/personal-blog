<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
        init() {
            const stored = localStorage.getItem('darkMode');
            if (stored !== null) {
                this.darkMode = stored === 'true';
            } else {
                this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
            
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            this.$watch('darkMode', value => {
                if (value) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                localStorage.setItem('darkMode', value);
            });
            
            document.addEventListener('livewire:navigated', () => {
                const stored = localStorage.getItem('darkMode');
                if (stored !== null) {
                    this.darkMode = stored === 'true';
                } else {
                    this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });
        },
        darkMode: false,
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
        }
    }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'My Dashboard' }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
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

<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col">
            <!-- Logo/Brand -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <a wire:navigate href="{{ route('dashboard.index') }}"
                    class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    BlogOne
                </a>
                @persist('dark-mode-toggle')
                <button @click="toggleDarkMode()"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    aria-label="Toggle dark mode">
                    <x-heroicon-o-sun x-show="!darkMode" class="w-5 h-5" />
                    <x-heroicon-o-moon x-show="darkMode" class="w-5 h-5" />
                </button>
                @endpersist
            </div>
            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2">
                <a wire:navigate href="{{ route('dashboard.index') }}"
                    class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    <x-heroicon-o-home class="w-5 h-5" />
                    <span>Dashboard</span>
                </a>

                @author
                <a wire:navigate href="{{ route('posts.create') }}"
                    class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    <span>Create Post</span>
                </a>
                @endauthor

                <a wire:navigate href="{{ route('posts.index') }}"
                    class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    <x-heroicon-o-document-text class="w-5 h-5" />
                    <span>All Posts</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white text-sm font-semibold">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>