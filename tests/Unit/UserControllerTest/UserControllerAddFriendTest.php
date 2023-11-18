<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerAddFriendTest extends APIUnitTestCase
{
    /*
    public function test_users_add_friend_fails_already_existing_friend()
    {

    }
    

    public function test_users_add_friend_fails_proposed_friend_user_not_found()
    {
        $user = $this->get_random_user();

        $this->actingAs($user)
            ->putJson('/api/user/' . $user->uuid . '/addFriend', [
                'friend_uuid' => $this->get_random_uuid()
            ])
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
    */

    public function test_users_add_friend_fails_validation()
    {
        $user = $this->get_random_user();
        
        $this->actingAs($user)
            ->putJson('/api/user/' . $user->uuid . '/addFriend', [
                'friend_uuid' => "..."
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_users_add_friend_adds_friend()
    {
        $user = $this->get_random_user();
        $otherUser = $this->get_random_user();
       
        $this->actingAs($otherUser);

        $this->app->get('auth')->forgetGuards(); // to have two users logged

        $this->actingAs($user)
            ->putJson('/api/user/' . $user->uuid . '/addFriend', [
                'friend_uuid' => $otherUser->uuid
            ])
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertEquals($otherUser->uuid, $user->friends->whereContains('friend_uuid', $otherUser->uuid));
    }
}