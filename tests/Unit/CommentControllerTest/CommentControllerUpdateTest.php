<?php

namespace Tests\Unit\CommentControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class CommentControllerUpdateTest extends APIUnitTestCase
{
    private function get_bad_request_body(): array
	{
        return [
            'content' => 123,
        ];
    }

    private function get_good_request_body(): array
	{
        return [
            'content' => 'New comment content',
        ];
    }

    public function test_comments_store_fails_validation()
    {
        $comment = $this->get_random_comment();

        $this->actingAs($this->get_random_user())
            ->putJson('/api/comments/' . $comment->uuid, $this->get_bad_request_body())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'content' => 'The content field must be a string.',
            ]);
    }

    public function test_update_comment_fails_comment_not_found(): void
    {
        $this->actingAs($this->get_random_user())
            ->putJson('/api/comments/' . $this->get_random_uuid(), $this->get_good_request_body())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_comment_updates_comment(): void
    {
        $comment = $this->get_random_comment();

        $this->actingAs($this->get_random_user())
            ->putJson('/api/comments/' . $comment->uuid, $this->get_good_request_body())
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                $json->has('message');
            });

        $comment->refresh();

        $this->assertEquals($this->get_good_request_body()['content'], $comment->content);
    }

}
