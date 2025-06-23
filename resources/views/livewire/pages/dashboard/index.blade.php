@volt
<?php

use function Livewire\Volt\{computed, layout, action, uses};
uses(\Livewire\WithPagination::class);

$posts = computed(fn() => \App\Models\Post::publishedPosts()
    ->author(auth()->id())
    ->latest('published_at')
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

layout('components.layouts.dashboard');
?>

<div>
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