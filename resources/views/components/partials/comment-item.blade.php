@props(['comment', 'depth' => 0, 'replyingTo' => null])

@php
$maxDepth = 5;
$isMaxDepth = $depth >= $maxDepth;
@endphp

<div
    class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 {{ $depth > 0 ? 'ml-8 mt-4 border-l-2 border-gray-300 dark:border-gray-700 pl-4' : '' }}">
    <div class="flex items-start gap-3">
        <div
            class="w-10 h-10 rounded-full bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
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
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap mb-2">
                {{ $comment->content }}
            </p>

            @auth
                @if (!$isMaxDepth)
                    <button wire:click="startReply({{ $comment->id }})"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline font-medium transition-colors">
                        Reply
                    </button>
                @endif
                <button wire:click="cancelReply"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline font-medium transition-colors">
                    Cancel
                </button>
            @endauth
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button wire:click="editComment({{ $comment->id }}, 'subCommentContent')"
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

    @auth
        @if ($replyingTo === $comment->id)
            <div class="mt-4 ml-13">
                <form wire:submit="addComment('subCommentContent')" class="space-y-3">
                    <textarea wire:model="subCommentContent" rows="3" placeholder="Write your reply..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    @error('commentContent')
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center gap-2">
                        <button type="submit" wire:loading.attr="disabled" wire:target="addComment"
                            class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="addComment">Post Reply</span>
                            <span wire:loading wire:target="addComment">Posting...</span>
                        </button>
                        <button type="button" wire:click="cancelReply"
                            class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endauth

    @if ($comment->replies->isNotEmpty())
        <div class="mt-4 space-y-4">
            @foreach ($comment->replies as $reply)
                <x-partials.comment-item :comment="$reply" :replyingTo="$replyingTo" :depth="$depth + 1" />
            @endforeach
        </div>
    @endif
</div>