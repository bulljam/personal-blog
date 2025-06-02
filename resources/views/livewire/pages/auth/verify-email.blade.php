@volt
<?php

use function Livewire\Volt\{mount, layout};

mount(function () {
    if (auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('posts.index');
    }
});

layout('components.layouts.blog');
?>

<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
        <div class="mb-6">
            <div
                class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-blue-100 dark:bg-blue-900/20">
                <x-heroicon-o-envelope class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center">Verify Your Email</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center">
                We've sent a verification link to your email address
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <p class="text-sm text-green-800 dark:text-green-200 flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
                    {{ session('status') }}
                </p>
            </div>
        @else
            <div class="mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                    Please check your email and click on the verification link to verify your account.
                </p>
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <x-heroicon-o-paper-airplane class="w-4 h-4" />
                    <span>Resend Verification Email</span>
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                Didn't receive the email? Check your spam folder or try resending.
            </p>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
            <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('posts.index') }}" wire:navigate
                    class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Back to posts
                </a>
            </p>
        </div>
    </div>
</div>
@endvolt