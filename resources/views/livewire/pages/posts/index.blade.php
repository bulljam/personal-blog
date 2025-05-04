@volt
<?php
use function Livewire\Volt\computed;
use function Livewire\Volt\layout;

$posts = computed(fn () => \App\Models\Post::whereNotNull('published_at')
    ->orderByDesc('published_at')
    ->get());

layout('components.layouts.blog');
?>

<div class="space-y-8">
<h2 class="text-3xl font-bold">All Posts</h2>

<div class="space-y-6">
    @foreach ($this->posts as $post)
        <article class="border-b border-gray-200 dark:border-gray-800 pb-6">
            <h3 class="text-2xl font-semibold mb-2">
                <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
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
        </article>
    @endforeach
</div>
    </div>
@endvolt
