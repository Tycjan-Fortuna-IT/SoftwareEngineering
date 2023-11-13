<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();

        $users->each(function ($user) use ($posts) {
            for ($i = 0; $i < 5; $i++) {
                Comment::factory()->create([
                    'user_id' => $user->id,
                    'post_id' => $posts->random()->id,
                ]);
            }
        });
    }
}
