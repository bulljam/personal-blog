@props(['title' => ''])

<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black/50" @click="$dispatch('close-modal')"></div>

    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h3>
                <button @click="$dispatch('close-modal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{ $slot }}
        </div>
    </div>
</div>