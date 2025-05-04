<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('posts')->as('posts.')->group(function () {
    Volt::route('/', 'pages.posts.index')->name('index');
    Volt::route('/create', 'pages.posts.create')->name('create');
    Volt::route('/{post:slug}', 'pages.posts.show')->name('show');
});


