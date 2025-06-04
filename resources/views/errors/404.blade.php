@extends('errors.layout')

@section('title', 'Page not found')

@section('content')
    <div class="text-center space-y-4">
        <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
            Error 404
        </p>

        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
            We couldn’t find that page.
        </h1>

        <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $exception->getMessage() ?: 'The page you are looking for may have been moved or deleted.' }}
        </p>
    </div>
@endsection