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

        session()->flash('success', 'Welcome Back ' . auth()->user()->name);

        return $this->redirectIntended(route('posts.index'));
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
        <div class="text-red-600">
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
        <button type="submit">Login</button>
    </form>
</div>
@endvolt