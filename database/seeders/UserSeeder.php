<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(25)
            ->create();

        $users = User::all();

        foreach ($users as $user) {
            $user->friends()->attach($users->random(5));
        }

        $users->each(function ($user) {
            $user->friends()->updateExistingPivot($user->friends->random()->id, ['favourite' => true]);
        });
    }
}
