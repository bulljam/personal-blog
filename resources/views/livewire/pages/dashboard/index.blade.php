@volt
<?php

use function Livewire\Volt\{state, computed, layout, action, uses};
uses(\Livewire\WithPagination::class);

state([
    'search' => '',
    'sortBy' => 'published_at',
    'direction' => 'desc',
    'dateFilter' => '',
]);

$posts = computed(function () {
    if (!auth()->check() || !auth()->user()->isAuthor()) {
        return null;
    }
    return \App\Models\Post::publishedPosts()
        ->author(auth()->id())
        ->search($this->search)
        ->orderBy($this->sortBy, $this->direction)
        ->paginate(5);
});
$favourites = computed(function () {
    if (auth()->check()) {
        return auth()->user()->favouritePosts()
            ->search($this->search)
            ->orderBy($this->sortBy, $this->direction)
            ->paginate(5);
    }
    return null;
});

$authorStats = computed(function () {
    if (!auth()->check() || !auth()->user()->isAuthor()) {
        return null;
    }

    $user_id = auth()->id();

    return [
        'totalPosts' => \App\Models\Post::totalPosts($user_id),
        'totalLikes' => \App\Models\Like::totalLikes($user_id),
        'totalComments' => \App\Models\Comment::totalComments($user_id),
        'totalFavourites' => \App\Models\Favourite::totalFavourites($user_id),
    ];
});

$readerStats = computed(function () {
    if (!auth()->check() || !auth()->user()->isReader()) {
        return null;
    }

    $user = auth()->user();

    return [
        'totalReactions' => $user->likes()->count(),
        'totalComments' => $user->comments()->count(),
        'totalFavourites' => $user->favourites()->count(),
    ];
});




$delete = action(function ($postId) {
    if (!auth()->check()) {
        abort(403, 'You are not allowed to delete this post');
    }

    if (!auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    $post = \App\Models\Post::findOrFail($postId);

    $this->authorize('delete', $post);

    $post->delete();

    session()->flash('success', 'Post deleted successfully');
});

$sort = action(function ($sortBy, $direction) {
    $this->sortBy = $sortBy;
    $this->direction = $direction;
    $this->resetPage();
});

$removeFromFavourites = action(function ($post_id) {
    $post = \App\Models\Post::findOrFail($post_id);
    $this->authorize('removeFromFavourites', $post);

    auth()->user()->favouritePost($post->id)?->delete();
});

layout('components.layouts.dashboard');
?>

<div>
    @if (session('success'))
        <div
            class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div
            class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 flex items-center gap-3">
            <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif
    @author
    @if($this->authorStats)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Posts -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Posts</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $this->authorStats['totalPosts'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <!-- Total Likes -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Likes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $this->authorStats['totalLikes'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <x-heroicon-o-hand-thumb-up class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <!-- Total Comments -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Comments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $this->authorStats['totalComments'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <!-- Total Favourites -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Favourites</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $this->authorStats['totalFavourites'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <x-heroicon-o-star class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endauthor
    @reader
    @if($this->readerStats)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Favourites -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Favourites</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $this->readerStats['totalFavourites'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <x-heroicon-o-star class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </div>

            <!-- Total Comments -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Comments Made</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $this->readerStats['totalComments'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <!-- Total Reactions -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reactions Given</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $this->readerStats['totalReactions'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <x-heroicon-o-hand-thumb-up class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endreader

    <div
        class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            @author
            <x-partials.table :posts="$this->posts" />
            @if($this->posts?->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $this->posts->links() }}
                </div>
            @endif
            @endauthor
            @reader
            <x-partials.favourites-table :favourites="$this->favourites" />
            @if($this->favourites?->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $this->favourites->links() }}
                </div>
            @endif
            @endreader
        </div>

    </div>
</div>
@endvolt