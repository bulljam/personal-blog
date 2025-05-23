@volt
<?php

use function Livewire\Volt\{state, layout, rules, action, mount};

state([
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'token' => '',
]);

mount(function () {
    $this->email = request()->query('email', '');
    $this->token = request()->query('token', '');
});

rules([
    'email' => 'required|email|max:255',
    'password' => 'required|confirmed|min:8|max:255',
    'token' => 'required|string',
]);

$resetPassword = action(function () {
    $this->validate();

    $status = \Illuminate\Support\Facades\Password::reset([
        'email' => $this->email,
        'password' => $this->password,
        'password_confirmation' => $this->password_confirmation,
        'token' => $this->token,
    ], function ($user, $password) {
        $user->password = $password;
        $user->save();
        $user->notify(new \App\Notifications\PasswordResetNotification());
    });

    if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
        session()->flash('success', __($status));
        return redirect()->route('login');
    }

    session()->flash('error', __($status));
});

layout('components.layouts.blog');

?>

<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Reset Password</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Enter your new password below</p>
            @if(session('error'))
                <p class="my-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                    {{ session('error') }}
                </p>
            @endif
        </div>

        <form wire:submit="resetPassword" class="space-y-5">
            <input type="hidden" name="token" wire:model="token" />
            <input type="hidden" name="email" wire:model="email" />

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    New Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="password" id="password" name="password" wire:model="password"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('password') border-red-500 dark:border-red-500 @enderror"
                        placeholder="••••••••" />
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Confirm Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        wire:model="password_confirmation"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('password_confirmation') border-red-500 dark:border-red-500 @enderror"
                        placeholder="••••••••" />
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                <span wire:loading wire:target="resetPassword" class="flex items-center gap-2">
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
    </div>
</div>
@endvolt