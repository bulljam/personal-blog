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
    'remember' => 'nullable|boolean',
]);

$login = action(function () {
    $this->validate();
    if (\Illuminate\Support\Facades\Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
        session()->regenerate();

        return $this->redirectIntended(route('posts.index'))->with('success', 'Welcome Back ' . \Illuminate\Support\Facades\Auth::user()->name);
    }

    return back()->withErrors([
        'email' => 'Invalid Email or Password.',
    ]);
});
layout('components.layouts.blog');

?>

<div>
    <form wire:submit="login">
        <label for="email">Email</label>
        <input type="text" name="email" wire:model="email" />
        <div>
            @errors('email')
            {{ $message }}
            @enderrors
        </div>
        <label for="password">Password</label>
        <input type="password" name="password" wire:model="password" />
        <div>
            @errors('password')
            {{ $message }}
            @enderrors
        </div>
        <button type="submit">Login</button>
    </form>
</div>
@endvolt