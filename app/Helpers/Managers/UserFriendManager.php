<?php

namespace App\Helpers\Managers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserFriendManager
{
    /**
     * Check if the given user has the given friend.
     *
     * @param User $user
     * @param User $friend
     * @return bool
     */
    public static function HasFriend(User $user, User $friend): bool
    {
        return DB::table('user_friend')
            ->where('user_id', $user->id)
            ->where('friend_id', $friend->id)
            ->exists();
    }

    /**
     * Add the given friend to the given user.
     *
     * @param User $user
     * @param User $friend
     * @return void
     */
    public static function AddFriend(User $user, User $friend): void
    {
        // relationship is bidirectional
        $user->friends()->attach($friend);
        $friend->friends()->attach($user);
    }

    /**
     * Remove the given friend from the given user.
     *
     * @param User $user
     * @param User $friend
     * @return void
     */
    public static function RemoveFriend(User $user, User $friend): void
    {
        $user->friends()->detach($friend);
        $friend->friends()->detach($user);
    }

    /**
     * Update favourite status of the given friend of the given user.
     *
     * @param User $user
     * @param User $friend
     * @param bool $favourite
     * @return void
     */
    public static function UpdateFavourite(User $user, User $friend, bool $favourite): void
    {
        $user->friends()->updateExistingPivot($friend->id, ['favourite' => $favourite]);
    }
}
