@volt
<?php

use function Livewire\Volt\action;
use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;


state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'role' => \App\Enums\Role::READER,
    'remember' => false,
    'role_page' => false,
    'user' => null,
]);

rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email|max:255',
    'password' => 'required|confirmed|min:8|max:255',
    'remember' => 'boolean',
]);

$register = action(function () {
    $this->validate();

    $fingerprint = md5(
        request()->ip() .
        request()->userAgent() .
        request()->header('Accept-Language', '')
    );
    $registerKey = "register:{$fingerprint}";

    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($registerKey, 5)) {
        $minutes = ceil(\Illuminate\Support\Facades\RateLimiter::availableIn($registerKey) / 60);

        session()->flash('limit', "Too many account registrations. Please try again in {$minutes} minutes");

        return;
    }

    $user = \App\Models\User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => \Illuminate\Support\Facades\Hash::make($this->password),
    ]);

    $this->role_page = true;
    $this->user = $user;


    \Illuminate\Support\Facades\RateLimiter::hit($registerKey, 3600);

    session()->forget('limit');
});

$setRole = action(function (string $role) {
    if ($enum = \App\Enums\Role::tryFrom($role)) {
        $this->role = $enum;
        $this->addRole();

        return;
    }

    session()->forget('error');
    session()->flash('error', 'Please fill choose a role before proceeding.');
});

$addRole = action(function () {
    if (!$this->user) {
        $this->reset();
        $this->role_page = false;
        session()->forget('error');
        session()->flash('error', 'Please fill in this form before choosing a role.');
        return;
    }

    $this->user->role = $this->role;
    $this->user->save();

    \Illuminate\Support\Facades\Auth::login($this->user, $this->remember);

    $this->user->sendEmailVerificationNotification();


    return redirect(route('verification.notice'));
});

layout('components.layouts.blog');
?>

<div class="max-w-md mx-auto">
    @if(session('error'))
        <p class="mb-4 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <x-heroicon-o-exclamation-circle class="w-4 h-4" />
            {{ session('error') }}
        </p>
    @endif
    @if($this->role_page)
        <div>
            <div wire:click="setRole('{{ \App\Enums\Role::AUTHOR->value }}')" class="bg-red-700 p-4">
                {{ \App\Enums\Role::AUTHOR->getLabel() }}
            </div>
            <div wire:click="setRole('{{ \App\Enums\Role::READER->value }}')" class="bg-green-700 p-4 my-4">
                {{ \App\Enums\Role::READER->getLabel() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create Account</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Sign up to start writing and sharing your posts</p>
            </div>
            @if(session('limit'))
                <p class="my-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                    {{ session('limit') }}
                </p>
            @endif

            <form wire:submit="register" class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Full Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-user class="h-5 w-5 text-gray-400" />
                        </div>
                        <input type="text" id="name" name="name" wire:model="name" autocomplete="off" @class([
                            'block w-full pl-10 pr-3 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                            'border-red-500 dark:border-red-500' => $errors->has('name'),
                            'border-gray-300 dark:border-gray-700' => !$errors->has('name'),
                        ])                    placeholder="John Doe" />
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" />
                        </div>
                        <input type="email" id="email" name="email" wire:model="email" autocomplete="off" @class([
                            'block w-full pl-10 pr-3 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                            'border-red-500 dark:border-red-500' => $errors->has('email'),
                            'border-gray-300 dark:border-gray-700' => !$errors->has('email'),
                        ]) placeholder="you@example.com" />
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
                        <input type="password" id="password" name="password" wire:model="password" autocomplete="new-password" @class([
                            'block w-full pl-10 pr-3 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                            'border-red-500 dark:border-red-500' => $errors->has('password'),
                            'border-gray-300 dark:border-gray-700' => !$errors->has('password'),
                        ]) placeholder="••••••••" />
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
                            autocomplete="new-password"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="••••••••" />
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" wire:model="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-800" />
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Remember me
                    </label>
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled" @disabled(session('limit'))>
                    <span wire:loading.remove wire:target="register">Create Account</span>
                    <span wire:loading wire:target="register" class="flex items-center gap-2">
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
                Already have an account?
                <a href="{{ route('login') }}" wire:navigate
                    class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Sign in
                </a>
            </p>
        </div>
    @endif
</div>
@endvolt