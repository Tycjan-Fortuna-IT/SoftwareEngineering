<?php

namespace Tests\Unit\PostControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class PostControllerShowTest extends APIUnitTestCase
{
    public function test_posts_show_fails_not_found()
    {
        $this->actingAs($this->get_random_user())
            ->get('/api/posts/' . $this->get_random_uuid())
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_posts_show_returns_correct_data()
    {
        $post = $this->get_random_post();

        $this->actingAs($this->get_random_user())
            ->get('/api/posts/' . $post->uuid)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $post) {
                    PostResourceValidator::validate($post);
                });
            });
    }
}
