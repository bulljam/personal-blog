@volt
<?php
use function Livewire\Volt\action;
use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'title' => '',
    'excerpt' => null,
    'content' => '',
]);

rules([
    'title' => 'required|string|max:255',
    'excerpt' => 'nullable|string|max:500',
    'content' => 'required|string|min:10',
]);

$store = action(function () {
    $this->validate();

    $createPostKey = "create-post:" . request()->user()->id;
    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($createPostKey, 5)) {
        $minutes = ceil(\Illuminate\Support\Facades\RateLimiter::availableIn($createPostKey) / 60);

        session()->flash('limit', "Too many posts created. Please try again in {$minutes} minutes");
        return;
    }

    $this->authorize('create', \App\Models\Post::class);

    $post = \App\Models\Post::create([
        'title' => $this->title,
        'slug' => \Illuminate\Support\Str::slug($this->title),
        'excerpt' => $this->excerpt,
        'content' => $this->content,
        'published_at' => now(),
        'user_id' => auth()->id(),
    ]);

    \Illuminate\Support\Facades\RateLimiter::hit($createPostKey, 3600);

    $this->reset();

    session()->forget('limit');

    return redirect()->route('posts.show', $post->slug)->with('success', 'Post published successfully.');
});

layout('components.layouts.dashboard');

?>

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('dashboard.index') }}" wire:navigate
                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Create New Post</h1>
        </div>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Share your thoughts and ideas with the world</p>
    </div>
    @if(session('limit'))
        <p class="my-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <x-heroicon-o-exclamation-circle class="w-4 h-4" />
            {{ session('limit') }}
        </p>
    @endif

    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
        <form wire:submit="store" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" wire:model="title"
                    autocomplete="off"
                    class="block w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @class(['border-gray-300 dark:border-gray-700' => !$errors->has('title'), 'border-red-500 dark:border-red-500' => $errors->has('title')])"
                    placeholder="Enter post title" />
                @error('title')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Excerpt
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">(optional, max 500
                        characters)</span>
                </label>
                <textarea id="excerpt" wire:model="excerpt" rows="3"
                    autocomplete="off"
                    class="block w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none @class(['border-gray-300 dark:border-gray-700' => !$errors->has('excerpt'), 'border-red-500 dark:border-red-500' => $errors->has('excerpt')])"
                    placeholder="A brief summary of your post..."></textarea>
                @if($excerpt)
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ strlen($excerpt) }}/500 characters
                    </p>
                @endif
                @error('excerpt')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea id="content" wire:model="content" rows="12"
                    autocomplete="off"
                    class="block w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none font-mono text-sm @class(['border-gray-300 dark:border-gray-700' => !$errors->has('content'), 'border-red-500 dark:border-red-500' => $errors->has('content')])"
                    placeholder="Write your post content here..."></textarea>
                @error('content')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('dashboard.index') }}" wire:navigate
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store" class="flex items-center gap-1">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                        Publish Post
                    </span>
                    <span wire:loading wire:target="store" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endvolt