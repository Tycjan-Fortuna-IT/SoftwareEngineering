<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerFavouriteUpdateTest extends APIUnitTestCase
{
    public function test_users_change_favourite_friend_fails_user_is_not_your_friend() // 400
    {
        $user = $this->get_random_user();
        $friend = $this->prepare_user();
        
        $this->actingAs($user)
            ->putJson('/api/users/' . $user->uuid . '/updateFavourite', [
                'friend_uuid' => $friend->uuid,
                'favourite' => true
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_users_change_favourite_friend_fails_validation() // 422
    {
        $user = $this->get_random_user();
        
        $this->actingAs($user)
            ->putJson('/api/users/' . $user->uuid . '/updateFavourite', [
                'friend_uuid' => "@#$%",
                'favourite' => 12
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_users_change_favourite_friend_changes_favourite_friend() // 200
    {
        $user = $this->get_random_user();
        $friend = $user->friends()->first();

        $this->actingAs($user)
            ->putJson('/api/users/' . $user->uuid . '/updateFavourite', [
                'friend_uuid' => $friend->uuid,
                'favourite' => true
            ])
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertDatabaseHas('user_friend', [
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'favourite' => true
        ]);
    }
}