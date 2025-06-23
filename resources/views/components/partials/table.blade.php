@props(['posts' => []])

<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Title
            </th>
            {{-- <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Excerpt
            </th> --}}
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Published
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Last Updated
            </th>
            <th scope="col"
                class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
        @forelse ($posts as $post)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div>
                            <a wire:navigate href="{{ route('posts.show', $post->slug) }}"
                                class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $post->title }}
                            </a>
                        </div>
                    </div>
                </td>
                {{-- <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                        @if($post->excerpt)
                            <span>
                                {{ Str::limit($post->excerpt, 40) }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">—</span>
                        @endif
                    </div>
                </td> --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                        <time datetime="{{ $post->published_at->toIso8601String() }}">
                            {{ $post->published_at->format('M j, Y') }}
                        </time>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        @if($post->is_edited)
                            <time datetime="{{ $post->updated_at->toIso8601String() }}">
                                {{ $post->updated_at->format('M j, Y') }}
                            </time>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">—</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <a wire:navigate href="{{ route('posts.show', $post->slug) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-md transition-colors">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </a>
                        <a wire:navigate href="{{ route('posts.edit', $post->slug) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                        </a>
                        <button wire:click="delete({{ $post->id }})"
                            wire:confirm="Are you sure you want to delete this post?"
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
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No posts yet</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            You haven't created any posts yet.
                        </p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>