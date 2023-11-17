<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerDestroyTest extends APIUnitTestCase
{
    public function test_users_destroy_fails_user_not_found()
    {
        $this->actingAs($this->get_random_user())
            ->delete('/api/users/' . $this->get_random_uuid())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_users_destroy_deletes_user()
    {
        $user = $this->get_random_user();

        $this->actingAs($user)
            ->delete('/api/users/' . $user->uuid)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('users', [
            'uuid' => $user->uuid
        ]);
    }
}