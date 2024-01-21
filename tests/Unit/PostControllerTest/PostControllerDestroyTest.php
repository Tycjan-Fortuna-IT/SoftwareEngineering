<?php

namespace Tests\Unit\PostControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class PostControllerDestroyTest extends APIUnitTestCase
{
    public function test_posts_destroy_fails_post_not_found()
    {
        $this->actingAs($this->get_random_user())
            ->delete('/api/posts/' . $this->get_random_uuid())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_posts_destroy_deletes_post()
    {
        $post = $this->get_random_post();

        $this->actingAs($this->get_random_user())
            ->delete('/api/posts/' . $post->uuid)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('posts', [
            'uuid' => $post->uuid
        ]);

        $this->assertDatabaseMissing('comments', [
            'post_id' => $post->id
        ]);
    }
}
