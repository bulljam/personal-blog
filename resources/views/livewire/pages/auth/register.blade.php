@volt
<?php

use function Livewire\Volt\{state, rules, action, layout};

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'remember' => false,
]);

rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email|max:255',
    'password' => 'required|confirmed|min:8|max:255',
    'remember' => 'nullable|boolean',
]);

$register = action(function () {
    $this->validate();

    $user = \App\Models\User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => $this->password,
    ]);


    \Illuminate\Support\Facades\Auth::login($user, $this->remember);

    return $this->redirectIntended(route('posts.index'));
});

layout('components.layouts.blog');
?>

<div>
    <form wire:submit="register">
        <label for="name">Name</label>
        <input type="text" name="name" wire:model="name" />
        <div>
            @error('name')
            {{ $message }}
            @enderror
        </div>
        <label for="email">Email</label>
        <input type="text" name="email" wire:model="email" />
        <div>
            @error('email')
            {{ $message }}
            @enderror
        </div>
        <label for="password">Password</label>
        <input type="password" name="password" wire:model="password" />
        <div>
            @error('password')
            {{ $message }}
            @enderror
        </div>
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" wire:model="password_confirmation" />

        <label for="remember">
            Remember me
            <input type="checkbox" name="remember" wire:model="remember" />
        </label>

        <button type="submit">Register</button>
    </form>
</div>
@endvolt