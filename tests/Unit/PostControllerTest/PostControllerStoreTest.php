<?php

namespace Tests\Unit\PostControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class PostControllerStoreTest extends APIUnitTestCase
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
        $this->actingAs($this->get_random_user())
            ->postJson('/api/posts', $this->get_bad_request_body())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'title' => 'The title field must be a string.',
                'description' => 'The description field must be a string.',
            ]);
    }

    public function test_posts_store_adds_new_post()
    {
        $this->actingAs($this->get_random_user())
            ->postJson('/api/posts', $this->get_good_request_body())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $post) {
                    PostResourceValidator::validate($post);
                })
                ->has('message');
            });
    }
}
