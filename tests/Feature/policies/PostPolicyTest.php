<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;

it('allows authors to create posts', function () {
    $author = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    expect($author->can('create', Post::class))->toBeTrue();
});

it('prevents readers from creating posts', function () {
    $reader = User::factory()->create([
        'role' => Role::READER,
    ]);

    expect($reader->can('create', Post::class))->toBeFalse();
});

it('allows authors to update their own posts', function () {
    $author = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $post = Post::factory()->create([
        'user_id' => $author->id,
    ]);

    expect($author->can('update', $post))->toBeTrue();
});

it('prevents authors from updating other authors\' posts', function () {
    $author1 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);
    $author2 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $post = Post::factory()->create([
        'user_id' => $author1->id,
    ]);

    expect($author2->can('update', $post))->toBeFalse();
});

it('allows authors to delete their own posts', function () {
    $author = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $post = Post::factory()->create([
        'user_id' => $author->id,
    ]);

    expect($author->can('delete', $post))->toBeTrue();
});

it('prevents authors from deleting other authors\' posts', function () {
    $author1 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);
    $author2 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $post = Post::factory()->create([
        'user_id' => $author1->id,
    ]);

    expect($author2->can('delete', $post))->toBeFalse();
});