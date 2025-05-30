@volt
<?php
use function Livewire\Volt\{uses, computed, action, layout};

uses(\Livewire\WithPagination::class);
uses(\Livewire\WithoutUrlPagination::class);
$posts = computed(fn() => \App\Models\Post::query()->whereNotNull('published_at')
    ->orderByDesc('published_at')
    ->paginate(10));

$delete = action(function ($postId) {
    if (!auth()->check()) {
        abort(403, 'Unauthorized action.');
    }

    if (!auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }
    $post = \App\Models\Post::findOrFail($postId);
    $this->authorize('delete', $post);
    $post->delete();
    session()->flash('success', 'Post deleted successfully');
    return;
});

layout('components.layouts.blog');
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">All Posts</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Discover the latest articles and stories</p>
        </div>
        @auth
            <a wire:navigate href="{{ route('posts.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span>Create Post</span>
            </a>
        @endauth
    </div>

    @if (session('success'))
        <div
            class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div
            class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 flex items-center gap-3">
            <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" />
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    <div class="space-y-6">
        @forelse ($this->posts as $post)
            <article
                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            <a wire:navigate href="{{ route('posts.show', $post->slug) }}"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h3>
                        @if($post->excerpt)
                            <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                {{ $post->excerpt }}
                            </p>
                        @endif
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-o-calendar class="w-4 h-4" />
                                <time datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->format('F j, Y') }}
                                    @if ($post->is_edited)
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-50 dark:bg-yellow-800/25 text-yellow-700 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-700 align-middle">
                                            edited
                                        </span>
                                    @endif
                                </time>
                            </div>
                            @if($post->user)
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-o-user class="w-4 h-4" />
                                    <span>{{ $post->user->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a wire:navigate href="{{ route('posts.show', $post->slug) }}"
                        class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        Read more
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                    @can('update', $post)
                        <a wire:navigate href="{{ route('posts.edit', $post->slug) }}"
                            class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                            <span>Edit</span>
                        </a>
                    @endcan
                    @can('delete', $post)
                        <button wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this post?"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition-colors">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            <span>Delete</span>
                        </button>
                    @endcan
                </div>
            </article>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800">
                <x-heroicon-o-document-text class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No posts yet</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Check back later for new content</p>
            </div>
        @endforelse
        {{ $this->posts->links() }}
    </div>
</div>
@endvolt