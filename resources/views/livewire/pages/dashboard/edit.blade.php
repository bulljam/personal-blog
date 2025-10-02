@volt
<?php

use function Livewire\Volt\{state, computed, mount, rules, action, layout};
state([
    'user' => null,
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'role' => '',
]);

$messages = computed(
    fn() => [
        'name' => 'Name',
        'email' => 'Email',
    ]
);
rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
    'password' => 'required|confirmed|min:8|max:255',
    'role' => 'required|in:' . \App\Enums\Role::AUTHOR->value . ',' . \App\Enums\Role::READER->value,
]);

mount(function () {
    $this->user = auth()->user();
    $this->name = $this->user->name;
    $this->email = $this->user->email;
    $this->role = $this->user->role->value;
});

$update = action(function ($field) {
    $this->validateOnly($field);

    if ($this->user->$field !== $this->$field) {
        $this->user->$field = $this->$field;
        $this->user->save();
        $this->user->refresh();

        session()->flash('success', $this->messages[$field] . ' updated successfully.');
        return redirect()->route('dashboard.edit');
    } else {
        session()->flash('info', 'No changes detected.');
    }
});

$updatePassword = action(function () {
    $this->validateOnly('password');

    if (password_verify($this->password, $this->user->password)) {
        $this->addError('password', 'The new password must be different from your current password.');
        return;
    }

    $this->user->password = \Illuminate\Support\Facades\Hash::make($this->password);
    $this->user->save();
    $this->user->refresh();

    $this->reset(['password', 'password_confirmation']);

    session()->flash('success', 'Password updated successfully.');
    return redirect()->route('dashboard.edit');
});

$updateRole = action(function ($value, $delete = false) {
    $this->validateOnly('role');

    $this->role = $value;

    if ($this->user->role->value === $this->role) {
        $this->addError('role', 'The new role must be different from your current role.');
        return;
    }

    $this->user->role = $this->role;
    $this->user->save();
    $this->user->refresh();

    if ($delete === true) {
        $this->user->posts()->delete();
    }

    session()->flash('success', 'Role updated successfully.');
    return redirect()->route('dashboard.edit');
});

layout('components.layouts.dashboard');
?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Profile Settings</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Manage your account information and preferences
        </p>
    </div>

    <!-- Success Message -->
    @session('success')
        <div
            class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endsession

    <!-- Profile Card -->
    <div
        class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <!-- Avatar Section -->
        <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-6">
                <div
                    class="w-20 h-20 rounded-full bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white text-2xl font-semibold">
                    {{ $this->user->initials() }}
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $this->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $this->email }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Profile Fields -->
        <div class="divide-y divide-gray-200 dark:divide-gray-800">
            <!-- Name -->
            <div
                class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex-1">
                    <label
                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Name
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $this->name }}
                    </p>
                </div>
                <x-partials.name-form :name="$this->name"  />
            </div>

            <!-- Email -->
            <div
                class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex-1">
                    <label
                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Email
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $this->email }}
                    </p>
                </div>
                <x-partials.email-form :name="$this->name"  />
            </div>

            <!-- Password -->
            <div
                class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex-1">
                    <label
                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Password
                    </label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                        ••••••••••••
                    </p>
                </div>
                <x-partials.password-form :password="$this->password"
                    :password_confirmation="$this->password_confirmation"  />
            </div>

            <!-- Role -->
            <div
                class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex-1">
                    <label
                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Role
                    </label>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                            {{ $this->user->role->getLabel() }}
                        </span>
                    </div>
                </div>
                <x-partials.role-form :role="$this->role" />
            </div>
        </div>
    </div>
</div>
@endvolt