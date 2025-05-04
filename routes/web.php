<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Volt::route('/posts', 'pages.posts.index')->name('posts.index');
Volt::route('/posts/{post:slug}', 'pages.posts.show')->name('posts.show');
