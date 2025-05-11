<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('posts')->as('posts.')->middleware('auth')->group(function () {

    Volt::route('/create', 'pages.posts.create')->middleware('throttle:create-post')->name('create');
    
    Volt::route('/{post:slug}/edit', 'pages.posts.edit')->name('edit');

    Route::withoutMiddleware('auth')->group(function () {
        Volt::route('/', 'pages.posts.index')->name('index');
        Volt::route('/{post:slug}', 'pages.posts.show')->name('show');
    });

});

Route::middleware('guest')->group(function () {
    Volt::route('/login', 'pages.auth.login')->middleware(['throttle:login'])->name('login');
    Volt::route('/register', 'pages.auth.register')->middleware(['throttle:register'])->name('register');
});

Route::middleware('auth')->post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');
