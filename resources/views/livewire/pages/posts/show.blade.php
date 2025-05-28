@volt
<?php

use function Livewire\Volt\{mount, state, action, layout};

$post = state(['post' => null]);

mount(function (\App\Models\Post $post) {
    $this->post = $post;
});
$delete = action(function ($postId) {
    $post = \App\Models\Post::findOrFail($postId);
    $this->authorize('delete', $post);
    $post->delete();
    session()->flash('success', 'Post deleted successfully');
});
layout('components.layouts.blog');
?>


<div class="space-y-8">
    <div>
        <a href="{{ route('posts.index') }}" wire:navigate
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            <span>Back to Posts</span>
        </a>
    </div>

    @if (session('success'))
        <div
            class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <article class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
        <header class="space-y-4 mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                {{ $this->post->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                @if($this->post->published_at)
                    <div class="flex items-center gap-1.5">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        <time datetime="{{ $this->post->published_at->toIso8601String() }}">
                            {{ $this->post->published_at->format('F j, Y') }}
                        </time>
                    </div>
                @endif
                @if($this->post->user)
                    <div class="flex items-center gap-1.5">
                        <x-heroicon-o-user class="w-4 h-4" />
                        <span>{{ $this->post->user->name }}</span>
                    </div>
                @endif
            </div>
        </header>

        @if($this->post->excerpt)
            <div
                class="mb-8 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 dark:border-blue-400 rounded-r-lg">
                <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ $this->post->excerpt }}
                </p>
            </div>
        @endif

        <div class="prose prose-lg dark:prose-invert max-w-none">
            <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                {{ $this->post->content }}
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex items-center gap-3">
            @can('update', $this->post)
                <a href="{{ route('posts.edit', $this->post->slug) }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    <span>Edit</span>
                </a>
            @endcan
            @can('delete', $this->post)
                <button wire:click="delete({{ $this->post->id }})" wire:confirm="Are you sure you want to delete this post?"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition-colors">
                    <x-heroicon-o-trash class="w-4 h-4" />
                    <span>Delete</span>
                </button>
            @endcan
        </div>
    </article>
</div>
@endvolt