@volt
<?php
use function Livewire\Volt\{state, rules, action, layout};

state([
    'title' => '',
    'excerpt' => null,
    'content' => ''
]);


rules([
    'title' => 'required|string|max:255',
    'excerpt' => 'nullable|string|max:500',
    'content' => 'required|string|min:10',
]);

$store = action(function () {
    $this->validate();

    $post = \App\Models\Post::create([
        'title' => $this->title,
        'slug' => \Illuminate\Support\Str::slug($this->title),
        'excerpt' => $this->excerpt,
        'content' => $this->content,
        'published_at' => now(),
        'user_id' => auth()->user()->id,
    ]);

    $this->reset();

    return redirect()->route('posts.show', $post->slug)->with('success', 'Post published successfully.');
});

layout('components.layouts.blog');

?>

<div>
    <form wire:submit="store">
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
        <button type="submit">Publish</button>
    </form>
</div>
@endvolt