<?php

namespace Database\Seeders;

use App\Models\Quest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 5): void
    {
        $users = User::all();

        $users->each(function ($user) use ($count) {
            Quest::factory()
                ->count($count)
                ->for($user)
                ->create();
        });
    }
}
