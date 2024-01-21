<?php

namespace App\Helpers\Managers;

use App\Models\User;

class UserLevelManager
{
    /**
     * Get the experience required for the given level.
     *
     * @param int $level
     * @return int
     */
    public static function GetExpForLevel(int $level): int
    {
        return (int)(500 * pow(1.24, $level));
    }

    /**
     * Add the given experience to the given user.
     * If the user has enough experience to level up, the level will be increased and the experience will be reset.
     *
     * @param User $user
     * @param int $exp
     * @return void
     */
    public static function AddExp(User $user, int $exp): void
    {
        $currentLevel = $user->level;
        $currentExp = $user->experience;

        $newExp = $currentExp + $exp;
        $newLevel = $currentLevel + 1;

        $expForLevel = self::GetExpForLevel($newLevel);

        if ($newExp >= $expForLevel) {
            $user->level = $newLevel;
            $user->experience = 0;
        } else {
            $user->experience = $newExp;
        }

        $user->save();
    }
}
