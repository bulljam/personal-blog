<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('posts')->as('posts.')->middleware('auth')->group(function () {
    Volt::route('/', 'pages.posts.index')->name('index')->withoutMiddleware('auth');

    Volt::route('/create', 'pages.posts.create')->name('create');
    Volt::route('/{post:slug}/edit', 'pages.posts.edit')->name('edit');
    Volt::route('/{post:slug}', 'pages.posts.show')->name('show');
});

Route::middleware('guest')->group(function () {
    Volt::route('/login', 'pages.auth.login')->name('login');
    Volt::route('/register', 'pages.auth.register')->name('register');
});

Route::middleware('auth')->post('/logout', function () {
    Auth::logout();
    session()->regenerate();

    return redirect()->route('login');
})->name('logout');
