<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function scopeFindByUser($query, $user_id)
    {
        if (!$user_id) {
            return $query;
        }

        return $query->where('user_id', $user_id);
    }

    public function scopeFindByPost($query, $post_id)
    {
        if (!$post_id) {
            return $query;
        }

        return $query->where('post_id', $post_id);
    }

    public function scopeFindUnique($query, $post_id, $user_id)
    {
        if (!$post_id || !$user_id) {
            return $query;
        }
        return $query->findByPost($post_id)->findByUser($user_id);
    }
}
