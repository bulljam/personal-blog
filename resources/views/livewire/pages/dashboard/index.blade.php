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

$posts = computed(fn() => \App\Models\Post::publishedPosts()
    ->author(auth()->id())
    ->search($this->search)
    ->orderBy($this->sortBy, $this->direction)
    ->paginate(5));

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

    <div
        class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <x-partials.table :posts="$this->posts" />
        </div>

        @if($this->posts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                {{ $this->posts->links() }}
            </div>
        @endif
    </div>
</div>
@endvolt