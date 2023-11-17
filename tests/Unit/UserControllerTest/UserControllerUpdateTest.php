<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerUpdateTest extends APIUnitTestCase
{
	private function get_bad_request_body(): array
	{
        return [
            'name' => 456,
            'email' => 2,
        ];
    }

    private function get_good_request_body(): array
	{
        return [
            'name' => 'New user name',
            'email' => 'user.e@gmail.com',
        ];
    }

    public function test_update_user_with_bad_request_body(): void
    {
        $user = $this->get_random_user();

        $this->putJson('/api/users/' . $user->uuid, $this->get_bad_request_body())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name', 'email'
            ]);
    }

    public function test_update_user_with_bad_uuid(): void
    {
        $this->putJson('/api/users/' . $this->get_random_uuid(), $this->get_good_request_body())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_user_with_already_existing_email(): void
    {
        $user = $this->get_random_user();
        $otherUser = $this->get_random_user();

        $this->putJson('/api/users/' . $user->uuid, [
                'email' => $otherUser->email
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'email'
            ]);
    }

    public function test_update_user_with_good_request_body(): void
    {
        $user = $this->get_random_user();

        $this->putJson('/api/users/' . $user->uuid, $this->get_good_request_body())
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertEquals($this->get_good_request_body()['name'], $user->name);
        $this->assertEquals($this->get_good_request_body()['email'], $user->email);
    }
}