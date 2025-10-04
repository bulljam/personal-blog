@props([
    'post' => [],
    'updating' => false,
])

<div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
        Comments ({{ $post->commentsCount() }})
    </h2>

    @auth
        <div class="mb-8">
            <form wire:submit="{{ $updating ? 'updateComment' : 'addComment' }}" class="space-y-4">
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Add a comment
                    </label>
                    <textarea id="comment" wire:model="commentContent" rows="4" placeholder="Write your comment here..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    @error('commentContent')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                @if ($updating)
                    <button type="submit" wire:loading.attr="disabled" wire:target="updateComment"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="updateComment">Update Comment</span>
                        <span wire:loading wire:target="updateComment">Updating...</span>
                    </button>
                @else
                    <button type="submit" wire:loading.attr="disabled" wire:target="addComment"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="addComment">Post Comment</span>
                        <span wire:loading wire:target="addComment">Posting...</span>
                    </button>
                @endif
            </form>
        </div>
    @else
        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Sign in</a> 
                to leave a comment.
            </p>
        </div>
    @endauth

    <div class="space-y-6">
        @forelse($post->comments()->latest()->get() as $comment)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        {{ $comment->user->initials() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $comment->user->name }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $comment->content }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="editComment({{ $comment->id }})"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            aria-label="Edit comment">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                        </button>
                        <button wire:confirm="Are you sure you want to delete this comment?"
                            wire:click="deleteComment({{ $comment->id }})"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-400 hover:text-red-600 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            aria-label="Delete comment">
                            <x-heroicon-o-trash class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">No comments yet. Be the first to comment!</p>
            </div>
        @endforelse
    </div>
</div>