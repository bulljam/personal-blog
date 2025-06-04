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
                if (! $published_at) {
                    return false;
                }

                $updated_at = $this->getAttribute('updated_at');

                return $updated_at?->gt($published_at->addSeconds(5));
            }
        );

    }
}
