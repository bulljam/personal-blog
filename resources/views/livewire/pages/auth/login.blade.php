@volt
<?php

use function Livewire\Volt\{state, rules, action, layout};

state([
    'email' => '',
    'password' => '',
    'remember' => false,
]);

rules([
    'email' => 'required|email|exists:users,email|max:255',
    'password' => 'required|min:8|max:255',
    'remember' => 'boolean',
]);



$login = action(function () {
    $this->validate();
    $fingerPrint = md5(
        request()->ip() .
        request()->input('email', '') .
        request()->userAgent() .
        request()->header('Accept-Language', '')
    );

    $loginKey = "login:{$fingerPrint}";

    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($loginKey, 5)) {
        $minutes = ceil(\Illuminate\Support\Facades\RateLimiter::availableIn($loginKey) / 60);

        session()->flash('limit', "Too many failed login attempts. Please try again in {$minutes} minutes");
        return;
    }
    if (\Illuminate\Support\Facades\Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
        session()->regenerate();

        session()->flash('success', 'Welcome Back ' . auth()->user()->name);

        session()->forget('limit');
        \Illuminate\Support\Facades\RateLimiter::clear($loginKey);

        return $this->redirectIntended(route('posts.index'));
    }
    $attempts = \Illuminate\Support\Facades\RateLimiter::attempts($loginKey);
    $retryAfter = 60 * pow(2, min($attempts, 5));
    \Illuminate\Support\Facades\RateLimiter::hit($loginKey, $retryAfter);

    $this->addError(
        'email',
        'Invalid Email or Password.',
    );

});

layout('components.layouts.blog');

?>

<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome Back</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Sign in to your account to continue</p>
            @if(session('limit'))
                <p class="my-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                    {{ session('limit') }}
                </p>
            @endif
            @if(session('success'))
                <p class="my-2 text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                    <x-heroicon-o-check class="w-4 h-4" />
                    {{ session('success') }}
                </p>
            @endif
        </div>
        <form wire:submit="login" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="email" id="email" name="email" wire:model="email"
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

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="password" id="password" name="password" wire:model="password"
                        class="block w-full pl-10 pr-3 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @class(['border-gray-300 dark:border-gray-700' => !$errors->has('password'), 'border-red-500 dark:border-red-500' => $errors->has('password')])"
                        placeholder="••••••••" />
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" wire:model="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-800" />
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Remember me
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled" @disabled(session('limit'))>
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2">
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

        <div class="mt-6 space-y-3">
            <a href="{{ route('password.forgot') }}" wire:navigate
                class="flex items-center justify-center gap-2 w-full py-2.5 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg transition-colors">
                <x-heroicon-o-key class="w-4 h-4" />
                <span>Forgot your password?</span>
            </a>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                Don't have an account?
                <a href="{{ route('register') }}" wire:navigate
                    class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Sign up
                </a>
            </p>
        </div>
    </div>
</div>
@endvolt