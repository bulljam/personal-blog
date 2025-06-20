@volt
<?php
use function Livewire\Volt\{uses, state, computed, action, layout};

uses(\Livewire\WithPagination::class);

state([
    'search' => '',
    'author' => '',
    'dateFilter' => '',
    'authorSearch' => '',
    'visible' => false,
]);

$posts = computed(function () {
    return \App\Models\Post::publishedPosts()
        ->search($this->search)
        ->author($this->author)
        ->date($this->dateFilter)
        ->paginate(5);
});

$authors = computed(fn() => \App\Models\User::authorsByName($this->authorSearch)->limit(6)->get());


$clearFilters = action(function () {
    $this->reset();
    $this->resetPage();
});

layout('components.layouts.blog');
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">All Posts</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Discover the latest articles and stories</p>
        </div>
        @author
        <a wire:navigate href="{{ route('posts.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Create Post</span>
        </a>
        @endauthor
    </div>
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


    <!-- Modern Filter Bar -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-4 sm:p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Search Posts
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" id="search" wire:model.live.debounce.300ms="search" autocomplete="off"
                        placeholder="Search by title, excerpt, or content..."
                        class="block w-full pl-10 pr-10 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @class(['border-gray-300 dark:border-gray-700' => !$errors->has('search'), 'border-red-500 dark:border-red-500' => $errors->has('search')])" />
                    <div wire:loading wire:target="search"
                        class="absolute inset-y-3 right-0 pr-3 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Author Filter - Searchable -->
            <div>
                <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Author
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-user class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" id="authorSearch" wire:model.live.debounce.300ms="authorSearch"
                        autocomplete="off" @focus="$wire.set('visible', true)" placeholder="Search author..."
                        class="block w-full pl-10 pr-10 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors border-gray-600 dark:border-gray-300" />

                    <!-- Dropdown results -->
                    @if($this->authorSearch && $this->visible)
                        <div x-data="{
                                                    showScrollIndicator: false,
                                                    init() {
                                                        this.$nextTick(() => {
                                                            this.checkScroll();
                                                            this.$refs.dropdown.addEventListener('scroll', () => this.checkScroll());
                                                        });
                                                        this.$watch('$wire.visible', (value) => {
                                                            if (value) {
                                                                this.$nextTick(() => this.checkScroll());
                                                            }
                                                        });
                                                    },
                                                    checkScroll() {
                                                        const el = this.$refs.dropdown;
                                                        if (!el) return;
                                                        this.showScrollIndicator = el.scrollHeight > el.clientHeight;
                                                    }
                                                }" class="absolute z-10 mt-1 w-full">
                            <!-- Dropdown Container -->
                            <div
                                class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden">
                                <!-- Scrollable Content -->
                                <div x-ref="dropdown" @click="$wire.set('visible', false)"
                                    class="max-h-40 overflow-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                    @foreach($this->authors as $author)
                                        <div wire:click="$set('author','{{ $author->id }}'); $set('visible', false)"
                                            class="px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition-colors duration-150 border-l-2 border-transparent hover:border-blue-500 dark:hover:border-blue-400">
                                            <span class="text-gray-900 dark:text-gray-100">{{ $author->name }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Mouse Scroll Indicator Overlay -->
                                <div x-show="showScrollIndicator" x-transition
                                    class="absolute inset-x-0 bottom-0 h-12 pointer-events-none flex items-end justify-center pb-2">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <!-- Mouse Icon -->
                                        <div
                                            class="relative w-4 h-6 border border-gray-400 dark:border-gray-500 rounded-full flex items-start justify-center pt-1 bg-gray-100 dark:bg-gray-700">
                                            <div
                                                class="w-0.5 h-1 bg-gray-500 dark:bg-gray-300 rounded-full animate-scroll-indicator">
                                            </div>
                                        </div>
                                        <!-- Scroll Text -->
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Scroll</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Date Filter -->
            <div>
                <label for="dateFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Date
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-calendar class="h-5 w-5 text-gray-400" />
                    </div>
                    <select id="dateFilter" wire:model.live="dateFilter"
                        class="block w-full pl-10 pr-10 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors appearance-none cursor-pointer @class(['border-gray-300 dark:border-gray-700' => !$errors->has('dateFilter'), 'border-red-500 dark:border-red-500' => $errors->has('dateFilter')])">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <x-heroicon-o-chevron-down class="h-5 w-5 text-gray-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Clear Filters Button -->
        @if ($this->search || $this->author || $this->dateFilter)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                <button wire:click="clearFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    <span>Clear all filters</span>
                </button>
            </div>
        @endif
    </div>
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No posts found</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if ($this->search || $this->author || $this->dateFilter)
                        Try adjusting your filters to see more results.
                    @else
                        Check back later for new content.
                    @endif
                </p>
            </div>
        @endforelse
        {{ $this->posts->links() }}
    </div>
</div>
@endvolt