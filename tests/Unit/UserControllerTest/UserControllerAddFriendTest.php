<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerAddFriendTest extends APIUnitTestCase
{
    public function test_users_add_friend_returns_unauthorized_error()
    {
        $this->putJson('/api/users/' . $this->get_random_uuid(), [
            'friend_uuid' => $this->get_random_uuid()
        ])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_users_add_friend_returns_already_existing_friend_error()
    {

    }

    public function test_users_add_friend_correctly()
    {

    }
}