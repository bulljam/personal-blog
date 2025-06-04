@extends('errors.layout')

@section('title', 'Forbidden')

@section('content')
    <div class="text-center space-y-4">
        <p class="text-sm font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">
            Error 403
        </p>

        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
            You are not allowed to do that.
        </h1>

        <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $exception->getMessage() ?: 'You do not have permission to perform this action.' }}
        </p>
    </div>
@endsection