<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function scopeExistingFavourite($query, $post_id, $user_id)
    {
        return $query->where('post_id', $post_id)->where('user_id', $user_id);
    }
    public static function totalFavourites($user_id)
    {
        return static::whereHas('post', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->count();
    }
}
