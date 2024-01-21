<?php

namespace Tests\Unit\CommentControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class CommentControllerStoreTest extends APIUnitTestCase
{
    private function get_bad_request_body(): array
	{
        return [
            'content' => 123,
            'post_uuid' => 2,
        ];
    }

    private function get_good_request_body(): array
	{
        return [
            'content' => 'New post name',
            'post_uuid' => $this->get_random_post()->uuid,
        ];
    }

    public function test_comments_store_fails_validation()
    {
        $this->actingAs($this->get_random_user())
            ->postJson('/api/comments', $this->get_bad_request_body())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'content' => 'The content field must be a string.',
                'post_uuid' => 'The post uuid field must be a valid UUID.',
            ]);
    }

    public function test_comments_store_adds_new_comment()
    {
        $this->actingAs($this->get_random_user())
            ->postJson('/api/comments', $this->get_good_request_body())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $comment) {
                    CommentResourceValidator::validate($comment);
                })
                ->has('message');
            });
    }
}
