<?php

namespace App\Helpers\Managers;

use App\Models\Quest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestManager
{
    /**
     * Get or generate quests for the user.
     *
     * @return Collection
     */
    public static function GetOrGenerateQuestsForUser(): Collection
    {
        $user = Auth::user();
        $quests = $user->quests;

        DB::beginTransaction();

        $quests->where('created_at', '<', now()->subDay())
            ->where('status', '!=', Quest::STATUS_IN_PROGRESS)
            ->each(function (Quest $quest) {
                $quest->delete();
            });

        $quests = $user->quests;

        for ($i = $quests->count(); $i < 3; $i++) {
            $quest = self::GenerateQuestForUser($user);

            $quests->push($quest);
        }

        DB::commit();

        return $quests;
    }

    private static function GenerateQuestForUser(User $user): Quest
    {
        // TODO: take a random quest from the pool prepared by NPC/Quest group
        // they maybe should be based on the player's level or something
        return Quest::create([
            'type' => fake()->numberBetween(1, 7),
            'status' => Quest::STATUS_IN_PROGRESS,
            'required' => fake()->numberBetween(1, 10),
            'collected' => 0,
            'reward' => fake()->numberBetween(100, 1000),
            'user_id' => $user->id,
        ]);
    }
}
