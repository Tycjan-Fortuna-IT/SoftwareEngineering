<?php

namespace Tests\Unit\CommentControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class CommentControllerDestroyTest extends APIUnitTestCase
{
    public function test_comments_destroy_fails_comment_not_found()
    {
        $this->actingAs($this->get_random_user())
            ->delete('/api/comments/' . $this->get_random_uuid())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_comments_destroy_deletes_comment()
    {
        $comment = $this->get_random_comment();

        $this->actingAs($this->get_random_user())
            ->delete('/api/comments/' . $comment->uuid)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id
        ]);
    }
}
