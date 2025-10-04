@volt
<?php

use function Livewire\Volt\{mount, rules, state, action, layout};

$post = state([
    'post' => null,
    'commentContent' => '',
    'comment' => null,
    'updating' => false,
]);

mount(function (\App\Models\Post $post) {
    $this->post = $post;
});

rules([
    'commentContent' => 'required|max:255',
]);

$delete = action(function () {
    $this->authorize('delete', $this->post);
    $this->post->delete();
    session()->flash('success', 'Post deleted successfully');
});

$toggleReaction = action(function ($type) {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $existingLike = \App\Models\Like::findUnique($this->post->id, auth()->id())->first();

    if ($existingLike) {
        if ($existingLike->type === $type) {
            $existingLike->delete();
        } else {
            $existingLike->type = $type;
            $existingLike->save();
        }
    } else {
        \App\Models\Like::create([
            'user_id' => auth()->id(),
            'post_id' => $this->post->id,
            'type' => $type,
        ]);
    }

    $this->post->refresh();
});

$addComment = action(function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $this->validate();

    \App\Models\Comment::create([
        'user_id' => auth()->id(),
        'post_id' => $this->post->id,
        'content' => $this->commentContent,
    ]);

    $this->reset('commentContent');
    $this->post->refresh();

});

$editComment = action(function ($comment_id) {
    $this->comment = \App\Models\Comment::findOrFail($comment_id);
    $this->commentContent = $this->comment->content;
    $this->updating = true;
});
$updateComment = action(function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    if (!$this->comment) {
        return;
    }
    $this->validate();

    $this->comment->update([
        'content' => $this->commentContent,
    ]);

    $this->reset(['updating', 'comment', 'commentContent']);
    $this->post->refresh();
});

$deleteComment = action(function ($comment_id) {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $comment = \App\Models\Comment::findOrFail($comment_id);
    $comment->delete();
    $this->post->refresh();
});
layout('components.layouts.dashboard');
?>


<div class="space-y-8">
    <div>
        <a href="{{ route('dashboard.index') }}" wire:navigate
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            <span>Back to Posts</span>
        </a>
    </div>

    @if (session('success'))
        <div
            class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
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

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Reactions Section -->
                <div class="flex items-center gap-3">
                    @auth
                        <button wire:click="toggleReaction('like')" wire:loading.attr="disabled" @class([
                            'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                            'text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30' => $this->post->isLikedBy(auth()->id()),
                            'text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' => !$this->post->isLikedBy(auth()->id()),
                        ])>
                            <x-heroicon-o-hand-thumb-up class="w-4 h-4" />
                            <span wire:loading.remove wire:target="toggleReaction('like')">
                                {{ $this->post->likesCount() }}
                            </span>
                            <svg wire:loading wire:target="toggleReaction('like')" class="animate-spin h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>

                        <button wire:click="toggleReaction('dislike')" wire:loading.attr="disabled" @class([
                            'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                            'text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30' => $this->post->isDislikedBy(auth()->id()),
                            'text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' => !$this->post->isDislikedBy(auth()->id()),
                        ])>
                            <x-heroicon-o-hand-thumb-down class="w-4 h-4" />
                            <span wire:loading.remove wire:target="toggleReaction('dislike')">
                                {{ $this->post->dislikesCount() }}
                            </span>
                            <svg wire:loading wire:target="toggleReaction('dislike')" class="animate-spin h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    @else
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <x-heroicon-o-hand-thumb-up class="w-4 h-4" />
                            <span>{{ $this->post->likesCount() }}</span>
                        </div>
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <x-heroicon-o-hand-thumb-down class="w-4 h-4" />
                            <span>{{ $this->post->dislikesCount() }}</span>
                        </div>
                    @endauth
                </div>

                <!-- Actions Section -->
                <div class="flex items-center gap-3">
                    @can('update', $this->post)
                        <a href="{{ route('posts.edit', $this->post->slug) }}" wire:navigate
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                            <span>Edit</span>
                        </a>
                    @endcan
                    @can('delete', $this->post)
                        <button wire:click="delete({{ $this->post->id }})"
                            wire:confirm="Are you sure you want to delete this post?"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition-colors">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            <span>Delete</span>
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <x-partials.comments-section :post="$this->post" :updating="$this->updating" />
    </article>
</div>
@endvolt