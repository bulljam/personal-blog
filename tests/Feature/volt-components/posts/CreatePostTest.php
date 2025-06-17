<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Livewire\Volt\Volt;

it('initializes with empty state', function () {
    Volt::test('pages.posts.create')
        ->assertSet('title', '')
        ->assertSet('excerpt', null)
        ->assertSet('content', '');
});

it('updates state when values are set', function () {
    Volt::test('pages.posts.create')
        ->set('title', 'My post')
        ->set('excerpt', 'My excerpt')
        ->set('content', 'My content')
        ->assertSet('title', 'My post')
        ->assertSet('excerpt', 'My excerpt')
        ->assertSet('content', 'My content');
});

it('validates required fields', function () {
    Volt::test('pages.posts.create')
        ->set('title', '')
        ->set('excerpt', '')
        ->set('content', '')
        ->call('store')
        ->assertHasErrors(['title', 'content'])
        ->assertHasNoErrors(['excerpt']);
});

it('creates a post', function () {
    $user = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $user->markEmailAsVerified();

    $this->actingAs($user);
    $page = visit('/posts/create');
    $page->fill('#title', 'My post')
        ->fill('#content', 'My content')
        ->click('Publish Post')
        ->assertPathIs('/posts/my-post')
        ->assertSee('Post published successfully');
});

it('creates post when store action is called on valid data', function () {
    $user = User::factory()->create([
        'role' => Role::AUTHOR,
    ]);

    $user->markEmailAsVerified();

    $component = Volt::test('pages.posts.create');
    dd($component->actingAs($user));

    $component->set('title', 'My post')
        ->set('content', 'My content')
        ->call('store')
        ->assertRedirect(route('posts.show', 'my-post'));

    expect(Post::where('user_id', $user->id)->where('title', 'My post')->exists())->toBeTrue();
});

it('displays create post form', function () {
    Volt::test('pages.posts.create')
        ->assertSee('Create New Post')
        ->assertSee('Title')
        ->assertSee('Content')
        ->assertSee('Enter post title')
        ->assertSee('Write your post content here...');
});