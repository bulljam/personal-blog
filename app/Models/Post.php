<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'published_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function isEdited(): Attribute
    {
        return Attribute::make(
            get: function () {
                $published_at = $this->getAttribute('published_at');
                if (!$published_at) {
                    return false;
                }

                $updated_at = $this->getAttribute('updated_at');

                return $updated_at?->gt($published_at->addSeconds(5));
            }
        );

    }

    public function scopePublishedPosts($query)
    {
        return $query
            ->whereNotNull('published_at');
    }

    public function scopeSearch($query, $value)
    {
        if (!$value) {
            return $query;
        }

        return $query->where(function ($q) use ($value) {
            $q->where('title', 'like', "%{$value}%")
                ->orWhere('content', 'like', "%{$value}%")
                ->orWhere('excerpt', 'like', "%{$value}%");
        });
    }

    public function scopeAuthor($query, $user_id)
    {
        if (!$user_id) {
            return $query;
        }

        return $query->where('user_id', $user_id);
    }
    public function scopeDate($query, $dateFilter)
    {
        if (!$dateFilter) {
            return $query;
        }

        return match ($dateFilter) {
            'today' => $query->whereDate('published_at', today()),
            'week' => $query->where('published_at', '>=', now()->startOfWeek()),
            'month' => $query->where('published_at', '>=', now()->startOfMonth()),
            'year' => $query->where('published_at', '>=', now()->startOfYear()),
            default => $query,
        };
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isLikedBy($user_id)
    {
        return $this->likes()->where('user_id', $user_id)->where('type', 'like')->exists();
    }
    public function isDislikedBy($user_id)
    {
        return $this->likes()->where('user_id', $user_id)->where('type', 'dislike')->exists();
    }
    public function likesCount()
    {
        return $this->likes()->where('type', 'like')->count();
    }
    public function dislikesCount()
    {
        return $this->likes()->where('type', 'dislike')->count();
    }

    public function UserReactionType($user_id)
    {
        $like = $this->likes()->where('user_id', $user_id)->first();
        if (!$like) {
            return;
        }
        return $like->type;
    }

    public function hasUserLiked($user_id)
    {
        return $this->UserReactionType($user_id) === 'like';
    }

    public function hasUserDisliked($user_id)
    {
        return $this->UserReactionType($user_id) === 'dislike';
    }
}
