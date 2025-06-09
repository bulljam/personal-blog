@volt
<?php

use function Livewire\Volt\{state, rules, action, layout};

state([
    'email' => '',
    'is_sent' => false,
]);

rules([
    'email' => 'required|email|max:255|exists:users,email'
]);

$notify = action(function () {
    $this->validate();

    $fingerPrint = md5(
        request()->input('email', '') .
        request()->ip() .
        request()->userAgent() .
        request()->header('Accept-Language', '')
    );
    $forgotPassKey = "forgotPassword:{$fingerPrint}";

    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($forgotPassKey, 5)) {
        $minutes = ceil(\Illuminate\Support\Facades\RateLimiter::availableIn($forgotPassKey) / 60);
        
        $this->resetErrorBag('email');

        $this->addError('email', "Too many password reset attempts. Please try again in {$minutes} minutes.");

        return;
    }

    \Illuminate\Support\Facades\RateLimiter::hit($forgotPassKey, 600);

    $status = \Illuminate\Support\Facades\Password::sendResetLink([
        'email' => $this->email,
    ]);

    if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
        session()->flash('success', __($status));
        $this->is_sent = true;

        \Illuminate\Support\Facades\RateLimiter::clear($forgotPassKey);
        return;
    }

    $this->addError('email', __($status));
});

layout('components.layouts.blog');

?>

<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Forgot Password</h2>
        </div>
        @if ($this->is_sent)
            <div class="text-center">
                <p class="my-2 text-sm text-green-600 dark:text-green-400 flex items-center gap-1 mb-4">
                    <x-heroicon-o-check class="w-4 h-4" />
                    {{ session('success') ?? 'Please check your email inbox for the password reset link.'}}
                </p>
                <a href="{{ route('login') }}" wire:navigate
                    class="inline-block font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Back to login
                </a>
            </div>
        @else
            <div class="mb-6">
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">We'll send you a
                    password reset link.</p>
            </div>
            <form wire:submit="notify" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Your Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" />
                        </div>
                        <input type="email" id="email" name="email" wire:model="email"
                            autocomplete="off"
                            class="block w-full pl-10 pr-3 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @class(['border-gray-300 dark:border-gray-700' => !$errors->has('email'), 'border-red-500 dark:border-red-500' => $errors->has('email')])"
                            placeholder="you@example.com" />
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="notify">Send Reset Link</span>
                    <span wire:loading wire:target="notify" class="flex items-center gap-2">
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
            </form>

            <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Remember your password?
                <a href="{{ route('login') }}" wire:navigate
                    class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Sign in
                </a>
            </p>
        @endif
    </div>
</div>
@endvolt