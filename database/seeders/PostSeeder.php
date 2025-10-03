<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Post::factory()->count(10)->create();
        // Post::factory()->count(1)->create([
        //     'title' => 'old',
        //     'published_at' => now()->subDays(3)
        // ]);
        // Post::factory()->count(1)->create([
        //     'title' => 'future',
        //     'published_at' => now()->addDays(3)
        // ]);

        Post::factory()->count(1)->create([
            'title' => 'future',
            'user_id' => User::factory()->create()->id,
            'published_at' => now()->subDays(3)
        ]);
    }
}
