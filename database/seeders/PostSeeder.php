<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 5): void
    {
        $users = User::all();

        $users->each(function ($user) use ($count) {
            Post::factory()->count($count)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
