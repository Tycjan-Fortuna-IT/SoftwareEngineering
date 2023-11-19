<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class UserControllerShowTest extends APIUnitTestCase
{
    public function test_users_show_fails_not_found()
    {
        $this->actingAs($this->get_random_user())
            ->get('/api/users/' . $this->get_random_uuid())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_users_show_returns_correct_data_without_friends()
    {
        $user = $this->get_random_user();

        $this->actingAs($user)
            ->get('/api/users/' . $user->uuid)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $user) {
                    UserResourceValidator::validate($user);
                });
            });
    }

    public function test_users_show_returns_correct_data_with_friends()
    {
        $user = $this->get_random_user();

        $this->actingAs($user)
            ->get('/api/users/' . $user->uuid . '?withFriends=true')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $user) {
                    UserResourceValidator::validate($user);
                });
            });
    }
}