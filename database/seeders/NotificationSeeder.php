<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 15): void
    {
        $users = User::all();

        $users->each(function (User $user) use ($count) {
            $user->notifications()->saveMany(
                Notification::factory()->count($count)->make()
            );
        });
    }
}
