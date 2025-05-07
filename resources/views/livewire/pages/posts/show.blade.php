@volt
<?php

use function Livewire\Volt\{mount, state, layout};

$post = state(['post' => null]);

mount(function (\App\Models\Post $post) {
    $this->post = $post;
});
layout('components.layouts.blog');
?>


<div class="space-y-8">
    <div>
        <a href="{{ route('posts.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Posts
        </a>
    </div>

    <header class="space-y-4">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100">
            {{ $this->post->title }}
        </h1>
        
        @if($this->post->published_at)
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Published on {{ $this->post->published_at->format('F j, Y') }}
            </div>
        @endif
    </header>

    @if($this->post->excerpt)
        <div class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed border-l-4 border-blue-500 dark:border-blue-400 pl-4 py-2">
            {{ $this->post->excerpt }}
        </div>
    @endif

    <article class="prose prose-lg dark:prose-invert max-w-none border-b border-gray-200 dark:border-gray-800">
        <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
            {{ $this->post->content }}
        </div>
    </article>

    <a href="{{ route('posts.edit', $this->post->slug) }}" wire:navigate class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
</div>
@endvolt

