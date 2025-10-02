@props(['name' => ''])

<div x-data="{ open: false }" @close-modal.window="open = false">
    <!-- Edit Button -->
    <button @click="open = true"
        class="ml-4 inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <x-heroicon-o-pencil class="w-4 h-4" />
    </button>

    <!-- Modal with Form -->
    <div x-show="open" x-cloak x-transition>
        <x-partials.modal title="Update Email">
            <form wire:submit="update('email')" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email
                    </label>
                    <input type="text" wire:model="email" @class([
                        'w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                        'border-red-500 dark:border-red-500' => $errors->has('name'),
                        'border-gray-300 dark:border-gray-700' => !$errors->has('name'),
                    ]) />

                    <!-- Error Message -->
                    @error('name')
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

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg transition-colors">
                        Save
                    </button>
                </div>
            </form>
        </x-partials.modal>
    </div>
</div>