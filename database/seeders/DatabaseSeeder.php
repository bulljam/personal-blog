<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->create([
            'name' => 'John Doe ',
            'email' => 'john.doe@example.com',
            'role' => Role::AUTHOR,
        ]);


        $this->call([
            PostSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Alex Brown ',
            'email' => 'alex.brown@example.com',
            'role' => Role::READER,
        ]);
    }
}
