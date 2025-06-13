<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;

it('belongs to user', function () {
    $user = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($post->user)->toBeInstanceOf(User::class);
    expect($post->user->id)->toBe($user->id);
});

it('checks if a post is edited', function () {
    $post = Post::factory()->create([
        'published_at' => now()->subMinutes(10),
    ]);
    $post->title = 'ABC';
    $post->save();

    expect($post->is_edited)->toBeTrue();
});

it('checks if a post is not edited when updated within 5 seconds', function () {
    $post = Post::factory()->create([
        'published_at' => now()->subSeconds(3),
    ]);
    $post->title = 'ABC';
    $post->save();

    expect($post->is_edited)->toBeFalse();
});

it('filters published posts scope', function () {
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create();
    $post3 = Post::factory()->create([
        'published_at' => null,
    ]);
    $posts = Post::publishedPosts()->get();

    expect($posts)->toHaveCount(2);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id);
    expect($posts->pluck('id')->values())->not->toContain($post3->id);
});

it('filters search scope', function () {
    $post1 = Post::factory()->create([
        'title' => 'abc',
    ]);
    $post2 = Post::factory()->create([
        'content' => 'abc',
    ]);
    $post3 = Post::factory()->create([
        'excerpt' => 'abc',
    ]);
    $post4 = Post::factory()->create();

    $posts = Post::search('abc')->get();
    expect($posts)->toHaveCount(3);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id, $post3->id);
    expect($posts->pluck('id')->values())->not->toContain($post4->id);

    $posts = Post::search('')->get();
    expect($posts)->toHaveCount(4);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id, $post3->id, $post4->id);
});

it('filters author scope', function () {
    $author1 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $author2 = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $post1 = Post::factory()->create([
        'user_id' => $author1->id,
    ]);

    $post2 = Post::factory()->create([
        'user_id' => $author1->id,
    ]);

    $post3 = Post::factory()->create([
        'user_id' => $author2->id,
    ]);

    $posts = Post::author($author1->id)->get();

    expect($posts)->toHaveCount(2);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id);
    expect($posts->pluck('id')->values())->not->toContain($post3->id);

    $posts = Post::author('')->get();

    expect($posts)->toHaveCount(3);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id, $post3->id);
});

it('filters date scope', function () {
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create([
        'published_at' => now()->startOfWeek(),
    ]);
    $post3 = Post::factory()->create([
        'published_at' => now()->startOfMonth(),
    ]);
    $post4 = Post::factory()->create([
        'published_at' => now()->startOfYear(),
    ]);
    $post5 = Post::factory()->create([
        'published_at' => now()->subYears(2),
    ]);
    $post6 = Post::factory()->create([
        'published_at' => now()->addWeeks(2),
    ]);

    $posts = Post::date('today')->get();
    expect($posts)->toHaveCount(1);
    expect($posts->pluck('id')->values())->toContain($post1->id);

    $posts = Post::date('week')->get();
    expect($posts)->toHaveCount(3);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id, $post6->id);

    $posts = Post::date('month')->get();
    expect($posts)->toHaveCount(4);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id, $post3->id, $post6->id);

    $posts = Post::date('year')->get();
    expect($posts)->toHaveCount(5);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id, $post3->id, $post4->id, $post6->id);

    $posts = Post::date('')->get();
    expect($posts)->toHaveCount(6);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id, $post3->id, $post4->id, $post5->id, $post6->id);
});