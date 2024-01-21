<?php

namespace Tests\Unit\CommentControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class CommentControllerShowTest extends APIUnitTestCase
{
    public function test_comments_show_fails_not_found()
    {
        $this->actingAs($this->get_random_user())
            ->get('/api/comments/' . $this->get_random_uuid())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_comments_show_returns_correct_data()
    {
        $comment = $this->get_random_comment();

        $this->actingAs($this->get_random_user())
            ->get('/api/comments/' . $comment->uuid)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $comment) {
                    CommentResourceValidator::validate($comment);
                });
            });
    }
}
