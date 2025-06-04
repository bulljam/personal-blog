<?php

use App\Notifications\EmailVerificationNotification;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('posts.index');
})->name('home');

Route::prefix('posts')->as('posts.')->group(function () {

    Route::middleware(['auth', 'verified', 'author'])->group(function () {
        Volt::route('/create', 'pages.posts.create')->name('create');
        Volt::route('/{post:slug}/edit', 'pages.posts.edit')->name('edit');
    });

    Volt::route('/', 'pages.posts.index')->name('index');
    Volt::route('/{post:slug}', 'pages.posts.show')->name('show');
});

Route::middleware('guest')->group(function () {
    Volt::route('/login', 'pages.auth.login')->name('login');
    Volt::route('/register', 'pages.auth.register')->name('register');
    Volt::route('/forgot-password', 'pages.auth.forgot-password')->name('password.forgot');
    Volt::route('/reset-password', 'pages.auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::prefix('email/verify')->middleware(['auth'])->group(function () {

    Volt::route('/', 'pages.auth.verify-email')->name('verification.notice');

    Route::post('/', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('posts.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('status', 'Verification link was sent.');
    })->name('verification.send');
    Route::middleware('signed')->get('/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        // $request->user()->notify(new EmailVerificationNotification());
        return redirect()->route('posts.index');
    })->name('verification.verify');
});
