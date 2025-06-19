<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Livewire\Volt\Volt;

it('initializes with empty state', function () {
    Volt::test('pages.posts.index')
        ->assertSet('search', '')
        ->assertSet('author', '')
        ->assertSet('dateFilter', '')
        ->assertSet('authorSearch', '')
        ->assertSet('visible', false);
});

it('updates the state when values are set', function () {
    Volt::test('pages.posts.index')
        ->set('search', 'My post')
        ->set('author', 1)
        ->set('dateFilter', 'month')
        ->set('authorSearch', 'Adam')
        ->set('visible', true)
        ->assertSet('search', 'My post')
        ->assertSet('author', 1)
        ->assertSet('dateFilter', 'month')
        ->assertSet('authorSearch', 'Adam')
        ->assertSet('visible', true);
});

it('filters posts when filters are set', function () {
    $author1 = User::factory()->create(['role' => Role::AUTHOR]);
    $author2 = User::factory()->create(['role' => Role::AUTHOR]);

    $post1 = Post::factory()->create([
        'title' => 'My post',
        'user_id' => $author1->id,
    ]);
    $post2 = Post::factory()->create([
        'content' => 'posting content',
        'user_id' => $author2->id,
        'published_at' => today()->startOfMonth(),
    ]);
    $post3 = Post::factory()->create([
        'title' => 'My title',
        'content' => 'My content',
        'excerpt' => 'My excerpt',
        'user_id' => $author1->id,
        'published_at' => today()->subMonths(2),
    ]);

    $component = Volt::test('pages.posts.index');
    $component->set('search', 'post');

    $posts = $component->posts;

    expect($posts->count())->toBe(2);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id);


    $component->set('search', '');
    $component->set('author', $author1->id);

    $posts = $component->posts;

    expect($posts->count())->toBe(2);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post3->id);

    $component->set('author', '');
    $component->set('dateFilter', 'month');

    $posts = $component->posts;

    expect($posts->count())->toBe(2);
    expect($posts->pluck('id')->values())->toContain($post1->id, $post2->id);

    $component->set('search', 'post')
        ->set('author', $author1->id)
        ->set('dateFilter', 'month');

    $posts = $component->posts;

    expect($posts->count())->toBe(1);
    expect($posts->pluck('id')->values())->toContain($post1->id);
});

it('filters authors by name when author search is set', function () {
    $author1 = User::factory()->create(['name' => 'John Doe', 'role' => Role::AUTHOR]);
    $author2 = User::factory()->create(['name' => 'Maria Johnson', 'role' => Role::AUTHOR]);

    Post::factory()->create([
        'user_id' => $author1->id,
    ]);
    Post::factory()->create([
        'user_id' => $author2->id,
    ]);

    $component = Volt::test('pages.posts.index');

    $component->set('authorSearch', 'john');

    $authors = $component->authors;

    expect($authors)->toHaveCount(2);
    expect($authors->pluck('id')->values())->toContain($author1->id, $author2->id);

    $newAuthors = User::factory(10)->create([
        'name' => 'John ' . fake()->lastName(),
        'role' => Role::AUTHOR,
    ]);

    foreach ($newAuthors as $author) {
        Post::factory()->create([
            'user_id' => $author->id,
        ]);
    }

    $component->set('authorSearch', 'john');
    $authors = $component->authors;

    expect($authors)->toHaveCount(6);
});

it('clear filters', function () {

    $author1 = User::factory()->create([
        'name' => 'Adam Smith',
        'role' => Role::AUTHOR,
    ]);

    $author2 = User::factory()->create([
        'name' => 'John Doe',
        'role' => Role::AUTHOR,
    ]);

    $post1 = Post::factory()->create([
        'title' => 'My post',
        'user_id' => $author1->id,
    ]);

    $post2 = Post::factory()->create([
        'content' => 'posting',
        'user_id' => $author2->id,
    ]);

    $component = Volt::test('pages.posts.index');
    $component->set('search', 'post')
        ->set('author', $author1->id)
        ->set('dateFilter', 'month')
        ->set('authorSearch', 'Adam')
        ->set('visible', true);


    $posts = $component->posts;

    expect($posts->count())->toBe(1);
    expect($posts->pluck('id')->values())->toContain($post1->id);

    $authors = $component->authors;

    expect($authors)->toHaveCount(1);
    expect($authors->pluck('id')->values())->toContain($author1->id);

    $component->call('clearFilters');

    $component->assertSet('search', '')
        ->assertSet('author', '')
        ->assertSet('dateFilter', '')
        ->assertSet('authorSearch', '')
        ->assertSet('visible', false);

    expect($component->posts->count())->toBe(2);
    expect($component->authors)->toHaveCount(2);
});

it('deletes a post by its ID', function () {
    $author = User::factory()->unverified()->create([
        'role' => Role::AUTHOR,
    ]);

    $post1 = Post::factory()->create([
        'user_id' => $author->id,
    ]);

    // Case I: User not authenticated
    $component = Volt::test('pages.posts.index');
    $component->call('delete', $post1->id);
    $component->assertForbidden();
    expect(Post::find($post1->id))->not->toBeNull();

    // Case II: User is authenticated but not verified
    $component = Volt::test('pages.posts.index');
    $component->actingAs($author);
    $component->call('delete', $post1->id);
    $component->assertRedirect(route('verification.notice'));

    expect(Post::find($post1->id))->not->toBeNull();

    // Case III: User authenticated & verified but can't delete other users posts
    $component = Volt::test('pages.posts.index');
    $component->actingAs($author);
    $author->markEmailAsVerified();
    $post2 = Post::factory()->create([
        'user_id' => User::factory()->create()->id,
    ]);
    $component->call('delete', $post2->id);
    $component->assertForbidden();
    expect(Post::find($post1->id))->not->toBeNull();
    expect(Post::find($post2->id))->not->toBeNull();

    // Case IV: User authenticated & verified & can delete its own posts
    $component = Volt::test('pages.posts.index');
    $component->actingAs($author);
    $component->call('delete', $post1->id);
    expect(Post::find($post1->id))->toBeNull();
});

it('displays all posts', function () {
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create();

    $component = Volt::test('pages.posts.index');
    $component->assertSee('All Posts');
    $component->assertSee($post1->title);
    $component->assertSee($post1->excerpt);
    $component->assertSee($post1->user->name);
    $component->assertSee($post2->title);
    $component->assertSee($post2->excerpt);
    $component->assertSee($post2->user->name);
    $component->assertDontSee('edited');

    $post2->title = 'New Title';
    $post2->updated_at = now()->addMinute();
    $post2->save();
    $component = Volt::test('pages.posts.index');
    $component->assertSee('edited');
});

it('shows create, edit, delete buttons to authors only', function () {
    $author = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);


    Post::factory()->create([
        'user_id' => $author->id,
    ]);

    $reader = User::factory()->create([
        'role' => Role::READER,
    ]);

    $this->actingAs($author);
    $component = Volt::test('pages.posts.index');
    $component->assertSee('Create Post');
    $component->assertSee('Edit');
    $component->assertSee('Delete');

    $this->actingAs($reader);
    $component = Volt::test('pages.posts.index');
    $component->assertDontSee('Create Post');
    $component->assertDontSee('Edit');
    $component->assertDontSee('Delete');
});

it('displays empty state when no posts exist', function () {
    Volt::test('pages.posts.index')
        ->assertSee('Check back later for new content.');
});