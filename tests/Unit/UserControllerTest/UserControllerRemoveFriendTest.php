<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerRemoveFriendTest extends APIUnitTestCase
{
    public function test_users_remove_friend_fails_not_friends()
    {
        $user = $this->get_random_user();
        $friend = $this->prepare_user();
        
        $this->actingAs($user)
            ->deleteJson('/api/users/' . $user->uuid . '/removeFriend', [
                'friend_uuid' => $friend->uuid
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_users_remove_friend_fails_proposed_friend_user_not_found()
    {
        $user = $this->get_random_user();

        $this->actingAs($user)
            ->deleteJson('/api/users/' . $user->uuid . '/removeFriend', [
                'friend_uuid' => $this->get_random_uuid()
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_users_remove_friend_fails_validation()
    {
        $user = $this->get_random_user();
        
        $this->actingAs($user)
            ->deleteJson('/api/users/' . $user->uuid . '/removeFriend', [
                'friend_uuid' => "@#$%"
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_users_remove_friend_removes_friend()
    {
        $user = $this->get_random_user();
        $friend = $user->friends()->first();

        $this->actingAs($user)
            ->deleteJson('/api/users/' . $user->uuid . '/removeFriend', [
                'friend_uuid' => $friend->uuid
            ])
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertDatabaseMissing('user_friend', [
            'user_id' => $user->id,
            'friend_id' => $friend->id
        ]);
    }
}