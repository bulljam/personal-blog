@volt
<?php

use function Livewire\Volt\{state, mount, rules, action, layout};

state([
    'post' => null,
    'title' => '',
    'excerpt' => '',
    'content' => '',
]);

mount(function (\App\Models\Post $post) {
    $this->post = $post;

    $this->title = $post->title;
    $this->excerpt = $post->excerpt;
    $this->content = $post->content;
});

rules([
    'title' => 'required|string|max:255',
    'excerpt' => 'nullable|string|max:500',
    'content' => 'required|string|min:10',
]);

$update = action(function () {
    $this->validate();

    $this->post->update([
        'title' => $this->title,
        'slug' => \Illuminate\Support\Str::slug($this->title),
        'excerpt' => $this->excerpt,
        'content' => $this->content,
    ]);

    return redirect()->route('posts.show', $this->post->slug)->with('success', 'Post edited successfully.');
});

layout('components.layouts.blog');

?>

<div>
    <form wire:submit="update">
        @csrf
        <label for="title">Title</label>
        <input type="text" wire:model="title" />
        <div>
            @error('title')
                {{ $message }}
            @enderror
        </div>
        <label for="excerpt">Excerpt</label>
        <textarea wire:model="excerpt"></textarea>
        <div>
            @error('excerpt')
                {{ $message }}
            @enderror
        </div>
        <label for="content">Content</label>
        <textarea wire:model="content"></textarea>
        <div>
            @error('content')
                {{ $message }}
            @enderror
        </div>
        <button type="submit">Edit</button>
    </form>
</div>

@endvolt