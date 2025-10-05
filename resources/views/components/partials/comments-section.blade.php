@props([
    'post' => [],
    'updating' => false,
    'replyingTo' => null,
])

<div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
        Comments ({{ $post->commentsCount() }})
    </h2>

    @auth
        @if (!$updating)
            <div class="mb-8">
                <form wire:submit="addComment" class="space-y-4">
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
                    <button type="submit" wire:loading.attr="disabled" wire:target="addComment"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="addComment">Post Comment</span>
                        <span wire:loading wire:target="addComment">Posting...</span>
                    </button>
                </form>
            </div>
        @else
            <div class="mb-8">
                <form wire:submit="updateComment" class="space-y-4">
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Edit comment
                        </label>
                        <textarea id="comment" wire:model="commentContent" rows="4" placeholder="Write your comment here..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        @error('commentContent')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" wire:loading.attr="disabled" wire:target="updateComment"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="updateComment">Update Comment</span>
                            <span wire:loading wire:target="updateComment">Updating...</span>
                        </button>
                        <button type="button" wire:click="cancelReply"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @else
        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Sign in</a> 
                to leave a comment.
            </p>
        </div>
    @endauth

    <div class="space-y-6">
        @forelse($post->comments()->whereNull('parent_id')->latest()->get() as $comment)
            <x-partials.comment-item :comment="$comment" :replyingTo="$replyingTo" :depth="0" />
        @empty
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">No comments yet. Be the first to comment!</p>
            </div>
        @endforelse
    </div>
</div>