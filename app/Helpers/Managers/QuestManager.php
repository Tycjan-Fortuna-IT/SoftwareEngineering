<?php

namespace App\Helpers\Managers;

use App\Models\Quest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestManager
{
    /**
     * The number of quests a user should have at any given time.
     */
    const QUEST_COUNT = 2;

    /**
     * Get or generate quests for the user.
     *
     * @return Collection
     */
    public static function GetOrGenerateQuestsForUser(): Collection
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $quests = $user->quests;

        DB::beginTransaction();

        Quest::where('created_at', '<', Carbon::now()->subDay())
            ->orWhere('status', '!=', Quest::STATUS_IN_PROGRESS)
            ->delete();

        $user->refresh();

        $quests = $user->quests;

        for ($i = $quests->count(); $i < self::QUEST_COUNT; $i++) {
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
            'required' => fake()->numberBetween(1, 3),
            'collected' => 0,
            'reward' => fake()->numberBetween(100, 500),
            'user_id' => $user->id,
        ]);
    }

    /**
     * Progress a quest for the user.
     *
     * @param User $user
     * @param int $level The game level
     */
    public static function ProgressQuestForUser(User $user, int $level): void
    {
        DB::beginTransaction();

        $userQuests = $user->quests()
            ->where('status', Quest::STATUS_IN_PROGRESS)
            ->where('type', $level)
            ->get();

        foreach ($userQuests as $quest) {
            $quest->collected++;

            if ($quest->collected >= $quest->required) {
                $quest->status = Quest::STATUS_COMPLETED;

                UserLevelManager::AddExp($user, $quest->reward);
            }

            $quest->save();
        }

        DB::commit();
    }
}
