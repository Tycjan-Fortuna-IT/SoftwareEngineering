<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            QuestionSeeder::class,
            UserSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            QuestSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
