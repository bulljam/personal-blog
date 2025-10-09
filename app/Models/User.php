<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isAuthor(): bool
    {
        return $this->role === Role::AUTHOR;
    }

    public function isReader(): bool
    {
        return $this->role === Role::READER;
    }

    public function scopeAuthors($query)
    {
        return $query->where('role', Role::AUTHOR->value)
            ->orderBy('name');
    }

    public function scopeAuthorsWithPosts($query)
    {
        return $query->authors()->whereHas('posts', function ($q) {
            $q->publishedPosts();
        });
    }

    public function scopeAuthorsByName($query, $name)
    {
        $query = $query->authorsWithPosts();
        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        return $query;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function favouritePost($post_id)
    {
        return $this->favourites()->where('post_id', $post_id)->first();
    }

    public function favouritePosts()
    {
        // $postsIds = $this->favourites->pluck('post_id');
        // return Post::whereIn('id', $postsIds);
        return $this->hasManyThrough(Post::class, Favourite::class, 'user_id', 'id', 'id', 'post_id');
    }

    public function hasInFavourites($post_id)
    {
        return $this->favourites()->where('post_id', $post_id)->exists();
    }
}
