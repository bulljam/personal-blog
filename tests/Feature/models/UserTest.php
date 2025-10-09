<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;

it('generates initials from name', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
    ]);

    expect($user->initials())->toBe('JD');
});

it('generates initials from a Single name', function () {
    $user = User::factory()->create([
        'name' => 'John',
    ]);

    expect($user->initials())->toBe('J');
});

it('checks if user is author', function () {
    $author = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $reader = User::factory()->create([
        'role' => Role::READER,
    ]);

    expect($author->isAuthor())->toBeTrue();
    expect($reader->isAuthor())->toBeFalse();
});

it('has many posts', function () {
    $user = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $posts = Post::factory(5)->create([
        'user_id' => $user->id,
    ]);

    expect($user->posts)->toHaveCount(5);

    expect(
        $user->posts->pluck('id')->sort()->values()
    )->toEqual($posts->pluck('id')->sort()->values());
});

it('filters author scope', function () {
    User::factory()->create([
        'name' => 'Adam Smith',
        'role' => Role::AUTHOR,
    ]);

    User::factory()->create([
        'role' => Role::READER,
    ]);

    User::factory()->create([
        'name' => 'Lionel Messi',
        'role' => Role::AUTHOR,
    ]);

    $authors = User::authors()->get();

    expect($authors)->toHaveCount(2);
    expect($authors->pluck('name')->values())->toContain('Adam Smith', 'Lionel Messi');
});

it('filters authors with posts scope', function () {
    $author1 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);
    $author2 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);
    $author3 = User::factory()->create([
        'role' => Role::READER,
    ]);
    $author4 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    Post::factory()->create([
        'user_id' => $author1->id,
    ]);
    Post::factory()->create([
        'user_id' => $author2->id,
    ]);
    Post::factory()->create([
        'user_id' => $author3->id,
    ]);

    $authorsWithPosts = User::authorsWithPosts()->get();

    expect($authorsWithPosts)->toHaveCount(2);
    expect($authorsWithPosts->pluck('id')->values())->toContain($author1->id, $author2->id);
});

it('filters authors by name scope', function () {
    $author1 = User::factory()->create([
        'name' => 'Robert Greene',
        'role' => Role::AUTHOR,
    ]);

    $author2 = User::factory()->create([
        'name' => 'Alexis Robert',
        'role' => Role::AUTHOR,
    ]);

    $author3 = User::factory()->create([
        'name' => 'Robert Anthony',
        'role' => Role::AUTHOR,
    ]);

    Post::factory()->create([
        'user_id' => $author1->id,
    ]);

    Post::factory()->create([
        'user_id' => $author2->id,
    ]);

    $authorsByName = User::authorsByName('Robert')->get();

    expect($authorsByName)->toHaveCount(2);
    expect($authorsByName->pluck('name')->values())->toContain('Robert Greene', 'Alexis Robert');
});
