<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 5): void
    {
        User::factory()
            ->count($count)
            ->create();

        // default postman user
        User::create([
            'name' => 'Postman',
            'email' => 'postman@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->friends()->attach($users->random(5));
        }

        $users->each(function ($user) {
            $user->friends()->updateExistingPivot($user->friends->random()->id, ['favourite' => true]);

            $user->tutorials()->each(function ($tutorial) {
                $tutorial->update([
                    'completed' => rand(0, 1),
                ]);
            });
        });

        // Seeding quizzes
        $questions = Question::all();
        $users->each(function ($user) use ($questions) {
            $user->quizzes()->createMany([
                [ 'result' => fake()->numberBetween(0, 100) ],
                [ 'result' => fake()->numberBetween(0, 100) ],
            ])->each(function ($quiz) use ($questions) {
                $quiz->questions()->attach($questions->random(5), [
                    'answer' => fake()->numberBetween(1, 4),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });
        });
    }
}
