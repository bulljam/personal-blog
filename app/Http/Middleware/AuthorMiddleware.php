<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class AuthorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->isAuthor()) {
            // return $request->expectsJson()
            //     ? abort(403, 'You are not authorize to create a post.')
            //     : Redirect::intended(URL::route('posts.index'));

            abort(403, 'You are not allowed to create a post');
        }
        return $next($request);
    }
}
