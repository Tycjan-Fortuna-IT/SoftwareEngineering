<?php

namespace Tests\Unit\TutorialControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class TutorialControllerUpdateTest extends APIUnitTestCase
{
    private function get_bad_request_body(): array
	{
        return [
            'completed' => 456,
        ];
    }

    private function get_good_request_body(): array
	{
        return [
            'completed' => true,
        ];
    }

    public function test_update_tutorial_fails_validation(): void
    {
        $user = $this->get_random_user();
        $tutorial = $user->tutorials()->first();

        $this->actingAs($user)
            ->putJson('/api/tutorials/' . $tutorial->uuid, $this->get_bad_request_body())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'completed',
            ]);
    }

    public function test_update_tutorial_fails_tutorial_not_found(): void
    {
        $this->actingAs($this->get_random_user())
            ->putJson('/api/tutorials/' . $this->get_random_uuid(), $this->get_good_request_body())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_tutorial_updates_tutorial(): void
    {
        $user = $this->get_random_user();
        $tutorial = $user->tutorials()->first();

        $this->actingAs($user)
            ->putJson('/api/tutorials/' . $tutorial->uuid, $this->get_good_request_body())
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Tutorial updated successfully',
            ]);

        $this->assertDatabaseHas('tutorials', [
            'uuid' => $tutorial->uuid,
            'completed' => true,
        ]);
    }
}
