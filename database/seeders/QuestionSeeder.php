<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 5): void
    {
        $questionPool = include app_path('Helpers/QuestionPool.php');

        foreach ($questionPool as $question) {
            Question::create($question);
        }
    }
}
