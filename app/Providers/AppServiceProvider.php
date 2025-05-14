<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('create-post', fn (Request $request) => Limit::perHour(5)->by($request->user()?->id ?? $request->ip())->response(fn () => redirect()->route('posts.index')->with('error', 'Too many new posts creation attempts. Please try again in an hour or contact support.')));

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->email.$request->ip())->response(fn () => back()->withErrors(['email' => 'Too many login attempts. Please try again in a minute or contact support.'])));

        RateLimiter::for('register', fn (Request $request) => Limit::perHour(4)->by($request->ip())->response(fn () => back()->withErrors(['email' => 'Too many registration attempts. Please try again in an hour or contact support.'])));
    }
}
