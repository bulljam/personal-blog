@props(['favourites' => []])

<div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
    <div class="relative max-w-md">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500" />
        </div>
        <input type="search" wire:model.live.debounce.300ms="search" name="search" autocomplete="off"
            placeholder="Search favourites..."
            class="block w-full pl-10 pr-10 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors border-gray-300 dark:border-gray-700" />
    </div>
</div>

<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <span>Title</span>
                    <div class="flex flex-col gap-0.5">
                        <button wire:click="sort('title', 'asc')"
                            class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <x-heroicon-o-chevron-up class="w-3 h-3" />
                        </button>
                        <button wire:click="sort('title', 'desc')"
                            class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <x-heroicon-o-chevron-down class="w-3 h-3" />
                        </button>
                    </div>
                </div>
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <span>Published</span>
                    <div class="flex flex-col gap-0.5">
                        <button wire:click="sort('published_at', 'asc')"
                            class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <x-heroicon-o-chevron-up class="w-3 h-3" />
                        </button>
                        <button wire:click="sort('published_at', 'desc')"
                            class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <x-heroicon-o-chevron-down class="w-3 h-3" />
                        </button>
                    </div>
                </div>
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <span>Last Updated</span>
                    <div class="flex flex-col gap-0.5">
                        <button wire:click="sort('updated_at', 'asc')"
                            class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <x-heroicon-o-chevron-up class="w-3 h-3" />
                        </button>
                        <button wire:click="sort('updated_at', 'desc')"
                            class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <x-heroicon-o-chevron-down class="w-3 h-3" />
                        </button>
                    </div>
                </div>
            </th>
            <th scope="col"
                class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
        @forelse ($favourites as $favourite)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div>
                            <a wire:navigate href="{{ route('posts.show', $favourite->slug) }}"
                                class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $favourite->title }}
                            </a>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                        <time datetime="{{ $favourite->published_at->toIso8601String() }}">
                            {{ $favourite->published_at->format('M j, Y') }}
                        </time>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        @if($favourite->is_edited)
                            <time datetime="{{ $favourite->updated_at->toIso8601String() }}">
                                {{ $favourite->updated_at->format('M j, Y') }}
                            </time>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">—</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <a wire:navigate href="{{ route('posts.show', $favourite->slug) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-md transition-colors">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </a>
                            <button wire:click="removeFromFavourites({{ $favourite->id }})"
                            wire:confirm="Are you sure you want to delete this favourite?"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition-colors">
                            <x-heroicon-o-trash class="w-4 h-4" />

                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <x-heroicon-o-document-text class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No favourites yet</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            You haven't created any favourites yet.
                        </p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>