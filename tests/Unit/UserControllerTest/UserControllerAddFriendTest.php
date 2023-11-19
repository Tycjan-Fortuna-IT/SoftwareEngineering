<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerAddFriendTest extends APIUnitTestCase
{
    public function test_users_add_friend_fails_already_existing_friend()
    {
        $user = $this->get_random_user();
        
        $this->actingAs($user)
            ->postJson('/api/users/' . $user->uuid . '/addFriend', [
                'friend_uuid' => $user->friends()->first()->uuid
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_users_add_friend_fails_proposed_friend_user_not_found()
    {
        $user = $this->get_random_user();

        $this->actingAs($user)
            ->postJson('/api/users/' . $user->uuid . '/addFriend', [
                'friend_uuid' => $this->get_random_uuid()
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_users_add_friend_fails_validation()
    {
        $user = $this->get_random_user();
        
        $this->actingAs($user)
            ->postJson('/api/users/' . $user->uuid . '/addFriend', [
                'friend_uuid' => "..."
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_users_add_friend_adds_friend()
    {
        $user = $this->prepare_user();
        $otherUser = $this->prepare_user();

        $this->actingAs($user)
            ->postJson('/api/users/' . $user->uuid . '/addFriend', [
                'friend_uuid' => $otherUser->uuid
            ])
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertEquals($otherUser->uuid, $user->friends()->where('friend_id', $otherUser->id)->first()->uuid);
    }
}