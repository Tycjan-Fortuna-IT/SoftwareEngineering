<?php

namespace Tests\Unit\PostControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class PostControllerUpdateTest extends APIUnitTestCase
{
    private function get_bad_request_body(): array
	{
        return [
            'title' => 123,
            'description' => 2,
        ];
    }

    private function get_good_request_body(): array
	{
        return [
            'title' => 'New post name',
            'description' => 'New post description',
        ];
    }

    public function test_posts_store_fails_validation()
    {
        $post = $this->get_random_post();

        $this->actingAs($this->get_random_user())
            ->putJson('/api/posts/' . $post->uuid, $this->get_bad_request_body())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'title' => 'The title field must be a string.',
                'description' => 'The description field must be a string.',
            ]);
    }

    public function test_update_post_fails_post_not_found(): void
    {
        $this->actingAs($this->get_random_user())
            ->putJson('/api/posts/' . $this->get_random_uuid(), $this->get_good_request_body())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_post_updates_post(): void
    {
        $post = $this->get_random_post();

        $this->actingAs($this->get_random_user())
            ->putJson('/api/posts/' . $post->uuid, $this->get_good_request_body())
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                $json->has('message');
            });

        $post->refresh();

        $this->assertEquals($this->get_good_request_body()['title'], $post->title);
        $this->assertEquals($this->get_good_request_body()['description'], $post->description);
    }

}
