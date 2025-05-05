@volt
<?php
use function Livewire\Volt\{computed, action, layout};

$posts = computed(fn() => \App\Models\Post::whereNotNull('published_at')
    ->orderByDesc('published_at')
    ->get());

$delete = action(function ($postId) {
    $post = \App\Models\Post::findOrFail($postId);
    $post->delete();
    session()->flash('success', 'Post deleted successfully');
});

layout('components.layouts.blog');
?>

<div class="space-y-8">
    <h2 class="text-3xl font-bold">All Posts</h2>
    <a wire:navigate href="{{ route('posts.create') }}" class="text-sm text-blue-500">Create</a>

    <div class="space-y-6">
        @foreach ($this->posts as $post)
            <article class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <h3 class="text-2xl font-semibold mb-2">
                    <a wire:navigate href="{{ route('posts.show', $post->slug) }}"
                        class="hover:text-blue-600 dark:hover:text-blue-400">
                        {{ $post->title }}
                    </a>
                </h3>
                @if($post->excerpt)
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ $post->excerpt }}
                    </p>
                @endif

                <div class="text-sm text-gray-500 dark:text-gray-500">
                    Published {{ $post->published_at->format('F j, Y') }}
                </div>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('posts.edit', $post->slug) }}" wire:navigate
                        class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
                    <button wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this post?"
                        class="bg-red-500 text-white px-4 py-2 rounded">
                        Delete
                    </button>
                </div>
            </article>
        @endforeach
    </div>
</div>
@endvolt