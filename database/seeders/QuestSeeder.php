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
    public function run(): void
    {
        $users = User::all();

        $users->each(function ($user) {
            Quest::factory()
                ->count(3)
                ->for($user)
                ->create();
        });
    }
}
