@props(['role' => '', 'updateRole' => null])

<div x-data="{ open: false }" @close-modal.window="open = false">
    <!-- Edit Button -->
    <button @click="open = true"
        class="ml-4 inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <x-heroicon-o-pencil class="w-4 h-4" />
    </button>

    <!-- Modal with Form -->
    <div x-show="open" x-cloak x-transition>
        <x-partials.modal title="Update Role">
            <div>
                @author
                <button type="button"
                    wire:confirm="Are you sure you want to become a {{ \App\Enums\Role::READER->getLabel() }}? All your posts will be deleted!"
                    wire:click="updateRole('{{ \App\Enums\Role::READER->value }}', true)"
                    class="w-full text-left p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="shrink-0 w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                            <x-heroicon-o-book-open class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ \App\Enums\Role::READER->getLabel() }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Read and explore blog posts from authors
                            </p>
                        </div>
                        <x-heroicon-o-chevron-right
                            class="w-5 h-5 text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors shrink-0 mt-1" />
                    </div>
                </button>
                @endauthor
                @reader
                <button type="button"
                    wire:confirm="Are you sure you want to become a {{ \App\Enums\Role::AUTHOR->getLabel() }} ?"
                    wire:click="updateRole('{{ \App\Enums\Role::AUTHOR->value }}')"
                    class="w-full text-left p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="shrink-0 w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                            <x-heroicon-o-pencil class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ \App\Enums\Role::AUTHOR->getLabel() }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Create and publish your own blog posts
                            </p>
                        </div>
                        <x-heroicon-o-chevron-right
                            class="w-5 h-5 text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors shrink-0 mt-1" />
                    </div>
                </button>
                @endreader

                <!-- Error Message -->
                @error('role')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center gap-1.5">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4 shrink-0" />
                        <span>{{ $message }}</span>
                    </p>
                @enderror

                <!-- Info Message -->
                @session('info')
                    <div
                        class="mt-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-3 flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" />
                        <p class="text-sm text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
                    </div>
                @endsession
            </div>
        </x-partials.modal>
    </div>
</div>